<?php
session_start();
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/email.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); // Prevenção contra XSS

    // Verificar se o e-mail existe no banco
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($usuario_id);
        $stmt->fetch();
        $stmt->close();

        // Criar um token seguro e definir validade (30 minutos)
        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Armazenar o token e a expiração no banco
        $stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expira = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expira, $usuario_id);

        if ($stmt->execute()) {
            // Enviar e-mail com link de redefinição
            $reset_link = "$base_url/views/auth/redefinir_senha.php?token=$token";
            $assunto = "Redefinição de Senha - Rechlytics";
            $mensagem = "Olá,\n\nVocê solicitou a redefinição de sua senha. Clique no link abaixo para criar uma nova senha:\n\n$reset_link\n\nEste link expirará em 30 minutos.\n\nSe você não solicitou essa alteração, ignore este e-mail.\n\nAtenciosamente,\nEquipe Rechlytics";
            enviarEmail($email, $assunto, $mensagem);

            $_SESSION['msg'] = "Um e-mail foi enviado para redefinir sua senha.";
        } else {
            $_SESSION['msg'] = "Erro ao processar a solicitação. Tente novamente.";
        }
    } else {
        $_SESSION['msg'] = "Se esse e-mail estiver cadastrado, um link será enviado.";
    }

    header("Location: $base_url/auth/esq_senha.php");
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
        echo "<p>" . htmlspecialchars($_SESSION['msg']) . "</p>";
        unset($_SESSION['msg']);
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Enviar Link</button>
    </form>
    <p><a href="<?php echo $base_url; ?>/login.php">Voltar ao Login</a></p>
</body>
</html>