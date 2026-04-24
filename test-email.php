<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
$mailConfig = $config['mail'];

echo "Config: ";
print_r($mailConfig);

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 4;
    $mail->isSMTP();
    $mail->Host = $mailConfig['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $mailConfig['smtp_user'];
    $mail->Password = $mailConfig['smtp_pass'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom($mailConfig['smtp_from'], $mailConfig['smtp_from_name']);
    $mail->addAddress('messabihwarda441@gmail.com');
    $mail->isHtml(true);
    $mail->Subject = 'Test';
    $mail->Body = 'Test email';

    if ($mail->send()) {
        echo "\nSuccess!";
    } else {
        echo "\nFailed: " . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage();
}