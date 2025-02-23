<?php
session_start();
include __DIR__ . '/config/session_check.php';
include __DIR__ . '/config/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /auth/login.php");
    exit();
}

// Pega o nome do usuário da sessão, se existir
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : "Usuário";

// Evita erro de NULL no htmlspecialchars()
$usuario_nome = htmlspecialchars($usuario_nome, ENT_QUOTES, 'UTF-8');

// Buscar os dashboards do usuário
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
    <h2>Bem-vindo, <?php echo $usuario_nome; ?>!</h2>
    <p><a href="/client/perfil.php">Editar Perfil</a></p>

    <h3>Seus Dashboards</h3>

    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="/client/ver_dashboard.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                        <?php echo htmlspecialchars($row['nome']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum dashboard disponível.</p>
    <?php endif; ?>

    <p><a href="/client/chat.php">Falar com Suporte</a></p>
    <p><a href="/client/logout.php">Sair</a></p>
</body>
</html>
