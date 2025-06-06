<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php';
include __DIR__ . '/../../controllers/email.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    dirname($_SERVER['SCRIPT_NAME'], 3),
    '/'
);

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $base_url/views/login.php");
    exit();
}

// Buscar clientes que têm mensagens
$clientes = $conn->query(
    "SELECT DISTINCT u.id, u.nome, u.email
     FROM chat_mensagens AS cm
     JOIN usuarios AS u ON cm.usuario_id = u.id
     ORDER BY u.nome"
);

$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : null;

// Buscar mensagens do cliente selecionado
$mensagens = [];
if ($cliente_id) {
    $stmt = $conn->prepare(
        "SELECT mensagem, remetente, data_envio
         FROM chat_mensagens
         WHERE usuario_id = ?
         ORDER BY data_envio ASC"
    );
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $mensagens = $stmt->get_result();
    $stmt->close();
}

// Enviar resposta e notificar o cliente por e-mail
if ($_SERVER["REQUEST_METHOD"] === "POST" && $cliente_id) {
    $mensagem = trim($_POST['mensagem']);
    $remetente = 'admin';

    $stmt = $conn->prepare(
        "INSERT INTO chat_mensagens (usuario_id, mensagem, remetente)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iss", $cliente_id, $mensagem, $remetente);
    $stmt->execute();
    $stmt->close();

    // Registrar log
    registrarLog(
        $conn,
        $_SESSION['usuario_id'],
        "Respondeu no chat para o cliente ID: $cliente_id"
    );

    // Buscar o e-mail do cliente
    $stmt = $conn->prepare("SELECT email FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $stmt->bind_result($email_cliente);
    $stmt->fetch();
    $stmt->close();

    if (!empty($email_cliente)) {
        // Enviar notificação por e-mail
        $assunto = "Nova resposta no chat – Rechlytics";
        $mensagem_email = "Olá, você recebeu uma nova resposta no chat do suporte. "
                        . "Acesse o link abaixo para visualizar:\n\n"
                        . "$base_url/views/chat.php";

        enviarEmail($email_cliente, $assunto, $mensagem_email);
    }

    header("Location: $base_url/views/admin/admin_chat.php?cliente_id=$cliente_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/estilos.css">
    <script src="../../public/js/script.js" defer></script>
    <title>Gestão de Chat – Rechlytics</title>
</head>
<body>
    <h2>Gestão de Chat</h2>

    <h3>Selecionar Cliente:</h3>
    <ul>
        <?php while ($cliente = $clientes->fetch_assoc()): ?>
            <?php
                // Para cada cliente, obter o remetente da última mensagem
                $stmtUlt = $conn->prepare(
                    "SELECT remetente
                     FROM chat_mensagens
                     WHERE usuario_id = ?
                     ORDER BY data_envio DESC
                     LIMIT 1"
                );
                $stmtUlt->bind_param("i", $cliente['id']);
                $stmtUlt->execute();
                $stmtUlt->bind_result($ultimo_remetente);
                $stmtUlt->fetch();
                $stmtUlt->close();

                // Se a última mensagem for do cliente, marca pendência
                $pendencia = ($ultimo_remetente === 'cliente');
            ?>
            <li class="<?php echo $pendencia ? 'pendencia' : ''; ?>">
                <a href="<?php echo $base_url; ?>/views/admin/admin_chat.php?cliente_id=<?php echo $cliente['id']; ?>">
                    <?php if ($pendencia): ?>
                        &#9888; <!-- Símbolo de alerta -->
                    <?php endif; ?>
                    <?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php if ($cliente_id): ?>
        <h3>Histórico de Mensagens</h3>

        <!-- Contêiner com rolagem vertical (até 500px) -->
        <div id="chat-box">
            <?php if ($mensagens->num_rows === 0): ?>
                <p style="text-align: center; color: #777;">Sem mensagens neste chat.</p>
            <?php else: ?>
                <?php while ($row = $mensagens->fetch_assoc()): ?>
                    <?php
                        $classe = ($row['remetente'] === 'cliente') ? 'msg-cliente' : 'msg-suporte';
                        $quem    = ($row['remetente'] === 'cliente') ? 'Cliente' : 'Suporte';
                        $dataFormatada = date("d/m/Y H:i", strtotime($row['data_envio']));
                    ?>
                    <div class="<?php echo $classe; ?>">
                        <div class="msg-conteudo">
                            <strong><?php echo $quem; ?>:</strong><br>
                            <?php echo nl2br(htmlspecialchars($row['mensagem'], ENT_QUOTES, 'UTF-8')); ?>
                            <div class="msg-data"><?php echo $dataFormatada; ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <form action="<?php echo $base_url; ?>/views/admin/admin_chat.php?cliente_id=<?php echo $cliente_id; ?>" method="POST">
            <label>Mensagem:</label><br>
            <textarea name="mensagem" rows="4" cols="60" required></textarea><br><br>
            <button type="submit">Responder</button>
        </form>
    <?php else: ?>
        <p>Selecione um cliente para visualizar o chat.</p>
    <?php endif; ?>

    <p><a href="<?php echo $base_url; ?>/views/admin/admin_dashboard.php">Voltar</a></p>
</body>
</html>
