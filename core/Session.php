<?php

namespace Core;

class Session {
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    public static function flash($key, $value = null) {
        if ($value === null) {
            $flash = self::get('flash_' . $key);
            self::remove('flash_' . $key);
            return $flash;
        }

        self::set('flash_' . $key, $value);
    }

    public static function setFlash($key, $value) {
        self::set('flash_' . $key, $value);
    }

    public static function getFlash($key) {
        return self::flash($key);
    }

    public static function hasFlash($key) {
        return self::has('flash_' . $key);
    }

    public static function all() {
        return $_SESSION;
    }

    public static function clear() {
        $_SESSION = [];
    }

    public static function error($message) {
        self::setFlash('error', $message);
    }

    public static function success($message) {
        self::setFlash('success', $message);
    }

    public static function warning($message) {
        self::setFlash('warning', $message);
    }

    public static function info($message) {
        self::setFlash('info', $message);
    }
}
