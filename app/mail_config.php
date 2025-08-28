<?php
use PHPMailer\PHPMailer\PHPMailer;

function makeMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'server163.web-hosting.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreply@queenzy.assoec.org';
    $mail->Password   = 'UNYOpat2017@';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL
    $mail->Port       = 465;

    $mail->setFrom('noreply@queenzy.assoec.org', 'Queenzy Stores');
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    return $mail;
}
