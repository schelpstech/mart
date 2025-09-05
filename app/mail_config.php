<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'query.php';
function sendEmail($to, $subject, $htmlContent, $altContent = "") {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'server163.web-hosting.com'; // e.g., smtp.gmail.com
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@queenzy.assoec.org'; 
        $mail->Password   = 'UNYOpat2017@'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('noreply@queenzy.assoec.org', 'Queenzy Store');
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
