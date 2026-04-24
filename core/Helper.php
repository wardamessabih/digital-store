<?php

namespace Core;

class Helper {
    public static function sanitize($string) {
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }

    public static function generateToken($length = 64) {
        return bin2hex(random_bytes($length / 2));
    }

    public static function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }

    public static function formatPrice($price, $currency = 'ر.س') {
        return number_format((float)$price, 2) . ' ' . $currency;
    }

    public static function timeAgo($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) return 'الآن';
        if ($diff < 3600) return floor($diff / 60) . ' دقيقة';
        if ($diff < 86400) return floor($diff / 3600) . ' ساعة';
        if ($diff < 604800) return floor($diff / 86400) . ' يوم';
        if ($diff < 2592000) return floor($diff / 604800) . ' أسبوع';
        if ($diff < 31536000) return floor($diff / 2592000) . ' شهر';

        return date('Y-m-d', $time);
    }

    public static function redirect($url) {
        header("Location: $url");
        exit;
    }

    public static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function uploadFile($file, $allowedTypes = null, $maxSize = null) {
        Config::load(dirname(__DIR__) . '/config.json');

        $allowedTypes = $allowedTypes ?? ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $maxSize = $maxSize ?? 10485760;

        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'خطأ في رفع الملف'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'خطأ في رفع الملف'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'حجم الملف كبير جداً'];
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedTypes)) {
            return ['success' => false, 'message' => 'نوع الملف غير مسموح'];
        }

        $uploadDir = dirname(__DIR__) . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newFileName = self::generateToken(32) . '.' . $fileExtension;
        $destination = $uploadDir . $newFileName;

        if (copy($file['tmp_name'], $destination)) {
            return [
                'success' => true,
                'filename' => $newFileName,
                'path' => '/uploads/' . $newFileName
            ];
        }

        return ['success' => false, 'message' => 'فشل في حفظ الملف'];
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public static function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    public static function arrayOnly($array, $keys) {
        return array_intersect_key($array, array_flip($keys));
    }

    public static function slugify($text) {
        $text = preg_replace('/\s+/', '-', trim($text));
        $text = preg_replace('/[^\p{L}\p{N}\-]/u', '', $text);
        return strtolower($text);
    }

    public static function truncate($text, $length = 100, $suffix = '...') {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }

    public static function generateApiKey() {
        return bin2hex(random_bytes(32));
    }

    public static function getStatusColor($status) {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            'approved' => 'success',
            'rejected' => 'danger'
        ];

        return $colors[$status] ?? 'secondary';
    }

    public static function getStatusText($status) {
        $texts = [
            'pending' => 'قيد الانتظار',
            'processing' => 'جاري المعالجة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'refunded' => 'مرتجع',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض'
        ];

        return $texts[$status] ?? $status;
    }
}
