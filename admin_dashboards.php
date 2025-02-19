<?php
session_start();
include 'includes/db.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $nome = $_POST['nome'];
    $url = $_POST['url'];

    $stmt = $conn->prepare("INSERT INTO dashboards (usuario_id, nome, url) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $nome, $url);
    $stmt->execute();
}

$result = $conn->query("SELECT id, nome, url FROM dashboards");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Dashboards - Rechlytics</title>
</head>
<body>
    <h2>Gerenciar Dashboards</h2>

    <form action="admin_dashboards.php" method="POST">
        <label>ID do Cliente:</label>
        <input type="number" name="usuario_id" required>
        <label>Nome do Dashboard:</label>
        <input type="text" name="nome" required>
        <label>URL do Dashboard:</label>
        <input type="text" name="url" required>
        <button type="submit">Adicionar Dashboard</button>
    </form>

    <h3>Dashboards Cadastrados</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($row['nome']); ?> - <a href="<?php echo htmlspecialchars($row['url']); ?>">Ver</a></li>
        <?php endwhile; ?>
    </ul>

    <p><a href="admin_dashboard.php">Voltar</a></p>
</body>
</html>
