<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rechlytics</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($_GET['erro'])): ?>
        <p style="color: red;">Usuário ou senha inválidos!</p>
    <?php endif; ?>
    <form action="includes/auth.php" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <button type="submit" name="login">Entrar</button>
    </form>
    <p><a href="esq_senha.php">Esqueci minha senha</a></p>
</body>
</html>
