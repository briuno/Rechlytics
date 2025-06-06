<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    $base_url = rtrim(
        (isset($_SERVER['HTTPS']) ? "https" : "http") .
        "://" . $_SERVER['HTTP_HOST'] .
        dirname($_SERVER['SCRIPT_NAME'], 3),
        '/'
    );
    header("Location: $base_url/views/login.php");
    exit();
}

// Obtém todos os usuários para o filtro
$usuarios_result = $conn->query("SELECT id, nome FROM usuarios ORDER BY nome");
$usuarios = $usuarios_result ? $usuarios_result->fetch_all(MYSQLI_ASSOC) : [];

// Obtém todos os tipos de log para o filtro
$tipos_log_result = $conn->query("SELECT DISTINCT acao FROM logs ORDER BY acao");
$tipos_log = $tipos_log_result ? $tipos_log_result->fetch_all(MYSQLI_ASSOC) : [];

// Parâmetros de filtro
$usuario_id = isset($_GET['usuario_id']) ? trim($_GET['usuario_id']) : '';
$tipo_log   = isset($_GET['tipo_log']) ? trim($_GET['tipo_log']) : '';

// Constrói consulta base
$query  = "SELECT logs.id, usuarios.nome AS usuario, logs.acao, logs.data
           FROM logs
           LEFT JOIN usuarios ON logs.usuario_id = usuarios.id";
$cond   = [];
$params = [];
$types  = '';

// Filtro por usuário
if ($usuario_id !== '') {
    $cond[]    = 'logs.usuario_id = ?';
    $params[]  = $usuario_id;
    $types    .= 'i';
}

// Filtro por tipo de log
if ($tipo_log !== '') {
    $cond[]    = 'logs.acao = ?';
    $params[]  = $tipo_log;
    $types    .= 's';
}

// Se houver condições, adiciona WHERE
if (!empty($cond)) {
    $query .= ' WHERE ' . implode(' AND ', $cond);
}

$query .= ' ORDER BY logs.data DESC LIMIT 100';

// Prepara e executa statement
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/estilos.css">
    <script src="../../public/js/script.js" defer></script>
    <title>Auditoria de Logs – Rechlytics</title>
</head>
<body>
    <h2 class="page-title">Auditoria de Logs</h2>

    <form method="GET">
        <!-- Filtro por Usuário -->
        <label for="usuario_id">Usuário:</label>
        <select name="usuario_id" id="usuario_id">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $u): ?>
                <option
                    value="<?php echo $u['id']; ?>"
                    <?php echo ($usuario_id === strval($u['id'])) ? 'selected' : ''; ?>
                >
                    <?php echo htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Filtro por Tipo de Log -->
        <label for="tipo_log">Tipo de Log:</label>
        <select name="tipo_log" id="tipo_log">
            <option value="">Todos</option>
            <?php foreach ($tipos_log as $t): ?>
                <option
                    value="<?php echo htmlspecialchars($t['acao'], ENT_QUOTES, 'UTF-8'); ?>"
                    <?php echo ($tipo_log === $t['acao']) ? 'selected' : ''; ?>
                >
                    <?php echo htmlspecialchars($t['acao'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filtrar</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['acao'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($row['data'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma ação registrada no sistema.</p>
    <?php endif; ?>

    <?php
    // Reconstrói $base_url para o link de retorno
    $base_url = rtrim(
        (isset($_SERVER['HTTPS']) ? "https" : "http") .
        "://" . $_SERVER['HTTP_HOST'] .
        dirname($_SERVER['SCRIPT_NAME'], 3),
        '/'
    );
    ?>
    <p><a href="<?php echo $base_url; ?>/views/admin/admin_dashboard.php">Voltar ao Painel</a></p>
</body>
</html>
