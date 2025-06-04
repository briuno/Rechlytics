<?php
// Rechlytics/controllers/email.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Este require não muda: ele aponta para C:\wamp64\www\Rechlytics\vendor\autoload.php
require __DIR__ . '/../vendor/autoload.php';

function enviarEmail(string $para, string $assunto, string $mensagem): bool {
    $mail = new PHPMailer(true);

    try {
        // ────────────────────────────────────────────────
        // REMOVA QUALQUER DEBUG/echo PARA NÃO ENVIAR SAÍDA
        // ────────────────────────────────────────────────
        // $mail->SMTPDebug   = 2;
        // $mail->Debugoutput = 'html';

        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'suporte@rechlytics.com';
        $mail->Password   = 'Bruno007#@'; // Use senha exata ou senha de App
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('suporte@rechlytics.com', 'Rechlytics Suporte');
        $mail->addAddress($para);

        $mail->Subject = $assunto;
        $mail->isHTML(false);
        $mail->Body    = $mensagem;
        $mail->AltBody = strip_tags($mensagem);

        $mail->send();
        // Retorna true em caso de sucesso, sem imprimir nada
        return true;

    } catch (Exception $e) {
        // Apenas grava no log do servidor; não imprime nada na tela
        error_log('PHPMailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
