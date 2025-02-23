<?php
include __DIR__ . '/config/db.php';

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Token inválido.");
}

$token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');

// Verificar se o token existe e ainda é válido
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_recuperacao = ? AND token_expira > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("Token inválido ou expirado.");
}

$stmt->bind_result($usuario_id);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - Rechlytics</title>
</head>
<body>
    <h2>Redefinir Senha</h2>
    <form action="/auth/atualizar_senha.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label>Nova Senha:</label>
        <input type="password" name="nova_senha" required minlength="8">
        <button type="submit">Redefinir</button>
    </form>
</body>
</html>
