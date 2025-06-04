<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Certifique-se de que o Composer está carregando corretamente

function enviarEmail($para, $assunto, $mensagem) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com'; // Se usar Gmail, troque por smtp.gmail.com
        $mail->SMTPAuth = true;
        $mail->Username = 'suporte@rechlytics.com'; // Seu e-mail SMTP
        $mail->Password = 'Bruno007#@'; // Senha do e-mail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SMTPS
        $mail->Port = 465; // Porta padrão do SMTP

        // Configuração do remetente e destinatário
        $mail->setFrom('suporte@rechlytics.com', 'Rechlytics Suporte');
        $mail->addAddress($para);
        $mail->Subject = $assunto;

        // Configuração do corpo do e-mail
        $mail->isHTML(false);
        $mail->Body = $mensagem;
        $mail->AltBody = strip_tags($mensagem); // Fallback para clientes que não suportam HTML

        // Enviar e-mail
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail para $para: " . $mail->ErrorInfo);
        return false;
    }
}
?>
