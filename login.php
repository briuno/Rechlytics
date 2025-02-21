<?php 
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: " . ($_SESSION['usuario_tipo'] == 'admin' ? "admin_dashboard.php" : "dashboard.php"));
    exit();
}

// Mensagens de erro dinâmicas
$mensagem_erro = "";
if (isset($_GET['erro'])) {
    switch ($_GET['erro']) {
        case 'login':
            $mensagem_erro = "Usuário ou senha inválidos!";
            break;
        case 'nao_ativado':
            $mensagem_erro = "Conta não ativada. Verifique seu e-mail.";
            break;
        case 'bloqueado':
            $tempo_restante = isset($_GET['tempo']) ? intval($_GET['tempo']) : 900;
            $mensagem_erro = "Muitas tentativas falhas! Aguarde " . ceil($tempo_restante / 60) . " minutos.";
            break;
    }
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
    
    <?php if (!empty($mensagem_erro)): ?>
        <p style="color: red;"><?php echo $mensagem_erro; ?></p>
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
