<?php
session_start();
include __DIR__ . '/../controllers/session_check.php';
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php'; // Para registrar ações no sistema

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /rechlytics/views/login.php");
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
        echo "<p>Perfil atualizado com sucesso!</p>";
        registrarLog($conn, $_SESSION['usuario_id'], "Atualizou perfil");
    } else {
        echo "<p>Erro ao atualizar perfil.</p>";
    }
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
    
    <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>CPF:</strong> <?php echo htmlspecialchars($cpf); ?></p>

    <form action="views/perfil.php" method="POST">
        <label>Empresa:</label>
        <input type="text" name="empresa" value="<?php echo htmlspecialchars($empresa); ?>">

        <label>Endereço:</label>
        <input type="text" name="endereco" value="<?php echo htmlspecialchars($endereco); ?>">

        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>">

        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="views/dashboard.php">Voltar</a></p>
</body>
</html>
