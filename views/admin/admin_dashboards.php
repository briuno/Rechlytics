<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: views/login.php");
    exit();
}

// Criar ou atualizar dashboards
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['salvar'])) {
    $usuario_id = $_POST['usuario_id'];
    $nome = trim($_POST['nome']);
    $url = trim($_POST['url']);

    if (isset($_POST['dashboard_id']) && $_POST['dashboard_id'] != '') {
        // Atualizar dashboard existente
        $dashboard_id = $_POST['dashboard_id'];
        $stmt = $conn->prepare("UPDATE dashboards SET nome=?, url=?, usuario_id=? WHERE id=?");
        $stmt->bind_param("ssii", $nome, $url, $usuario_id, $dashboard_id);
        $stmt->execute();

        // Registrar log de edição
        registrarLog($conn, $_SESSION['usuario_id'], "Editou o dashboard ID: $dashboard_id");
    } else {
        // Criar novo dashboard
        $stmt = $conn->prepare("INSERT INTO dashboards (usuario_id, nome, url) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $nome, $url);
        $stmt->execute();
        $dashboard_id = $stmt->insert_id; // Obtém o ID do dashboard recém-criado

        // Registrar log de criação
        registrarLog($conn, $_SESSION['usuario_id'], "Criou um novo dashboard: $nome (ID: $dashboard_id)");
    }
}

// Excluir dashboard
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir'])) {
    $dashboard_id = $_POST['dashboard_id'];
    $stmt = $conn->prepare("DELETE FROM dashboards WHERE id=?");
    $stmt->bind_param("i", $dashboard_id);
    $stmt->execute();

    // Registrar log de exclusão
    registrarLog($conn, $_SESSION['usuario_id'], "Excluiu o dashboard ID: $dashboard_id");
}

// Buscar dashboards existentes
$result = $conn->query("SELECT dashboards.id, dashboards.nome, dashboards.url, usuarios.id AS usuario_id, usuarios.nome AS cliente 
                        FROM dashboards 
                        JOIN usuarios ON dashboards.usuario_id = usuarios.id");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Dashboards - Rechlytics</title>
</head>
<body>
    <h2>Gerenciar Dashboards</h2>

    <form action="views/admin/admin_dashboards.php" method="POST">
        <input type="hidden" name="dashboard_id" id="dashboard_id">
        <label>ID do Cliente:</label>
        <input type="number" name="usuario_id" id="usuario_id" required>
        <label>Nome do Dashboard:</label>
        <input type="text" name="nome" id="nome" required>
        <label>URL do Dashboard:</label>
        <input type="text" name="url" id="url" required>
        <button type="submit" name="salvar">Salvar</button>
    </form>

    <h3>Dashboards Cadastrados</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <strong><?php echo htmlspecialchars($row['nome']); ?></strong> - Cliente: <?php echo htmlspecialchars($row['cliente']); ?>
                <br> <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank">Ver Dashboard</a>
                <br>
                <button onclick="editarDashboard('<?php echo $row['id']; ?>', '<?php echo $row['usuario_id']; ?>', '<?php echo htmlspecialchars($row['nome']); ?>', '<?php echo htmlspecialchars($row['url']); ?>')">Editar</button>
                <form action="views/admin/admin_dashboards.php" method="POST" style="display:inline;">
                    <input type="hidden" name="dashboard_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="excluir" onclick="return confirm('Tem certeza que deseja excluir este dashboard?');">Excluir</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <script>
        function editarDashboard(id, usuarioId, nome, url) {
            document.getElementById('dashboard_id').value = id;
            document.getElementById('usuario_id').value = usuarioId;
            document.getElementById('nome').value = nome;
            document.getElementById('url').value = url;
        }
    </script>

    <p><a href="views/admin/admin_dashboard.php">Voltar</a></p>
</body>
</html>
