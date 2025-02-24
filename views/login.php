<?php 
session_start();

// Verificar se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    // Redirecionamento conforme o tipo de usuário
    header("Location: " . ($_SESSION['usuario_tipo'] == 'admin' ? "/rechlytics/views/admin/admin_dashboard.php" : "/rechlytics/views/dashboard.php"));
    exit();
}

// Capturar mensagem de erro da sessão, se existir
$mensagem_erro = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : '';
unset($_SESSION['erro_login']); // Remover erro após exibição
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rechlytics</title>
    <script>
        // Exibir pop-up de erro se houver mensagem
        window.onload = function() {
            var mensagemErro = "<?php echo isset($mensagem_erro) ? addslashes($mensagem_erro) : ''; ?>";
            if (mensagemErro) {
                alert(mensagemErro);
            }
        };
    </script>
</head>
<body>
    <h2>Login</h2>

    <form action="/rechlytics/controllers/auth.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit" name="login">Entrar</button>
    </form>

    <p><a href="/rechlytics/views/auth/esq_senha.php">Esqueci minha senha</a></p>
</body>
</html>
