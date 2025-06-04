<?php
// Rechlytics/controllers/auth.php

session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/log.php';
include __DIR__ . '/email.php';

$limite_tentativas = 5;
$tempo_bloqueio   = 15 * 60; // 15 minutos

// Monta a URL base dinamicamente
$base_url = rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
    . "://"
    . $_SERVER['HTTP_HOST']
    . dirname($_SERVER['SCRIPT_NAME'], 2),
    '/'
);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // ────────────────────────────────────────────────────
    // Verificação de bloqueio por tentativas (5 tentativas)
    // ────────────────────────────────────────────────────
    if (isset($_SESSION['tentativas_login'][$email]) 
        && $_SESSION['tentativas_login'][$email]['tentativas'] >= $limite_tentativas
    ) {
        $tempo_restante = 
            $_SESSION['tentativas_login'][$email]['tempo'] 
            + $tempo_bloqueio 
            - time();
        if ($tempo_restante > 0) {
            $_SESSION['erro_login'] = 
                "Muitas tentativas falhas! Aguarde " 
                . ceil($tempo_restante / 60) . " minutos.";
            header("Location: $base_url/views/login.php");
            exit();
        } else {
            // Resetar tentativas após bloqueio expirado
            unset($_SESSION['tentativas_login'][$email]);
        }
    }

    // ────────────────────────────────────────────────────
    // Consulta usuário no banco
    // ────────────────────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT id, nome, senha, tipo, email_verificado 
        FROM usuarios 
        WHERE email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $hash_senha, $tipo, $email_verificado);
        $stmt->fetch();

        // ────────────────────────────────────────────────────
        // Verifica se a conta está ativada (email_verificado = 1)
        // ────────────────────────────────────────────────────
        if ((int)$email_verificado === 0) {
            $_SESSION['erro_login'] = "Conta não ativada. Verifique seu e-mail de ativação.";
            header("Location: $base_url/views/login.php");
            exit();
        }

        // ────────────────────────────────────────────────────
        // Verifica senha
        // ────────────────────────────────────────────────────
        if (password_verify($senha, $hash_senha)) {
            // ────────────────────────────────────────────────────
            // Gera código 2FA e calcula expiração (10 minutos)
            // ────────────────────────────────────────────────────
            $codigo_2fa = strval(rand(100000, 999999));
            $expira_2fa = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // Atualiza no banco
            $upd = $conn->prepare("
                UPDATE usuarios 
                SET two_factor_code = ?, two_factor_expira = ? 
                WHERE id = ?
            ");
            $upd->bind_param("ssi", $codigo_2fa, $expira_2fa, $id);
            $upd->execute();

            // ────────────────────────────────────────────────────
            // Monta e envia o e-mail 2FA
            // ────────────────────────────────────────────────────
            $assunto  = "Seu Código 2FA – Rechlytics";
            $mensagem = 
                "Olá $nome,\n\n"
                . "Seu código de autenticação é: **$codigo_2fa**\n\n"
                . "Este código expira em 10 minutos.\n\n"
                . "Caso não tenha solicitado, ignore este e-mail.\n\n"
                . "Atenciosamente,\nEquipe Rechlytics";

            $sucessoEnvio = enviarEmail($email, $assunto, $mensagem);

            if ($sucessoEnvio) {
                // Se e-mail enviado com sucesso, guarda o ID para a etapa 2FA
                $_SESSION['usuario_2fa'] = $id;
                header("Location: $base_url/views/auth/verificar_2fa.php");
                exit();
            } else {
                // Se falhou o envio, volta para login com mensagem de erro
                $_SESSION['erro_login'] = "Não foi possível enviar o e-mail de verificação. Tente novamente mais tarde.";
                header("Location: $base_url/views/login.php");
                exit();
            }
        } else {
            $_SESSION['erro_login'] = "Usuário ou senha inválidos!";
        }
    } else {
        $_SESSION['erro_login'] = "Usuário ou senha inválidos!";
    }

    // ────────────────────────────────────────────────────
    // Registrar tentativa falha
    // ────────────────────────────────────────────────────
    if (!isset($_SESSION['tentativas_login'][$email])) {
        $_SESSION['tentativas_login'][$email] = [
            "tentativas" => 0,
            "tempo"      => time()
        ];
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
