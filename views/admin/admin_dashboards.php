<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    dirname($_SERVER['SCRIPT_NAME'], 3),
    '/'
);

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: $base_url/views/login.php");
    exit();
}

// Buscar lista de clientes para o select (ID – Nome)
$usuarios_result = $conn->query("SELECT id, nome FROM usuarios WHERE tipo = 'cliente' ORDER BY nome");
$usuarios = $usuarios_result ? $usuarios_result->fetch_all(MYSQLI_ASSOC) : [];

// Criar ou atualizar dashboards
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['salvar'])) {
    $usuario_id = intval($_POST['usuario_id']);
    $nome       = trim($_POST['nome']);
    $url        = trim($_POST['url']);

    if (!empty($_POST['dashboard_id'])) {
        // Atualizar dashboard existente
        $dashboard_id = intval($_POST['dashboard_id']);
        $stmt = $conn->prepare("UPDATE dashboards SET nome = ?, url = ?, usuario_id = ? WHERE id = ?");
        $stmt->bind_param("ssii", $nome, $url, $usuario_id, $dashboard_id);
        $stmt->execute();
        $stmt->close();

        // Registrar log de edição
        registrarLog($conn, $_SESSION['usuario_id'], "Editou o dashboard ID: $dashboard_id");
    } else {
        // Criar novo dashboard
        $stmt = $conn->prepare("INSERT INTO dashboards (usuario_id, nome, url) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $nome, $url);
        $stmt->execute();
        $dashboard_id = $stmt->insert_id;
        $stmt->close();

        // Registrar log de criação
        registrarLog($conn, $_SESSION['usuario_id'], "Criou um novo dashboard: $nome (ID: $dashboard_id)");
    }
}

// Excluir dashboard
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excluir'])) {
    $dashboard_id = intval($_POST['dashboard_id']);
    $stmt = $conn->prepare("DELETE FROM dashboards WHERE id = ?");
    $stmt->bind_param("i", $dashboard_id);
    $stmt->execute();
    $stmt->close();

    // Registrar log de exclusão
    registrarLog($conn, $_SESSION['usuario_id'], "Excluiu o dashboard ID: $dashboard_id");
}

// Buscar dashboards existentes
$result = $conn->query(
    "SELECT d.id, d.nome, d.url, u.id AS usuario_id, u.nome AS cliente
     FROM dashboards AS d
     JOIN usuarios AS u ON d.usuario_id = u.id
     ORDER BY u.nome, d.nome"
);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/estilos.css">
    <script src="../../public/js/script.js" defer></script>
    <title>Gerenciar Dashboards – Rechlytics</title>
</head>
<body>
    <h2 class="page-title">Gerenciar Dashboards</h2>

    <form action="<?php echo $base_url; ?>/views/admin/admin_dashboards.php" method="POST">
        <input type="hidden" name="dashboard_id" id="dashboard_id">

        <label for="usuario_id">Cliente (ID – Nome):</label>
        <select name="usuario_id" id="usuario_id" required>
            <option value="">Selecione um cliente</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?php echo htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($u['id'] . ' – ' . $u['nome'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nome">Nome do Dashboard:</label>
        <input type="text" name="nome" id="nome" required>

        <label for="url">URL do Dashboard:</label>
        <input type="text" name="url" id="url" required>

        <button type="submit" name="salvar">Salvar</button>
    </form>

    <h3>Dashboards Cadastrados</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="dashboard-item">
                <strong><?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <br>
                Cliente: <?php echo htmlspecialchars($row['usuario_id'] . ' – ' . $row['cliente'], ENT_QUOTES, 'UTF-8'); ?>
                <br>
                <a href="<?php echo htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Ver Dashboard</a>
                <div class="dashboard-actions">
                    <form action="<?php echo $base_url; ?>/views/admin/admin_dashboards.php" method="POST" style="display: inline;">
                        <input type="hidden" name="dashboard_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="excluir">Excluir</button>
                    </form>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>

    <p><a href="<?php echo $base_url; ?>/views/admin/admin_dashboard.php">Voltar ao Painel</a></p>
</body>
</html>
