<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';

// Obtém todos os usuários para o filtro
$usuarios_result = $conn->query("SELECT id, nome FROM usuarios ORDER BY nome");
$usuarios = $usuarios_result ? $usuarios_result->fetch_all(MYSQLI_ASSOC) : [];

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3), '/');

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $base_url/views/login.php");
    exit();
}

// Verifica se a tabela 'logs' existe antes de tentar buscar os registros
$tableExistsQuery = $conn->query("SHOW TABLES LIKE 'logs'");
// Parâmetros de filtro
$usuario_id = isset($_GET['usuario_id']) ? trim($_GET['usuario_id']) : '';
$tipo_log   = isset($_GET['tipo_log']) ? trim($_GET['tipo_log']) : '';
$apos_login = isset($_GET['apos_login']) && $usuario_id !== '';
$ultimo_login = null;

if ($tableExistsQuery->num_rows > 0) {
    $query  = "SELECT logs.id, usuarios.nome AS usuario, logs.acao, logs.data FROM logs LEFT JOIN usuarios ON logs.usuario_id = usuarios.id";
    $cond   = [];
    $params = [];
    $types  = '';

    if ($usuario_id !== '') {
        $cond[] = 'logs.usuario_id = ?';
        $params[] = $usuario_id;
        $types .= 'i';

        if ($apos_login) {
            $loginStmt = $conn->prepare("SELECT data FROM logs WHERE usuario_id = ? AND acao LIKE '%Login%' ORDER BY data DESC LIMIT 1");
            $loginStmt->bind_param('i', $usuario_id);
            $loginStmt->execute();
            $loginStmt->bind_result($ultimo_login);
            $loginStmt->fetch();
            $loginStmt->close();
            if ($ultimo_login) {
                $cond[] = 'logs.data >= ?';
                $params[] = $ultimo_login;
                $types .= 's';
            }
        }
    }

    if ($tipo_log !== '') {
        $cond[] = 'logs.acao LIKE ?';
        $params[] = '%' . $tipo_log . '%';
        $types .= 's';
    }

    if ($cond) {
        $query .= ' WHERE ' . implode(' AND ', $cond);
    }
    $query .= ' ORDER BY logs.data DESC LIMIT 100';

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
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

    <form method="GET">
        <label for="usuario_id">Usuário:</label>
        <select name="usuario_id" id="usuario_id">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?php echo $u['id']; ?>" <?php echo ($usuario_id === strval($u['id'])) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($u['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tipo_log">Tipo de Log:</label>
        <input type="text" name="tipo_log" id="tipo_log" value="<?php echo htmlspecialchars($tipo_log); ?>">

        <label>
            <input type="checkbox" name="apos_login" value="1" <?php echo $apos_login ? 'checked' : ''; ?>>
            Apenas após o último login
        </label>

        <button type="submit">Filtrar</button>
    </form>

    <?php if ($apos_login && $usuario_id !== '' && $ultimo_login): ?>
        <p>Último login em: <?php echo date('d/m/Y H:i', strtotime($ultimo_login)); ?></p>
    <?php endif; ?>

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
