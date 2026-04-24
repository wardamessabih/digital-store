<?php

use Core\Helper;
use Core\Auth;

$router->get('/api/test', function() {
    Helper::jsonResponse(['success' => true, 'message' => 'API works!']);
});

$router->post('/api/cart/add', function() {
    try {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $customField = $_POST['custom_field'] ?? null;

        if ($productId <= 0) {
            Helper::jsonResponse(['success' => false, 'message' => 'بيانات غير صحيحة'], 400);
        }

        $product = \App\Models\Product::find($productId);

        if (!$product) {
            Helper::jsonResponse(['success' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        if (!\App\Models\Product::isInStock($productId) && $product['stock'] !== -1) {
            Helper::jsonResponse(['success' => false, 'message' => 'المنتج غير متوفر'], 400);
        }

        $cart = $_SESSION['cart'] ?? [];

        $cartItem = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $product['price'],
            'name' => $product['name'],
            'image' => $product['image_url'],
            'custom_field_required' => $product['requires_custom_field'],
            'custom_field_label' => $product['custom_field_label'],
            'custom_field' => $customField
        ];

        $cart[$productId] = $cartItem;
        $_SESSION['cart'] = $cart;

        Helper::jsonResponse([
            'success' => true,
            'message' => 'تمت الإضافة للسلة',
            'cart_count' => count($cart)
        ]);
    } catch (Exception $e) {
        error_log('Cart Error: ' . $e->getMessage());
        Helper::jsonResponse(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
});

$router->post('/api/cart/remove', function() {
    $productId = (int)($_POST['product_id'] ?? 0);

    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    Helper::jsonResponse([
        'success' => true,
        'message' => 'تم الحذف من السلة',
        'cart_count' => count($_SESSION['cart'] ?? [])
    ]);
});

$router->get('/api/cart', function() {
    $cart = $_SESSION['cart'] ?? [];
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    Helper::jsonResponse([
        'success' => true,
        'cart' => array_values($cart),
        'total' => $total,
        'count' => count($cart)
    ]);
});

$router->post('/api/cart/clear', function() {
    $_SESSION['cart'] = [];

    Helper::jsonResponse([
        'success' => true,
        'message' => 'تم تفريغ السلة'
    ]);
});

$router->post('/api/order/create', function() {
    if (!Auth::isLoggedIn()) {
        Helper::jsonResponse(['success' => false, 'message' => 'يرجى تسجيل الدخول أولاً'], 401);
    }

    $paymentMethod = $_POST['payment_method'] ?? 'wallet';
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        Helper::jsonResponse(['success' => false, 'message' => 'السلة فارغة'], 400);
    }

    $results = [];
    $hasError = false;

    foreach ($cart as $productId => $item) {
        $result = \App\Models\Order::create(
            Auth::id(),
            $productId,
            $item['quantity'],
            $paymentMethod,
            $item['custom_field'] ?? null
        );

        if (!$result['success']) {
            $hasError = true;
            $results[] = [
                'product_id' => $productId,
                'product_name' => $item['name'],
                'success' => false,
                'message' => $result['message']
            ];
        } else {
            $results[] = [
                'product_id' => $productId,
                'product_name' => $item['name'],
                'success' => true,
                'order_id' => $result['order_id']
            ];
        }
    }

    if (!$hasError) {
        $_SESSION['cart'] = [];
    }

    Helper::jsonResponse([
        'success' => !$hasError,
        'results' => $results
    ]);
});

$router->get('/api/user/notifications', function() {
    if (!Auth::isLoggedIn()) {
        Helper::jsonResponse(['success' => false, 'message' => 'غير مصرح'], 401);
    }

    $notifications = \App\Models\Notification::getUserNotifications(Auth::id(), 10);
    $unreadCount = \App\Models\Notification::getUnreadCount(Auth::id());

    Helper::jsonResponse([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
});

$router->post('/api/user/notifications/read', function() {
    if (!Auth::isLoggedIn()) {
        Helper::jsonResponse(['success' => false, 'message' => 'غير مصرح'], 401);
    }

    $notificationId = (int)($_POST['notification_id'] ?? 0);

    if ($notificationId > 0) {
        \App\Models\Notification::markAsRead($notificationId, Auth::id());
    } else {
        \App\Models\Notification::markAllAsRead(Auth::id());
    }

    Helper::jsonResponse(['success' => true]);
});

$router->get('/api/user/wallet', function() {
    if (!Auth::isLoggedIn()) {
        Helper::jsonResponse(['success' => false, 'message' => 'غير مصرح'], 401);
    }

    $balance = \App\Models\Wallet::getBalance(Auth::id());

    Helper::jsonResponse([
        'success' => true,
        'balance' => $balance
    ]);
});

$router->post('/api/order/cancel/{id}', function($id) {
    if (!Auth::isLoggedIn()) {
        Helper::jsonResponse(['success' => false, 'message' => 'غير مصرح'], 401);
    }

    $result = \App\Models\Order::cancel($id, Auth::id());
    Helper::jsonResponse($result);
});

$router->get('/api/products', function() {
    $type = $_GET['type'] ?? null;
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;

    if ($search) {
        $products = \App\Models\Product::search($search);
    } elseif ($type) {
        $products = \App\Models\Product::getByType($type);
    } elseif ($category) {
        $cat = \App\Models\Category::findBySlug($category);
        $products = $cat ? \App\Models\Product::getByCategory($cat['id']) : [];
    } else {
        $products = \App\Models\Product::all();
    }

    Helper::jsonResponse([
        'success' => true,
        'products' => $products
    ]);
});

$router->get('/api/product/{id}', function($id) {
    $product = \App\Models\Product::find($id);

    if (!$product) {
        Helper::jsonResponse(['success' => false, 'message' => 'المنتج غير موجود'], 404);
    }

    Helper::jsonResponse([
        'success' => true,
        'product' => $product
    ]);
});
