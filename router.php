<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SERVER['REQUEST_URI'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/') ?: '/';

require dirname(__DIR__) . '/index.php';
