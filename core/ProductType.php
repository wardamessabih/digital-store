<?php

namespace Core;

use Core\Database;

class ProductType {
    private static $db;

    public static function init() {
        self::$db = Database::getInstance();
    }

    public static function all() {
        $defaultTypes = [
            ['value' => 'code', 'label' => 'كود', 'icon' => 'bi-key', 'color' => '#6366f1'],
            ['value' => 'subscription', 'label' => 'اشتراك', 'icon' => 'bi-repeat', 'color' => '#8b5cf6'],
            ['value' => 'game_id', 'label' => 'رقم لعبة', 'icon' => 'bi-controller', 'color' => '#ec4899'],
            ['value' => 'phone', 'label' => 'هاتف', 'icon' => 'bi-phone', 'color' => '#f97316'],
            ['value' => 'link', 'label' => 'رابط', 'icon' => 'bi-link-45deg', 'color' => '#06b6d4'],
            ['value' => 'credit', 'label' => 'رصيد', 'icon' => 'bi-credit-card', 'color' => '#22c55e']
        ];

        $savedTypes = self::$db->fetchAll('SELECT * FROM product_types ORDER BY id');

        $types = [];
        foreach ($defaultTypes as $dt) {
            $types[$dt['value']] = $dt;
        }

        foreach ($savedTypes as $st) {
            $types[$st['value']] = $st;
        }

        return $types;
    }

    public static function find($value) {
        return self::$db->fetch('SELECT * FROM product_types WHERE value = ?', [$value]);
    }

    public static function create($data) {
        return self::$db->insert('product_types', [
            'value' => $data['value'],
            'label' => $data['label'],
            'icon' => $data['icon'] ?? 'bi-tag',
            'color' => $data['color'] ?? '#6366f1'
        ]);
    }

    public static function delete($value) {
        return self::$db->delete('product_types', 'value = ?', [$value]);
    }
}

ProductType::init();
