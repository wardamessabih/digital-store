<?php

namespace Core;

class Config {
    private static $config = [];
    private static $loaded = false;

    public static function load($configFile = null) {
        if (self::$loaded) {
            return;
        }

        $configFile = $configFile ?? dirname(__DIR__) . '/config.json';

        if (file_exists($configFile)) {
            self::$config = json_decode(file_get_contents($configFile), true) ?? [];
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function set($key, $value) {
        if (!self::$loaded) {
            self::load();
        }

        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public static function save($configFile = null) {
        $configFile = $configFile ?? dirname(__DIR__) . '/config.json';
        file_put_contents($configFile, json_encode(self::$config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function all() {
        if (!self::$loaded) {
            self::load();
        }
        return self::$config;
    }
}
