<?php

namespace App\Models;

use Core\Database;
use Core\Helper;
use Core\Notification;
use Core\Wallet;
use Core\Auth;

class Order {
    private static $db;

    public static function init() {
        self::$db = Database::getInstance();
    }

    public static function create($userId, $productId, $quantity, $paymentMethod, $customFieldValue = null) {
        $product = Product::find($productId);

        if (!$product) {
            return ['success' => false, 'message' => 'المنتج غير موجود'];
        }

        if ($product['stock'] === 0) {
            return ['success' => false, 'message' => 'المنتج غير متوفر حالياً'];
        }

        $totalPrice = $product['price'] * $quantity;

        if ($paymentMethod === 'wallet') {
            $balance = Wallet::getBalance($userId);
            if ($balance < $totalPrice) {
                return ['success' => false, 'message' => 'الرصيد غير كافٍ'];
            }
        }

        $orderNumber = Helper::generateOrderNumber();

        self::$db->beginTransaction();

        try {
            $orderId = self::$db->insert('orders', [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'custom_field_value' => $customFieldValue,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($paymentMethod === 'wallet') {
                Wallet::deductFunds($userId, $totalPrice, "شراء: {$product['name']}");
            }

            if ($product['stock'] !== -1) {
                Product::decrementStock($productId, $quantity);
            }

            Notification::send(
                $userId,
                'تم إنشاء طلبك',
                "تم إنشاء طلب رقم #{$orderId} بقيمة {$totalPrice} ر.س",
                'order',
                $orderId
            );

            self::$db->commit();

            return ['success' => true, 'order_id' => $orderId, 'order_number' => $orderNumber];
        } catch (\Exception $e) {
            self::$db->rollback();
            return ['success' => false, 'message' => 'فشل في إنشاء الطلب'];
        }
    }

    public static function find($id) {
        return self::$db->fetch(
            'SELECT o.*, p.name as product_name, p.image_url as product_image, u.name as user_name
             FROM orders o
             JOIN products p ON o.product_id = p.id
             JOIN users u ON o.user_id = u.id
             WHERE o.id = ?',
            [$id]
        );
    }

    public static function getUserOrders($userId, $limit = 20) {
        return self::$db->fetchAll(
            'SELECT o.*, p.name as product_name, p.image_url as product_image
             FROM orders o
             JOIN products p ON o.product_id = p.id
             WHERE o.user_id = ?
             ORDER BY o.created_at DESC
             LIMIT ?',
            [$userId, $limit]
        );
    }

    public static function getAll($status = null, $limit = 50) {
        $sql = 'SELECT o.*, p.name as product_name, u.name as user_name, u.email as user_email
                FROM orders o
                JOIN products p ON o.product_id = p.id
                JOIN users u ON o.user_id = u.id';

        $params = [];

        if ($status) {
            $sql .= ' WHERE o.status = ?';
            $params[] = $status;
        }

        $sql .= ' ORDER BY o.created_at DESC LIMIT ?';
        $params[] = $limit;

        return self::$db->fetchAll($sql, $params);
    }

    public static function updateStatus($orderId, $status, $deliveryData = null, $notes = null) {
        $updateData = ['status' => $status];

        if ($deliveryData !== null) {
            $updateData['delivery_data'] = json_encode($deliveryData, JSON_UNESCAPED_UNICODE);
        }

        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        self::$db->update('orders', $updateData, 'id = ?', ['id' => $orderId]);

        $order = self::find($orderId);
        Notification::orderNotification($order['user_id'], $orderId, $status);

        return true;
    }

    public static function deliverOrder($orderId, $deliveryData) {
        return self::updateStatus($orderId, 'completed', $deliveryData);
    }

    public static function cancel($orderId, $userId) {
        $order = self::find($orderId);

        if (!$order) {
            return ['success' => false, 'message' => 'الطلب غير موجود'];
        }

        if ($order['user_id'] !== $userId && !Auth::isAdmin()) {
            return ['success' => false, 'message' => 'غير مصرح'];
        }

        if (!in_array($order['status'], ['pending', 'processing'])) {
            return ['success' => false, 'message' => 'لا يمكن إلغاء هذا الطلب'];
        }

        self::$db->beginTransaction();

        try {
            self::updateStatus($orderId, 'cancelled');

            if ($order['payment_method'] === 'wallet') {
                Wallet::addFunds($userId, $order['total_price'], "استرداد طلب #{$orderId}");
            }

            $product = Product::find($order['product_id']);
            if ($product && $product['stock'] !== -1) {
                self::$db->query(
                    'UPDATE products SET stock = stock + ? WHERE id = ?',
                    [$order['quantity'], $order['product_id']]
                );
            }

            self::$db->commit();

            return ['success' => true];
        } catch (\Exception $e) {
            self::$db->rollback();
            return ['success' => false, 'message' => 'فشل في إلغاء الطلب'];
        }
    }

    public static function getStats() {
        $stats = [];

        $stats['total_orders'] = self::$db->fetch('SELECT COUNT(*) as count FROM orders')['count'];
        $stats['pending_orders'] = self::$db->fetch('SELECT COUNT(*) as count FROM orders WHERE status = "pending"')['count'];
        $stats['processing_orders'] = self::$db->fetch('SELECT COUNT(*) as count FROM orders WHERE status = "processing"')['count'];
        $stats['completed_orders'] = self::$db->fetch('SELECT COUNT(*) as count FROM orders WHERE status = "completed"')['count'];
        $stats['total_revenue'] = self::$db->fetch('SELECT SUM(total_price) as total FROM orders WHERE status IN ("completed", "processing")')['total'] ?? 0;

        return $stats;
    }

    public static function countByStatus($status) {
        $result = self::$db->fetch('SELECT COUNT(*) as count FROM orders WHERE status = ?', [$status]);
        return $result['count'] ?? 0;
    }

    public static function getRecent($limit = 10) {
        return self::$db->fetchAll(
            'SELECT o.*, p.name as product_name, u.name as user_name
             FROM orders o
             JOIN products p ON o.product_id = p.id
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }
}

Order::init();
