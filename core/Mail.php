<?php

namespace Core;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail {
    private static function getConfig() {
        $configFile = __DIR__ . '/../config.json';
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            $mail = $config['mail'] ?? [];
            return [
                'host' => $mail['smtp_host'] ?? '',
                'port' => $mail['smtp_port'] ?? 587,
                'username' => $mail['smtp_user'] ?? '',
                'password' => $mail['smtp_pass'] ?? '',
                'from' => $mail['smtp_from'] ?? 'noreply@example.com',
                'from_name' => $mail['smtp_from_name'] ?? 'المتجر الرقمي',
            ];
        }
        return [
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'from' => 'noreply@example.com',
            'from_name' => 'المتجر الرقمي',
        ];
    }

    public static function send($to, $subject, $body, $isHtml = true) {
        $config = self::getConfig();

        if (empty($config['host']) || empty($config['username'])) {
            return ['success' => false, 'error' => 'Configuration SMTP manquante'];
        }

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
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

            $mail->setFrom($config['from'], $config['from_name']);
            $mail->addAddress($to);
            $mail->isHtml($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;

            if ($mail->send()) {
                return ['success' => true];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
        
        return ['success' => false, 'error' => 'Échec envoi email'];
    }

    public static function isConfigured() {
        $config = self::getConfig();
        return !empty($config['host']) && !empty($config['username']);
    }
}