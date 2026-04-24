<?php

namespace Core;

use Core\Database;
use Core\Helper;

class Auth {
    private static $user = null;
    private static $db = null;

    public static function init() {
        self::$db = Database::getInstance();
        self::startSession();
        self::loadUser();
    }

    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
    }

    private static function loadUser() {
        if (isset($_SESSION['user_id'])) {
            $user = self::$db->fetch(
                'SELECT * FROM users WHERE id = ? AND is_active = 1',
                [$_SESSION['user_id']]
            );

            if ($user) {
                self::$user = $user;
                return;
            }

            self::logout();
        }

        if (isset($_COOKIE['remember_token'])) {
            $user = self::$db->fetch(
                'SELECT * FROM users WHERE api_token = ? AND is_active = 1',
                [$_COOKIE['remember_token']]
            );

            if ($user) {
                self::$user = $user;
                $_SESSION['user_id'] = $user['id'];
                return;
            }
        }
    }

    public static function attempt($email, $password, $remember = false) {
        $user = self::$db->fetch(
            'SELECT * FROM users WHERE email = ? AND is_active = 1',
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        self::$user = $user;

        if ($remember) {
            $token = Helper::generateToken(64);
            self::$db->update('users', ['api_token' => $token], 'id = ?', ['id' => $user['id']]);
            setcookie('remember_token', $token, time() + 86400 * 30, '/', '', true, true);
        }

        self::logActivity('login', 'تسجيل دخول ناجح');

        return true;
    }

    public static function register($data) {
        if (self::$db->fetch('SELECT id FROM users WHERE email = ?', [$data['email']])) {
            return ['success' => false, 'message' => 'البريد الإلكتروني مستخدم بالفعل'];
        }

        $role = $data['role'] ?? 'user';
        
        if ($role === 'admin') {
            $validAdminCode = Config::get('app.admin_code', '');
            $providedCode = $data['admin_code'] ?? '';
            
            if (empty($validAdminCode) || $providedCode !== $validAdminCode) {
                return ['success' => false, 'message' => 'كود الأدمن غير صحيح'];
            }
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, [
            'cost' => 12
        ]);

        $userId = self::$db->insert('users', [
            'name' => Helper::sanitize($data['name']),
            'email' => Helper::sanitize($data['email']),
            'password' => $hashedPassword,
            'role' => $role,
            'wallet_balance' => 0.00
        ]);

        if ($userId) {
            self::logActivity('register', 'تسجيل حساب جديد - ' . ($role === 'admin' ? 'أدمن' : 'مستخدم'));
            return ['success' => true, 'user_id' => $userId];
        }

        return ['success' => false, 'message' => 'فشل في إنشاء الحساب'];
    }

    public static function user() {
        return self::$user;
    }

    public static function id() {
        return self::$user['id'] ?? null;
    }

    public static function isLoggedIn() {
        return self::$user !== null;
    }

    public static function isAdmin() {
        return self::$user && self::$user['role'] === 'admin';
    }

    public static function check() {
        if (!self::isLoggedIn()) {
            if (Helper::isAjax()) {
                Helper::jsonResponse(['error' => 'غير مصرح'], 401);
            }
            Helper::redirect('/auth/login');
        }
    }

    public static function checkAdmin() {
        if (!self::isAdmin()) {
            if (!self::isLoggedIn()) {
                Helper::redirect('/auth/login');
            }
            if (Helper::isAjax()) {
                Helper::jsonResponse(['error' => 'غير مصرح'], 403);
            }
            Helper::redirect('/');
        }
    }

    public static function logout() {
        if (self::$db && self::$user) {
            self::$db->update('users', ['api_token' => null], 'id = :id', ['id' => self::$user['id']]);
        }

        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();

        self::$user = null;
    }

    public static function updateLastActivity() {
        if (self::$user) {
            $_SESSION['last_activity'] = time();
        }
    }

    public static function hasRole($role) {
        return self::$user && self::$user['role'] === $role;
    }

    public static function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, [
            'cost' => 12
        ]);

        return self::$db->update('users', ['password' => $hashedPassword], 'id = ?', ['id' => $userId]);
    }

    private static function logActivity($action, $description) {
        if (self::$user) {
            self::$db->insert('activity_logs', [
                'user_id' => self::$user['id'],
                'action' => $action,
                'description' => $description,
                'ip_address' => Helper::getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        }
    }

    public static function createApiToken($userId) {
        $token = Helper::generateApiKey();
        self::$db->update('users', ['api_token' => $token], 'id = ?', ['id' => $userId]);
        return $token;
    }

    public static function verifyApiToken($token) {
        return self::$db->fetch('SELECT * FROM users WHERE api_token = ? AND is_active = 1', [$token]);
    }
}

Auth::init();
