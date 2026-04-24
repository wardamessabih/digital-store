<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

try {
    require 'core/Config.php';
    \Core\Config::load('config.json');

    require 'core/Database.php';
    require 'core/Helper.php';
    require 'core/Auth.php';
    require 'core/Wallet.php';
    require 'core/Notification.php';
    require 'app/models/Product.php';
    require 'app/models/Order.php';
    require 'app/Payment/PaymentGateway.php';

    if (!\Core\Auth::isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'يرجى تسجيل الدخول أولاً'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $userId = \Core\Auth::id();
    $cart = $_SESSION['cart'] ?? [];
    $paymentMethod = $_POST['payment_method'] ?? 'wallet';

    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'السلة فارغة'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $orderData = [
        'user_id' => $userId,
        'payment_method' => $paymentMethod
    ];

    if ($paymentMethod === 'visa') {
        $orderData['card_number'] = $_POST['card_number'] ?? '';
        $orderData['card_expiry'] = $_POST['card_expiry'] ?? '';
        $orderData['card_cvc'] = $_POST['card_cvc'] ?? '';
        $orderData['card_holder'] = $_POST['card_holder'] ?? '';
    } elseif ($paymentMethod === 'paypal') {
        $orderData['paypal_email'] = $_POST['paypal_email'] ?? '';
    } elseif ($paymentMethod === 'brimoob') {
        $orderData['brimoob_phone'] = $_POST['brimoob_phone'] ?? '';
    }

    $paymentResult = \App\Payment\PaymentGateway::process($paymentMethod, $total, $orderData);

    if (!$paymentResult['success']) {
        echo json_encode(['success' => false, 'message' => $paymentResult['message']], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $transactionId = $paymentResult['transaction_id'] ?? 'TXN-' . time();
    $isPending = $paymentResult['pending'] ?? false;

    $db = \Core\Database::getInstance();

    foreach ($cart as $productId => $item) {
        $product = \App\Models\Product::find($productId);
        if (!$product) continue;

        $orderData = [
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $item['quantity'],
            'total_price' => $item['price'] * $item['quantity'],
            'payment_method' => $paymentMethod,
            'status' => $isPending ? 'pending' : 'processing',
            'custom_field_value' => $item['custom_field'] ?? null,
            'notes' => 'Transaction: ' . $transactionId
        ];

        $db->insert('orders', $orderData);

        \App\Models\Product::decrementStock($productId, $item['quantity']);

        \Core\Notification::send(
            $userId,
            'طلب جديد',
            'تم إنشاء طلب ' . $product['name'] . ' - ' . ($isPending ? 'بانتظار الدفع' : 'جاري المعالجة'),
            'order'
        );
    }

    $_SESSION['cart'] = [];

    $message = $isPending 
        ? 'تم إنشاء الطلب بنجاح! ' . $paymentResult['message']
        : 'تم إتمام الطلب والدفع بنجاح!';

    echo json_encode([
        'success' => true,
        'message' => $message,
        'transaction_id' => $transactionId,
        'pending' => $isPending
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log('Checkout Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
