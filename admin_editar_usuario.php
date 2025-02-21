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

// Verifica se um ID de usuário foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuário inválido.");
}

$usuario_id = $_GET['id'];

// Buscar os dados do usuário
$stmt = $conn->prepare("SELECT nome, email, tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuário não encontrado.");
}

$usuario = $result->fetch_assoc();

// Atualizar usuário no banco
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $tipo = trim($_POST['tipo']);

    // Atualizar os dados do usuário
    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nome, $email, $tipo, $usuario_id);

    if ($stmt->execute()) {
        registrarLog($conn, $_SESSION['usuario_id'], "Editou o usuário ID $usuario_id");
        $_SESSION['msg'] = "Usuário atualizado com sucesso!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['msg'] = "Erro ao atualizar usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário - Rechlytics</title>
</head>
<body>
    <h2>Editar Usuário</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <p style="color: red;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
    <?php endif; ?>

    <form action="admin_editar_usuario.php?id=<?php echo $usuario_id; ?>" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

        <label>Tipo de Usuário:</label>
        <select name="tipo" required>
            <option value="cliente" <?php echo ($usuario['tipo'] === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
            <option value="admin" <?php echo ($usuario['tipo'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="admin_dashboard.php">Voltar</a></p>
</body>
</html>
