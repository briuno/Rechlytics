<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';

// Caminho base para evitar problemas no redirecionamento
$base_url = dirname($_SERVER['SCRIPT_NAME'], 3);

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $base_url/views/login.php");
    exit();
}

// Verifica se a tabela 'logs' existe antes de tentar buscar os registros
$tableExistsQuery = $conn->query("SHOW TABLES LIKE 'logs'");
if ($tableExistsQuery->num_rows > 0) {
    // Buscar os logs do sistema
    $stmt = $conn->prepare("SELECT logs.id, usuarios.nome AS usuario, logs.acao, logs.data 
                            FROM logs 
                            LEFT JOIN usuarios ON logs.usuario_id = usuarios.id 
                            ORDER BY logs.data DESC 
                            LIMIT 100");
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false; // Nenhuma tabela 'logs' encontrada
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Auditoria de Logs - Rechlytics</title>
</head>
<body>
    <h2>Auditoria de Logs</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Ação</th>
                <th>Data</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['usuario'] ?? 'Sistema'); ?></td>
                    <td><?php echo htmlspecialchars($row['acao']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($row['data'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Nenhuma ação registrada no sistema.</p>
    <?php endif; ?>

    <p><a href="<?php echo $base_url; ?>/views/admin/admin_dashboard.php">Voltar ao Painel</a></p>
</body>
</html>
