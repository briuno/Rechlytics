<?php
session_start();
include 'includes/session_check_admin.php';
include 'includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Criar ou atualizar dashboards
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['salvar'])) {
    $usuario_id = $_POST['usuario_id'];
    $nome = $_POST['nome'];
    $url = $_POST['url'];

    if (isset($_POST['dashboard_id']) && $_POST['dashboard_id'] != '') {
        // Atualizar dashboard existente
        $dashboard_id = $_POST['dashboard_id'];
        $stmt = $conn->prepare("UPDATE dashboards SET nome=?, url=?, usuario_id=? WHERE id=?");
        $stmt->bind_param("ssii", $nome, $url, $usuario_id, $dashboard_id);
    } else {
        // Criar novo dashboard
        $stmt = $conn->prepare("INSERT INTO dashboards (usuario_id, nome, url) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $nome, $url);
    }
    $stmt->execute();
}

// Excluir dashboard
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir'])) {
    $dashboard_id = $_POST['dashboard_id'];
    $stmt = $conn->prepare("DELETE FROM dashboards WHERE id=?");
    $stmt->bind_param("i", $dashboard_id);
    $stmt->execute();
}

// Buscar dashboards existentes
$result = $conn->query("SELECT dashboards.id, dashboards.nome, dashboards.url, usuarios.nome AS cliente FROM dashboards 
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

    <form action="admin_dashboards.php" method="POST">
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
                <button onclick="editarDashboard('<?php echo $row['id']; ?>', '<?php echo $row['cliente']; ?>', '<?php echo $row['nome']; ?>', '<?php echo $row['url']; ?>')">Editar</button>
                <form action="admin_dashboards.php" method="POST" style="display:inline;">
                    <input type="hidden" name="dashboard_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="excluir" onclick="return confirm('Tem certeza que deseja excluir este dashboard?');">Excluir</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <script>
        function editarDashboard(id, usuario, nome, url) {
            document.getElementById('dashboard_id').value = id;
            document.getElementById('usuario_id').value = usuario;
            document.getElementById('nome').value = nome;
            document.getElementById('url').value = url;
        }
    </script>

    <p><a href="admin_dashboard.php">Voltar</a></p>
</body>
</html>
