<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../controller/env.php';
require_once 'query.php';
function sendEmail($to, $subject, $htmlContent, $altContent = "") {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = env_value('SMTP_HOST', 'queenzystores.com');
        $mail->SMTPAuth   = env_bool('SMTP_AUTH', true);
        $mail->Username   = env_value('SMTP_USERNAME', 'noreply@queenzystores.com');
        $mail->Password   = env_value('SMTP_PASSWORD', '');
        $smtpSecure       = strtolower((string) env_value('SMTP_SECURE', 'ssl'));
        $mail->SMTPSecure = $smtpSecure === 'tls'
            ? PHPMailer::ENCRYPTION_STARTTLS
            : ($smtpSecure === 'none' || $smtpSecure === 'false' ? false : PHPMailer::ENCRYPTION_SMTPS);
        $mail->Port       = (int) env_value('SMTP_PORT', 465);

        // Recipients
        $mail->setFrom(
            env_value('MAIL_FROM_ADDRESS', 'noreply@queenzystores.com'),
            env_value('MAIL_FROM_NAME', 'Queenzy Stores')
        );
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;
        $mail->AltBody = $altContent ?: strip_tags($htmlContent);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
