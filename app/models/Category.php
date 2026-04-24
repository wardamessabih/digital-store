<?php

namespace App\Models;

use Core\Database;
use Core\Helper;

class Category {
    private static $db;

    public static function init() {
        self::$db = Database::getInstance();
    }

    public static function all($activeOnly = true) {
        $sql = 'SELECT c.*, 
                (SELECT COUNT(*) FROM products WHERE category_id = c.id AND is_active = 1) as product_count
                FROM categories c';
        if ($activeOnly) {
            $sql .= ' WHERE c.is_active = 1';
        }
        $sql .= ' ORDER BY c.name ASC';
        return self::$db->fetchAll($sql);
    }

    public static function find($id) {
        return self::$db->fetch('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public static function findBySlug($slug) {
        return self::$db->fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }

    public static function create($data) {
        $slug = Helper::slugify($data['name']);

        $existing = self::$db->fetch('SELECT id FROM categories WHERE slug = ?', [$slug]);
        if ($existing) {
            $slug .= '-' . time();
        }

        return self::$db->insert('categories', [
            'name' => htmlspecialchars($data['name']),
            'slug' => $slug,
            'description' => $data['description'] ?? '',
            'image_url' => $data['image_url'] ?? '',
            'is_active' => isset($data['is_active']) ? 1 : 1
        ]);
    }

    public static function update($id, $data) {
        $categoryData = [];

        if (isset($data['name'])) {
            $categoryData['name'] = htmlspecialchars($data['name']);
            $categoryData['slug'] = Helper::slugify($data['name']);
        }

        $allowedFields = ['description', 'image_url', 'is_active'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $categoryData[$field] = $field === 'is_active' ? (isset($data[$field]) ? 1 : 0) : $data[$field];
            }
        }

        if (empty($categoryData)) {
            return false;
        }

        return self::$db->update('categories', $categoryData, 'id = ?', ['id' => $id]);
    }

    public static function delete($id) {
        $products = self::$db->fetchAll('SELECT id FROM products WHERE category_id = ?', [$id]);
        if (!empty($products)) {
            self::$db->update('products', ['category_id' => null], 'category_id = ?', [$id]);
        }

        return self::$db->delete('categories', 'id = ?', [$id]);
    }

    public static function getProductCount($categoryId) {
        $result = self::$db->fetch(
            'SELECT COUNT(*) as count FROM products WHERE category_id = ? AND is_active = 1',
            [$categoryId]
        );
        return $result['count'] ?? 0;
    }
}

Category::init();
