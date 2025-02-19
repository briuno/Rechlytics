<?php
session_start();
include 'includes/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Busca os dashboards disponíveis para o usuário logado
$stmt = $conn->prepare("SELECT id, nome, url FROM dashboards WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Rechlytics</title>
</head>
<body>
    <h2>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</h2>
    <h3>Seus Dashboards</h3>

    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="ver_dashboard.php?id=<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['nome']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum dashboard disponível.</p>
    <?php endif; ?>

    <p><a href="chat.php">Falar com Suporte</a></p>
    <p><a href="logout.php">Sair</a></p>
</body>
</html>
