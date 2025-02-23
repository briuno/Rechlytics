<?php
session_start();
include __DIR__ . '/config/session_check.php';
include __DIR__ . '/config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Atualizar perfil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (!empty($_POST['nova_senha'])) {
        $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nome=?, email=?, senha=? WHERE id=?");
        $stmt->bind_param("sssi", $nome, $email, $nova_senha, $usuario_id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $nome, $email, $usuario_id);
    }

    if ($stmt->execute()) {
        $_SESSION['usuario_nome'] = $nome;
        echo "<p>Perfil atualizado com sucesso!</p>";
        registrarLog($conn, $_SESSION['usuario_id'], "Atualizou perfil");
    } else {
        echo "<p>Erro ao atualizar perfil.</p>";
    }
}

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT nome, email FROM usuarios WHERE id=?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nome, $email);
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
    <form action="/client/perfil.php" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <label>Nova Senha (opcional):</label>
        <input type="password" name="nova_senha">
        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="/client/dashboard.php">Voltar</a></p>
</body>
</html>
