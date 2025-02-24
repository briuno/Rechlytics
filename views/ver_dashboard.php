<?php
session_start();
include __DIR__ . '/../controllers/session_check.php';
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /rechlytics/views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$dashboard_id = $_GET['id'] ?? 0;

// Prevenção contra SQL Injection
$stmt = $conn->prepare("SELECT nome, url FROM dashboards WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $dashboard_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Dashboard não encontrado ou acesso negado.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['nome']); ?> - Rechlytics</title>
</head>
<body>
    <h2><?php echo htmlspecialchars($row['nome']); ?></h2>
    <iframe src="<?php echo htmlspecialchars($row['url']); ?>" width="100%" height="600px"></iframe>
    <p><a href="/rechlytics/views/dashboard.php">Voltar</a></p>
</body>
</html>
