<?php
session_start();
include 'includes/session_check_admin.php';
include 'includes/db.php';
include 'includes/log.php'; // Registro de logs

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Rechlytics</title>
</head>
<body>
    <h2>Painel Administrativo</h2>
    <h3>Usuários Cadastrados</h3>

    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['nome']); ?></strong> - 
                    <?php echo htmlspecialchars($row['email']); ?> - 
                    <?php echo htmlspecialchars(ucfirst($row['tipo'])); ?>  
                    (Cadastrado em: <?php echo date("d/m/Y H:i", strtotime($row['data_criacao'])); ?>) 
                    - <a href="admin_editar_usuario.php?id=<?php echo $row['id']; ?>">Editar</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum usuário cadastrado.</p>
    <?php endif; ?>

    <h3>Gerenciamento</h3>
    <p><a href="admin_dashboards.php">Gerenciar Dashboards</a></p>
    <p><a href="admin_logs.php">Ver Auditoria de Logs</a></p>
    <p><a href="admin_chat.php">Ver Mensagens</a></p>
    <p><a href="logout.php">Sair</a></p>
</body>
</html>
