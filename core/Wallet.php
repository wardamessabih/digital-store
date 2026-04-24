<?php

namespace Core;

use Core\Database;

class Wallet {
    private static $db;

    public static function init() {
        self::$db = Database::getInstance();
    }

    public static function getBalance($userId) {
        $user = self::$db->fetch('SELECT wallet_balance FROM users WHERE id = ?', [$userId]);
        return $user['wallet_balance'] ?? 0;
    }

    public static function addFunds($userId, $amount, $description = '') {
        self::$db->beginTransaction();

        try {
            self::$db->query(
                'UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?',
                [$amount, $userId]
            );

            self::logTransaction($userId, 'deposit', $amount, $description);

            self::$db->commit();
            return true;
        } catch (\Exception $e) {
            self::$db->rollback();
            return false;
        }
    }

    public static function deductFunds($userId, $amount, $description = '') {
        $currentBalance = self::getBalance($userId);

        if ($currentBalance < $amount) {
            return ['success' => false, 'message' => 'الرصيد غير كافٍ'];
        }

        self::$db->beginTransaction();

        try {
            self::$db->query(
                'UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?',
                [$amount, $userId]
            );

            self::logTransaction($userId, 'withdrawal', $amount, $description);

            self::$db->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            self::$db->rollback();
            return ['success' => false, 'message' => 'فشل في خصم الرصيد'];
        }
    }

    public static function requestDeposit($userId, $amount, $paymentMethod, $invoiceImage = null, $transactionId = null) {
        return self::$db->insert('wallet_requests', [
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'invoice_image_url' => $invoiceImage,
            'transaction_id' => $transactionId,
            'status' => 'pending'
        ]);
    }

    public static function getUserRequests($userId, $limit = 20) {
        return self::$db->fetchAll(
            'SELECT * FROM wallet_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT ?',
            [$userId, $limit]
        );
    }

    public static function getPendingRequests() {
        return self::$db->fetchAll(
            'SELECT wr.*, u.name as user_name, u.email as user_email
             FROM wallet_requests wr
             JOIN users u ON wr.user_id = u.id
             WHERE wr.status = "pending"
             ORDER BY wr.created_at ASC'
        );
    }

    public static function approveRequest($requestId, $adminId) {
        $request = self::$db->fetch('SELECT * FROM wallet_requests WHERE id = ?', [$requestId]);

        if (!$request || $request['status'] !== 'pending') {
            return ['success' => false, 'message' => 'الطلب غير موجود أو تم معالجته'];
        }

        self::$db->beginTransaction();

        try {
            self::addFunds($request['user_id'], $request['amount'], 'شحن محفظة - موافقة طلب #' . $requestId);

            self::$db->update(
                'wallet_requests',
                [
                    'status' => 'approved',
                    'processed_by' => $adminId,
                    'processed_at' => date('Y-m-d H:i:s')
                ],
                'id = ?',
                ['id' => $requestId]
            );

            Notification::send(
                $request['user_id'],
                'تم شحن محفظتك',
                "تم شحن {$request['amount']} ر.س إلى محفظتك بنجاح",
                'wallet'
            );

            self::$db->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            self::$db->rollback();
            return ['success' => false, 'message' => 'فشل في الموافقة على الطلب'];
        }
    }

    public static function rejectRequest($requestId, $adminId, $reason = '') {
        $request = self::$db->fetch('SELECT * FROM wallet_requests WHERE id = ?', [$requestId]);

        if (!$request || $request['status'] !== 'pending') {
            return ['success' => false, 'message' => 'الطلب غير موجود أو تم معالجته'];
        }

        self::$db->update(
            'wallet_requests',
            [
                'status' => 'rejected',
                'admin_notes' => $reason,
                'processed_by' => $adminId,
                'processed_at' => date('Y-m-d H:i:s')
            ],
            'id = ?',
            ['id' => $requestId]
        );

        Notification::send(
            $request['user_id'],
            'تم رفض طلب الشحن',
            "تم رفض طلب شحن المحفظة. " . ($reason ?: 'يرجى التواصل مع الدعم'),
            'wallet'
        );

        return ['success' => true];
    }

    private static function logTransaction($userId, $type, $amount, $description) {
        self::$db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'wallet_' . $type,
            'description' => $description ?: "{$type}: {$amount}",
            'ip_address' => Helper::getClientIP()
        ]);
    }

    public static function getTransactionHistory($userId, $limit = 50) {
        return self::$db->fetchAll(
            'SELECT * FROM activity_logs WHERE user_id = ? AND action LIKE "wallet_%" ORDER BY created_at DESC LIMIT ?',
            [$userId, $limit]
        );
    }
}

Wallet::init();
