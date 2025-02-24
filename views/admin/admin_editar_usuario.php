<?php
session_start();
include __DIR__ . '/../../controllers/session_check_admin.php';
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/log.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: /rechlytics/views/login.php");
    exit();
}

// Verifica se um ID de usuário foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuário inválido.");
}

$usuario_id = $_GET['id'];

// Buscar os dados do usuário
$stmt = $conn->prepare("SELECT nome, email, tipo, cpf, empresa, endereco, telefone FROM usuarios WHERE id = ?");
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
    $cpf = trim($_POST['cpf']);
    $empresa = trim($_POST['empresa']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);

    // Garantir que o tipo de usuário seja válido
    if (!in_array($tipo, ['cliente', 'admin'])) {
        die("Tipo de usuário inválido.");
    }

    // Atualizar os dados do usuário
    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ?, cpf = ?, empresa = ?, endereco = ?, telefone = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $nome, $email, $tipo, $cpf, $empresa, $endereco, $telefone, $usuario_id);

    if ($stmt->execute()) {
        registrarLog($conn, $_SESSION['usuario_id'], "Editou o usuário ID $usuario_id");
        $_SESSION['msg'] = "Usuário atualizado com sucesso!";
        header("Location: /rechlytics/views/admin/admin_dashboard.php");
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

    <form action="/rechlytics/views/admin/admin_editar_usuario.php?id=<?php echo $usuario_id; ?>" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

        <label>Tipo de Usuário:</label>
        <select name="tipo" required>
            <option value="cliente" <?php echo ($usuario['tipo'] === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
            <option value="admin" <?php echo ($usuario['tipo'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>

        <label>CPF:</label>
        <input type="text" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>" required>

        <label>Empresa:</label>
        <input type="text" name="empresa" value="<?php echo htmlspecialchars($usuario['empresa']); ?>">

        <label>Endereço:</label>
        <textarea name="endereco"><?php echo htmlspecialchars($usuario['endereco']); ?></textarea>

        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">

        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="/rechlytics/views/admin/admin_dashboard.php">Voltar</a></p>
</body>
</html>
