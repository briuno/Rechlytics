<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/email.php';

// Caminho base dinâmico com domínio correto
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
    . "://" . $_SERVER['HTTP_HOST']
    . dirname(dirname($_SERVER['SCRIPT_NAME'])),
    '/'
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validação mínima de formato
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "⚠ E-mail inválido.";
        header("Location: $base_url/auth/esq_senha.php");
        exit();
    }

    // Verificar se o e-mail existe no banco
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$stmt) {
        error_log("Prepare SELECT falhou: " . $conn->error);
        $_SESSION['msg'] = "Erro ao processar a solicitação. Tente novamente.";
        header("Location: $base_url/auth/esq_senha.php");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($usuario_id);
        $stmt->fetch();
        $stmt->close();

        // Gera token seguro de 64 caracteres e define validade de 30 minutos (com PHP)
        $token  = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Atualizar token e expiração no banco
        $stmt2 = $conn->prepare("
            UPDATE usuarios 
               SET reset_token = ?, reset_token_expira = ? 
             WHERE id = ?
        ");
        if (!$stmt2) {
            error_log("Prepare UPDATE falhou: " . $conn->error);
            $_SESSION['msg'] = "Erro ao processar a solicitação. Tente novamente.";
            header("Location: $base_url/auth/esq_senha.php");
            exit();
        }
        $stmt2->bind_param("ssi", $token, $expira, $usuario_id);
        if (!$stmt2->execute()) {
            error_log("Execute UPDATE falhou: " . $stmt2->error);
            $_SESSION['msg'] = "Erro ao processar a solicitação. Tente novamente.";
            $stmt2->close();
            header("Location: $base_url/auth/esq_senha.php");
            exit();
        }
        if ($stmt2->affected_rows === 0) {
            error_log("Nenhum registro atualizado para usuário_id = $usuario_id");
            $_SESSION['msg'] = "Erro ao processar a solicitação. Contate o suporte.";
            $stmt2->close();
            header("Location: $base_url/auth/esq_senha.php");
            exit();
        }
        $stmt2->close();

        // Monta link de redefinição
        $reset_link = "$base_url/auth/redefinir_senha.php?token=" . urlencode($token);
        $assunto    = "Redefinição de Senha - Rechlytics";
        $mensagem   = "Olá,\n\nVocê solicitou a redefinição de sua senha. "
                    . "Clique no link abaixo para criar uma nova senha:\n\n"
                    . "$reset_link\n\n"
                    . "Este link expirará em 30 minutos.\n\n"
                    . "Se você não solicitou essa alteração, ignore este e-mail.\n\n"
                    . "Atenciosamente,\nEquipe Rechlytics";

        if (!enviarEmail($email, $assunto, $mensagem)) {
            error_log("Falha ao enviar e-mail para $email");
            $_SESSION['msg'] = "Erro ao enviar e-mail. Tente novamente.";
        } else {
            $_SESSION['msg'] = "Um e-mail foi enviado para redefinir sua senha.";
        }
    } else {
        // Genérico: não expor se o e-mail existe
        $_SESSION['msg'] = "Se esse e-mail estiver cadastrado, um link será enviado.";
        $stmt->close();
    }

    header("Location: $base_url/auth/esq_senha.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <link rel="stylesheet" href="../../public/css/esqueciSenha.css">
    <script src="../../public/js/script.js" defer></script>
    <title>Esqueci Minha Senha - Rechlytics</title>
</head>
<body>
    <h2 class="page-title">Recuperação de Senha</h2>

    <?php
    if (isset($_SESSION['msg'])) {
        echo "<p>" . htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') . "</p>";
        unset($_SESSION['msg']);
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="form-auth">
        <label>Email:</label>
        <input type="email" placeholder="exemplo@dominio.com" name="email" required>
        <button type="submit">Enviar Link</button>
    </form>
    <p><a href="<?php echo $base_url; ?>/login.php">Voltar ao Login</a></p>
</body>
</html>
