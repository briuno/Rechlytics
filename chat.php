<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = $_POST['mensagem'];
    $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['usuario_id'], $mensagem);
    $stmt->execute();
}

$result = $conn->query("SELECT mensagem, data_envio FROM chat_mensagens WHERE usuario_id = {$_SESSION['usuario_id']} ORDER BY data_envio DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Suporte - Rechlytics</title>
</head>
<body>
    <h2>Chat com Suporte</h2>

    <form action="chat.php" method="POST">
        <label>Mensagem:</label>
        <textarea name="mensagem" required></textarea>
        <button type="submit">Enviar</button>
    </form>

    <h3>Histórico</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($row['mensagem']); ?> (<?php echo $row['data_envio']; ?>)</li>
        <?php endwhile; ?>
    </ul>

    <p><a href="dashboard.php">Voltar</a></p>
</body>
</html>
