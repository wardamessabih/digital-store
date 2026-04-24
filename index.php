<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\' => __DIR__ . '/core/',
        'App\\' => __DIR__ . '/app/',
        'Database\\' => __DIR__ . '/database/'
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }

    $vendorPath = __DIR__ . '/vendor/autoload.php';
    if (file_exists($vendorPath)) {
        require_once $vendorPath;
    }
});

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri = rtrim($uri, '/') ?: '/';

if (preg_match('/\.(?:css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $uri)) {
    $filePath = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . $uri);
    if (file_exists($filePath)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}

use Core\Config;
use Core\Router;
use Core\Helper;

Config::load(__DIR__ . '/config.json');

date_default_timezone_set(Config::get('app.timezone', 'Asia/Riyadh'));

if (strpos($uri, '/uploads/') === 0) {
    $filePath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $uri);
    if (file_exists($filePath)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Cache-Control: max-age=86400');
        readfile($filePath);
        exit;
    }
}

$router = new Router();

require_once __DIR__ . '/routes/web.php';
require_once __DIR__ . '/routes/api.php';
require_once __DIR__ . '/routes/admin.php';

$_SERVER['REQUEST_URI'] = $uri;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
