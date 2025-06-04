<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/session_check.php';
include __DIR__ . '/../controllers/log.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: $base_url/views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Enviar nova mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = trim($_POST['mensagem']);
    $mensagem = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); // Evita XSS
    $remetente = 'cliente';

    if (!empty($mensagem)) {
        // ALTERAÇÃO: INSERT vai para chat_mensagens
        $stmt = $conn->prepare(
            "INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $usuario_id, $mensagem, $remetente);
        $stmt->execute();

        registrarLog($conn, $_SESSION['usuario_id'], "Enviou mensagem no chat");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Chat com Suporte - Rechlytics</title>

</head>
<body>
    <h2>Chat com Suporte</h2>

    <div id="chat-box"></div>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label>Mensagem:</label>
        <textarea name="mensagem" required></textarea>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/dashboard.php">Voltar</a></p>
</body>
</html>
