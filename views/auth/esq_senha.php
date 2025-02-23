<?php
session_start();
include 'includes/db.php';
include 'includes/email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Verificar se o e-mail existe no banco
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($usuario_id);
        $stmt->fetch();

        // Criar um token seguro e definir validade (30 minutos)
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Armazenar o token e a expiração no banco
        $stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expira = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expira, $usuario_id);
        $stmt->execute();

        // Enviar e-mail com link de redefinição
        $reset_link = "https://rechlytics.com/redefinir_senha.php?token=$token";
        $assunto = "Redefinição de Senha - Rechlytics";
        $mensagem = "Olá,\n\nVocê solicitou a redefinição de sua senha. Clique no link abaixo para criar uma nova senha:\n\n$reset_link\n\nEste link expirará em 30 minutos.\n\nSe você não solicitou essa alteração, ignore este e-mail.\n\nAtenciosamente,\nEquipe Rechlytics";
        enviarEmail($email, $assunto, $mensagem);

        $_SESSION['msg'] = "Um e-mail foi enviado para redefinir sua senha.";
    } else {
        $_SESSION['msg'] = "Se esse e-mail estiver cadastrado, um link será enviado.";
    }

    header("Location: esq_senha.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Esqueci Minha Senha - Rechlytics</title>
</head>
<body>
    <h2>Recuperação de Senha</h2>
    <?php
    if (isset($_SESSION['msg'])) {
        echo "<p style='color: green;'>" . $_SESSION['msg'] . "</p>";
        unset($_SESSION['msg']);
    }
    ?>
    <form action="esq_senha.php" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Enviar Link</button>
    </form>
    <p><a href="login.php">Voltar ao Login</a></p>
</body>
</html>
