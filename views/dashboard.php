<?php
session_start();
include __DIR__ . '/../controllers/session_check.php';
include __DIR__ . '/../config/db.php';

$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: $base_url/views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : "Usuário";
$usuario_nome = htmlspecialchars($usuario_nome, ENT_QUOTES, 'UTF-8');

$stmt = $conn->prepare("SELECT id, nome, url FROM dashboards WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../public/css/dashboard.css">
    <script src="../public/js/script.js" defer></script>
    <title>Dashboard - Rechlytics</title>
</head>
<body>

<div class="principal">
    <div class="header">
        <h2 class="page-title">Bem-vindo, <?php echo $usuario_nome; ?>!</h2>
        <a href="<?php echo $base_url; ?>/views/logout.php">
            <img src="https://cdn-icons-png.flaticon.com/512/5509/5509486.png" alt="ImagemSAIR" height="45px">
        </a>
    </div>
    
    <section class="menu">
        <div class="card">
            <a href="<?php echo $base_url; ?>/views/perfil.php">
                <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="userIcon" height="300px">
            </a>
        </div>

        <div class="card">
            <a href="<?php echo $base_url; ?>/views/chat.php">
                <img src="https://cdn-icons-png.flaticon.com/512/724/724715.png" alt="chatIcon" class="chat-icon" height="300px">
            </a>
        </div>

        <div class="card">
            <div class="dashboard-container" id="dashboardToggle">
                <img src="https://cdn-icons-png.flaticon.com/512/17749/17749234.png" alt="dashboardIcon" class="dashboard-icon">

                <div class="dashboard-menu" id="dashboardMenu">
                    <h3>Seus Dashboards</h3>
                    <?php if ($result->num_rows > 0): ?>
                        <ul>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li>
                                    <a href="<?php echo $base_url; ?>/views/ver_dashboard.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                                        <?php echo htmlspecialchars($row['nome']); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nenhum dashboard disponível.</p>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                const dashboardToggle = document.getElementById('dashboardToggle');
                const dashboardMenu = document.getElementById('dashboardMenu');

                dashboardToggle.addEventListener('click', function(event) {
                    event.stopPropagation();
                    dashboardMenu.classList.toggle('active');
                });

                document.addEventListener('click', function() {
                    dashboardMenu.classList.remove('active');
                });
            </script>
        </div>
    </section>
</div>

</body>
</html>
