<?php
include 'includes/db.php';

if (!isset($_GET['token'])) {
    die("Token inválido.");
}

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_recuperacao=? AND token_expira > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("Token inválido ou expirado.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - Rechlytics</title>
</head>
<body>
    <h2>Redefinir Senha</h2>
    <form action="includes/atualizar_senha.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label>Nova Senha:</label>
        <input type="password" name="nova_senha" required>
        <button type="submit">Redefinir</button>
    </form>
</body>
</html>
