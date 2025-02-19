<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Enviar nova mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = $_POST['mensagem'];
    $remetente = 'cliente';

    $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuario_id, mensagem, remetente) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $mensagem, $remetente);
    $stmt->execute();
}

// Buscar mensagens do cliente e do admin
$stmt = $conn->prepare("SELECT mensagem, remetente, data_envio FROM chat_mensagens WHERE usuario_id = ? ORDER BY data_envio ASC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Chat com Suporte - Rechlytics</title>
</head>
<body>
    <h2>Chat com Suporte</h2>

    <div style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;">
        <?php while ($row = $result->fetch_assoc()): ?>
            <p>
                <strong><?php echo ($row['remetente'] === 'cliente') ? "Você" : "Suporte"; ?>:</strong>
                <?php echo htmlspecialchars($row['mensagem']); ?>
                <small>(<?php echo $row['data_envio']; ?>)</small>
            </p>
        <?php endwhile; ?>
    </div>

    <form action="chat.php" method="POST">
        <label>Mensagem:</label>
        <textarea name="mensagem" required></textarea>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="dashboard.php">Voltar</a></p>
</body>
</html>
