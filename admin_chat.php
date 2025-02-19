<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT chat_mensagens.id, usuarios.nome, chat_mensagens.mensagem, chat_mensagens.data_envio 
                        FROM chat_mensagens 
                        JOIN usuarios ON chat_mensagens.usuario_id = usuarios.id 
                        ORDER BY chat_mensagens.data_envio DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mensagens - Rechlytics</title>
</head>
<body>
    <h2>Mensagens dos Clientes</h2>

    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><strong><?php echo htmlspecialchars($row['nome']); ?>:</strong> <?php echo htmlspecialchars($row['mensagem']); ?> (<?php echo $row['data_envio']; ?>)</li>
        <?php endwhile; ?>
    </ul>

    <p><a href="admin_dashboard.php">Voltar</a></p>
</body>
</html>
