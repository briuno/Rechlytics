<?php
session_start();
include __DIR__ . '/config/db.php';
include __DIR__ . '/config/session_check.php';
include __DIR__ . '/config/log.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Enviar nova mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = trim($_POST['mensagem']);
    $mensagem = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); // Evita XSS
    $remetente = 'cliente';

    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)");
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
    <script>
        function atualizarMensagens() {
            fetch('/client/get_mensagens.php')
                .then(response => response.json())
                .then(data => {
                    let chatBox = document.getElementById("chat-box");
                    chatBox.innerHTML = "";

                    data.forEach(msg => {
                        chatBox.innerHTML += `<p><strong>${msg.remetente}:</strong> ${msg.mensagem} <small>(${msg.data_envio})</small></p>`;
                    });

                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        }

        setInterval(atualizarMensagens, 5000); // Atualiza a cada 5 segundos
        window.onload = atualizarMensagens;
    </script>
</head>
<body>
    <h2>Chat com Suporte</h2>

    <div id="chat-box" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;"></div>

    <form action="/client/chat.php" method="POST">
        <label>Mensagem:</label>
        <textarea name="mensagem" required></textarea>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="/client/dashboard.php">Voltar</a></p>
</body>
</html>
