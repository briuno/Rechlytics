<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php'; // Adicionado corretamente
include __DIR__ . '/../../controllers/email.php'; // Inclui o sistema de e-mail

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: /rechlytics/views/login.php");
    exit();
}

// Buscar clientes que têm mensagens
$clientes = $conn->query("SELECT DISTINCT usuarios.id, usuarios.nome, usuarios.email 
                          FROM chat_mensagens 
                          JOIN usuarios ON chat_mensagens.usuario_id = usuarios.id");

$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : null;

// Buscar mensagens do cliente selecionado
$mensagens = [];
if ($cliente_id) {
    $stmt = $conn->prepare("SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $mensagens = $stmt->get_result();
}

// Enviar resposta e notificar o cliente por e-mail
if ($_SERVER["REQUEST_METHOD"] == "POST" && $cliente_id) {
    $mensagem = trim($_POST['mensagem']);
    $remetente = 'admin';

    $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $cliente_id, $mensagem, $remetente);
    $stmt->execute();

    // **Aqui estava o erro: A função registrarLog não era encontrada porque o log.php não estava incluído corretamente**
    registrarLog($conn, $_SESSION['usuario_id'], "Respondeu no chat para o cliente ID: $cliente_id");

    // Buscar o e-mail do cliente
    $stmt = $conn->prepare("SELECT email FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $stmt->bind_result($email_cliente);
    $stmt->fetch();
    $stmt->close();

    if (!empty($email_cliente)) {
        // Enviar notificação por e-mail
        $assunto = "Nova resposta no chat - Rechlytics";
        $mensagem_email = "Olá, você recebeu uma nova resposta no chat do suporte. Acesse o link abaixo para visualizar:\n\nhttps://rechlytics.com/views/chat.php";

        enviarEmail($email_cliente, $assunto, $mensagem_email);
    }

    echo "<script>window.location.href='/rechlytics/views/admin/admin_chat.php?cliente_id=$cliente_id';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Chat - Rechlytics</title>
</head>
<body>
    <h2>Gestão de Chat</h2>

    <h3>Selecionar Cliente:</h3>
    <ul>
        <?php while ($cliente = $clientes->fetch_assoc()): ?>
            <li><a href="/rechlytics/views/admin/admin_chat.php?cliente_id=<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nome']); ?></a></li>
        <?php endwhile; ?>
    </ul>

    <?php if ($cliente_id): ?>
        <h3>Histórico de Mensagens</h3>

        <div style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;">
            <?php while ($row = $mensagens->fetch_assoc()): ?>
                <p>
                    <strong><?php echo ($row['remetente'] === 'cliente') ? "Cliente" : "Suporte"; ?>:</strong>
                    <?php echo htmlspecialchars($row['mensagem']); ?>
                    <small>(<?php echo date("d/m/Y H:i", strtotime($row['data_envio'])); ?>)</small>
                </p>
            <?php endwhile; ?>
        </div>

        <form action="/rechlytics/views/admin/admin_chat.php?cliente_id=<?php echo $cliente_id; ?>" method="POST">
            <label>Mensagem:</label>
            <textarea name="mensagem" required></textarea>
            <button type="submit">Responder</button>
        </form>
    <?php else: ?>
        <p>Selecione um cliente para visualizar o chat.</p>
    <?php endif; ?>

    <p><a href="/rechlytics/views/admin/admin_dashboard.php">Voltar</a></p>
</body>
</html>
