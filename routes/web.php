<?php

use Core\Helper;
use Core\Auth;

$router->get('/', function() {
    $products = \App\Models\Product::getFeatured(12);
    $categories = \App\Models\Category::all();

    extract(['products' => $products, 'categories' => $categories]);
    include __DIR__ . '/../views/pages/home.php';
});

$router->get('/products', function() {
    $type = $_GET['type'] ?? null;
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;

    if ($search) {
        $products = \App\Models\Product::search($search);
    } elseif ($type) {
        $products = \App\Models\Product::getByType($type);
    } elseif ($category) {
        $cat = \App\Models\Category::findBySlug($category);
        if ($cat) {
            $products = \App\Models\Product::getByCategory($cat['id']);
        } else {
            $products = \App\Models\Product::all();
        }
    } else {
        $products = \App\Models\Product::all();
    }

    $categories = \App\Models\Category::all();

    extract(['products' => $products, 'categories' => $categories]);
    include __DIR__ . '/../views/pages/products.php';
});

$router->get('/product/{id}', function($id) {
    $product = \App\Models\Product::find($id);

    if (!$product) {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
        exit;
    }

    include __DIR__ . '/../views/pages/product.php';
});

$router->get('/auth/login', function() {
    if (Auth::isLoggedIn()) {
        Helper::redirect('/');
    }
    include __DIR__ . '/../views/auth/login.php';
});

$router->get('/auth/forgot-password', function() {
    include __DIR__ . '/../views/auth/forgot-password.php';
});

