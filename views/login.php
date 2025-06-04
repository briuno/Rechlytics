<?php
session_start();
$mensagem_erro = $_SESSION['erro_login'] ?? '';
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login – Rechlytics</title>
    <style>
        /* Estilo simples para ilustrar */
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        h2 { margin-bottom: 16px; }
        .erro { color: #c00; margin-bottom: 12px; }
        input, button { width: 100%; padding: 8px; margin: 6px 0 12px; border-radius: 4px; }
        input { border: 1px solid #ccc; }
        button { background: #007BFF; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        a { color: #007BFF; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login – Rechlytics</h2>

        <?php if ($mensagem_erro): ?>
            <div class="erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
        <?php endif; ?>

        <form action="../controllers/auth.php" method="POST">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required autocomplete="off">

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required minlength="8" autocomplete="off">

            <button type="submit" name="login">Entrar</button>
        </form>

        <p style="margin-top: 12px;">
            <a href="auth/esq_senha.php">Esqueci minha senha</a>
        </p>
    </div>
</body>
</html>
