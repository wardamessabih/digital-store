<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

use Core\Auth;
use Core\Notification;

if (!Auth::isLoggedIn()) {
    echo "data: " . json_encode(['error' => 'Unauthorized']) . "\n\n";
    exit;
}

$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

while (true) {
    $notifications = Notification::getUserNotifications(Auth::id(), 10);
    $unreadCount = Notification::getUnreadCount(Auth::id());

    $newNotifications = array_filter($notifications, function($n) use ($lastId) {
        return $n['id'] > $lastId;
    });

    if (!empty($newNotifications)) {
        echo "data: " . json_encode([
            'success' => true,
            'notifications' => array_values($newNotifications),
            'unread_count' => $unreadCount
        ]) . "\n\n";
        ob_flush();
        flush();
    }

    if (connection_aborted()) {
        break;
    }

    sleep(5);
}
