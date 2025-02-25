<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php';
include __DIR__ . '/../controllers/email.php';

$limite_tentativas = 5;
$tempo_bloqueio = 15 * 60; // 15 minutos
$tempo_validade_2fa = 24 * 60 * 60; // 24 horas

// Caminho base dinâmico
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Verifica tentativas de login por e-mail
    if (isset($_SESSION['tentativas_login'][$email]) && $_SESSION['tentativas_login'][$email]['tentativas'] >= $limite_tentativas) {
        $tempo_restante = $_SESSION['tentativas_login'][$email]['tempo'] + $tempo_bloqueio - time();
        if ($tempo_restante > 0) {
            $_SESSION['erro_login'] = "Muitas tentativas falhas! Aguarde " . ceil($tempo_restante / 60) . " minutos.";
            header("Location: $base_url/views/login.php");
            exit();
        } else {
            unset($_SESSION['tentativas_login'][$email]); // Resetar tentativas após o tempo de bloqueio
        }
    }

    // Buscar usuário no banco
    $stmt = $conn->prepare("SELECT id, nome, senha, tipo, email_verificado, two_factor_code, two_factor_expira, two_factor_valid_until FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $hash_senha, $tipo, $email_verificado, $codigo_armazenado, $expira_2fa, $validade_2fa);
        $stmt->fetch();

        // Verificar se a conta foi ativada
        if ($email_verificado == 0) {
            $_SESSION['erro_login'] = "Conta não ativada. Verifique seu e-mail.";
            header("Location: $base_url/views/login.php");
            exit();
        }

        // Verificar senha
        if (password_verify($senha, $hash_senha)) {
            // Se o 2FA ainda for válido (24 horas), pular verificação
            if (!empty($validade_2fa) && strtotime($validade_2fa) > time()) {
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_tipo'] = $tipo;

                // Redirecionar para dashboard correto
                header("Location: " . ($tipo === 'admin' ? "$base_url/views/admin/admin_dashboard.php" : "$base_url/views/dashboard.php"));
                exit();
            }

            // Gerar novo código 2FA
            $codigo_2fa = rand(100000, 999999);
            $expira_2fa = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // Atualizar código 2FA e validade no banco
            $stmt = $conn->prepare("UPDATE usuarios SET two_factor_code = ?, two_factor_expira = ?, two_factor_valid_until = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ?");
            $stmt->bind_param("ssi", $codigo_2fa, $expira_2fa, $id);
            $stmt->execute();

            // Enviar e-mail com o código 2FA
            $assunto = "Seu Código 2FA - Rechlytics";
            $mensagem = "Olá $nome,\n\n"
                . "Seu código de autenticação é: **$codigo_2fa**\n\n"
                . "Este código expira em 10 minutos.\n\n"
                . "Se você não tentou fazer login, ignore este e-mail.\n\n"
                . "Atenciosamente,\nEquipe Rechlytics";

            enviarEmail($email, $assunto, $mensagem);

            // Armazena sessão temporária para 2FA
            $_SESSION['usuario_2fa'] = $id;
            header("Location: $base_url/views/auth/verificar_2fa.php");
            exit();
        } else {
            $_SESSION['erro_login'] = "Usuário ou senha inválidos!";
        }
    } else {
        $_SESSION['erro_login'] = "Usuário ou senha inválidos!";
    }

    // Registra tentativa falha
    if (!isset($_SESSION['tentativas_login'][$email])) {
        $_SESSION['tentativas_login'][$email] = ["tentativas" => 0, "tempo" => time()];
    }

    $_SESSION['tentativas_login'][$email]['tentativas']++;

    if ($_SESSION['tentativas_login'][$email]['tentativas'] >= $limite_tentativas) {
        $_SESSION['tentativas_login'][$email]['tempo'] = time();
        $_SESSION['erro_login'] = "Muitas tentativas falhas! Tente novamente em 15 minutos.";
    }

    header("Location: $base_url/views/login.php");
    exit();
}
?>
