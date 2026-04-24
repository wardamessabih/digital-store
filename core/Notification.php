<?php

namespace Core;

use Core\Database;

class Notification {
    private static $db;

    public static function init() {
        self::$db = Database::getInstance();
    }

    public static function send($userId, $title, $message, $type = 'system', $orderId = null) {
        return self::$db->insert('notifications', [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_order_id' => $orderId
        ]);
    }

    public static function sendToAll($title, $message, $type = 'system') {
        $users = self::$db->fetchAll('SELECT id FROM users WHERE role = "user"');

        foreach ($users as $user) {
            self::send($user['id'], $title, $message, $type);
        }
    }

    public static function getUserNotifications($userId, $limit = 20) {
        return self::$db->fetchAll(
            'SELECT * FROM notifications WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC LIMIT ?',
            [$userId, $limit]
        );
    }

    public static function getUnreadCount($userId) {
        $result = self::$db->fetch(
            'SELECT COUNT(*) as count FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0',
            [$userId]
        );
        return $result['count'] ?? 0;
    }

    public static function markAsRead($notificationId, $userId) {
        self::$db->update(
            'notifications',
            ['is_read' => 1],
            'id = ? AND user_id = ?',
            ['id' => $notificationId, 'user_id' => $userId]
        );
    }

    public static function markAllAsRead($userId) {
        self::$db->query(
            'UPDATE notifications SET is_read = 1 WHERE user_id = ? OR user_id IS NULL',
            [$userId]
        );
    }

    public static function delete($notificationId, $userId) {
        self::$db->delete(
            'notifications',
            'id = ? AND user_id = ?',
            [$notificationId, $userId]
        );
    }

    public static function deleteAll($userId) {
        self::$db->query('DELETE FROM notifications WHERE user_id = ?', [$userId]);
    }

    public static function orderNotification($userId, $orderId, $status) {
        $statusTexts = [
            'processing' => 'جاري معالجة طلبك',
            'completed' => 'تم تسليم طلبك',
            'cancelled' => 'تم إلغاء طلبك'
        ];

        $title = $statusTexts[$status] ?? 'تحديث في طلبك';
        $message = "طلبك رقم #{$orderId} {$title}";

        return self::send($userId, $title, $message, 'order', $orderId);
    }
}

Notification::init();
