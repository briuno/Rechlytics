<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Buscar clientes que têm mensagens
$clientes = $conn->query("SELECT DISTINCT usuarios.id, usuarios.nome FROM chat_mensagens 
                          JOIN usuarios ON chat_mensagens.usuario_id = usuarios.id");

$cliente_id = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : null;

// Enviar resposta
if ($_SERVER["REQUEST_METHOD"] == "POST" && $cliente_id) {
    $mensagem = $_POST['mensagem'];
    $remetente = 'admin';

    $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $cliente_id, $mensagem, $remetente);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Chat - Rechlytics</title>
    <script>
        function atualizarMensagens() {
            let clienteId = "<?php echo $cliente_id; ?>";
            if (!clienteId) return;

            fetch('includes/get_mensagens.php?cliente_id=' + clienteId)
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
    <h2>Gestão de Chat</h2>

    <h3>Selecionar Cliente:</h3>
    <ul>
        <?php while ($cliente = $clientes->fetch_assoc()): ?>
            <li><a href="admin_chat.php?cliente_id=<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nome']); ?></a></li>
        <?php endwhile; ?>
    </ul>

    <?php if ($cliente_id): ?>
        <h3>Histórico de Mensagens</h3>

        <div id="chat-box" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;"></div>

        <form action="admin_chat.php?cliente_id=<?php echo $cliente_id; ?>" method="POST">
            <label>Mensagem:</label>
            <textarea name="mensagem" required></textarea>
            <button type="submit">Responder</button>
        </form>
    <?php endif; ?>

    <p><a href="admin_dashboard.php">Voltar</a></p>
</body>
</html>
