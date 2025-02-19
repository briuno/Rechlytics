<?php
session_start();
include 'includes/db.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT id, nome, email FROM usuarios WHERE tipo = 'cliente'");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - Rechlytics</title>
</head>
<body>
    <h2>Painel Administrativo</h2>
    <h3>Clientes Cadastrados</h3>

    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($row['nome']); ?> - <?php echo htmlspecialchars($row['email']); ?></li>
        <?php endwhile; ?>
    </ul>

    <p><a href="admin_dashboards.php">Gerenciar Dashboards</a></p>
    <p><a href="admin_chat.php">Ver Mensagens</a></p>
    <p><a href="logout.php">Sair</a></p>
</body>
</html>
