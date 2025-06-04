<?php
// Rechlytics/controllers/email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// NÃO MODIFICAR A ESTRUTURA DE PASTAS: este require deve apontar para
//  C:\wamp64\www\Rechlytics\vendor\autoload.php
require __DIR__ . '/../vendor/autoload.php';

function enviarEmail(string $para, string $assunto, string $mensagem): bool {
    $mail = new PHPMailer(true);

    try {
        // =====================
        // DEBUG: MOSTRAR TROCA SMTP NO BROWSER
        // =====================
        $mail->SMTPDebug   = 2;           // 0 = off; 1 = client; 2 = client & server
        $mail->Debugoutput = 'html';

        // =====================
        // CONFIGURAÇÕES SMTP (HOSTINGER)
        // =====================
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'suporte@rechlytics.com'; // E-mail criado na Hostinger
        // Atenção: se você cadastrou “senha de App” no painel da Hostinger, use-a aqui.
        // Se a senha tiver caracteres especiais (ex.: #, @), passe a versão sem caracteres
        // especiais temporariamente para testar (ex.: Teste1234)
        $mail->Password   = 'Bruno007#@'; // Senha do e-mail criado na Hostinger
        // Experimente porta 465 com SMTPS (SSL implícito)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // =====================
        // REMETENTE E DESTINATÁRIO
        // =====================
        // O setFrom DEVE ser idêntico ao Username
        $mail->setFrom('suporte@rechlytics.com', 'Rechlytics Suporte');
        $mail->addAddress($para);

        // =====================
        // CONTEÚDO DO E-MAIL
        // =====================
        $mail->Subject = $assunto;
        $mail->isHTML(false);
        $mail->Body    = $mensagem;
        $mail->AltBody = strip_tags($mensagem);

        // =====================
        // DISPARO DO E-MAIL
        // =====================
        $mail->send();
        echo '<p style="color: green;">Email enviado com sucesso para ' 
             . htmlspecialchars($para) . '.</p>';
        return true;

    } catch (Exception $e) {
        // GRAVA NO LOG DO SERVIDOR
        error_log('PHPMailer Error: ' . $mail->ErrorInfo);
        // EXIBE NO BROWSER PARA DIAGNÓSTICO (REMOVER EM PRODUÇÃO)
        echo '<pre style="color: red;">Falha ao enviar e-mail: ' 
             . htmlspecialchars($mail->ErrorInfo) . '</pre>';
        return false;
    }
}
?>
