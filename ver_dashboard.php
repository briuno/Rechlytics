<?php
session_start();
include 'includes/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$dashboard_id = $_GET['id'] ?? 0;

// Busca o dashboard selecionado
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
    <p><a href="dashboard.php">Voltar</a></p>
</body>
</html>
