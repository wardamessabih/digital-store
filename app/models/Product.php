<?php

namespace App\Models;

use Core\Database;

class Product {
    private static $db;

    public static function init() {
        self::$db = Database::getInstance();
    }

    public static function all($activeOnly = true) {
        $sql = 'SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id';

        if ($activeOnly) {
            $sql .= ' WHERE p.is_active = 1';
        }

        $sql .= ' ORDER BY p.created_at DESC';

        return self::$db->fetchAll($sql);
    }

    public static function find($id) {
        return self::$db->fetch(
            'SELECT p.*, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ?',
            [$id]
        );
    }

    public static function findBySlug($slug) {
        return self::$db->fetch(
            'SELECT p.*, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.slug = ? AND p.is_active = 1',
            [$slug]
        );
    }

    public static function getByCategory($categoryId, $activeOnly = true) {
        $sql = 'SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ?';

        if ($activeOnly) {
            $sql .= ' AND p.is_active = 1';
        }

        $sql .= ' ORDER BY p.created_at DESC';

        return self::$db->fetchAll($sql, [$categoryId]);
    }

    public static function create($data) {
        $productData = [
            'category_id' => $data['category_id'] ?? null,
            'name' => htmlspecialchars($data['name']),
            'description' => $data['description'] ?? '',
            'price' => (float)$data['price'],
            'image_url' => $data['image_url'] ?? '',
            'type' => $data['type'] ?? 'code',
            'requires_custom_field' => isset($data['requires_custom_field']) ? 1 : 0,
            'custom_field_label' => $data['custom_field_label'] ?? null,
            'delivery_data' => $data['delivery_data'] ?? null,
            'stock' => isset($data['stock']) ? (int)$data['stock'] : -1,
            'is_active' => isset($data['is_active']) ? 1 : 1
        ];

        return self::$db->insert('products', $productData);
    }

    public static function update($id, $data) {
        $productData = [];

        $allowedFields = ['category_id', 'name', 'description', 'price', 'image_url',
                         'type', 'requires_custom_field', 'custom_field_label',
                         'delivery_data', 'stock', 'is_active'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'requires_custom_field' || $field === 'is_active') {
                    $productData[$field] = isset($data[$field]) ? 1 : 0;
                } elseif ($field === 'price') {
                    $productData[$field] = (float)$data[$field];
                } elseif ($field === 'stock') {
                    $productData[$field] = (int)$data[$field];
                } else {
                    $productData[$field] = $data[$field];
                }
            }
        }

        if (empty($productData)) {
            return false;
        }

        return self::$db->update('products', $productData, 'id = ?', ['id' => $id]);
    }

    public static function delete($id) {
        return self::$db->delete('products', 'id = ?', [$id]);
    }

    public static function search($query, $activeOnly = true) {
        $sql = 'SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE (p.name LIKE ? OR p.description LIKE ?)';

        $params = ["%{$query}%", "%{$query}%"];

        if ($activeOnly) {
            $sql .= ' AND p.is_active = 1';
        }

        $sql .= ' ORDER BY p.created_at DESC';

        return self::$db->fetchAll($sql, $params);
    }

    public static function getFeatured($limit = 8) {
        return self::$db->fetchAll(
            'SELECT p.*, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public static function getByType($type, $activeOnly = true) {
        $sql = 'SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.type = ?';

        if ($activeOnly) {
            $sql .= ' AND p.is_active = 1';
        }

        $sql .= ' ORDER BY p.created_at DESC';

        return self::$db->fetchAll($sql, [$type]);
    }

    public static function count($activeOnly = true) {
        $sql = 'SELECT COUNT(*) as count FROM products';
        if ($activeOnly) {
            $sql .= ' WHERE is_active = 1';
        }
        $result = self::$db->fetch($sql);
        return $result['count'] ?? 0;
    }

    public static function decrementStock($id, $quantity = 1) {
        self::$db->query(
            'UPDATE products SET stock = stock - ? WHERE id = ? AND stock > 0',
            [$quantity, $id]
        );
    }

    public static function isInStock($id) {
        $product = self::$db->fetch('SELECT stock FROM products WHERE id = ?', [$id]);
        return $product && ($product['stock'] === -1 || $product['stock'] > 0);
    }
}

Product::init();
