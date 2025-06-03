<?php
session_start();
include __DIR__ . '/../controllers/session_check.php';
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php';

// Caminho base dinâmico com domínio corret
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: $base_url/views/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Atualizar perfil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empresa = trim($_POST['empresa']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);

    $stmt = $conn->prepare("UPDATE usuarios SET empresa=?, endereco=?, telefone=? WHERE id=?");
    $stmt->bind_param("sssi", $empresa, $endereco, $telefone, $usuario_id);

    if ($stmt->execute()) {
        registrarLog($conn, $_SESSION['usuario_id'], "Atualizou perfil");
        $_SESSION['msg'] = "Perfil atualizado com sucesso!";
    } else {
        $_SESSION['msg'] = "Erro ao atualizar perfil.";
    }

    header("Location: $base_url/views/perfil.php");
    exit();
}

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT nome, email, cpf, empresa, endereco, telefone FROM usuarios WHERE id=?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nome, $email, $cpf, $empresa, $endereco, $telefone);
$stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Rechlytics</title>
</head>
<body>
    <h2>Meu Perfil</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <p style="color: green;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
    <?php endif; ?>

    <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>CPF:</strong> <?php echo htmlspecialchars($cpf); ?></p>

    <form action="<?php echo $base_url; ?>/views/perfil.php" method="POST">
        <label>Empresa:</label>
        <input type="text" name="empresa" value="<?php echo htmlspecialchars($empresa); ?>">

        <label>Endereço:</label>
        <input type="text" name="endereco" value="<?php echo htmlspecialchars($endereco); ?>">

        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>">

        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/dashboard.php">Voltar</a></p>
</body>
</html>
