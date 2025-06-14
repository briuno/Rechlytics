<?php
session_start();
require_once __DIR__ . '/../../controllers/session_check_admin.php'; 
require_once __DIR__ . '/../../config/db.php'; 
require_once __DIR__ . '/../../controllers/log.php'; // Agora usando require_once

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3), '/');

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $base_url/views/login.php");
    exit();
}

// Registra o acesso ao painel administrativo
registrarLog($conn, $_SESSION['usuario_id'], "Acessou o Painel Administrativo");

// Buscar lista de clientes cadastrados
$stmt = $conn->prepare("SELECT id, nome, email, tipo, data_criacao FROM usuarios ORDER BY data_criacao DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/estilos.css">
    <script src="../../public/js/script.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Rechlytics</title>
</head>
<body>
    <h2 class="page-title">Painel Administrativo</h2>
    <h3>Usuários Cadastrados</h3>

    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['nome']); ?></strong> - 
                    <?php echo htmlspecialchars($row['email']); ?> - 
                    <?php echo htmlspecialchars(ucfirst($row['tipo'])); ?>  
                    (Cadastrado em: <?php echo date("d/m/Y H:i", strtotime($row['data_criacao'])); ?>) 
                    - <a href="<?php echo $base_url; ?>/views/admin/admin_editar_usuario.php?id=<?php echo $row['id']; ?>">Editar</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum usuário cadastrado.</p>
    <?php endif; ?>

    <h3>Gerenciamento</h3>
    <p><a href="<?php echo $base_url; ?>/views/admin/admin_dashboards.php">Gerenciar Dashboards</a></p>
    <p><a href="<?php echo $base_url; ?>/views/admin/admin_logs.php">Ver Auditoria de Logs</a></p>
    <p><a href="<?php echo $base_url; ?>/views/admin/admin_chat.php">Ver Mensagens</a></p>
    <p><a href="<?php echo $base_url; ?>/views/logout.php">Sair</a></p>
</body>
</html>
