<?php
session_start();
$mensagem_erro = $_SESSION['erro_login'] ?? '';
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../public/css/login.css">
    <script src="../public/js/script.js" defer></script>
    <title>Login – Rechlytics</title>
</head>
<body>
    <div class="container">
        <h2 class="page-title">Login – Rechlytics</h2>

        <?php if ($mensagem_erro): ?>
            <div class="erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
        <?php endif; ?>

        <form action="../controllers/auth.php" method="POST" class="form-auth">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="exemplo@dominio.com" required autocomplete="off">

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Sua senha" required minlength="8" autocomplete="off">

            <button type="submit" name="login">Entrar</button>
        </form>

        <p>
            <a href="auth/esq_senha.php">Esqueci minha senha</a>
        </p>
    </div>
</body>
</html>
