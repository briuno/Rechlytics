<?php
session_start();
include __DIR__ . '/db.php';
include __DIR__ . '/log.php';
include __DIR__ . '/email.php';

$limite_tentativas = 5;
$tempo_bloqueio = 15 * 60; // 15 minutos

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Verificar se o usuário já excedeu tentativas de login
    if (isset($_SESSION['tentativas_login'][$email]) && $_SESSION['tentativas_login'][$email]['tentativas'] >= $limite_tentativas) {
        $tempo_restante = $_SESSION['tentativas_login'][$email]['tempo'] + $tempo_bloqueio - time();
        if ($tempo_restante > 0) {
            $_SESSION['erro_login'] = "Muitas tentativas falhas! Aguarde " . ceil($tempo_restante / 60) . " minutos.";
            header("Location: ../login.php");
            exit();
        } else {
            unset($_SESSION['tentativas_login'][$email]); // Resetar tentativas após o tempo de bloqueio
        }
    }

    // Buscar usuário no banco de dados
    $stmt = $conn->prepare("SELECT id, nome, senha, tipo, email_verificado FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $hash_senha, $tipo, $email_verificado);
        $stmt->fetch();

        // Verificar se a conta foi ativada
        if ($email_verificado == 0) {
            registrarLog($conn, $id, "Tentativa de login com conta não ativada");
            $_SESSION['erro_login'] = "Conta não ativada. Verifique seu e-mail.";
            header("Location: ../login.php");
            exit();
        }

        // Verificar senha
        if (password_verify($senha, $hash_senha)) {
            unset($_SESSION['tentativas_login'][$email]); // Resetar tentativas

            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_tipo'] = $tipo;
            $_SESSION['last_activity'] = time();
            unset($_SESSION['erro_login']); // Limpar mensagem de erro se login for bem-sucedido

            registrarLog($conn, $id, "Login realizado");

            // Redirecionar usuário
            header("Location: " . ($tipo == 'admin' ? "../admin_dashboard.php" : "../dashboard.php"));
            exit();
        } else {
            registrarLog($conn, $id, "Tentativa de login com senha incorreta");
            $_SESSION['erro_login'] = "Usuário ou senha inválidos!";
        }
    } else {
        registrarLog($conn, null, "Tentativa de login com e-mail não cadastrado: $email");
        $_SESSION['erro_login'] = "Usuário ou senha inválidos!";
    }

    // Contabilizar tentativa de login falha
    if (!isset($_SESSION['tentativas_login'][$email])) {
        $_SESSION['tentativas_login'][$email] = ['tentativas' => 1, 'tempo' => time()];
    } else {
        $_SESSION['tentativas_login'][$email]['tentativas']++;
        $_SESSION['tentativas_login'][$email]['tempo'] = time();
    }

    // Enviar e-mail se o usuário atingir o limite de tentativas
    if ($_SESSION['tentativas_login'][$email]['tentativas'] >= $limite_tentativas) {
        $stmt = $conn->prepare("SELECT email, nome FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($email_usuario, $nome_usuario);
            $stmt->fetch();

            // Enviar e-mail de alerta
            $assunto = "Alerta de Tentativas de Login - Rechlytics";
            $mensagem = "Olá $nome_usuario,\n\nDetectamos várias tentativas de login sem sucesso na sua conta.\n"
                . "Por motivos de segurança, sua conta foi temporariamente bloqueada por 15 minutos.\n"
                . "Se você esqueceu sua senha, clique no link abaixo para redefini-la:\n"
                . "https://rechlytics.com/esq_senha.php\n\n"
                . "Se não foi você quem tentou acessar sua conta, recomendamos alterar sua senha imediatamente.\n\n"
                . "Atenciosamente,\nEquipe Rechlytics";

            enviarEmail($email_usuario, $assunto, $mensagem);
        }
    }

    header("Location: ../login.php");
    exit();
}
?>