$router->post('/auth/forgot-password', function() {
    header('Content-Type: application/json');
    
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'يرجى إدخال بريد إلكتروني صحيح'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $db = \Core\Database::getInstance();
    $user = $db->fetch('SELECT id, name FROM users WHERE email = ?', [$email]);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير مسجل'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    try {
        $db->insert('password_resets', [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expires
        ]);
    } catch (Exception $e) {
        $db->update('password_resets', [
            'token' => $token,
            'expires_at' => $expires,
            'used' => 0
        ], 'email = ?', ['email' => $email]);
    }

    $resetLink = \Core\Config::get('app.url', 'http://localhost:8000') . '/auth/reset-password?token=' . $token;

    $subject = 'إعادة تعيين كلمة المرور - المتجر الرقمي';
    $body = '
    <div style="font-family: Arial, sans-serif; direction: rtl; text-align: right; max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">المتجر الرقمي</h2>
        <p>مرحباً ' . htmlspecialchars($user['name']) . '</p>
        <p>لقد طلبت إعادة تعيين كلمة المرور الخاصة بك. انقر على الرابط أدناه:</p>
        <p>
            <a href="' . $resetLink . '" style="display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">
                إعادة تعيين كلمة المرور
            </a>
        </p>
        <p>أو انسخ الرابط التالي: ' . $resetLink . '</p>
        <p style="color: #666; font-size: 14px;">هذا الرابط صالح لمدة ساعة واحدة فقط.</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #999; font-size: 12px;">إذا لم تطلب هذا، يمكنك تجاهل هذه الرسالة.</p>
    </div>';

    $mailSent = \Core\Mail::send($email, $subject, $body);

    if (!$mailSent['success']) {
        error_log("Failed to send password reset email to $email: " . ($mailSent['error'] ?? 'unknown'));
        echo json_encode(['success' => false, 'message' => 'فشل في إرسال البريد الإلكتروني: ' . ($mailSent['error'] ?? 'تحقق من إعدادات SMTP')], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني'
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

$router->get('/auth/reset-password', function() {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        \Core\Session::error('رابط غير صالح');
        Helper::redirect('/auth/forgot-password');
    }

    $db = \Core\Database::getInstance();
    $reset = $db->fetch(
        'SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0',
        [$token]
    );

    if (!$reset) {
        \Core\Session::error('رابط الاستعادة منتهي الصلاحية أو غير صالح');
        Helper::redirect('/auth/forgot-password');
    }

    $pageTitle = 'إعادة تعيين كلمة المرور';
    include __DIR__ . '/../views/auth/reset-password.php';
});

$router->post('/auth/reset-password', function() {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirmation'] ?? '';

    if (empty($token) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'يرجى ملء جميع الحقول'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'كلمات المرور غير متطابقة'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $db = \Core\Database::getInstance();
    $reset = $db->fetch(
        'SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0',
        [$token]
    );

    if (!$reset) {
        echo json_encode(['success' => false, 'message' => 'رابط الاستعادة منتهي الصلاحية'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $db->update('users', ['password' => $hashedPassword], 'email = ?', ['email' => $reset['email']]);

    $db->update('password_resets', ['used' => 1], 'token = ?', ['token' => $token]);

    echo json_encode([
        'success' => true,
        'message' => 'تم إعادة تعيين كلمة المرور بنجاح',
        'redirect' => '/auth/login'
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

$router->post('/auth/login', function() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        \Core\Session::error('يرجى إدخال البريد الإلكتروني وكلمة المرور');
        Helper::redirect('/auth/login');
    }

    if (Auth::attempt($email, $password, $remember)) {
        Helper::redirect('/');
    } else {
        \Core\Session::error('البريد الإلكتروني أو كلمة المرور غير صحيحة');
        Helper::redirect('/auth/login');
    }
});

$router->get('/auth/register', function() {
    if (Auth::isLoggedIn()) {
        Helper::redirect('/');
    }
    include __DIR__ . '/../views/auth/register.php';
});

$router->post('/auth/register', function() {
    $data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'password_confirmation' => $_POST['password_confirmation'] ?? '',
        'role' => $_POST['role'] ?? 'user',
        'admin_code' => $_POST['admin_code'] ?? ''
    ];

    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        \Core\Session::error('يرجى ملء جميع الحقول المطلوبة');
        Helper::redirect('/auth/register');
    }

    if ($data['password'] !== $data['password_confirmation']) {
        \Core\Session::error('كلمات المرور غير متطابقة');
        Helper::redirect('/auth/register');
    }

    if (strlen($data['password']) < 6) {
        \Core\Session::error('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
        Helper::redirect('/auth/register');
    }

    if (!in_array($data['role'], ['user', 'admin'])) {
        $data['role'] = 'user';
    }

    if ($data['role'] === 'admin' && empty($data['admin_code'])) {
        \Core\Session::error('يرجى إدخال كود الأدمن');
        Helper::redirect('/auth/register');
    }

    $result = Auth::register($data);

    if ($result['success']) {
        $roleText = $data['role'] === 'admin' ? 'حساب الأدمن' : 'حسابك';
        \Core\Session::success("تم إنشاء {$roleText} بنجاح! يرجى تسجيل الدخول");
        Helper::redirect('/auth/login');
    } else {
        \Core\Session::error($result['message']);
        Helper::redirect('/auth/register');
    }
});

$router->get('/auth/logout', function() {
    Auth::logout();
    Helper::redirect('/');
});

$router->get('/cart', function() {
    include __DIR__ . '/../views/pages/cart.php';
});

$router->get('/orders', function() {
    Auth::check();

    $orders = \App\Models\Order::getUserOrders(Auth::id());
    include __DIR__ . '/../views/pages/orders.php';
});

$router->get('/order/{id}', function($id) {
    Auth::check();

    $order = \App\Models\Order::find($id);

    if (!$order || $order['user_id'] !== Auth::id()) {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
        exit;
    }

    include __DIR__ . '/../views/pages/order-details.php';
});

$router->get('/wallet', function() {
    Auth::check();

    $balance = \Core\Wallet::getBalance(Auth::id());
    $requests = \Core\Wallet::getUserRequests(Auth::id());

    extract(['balance' => $balance, 'requests' => $requests]);
    include __DIR__ . '/../views/pages/wallet.php';
});

$router->post('/wallet/request', function() {
    Auth::check();

    $amount = (float)($_POST['amount'] ?? 0);
    $paymentMethod = $_POST['payment_method'] ?? '';
    $transactionId = $_POST['transaction_id'] ?? '';

    if ($amount <= 0) {
        \Core\Session::error('يرجى إدخال مبلغ صحيح');
        Helper::redirect('/wallet');
    }

    $invoiceImage = null;
    if (isset($_FILES['invoice_image']) && $_FILES['invoice_image']['error'] === UPLOAD_ERR_OK) {
        $upload = Helper::uploadFile($_FILES['invoice_image']);
        if ($upload['success']) {
            $invoiceImage = $upload['path'];
        }
    }

    \Core\Wallet::requestDeposit(Auth::id(), $amount, $paymentMethod, $invoiceImage, $transactionId);
    \Core\Session::success('تم إرسال طلب الشحن بنجاح! سيتم مراجعته قريباً');

    Helper::redirect('/wallet');
});

$router->get('/notifications', function() {
    Auth::check();

    $notifications = \Core\Notification::getUserNotifications(Auth::id());
    include __DIR__ . '/../views/pages/notifications.php';
});

$router->get('/notifications/mark-read/{id}', function($id) {
    Auth::check();
    \Core\Notification::markAsRead($id, Auth::id());
    Helper::redirect('/notifications');
});

$router->get('/notifications/mark-all-read', function() {
    Auth::check();
    \Core\Notification::markAllAsRead(Auth::id());
    Helper::redirect('/notifications');
});

$router->get('/profile', function() {
    Auth::check();

    $user = Auth::user();
    include __DIR__ . '/../views/pages/profile.php';
});

$router->post('/profile/update', function() {
    Auth::check();

    $name = trim($_POST['name'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['new_password_confirmation'] ?? '';

    if (empty($name)) {
        \Core\Session::error('يرجى إدخال الاسم');
        Helper::redirect('/profile');
    }

    $db = \Core\Database::getInstance();
    $db->update('users', ['name' => $name], 'id = ?', ['id' => Auth::id()]);

    if (!empty($newPassword)) {
        if ($newPassword !== $confirmPassword) {
            \Core\Session::error('كلمات المرور غير متطابقة');
            Helper::redirect('/profile');
        }

        if (strlen($newPassword) < 6) {
            \Core\Session::error('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
            Helper::redirect('/profile');
        }

        Auth::updatePassword(Auth::id(), $newPassword);
    }

    \Core\Session::success('تم تحديث الملف الشخصي بنجاح');
    Helper::redirect('/profile');
});

$router->get('/payment/success', function() {
    include __DIR__ . '/../views/pages/payment-success.php';
});

$router->get('/payment/cancel', function() {
    include __DIR__ . '/../views/pages/payment-cancel.php';
});
