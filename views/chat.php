<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/session_check.php';
include __DIR__ . '/../controllers/log.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    dirname($_SERVER['SCRIPT_NAME'], 2),
    '/'
);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: $base_url/views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// 1. Capturar histórico de mensagens (cliente + suporte)
$historico = [];
$queryHist = "SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC";
$stmtHist = $conn->prepare($queryHist);
$stmtHist->bind_param("i", $usuario_id);
$stmtHist->execute();
$resHist = $stmtHist->get_result();

while ($row = $resHist->fetch_assoc()) {
    $historico[] = $row;
}
$stmtHist->close();

// 2. Enviar nova mensagem
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensagem = trim($_POST['mensagem']);
    $mensagem = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); // Prevê XSS
    $remetente = 'cliente';

    if (!empty($mensagem)) {
        // Inserção na tabela de chat
        $stmtIns = $conn->prepare(
            "INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)"
        );
        $stmtIns->bind_param("iss", $usuario_id, $mensagem, $remetente);
        $stmtIns->execute();
        $stmtIns->close();

        // Registrar log de envio
        registrarLog($conn, $_SESSION['usuario_id'], "Enviou mensagem no chat");

        // Redirecionar para evitar reenvio no F5
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Chat com Suporte – Rechlytics</title>
    <style>
        /* Estilização básica para diferenciar remetentes */
        #chat-box {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #CCC;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #F9F9F9;
        }
        .msg-cliente {
            text-align: right;
            margin: 8px 0;
        }
        .msg-suporte {
            text-align: left;
            margin: 8px 0;
        }
        .msg-conteudo {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            max-width: 70%;
            font-size: 14px;
        }
        .msg-cliente .msg-conteudo {
            background-color: #DCF8C6;
        }
        .msg-suporte .msg-conteudo {
            background-color: #FFFFFF;
            border: 1px solid #DDD;
        }
        .msg-data {
            font-size: 12px;
            color: #777;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <h2>Chat com Suporte</h2>

    <!-- 3. Exibição do histórico -->
    <div id="chat-box">
        <?php if (empty($historico)): ?>
            <p style="text-align: center; color: #777;">Nenhuma mensagem trocada ainda.</p>
        <?php else: ?>
            <?php foreach ($historico as $msg): ?>
                <?php
                    // Identificar a classe conforme remetente
                    $classe = ($msg['remetente'] === 'cliente') ? 'msg-cliente' : 'msg-suporte';
                    $quem = ($msg['remetente'] === 'cliente') ? 'Você' : 'Suporte';
                    $dataFormatada = date('d/m/Y H:i', strtotime($msg['data_envio']));
                ?>
                <div class="<?php echo $classe; ?>">
                    <div class="msg-conteudo">
                        <?php echo nl2br(htmlspecialchars($msg['mensagem'], ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                    <div class="msg-data">
                        <strong><?php echo $quem; ?>:</strong> <?php echo $dataFormatada; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- 4. Formulário de envio -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label for="mensagem">Mensagem:</label><br>
        <textarea id="mensagem" name="mensagem" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/dashboard.php">Voltar</a></p>
</body>
</html>
