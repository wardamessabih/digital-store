<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require 'core/Config.php';
    \Core\Config::load('config.json');

    require 'core/Database.php';
    require 'core/Helper.php';
    require 'app/models/Product.php';

    $action = $_GET['action'] ?? $_POST['action'] ?? 'add';

    if ($action === 'count') {
        $cart = $_SESSION['cart'] ?? [];
        echo json_encode([
            'success' => true,
            'cart_count' => count($cart)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'remove') {
        $productId = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        echo json_encode([
            'success' => true,
            'message' => 'تم الحذف من السلة',
            'cart_count' => count($_SESSION['cart'] ?? [])
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        echo json_encode([
            'success' => true,
            'message' => 'تم تفريغ السلة',
            'cart_count' => 0
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $product = \App\Models\Product::find($productId);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'المنتج غير موجود'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cart = $_SESSION['cart'] ?? [];

    $cart[$productId] = [
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $product['price'],
        'name' => $product['name'],
        'image' => $product['image_url'],
        'custom_field_required' => $product['requires_custom_field'],
        'custom_field_label' => $product['custom_field_label']
    ];

    $_SESSION['cart'] = $cart;

    echo json_encode([
        'success' => true,
        'message' => 'تمت الإضافة للسلة',
        'cart_count' => count($cart),
        'product_name' => $product['name']
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log('Cart Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
