<?php
session_start();
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if (isset($_SESSION['usuario_id'])) {
    header("Location: " . ($_SESSION['usuario_tipo'] == 'admin' ? "$base_url/views/admin/admin_dashboard.php" : "$base_url/views/dashboard.php"));
    exit();
}

$mensagem_erro = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : '';
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Rechlytics</title>
</head>
<body>
    <h2>Login</h2>

    <?php if ($mensagem_erro): ?>
        <p><?php echo htmlspecialchars($mensagem_erro); ?></p>
    <?php endif; ?>

    <form action="<?php echo $base_url; ?>/controllers/auth.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required minlength="8">

        <button type="submit" name="login">Entrar</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/auth/esq_senha.php">Esqueci minha senha</a></p>
</body>
</html>