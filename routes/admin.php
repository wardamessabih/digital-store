<?php

$router->group('/admin', function() use ($router) {
    $router->get('/', function() {
        \Core\Auth::checkAdmin();

        $stats = \App\Models\Order::getStats();
        $recentOrders = \App\Models\Order::getRecent(10);
        $pendingRequests = \Core\Wallet::getPendingRequests();
        $productCount = \App\Models\Product::count();
        $userCount = \Core\Database::getInstance()->fetch('SELECT COUNT(*) as count FROM users')['count'];

        extract([
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'pendingRequests' => $pendingRequests,
            'productCount' => $productCount,
            'userCount' => $userCount
        ]);

        include __DIR__ . '/../views/admin/dashboard.php';
    });

    $router->get('/products', function() {
        \Core\Auth::checkAdmin();

        $products = \App\Models\Product::all(false);
        $categories = \App\Models\Category::all();

        extract(['products' => $products, 'categories' => $categories]);
        include __DIR__ . '/../views/admin/products.php';
    });

    $router->get('/products/create', function() {
        \Core\Auth::checkAdmin();

        $categories = \App\Models\Category::all();
        $types = \Core\ProductType::all();
        extract(['categories' => $categories, 'types' => $types]);
        include __DIR__ . '/../views/admin/product-form.php';
    });

    $router->post('/products/create', function() {
        \Core\Auth::checkAdmin();

        $data = $_POST;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = \Core\Helper::uploadFile($_FILES['image']);
            if ($upload['success']) {
                $data['image_url'] = $upload['path'];
            }
        }

        $data['delivery_data'] = $_POST['delivery_data'] ?? null;

        if (\App\Models\Product::create($data)) {
            \Core\Session::success('تم إنشاء المنتج بنجاح');
        } else {
            \Core\Session::error('فشل في إنشاء المنتج');
        }

        \Core\Helper::redirect('/admin/products');
    });

    $router->get('/products/edit/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $product = \App\Models\Product::find($id);
        $categories = \App\Models\Category::all();
        $types = \Core\ProductType::all();

        if (!$product) {
            \Core\Session::error('المنتج غير موجود');
            \Core\Helper::redirect('/admin/products');
        }

        extract(['product' => $product, 'categories' => $categories, 'types' => $types, 'isEdit' => true]);
        include __DIR__ . '/../views/admin/product-form.php';
    });

    $router->post('/products/edit/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $data = $_POST;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = \Core\Helper::uploadFile($_FILES['image']);
            if ($upload['success']) {
                $data['image_url'] = $upload['path'];
            }
        }

        $data['delivery_data'] = $_POST['delivery_data'] ?? null;

        if (\App\Models\Product::update($id, $data)) {
            \Core\Session::success('تم تحديث المنتج بنجاح');
        } else {
            \Core\Session::error('فشل في تحديث المنتج');
        }

        \Core\Helper::redirect('/admin/products');
    });

    $router->post('/products/delete/{id}', function($id) {
        \Core\Auth::checkAdmin();

        if (\App\Models\Product::delete($id)) {
            \Core\Session::success('تم حذف المنتج بنجاح');
        } else {
            \Core\Session::error('فشل في حذف المنتج');
        }

        \Core\Helper::redirect('/admin/products');
    });

    $router->get('/orders', function() {
        \Core\Auth::checkAdmin();

        $status = $_GET['status'] ?? null;
        $orders = \App\Models\Order::getAll($status);

        extract(['orders' => $orders, 'currentStatus' => $status]);
        include __DIR__ . '/../views/admin/orders.php';
    });

    $router->get('/orders/view/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $order = \App\Models\Order::find($id);

        if (!$order) {
            \Core\Session::error('الطلب غير موجود');
            \Core\Helper::redirect('/admin/orders');
        }

        extract(['order' => $order]);
        include __DIR__ . '/../views/admin/order-view.php';
    });

    $router->post('/orders/update-status/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $status = $_POST['status'] ?? '';
        $deliveryData = $_POST['delivery_data'] ?? null;
        $notes = $_POST['notes'] ?? null;

        \App\Models\Order::updateStatus($id, $status, $deliveryData, $notes);

        \Core\Session::success('تم تحديث حالة الطلب');
        \Core\Helper::redirect("/admin/orders/view/{$id}");
    });

    $router->get('/wallet-requests', function() {
        \Core\Auth::checkAdmin();

        $requests = \App\Models\Wallet::getPendingRequests();
        $allRequests = \Core\Database::getInstance()->fetchAll(
            'SELECT wr.*, u.name as user_name, u.email as user_email, p.name as processor_name
             FROM wallet_requests wr
             JOIN users u ON wr.user_id = u.id
             LEFT JOIN users p ON wr.processed_by = p.id
             ORDER BY wr.created_at DESC
             LIMIT 100'
        );

        extract(['pendingRequests' => $requests, 'allRequests' => $allRequests]);
        include __DIR__ . '/../views/admin/wallet-requests.php';
    });

    $router->post('/wallet-requests/approve/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $result = \Core\Wallet::approveRequest($id, \Core\Auth::id());

        if ($result['success']) {
            \Core\Session::success('تم الموافقة على طلب الشحن');
        } else {
            \Core\Session::error($result['message'] ?? 'فشل في الموافقة');
        }

        \Core\Helper::redirect('/admin/wallet-requests');
    });

    $router->post('/wallet-requests/reject/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $reason = $_POST['reason'] ?? '';
        $result = \Core\Wallet::rejectRequest($id, \Core\Auth::id(), $reason);

        if ($result['success']) {
            \Core\Session::success('تم رفض طلب الشحن');
        } else {
            \Core\Session::error($result['message'] ?? 'فشل في الرفض');
        }

        \Core\Helper::redirect('/admin/wallet-requests');
    });

    $router->get('/categories', function() {
        \Core\Auth::checkAdmin();

        $categories = \App\Models\Category::all(false);
        include __DIR__ . '/../views/admin/categories.php';
    });

    $router->post('/categories/create', function() {
        \Core\Auth::checkAdmin();

        $data = $_POST;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = \Core\Helper::uploadFile($_FILES['image']);
            if ($upload['success']) {
                $data['image_url'] = $upload['path'];
            }
        }

        $result = \App\Models\Category::create($data);

        if (\Core\Helper::isAjax()) {
            if ($result) {
                $newCategory = \App\Models\Category::find($result);
                echo json_encode(['success' => true, 'category' => $newCategory]);
            } else {
                echo json_encode(['success' => false, 'message' => 'فشل في إنشاء التصنيف']);
            }
            exit;
        }

        if ($result) {
            \Core\Session::success('تم إنشاء التصنيف بنجاح');
        } else {
            \Core\Session::error('فشل في إنشاء التصنيف');
        }

        \Core\Helper::redirect('/admin/categories');
    });

    $router->post('/categories/update/{id}', function($id) {
        \Core\Auth::checkAdmin();

        $data = $_POST;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = \Core\Helper::uploadFile($_FILES['image']);
            if ($upload['success']) {
                $data['image_url'] = $upload['path'];
            }
        }

        if (\App\Models\Category::update($id, $data)) {
            \Core\Session::success('تم تحديث التصنيف بنجاح');
        } else {
            \Core\Session::error('فشل في تحديث التصنيف');
        }

        \Core\Helper::redirect('/admin/categories');
    });

    $router->post('/categories/delete/{id}', function($id) {
        \Core\Auth::checkAdmin();

        if (\App\Models\Category::delete($id)) {
            \Core\Session::success('تم حذف التصنيف بنجاح');
        } else {
            \Core\Session::error('فشل في حذف التصنيف');
        }

        \Core\Helper::redirect('/admin/categories');
    });

    $router->get('/users', function() {
        \Core\Auth::checkAdmin();

        $users = \Core\Database::getInstance()->fetchAll(
            'SELECT u.*,
             (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as orders_count,
             (SELECT SUM(total_price) FROM orders WHERE user_id = u.id AND status = "completed") as total_spent
             FROM users u
             ORDER BY u.created_at DESC'
        );

        extract(['users' => $users]);
        include __DIR__ . '/../views/admin/users.php';
    });

    $router->get('/types', function() {
        \Core\Auth::checkAdmin();

        $db = \Core\Database::getInstance();
        $defaultTypes = [
            ['value' => 'code', 'label' => 'كود', 'icon' => 'bi-key', 'color' => '#6366f1'],
            ['value' => 'subscription', 'label' => 'اشتراك', 'icon' => 'bi-repeat', 'color' => '#8b5cf6'],
            ['value' => 'game_id', 'label' => 'رقم لعبة', 'icon' => 'bi-controller', 'color' => '#ec4899'],
            ['value' => 'phone', 'label' => 'هاتف', 'icon' => 'bi-phone', 'color' => '#f97316'],
            ['value' => 'link', 'label' => 'رابط', 'icon' => 'bi-link-45deg', 'color' => '#06b6d4'],
            ['value' => 'credit', 'label' => 'رصيد', 'icon' => 'bi-credit-card', 'color' => '#22c55e']
        ];

        $savedTypes = $db->fetchAll('SELECT * FROM product_types ORDER BY id');
        $savedTypesMap = [];
        foreach ($savedTypes as $st) {
            $savedTypesMap[$st['value']] = $st;
        }

        $types = [];
        foreach ($defaultTypes as $dt) {
            $count = $db->fetch('SELECT COUNT(*) as c FROM products WHERE type = ?', [$dt['value']])['c'] ?? 0;
            $types[] = array_merge($dt, ['count' => $count]);
        }

        foreach ($savedTypes as $st) {
            $count = $db->fetch('SELECT COUNT(*) as c FROM products WHERE type = ?', [$st['value']])['c'] ?? 0;
            $types[] = array_merge($st, ['count' => $count]);
        }

        extract(['types' => $types]);
        include __DIR__ . '/../views/admin/types.php';
    });

    $router->post('/types/create', function() {
        \Core\Auth::checkAdmin();

        $value = strtolower(trim($_POST['value'] ?? ''));
        $label = trim($_POST['label'] ?? '');
        $icon = $_POST['icon'] ?? 'bi-tag';
        $color = $_POST['color'] ?? '#6366f1';

        if (empty($value) || empty($label)) {
            \Core\Session::error('يرجى ملء جميع الحقول المطلوبة');
            \Core\Helper::redirect('/admin/types');
            return;
        }

        $db = \Core\Database::getInstance();
        $existing = $db->fetch('SELECT id FROM product_types WHERE value = ?', [$value]);
        
        if ($existing) {
            if (\Core\Helper::isAjax()) {
                echo json_encode(['success' => false, 'message' => 'هذا النوع موجود بالفعل']);
                exit;
            }
            \Core\Session::error('هذا النوع موجود بالفعل');
            \Core\Helper::redirect('/admin/types');
            return;
        }

        $db->insert('product_types', [
            'value' => $value,
            'label' => $label,
            'icon' => $icon,
            'color' => $color
        ]);

        if (\Core\Helper::isAjax()) {
            echo json_encode(['success' => true, 'type' => ['value' => $value, 'label' => $label, 'icon' => $icon, 'color' => $color]]);
            exit;
        }

        \Core\Session::success('تم إضافة النوع بنجاح');
        \Core\Helper::redirect('/admin/types');
    });

    $router->post('/types/delete/{value}', function($value) {
        \Core\Auth::checkAdmin();

        $db = \Core\Database::getInstance();
        $db->delete('product_types', 'value = ?', [$value]);

        \Core\Session::success('تم حذف النوع بنجاح');
        \Core\Helper::redirect('/admin/types');
    });

    $router->get('/settings', function() {
        \Core\Auth::checkAdmin();

        $db = \Core\Database::getInstance();

        foreach ($_POST as $key => $value) {
            if (in_array($key, ['_token', 'submit'])) continue;

            $existing = $db->fetch('SELECT id FROM settings WHERE setting_key = ?', [$key]);

            if ($existing) {
                $db->update('settings', ['setting_value' => $value], 'setting_key = ?', ['setting_key' => $key]);
            } else {
                $db->insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
            }
        }

        \Core\Session::success('تم حفظ الإعدادات');
        \Core\Helper::redirect('/admin/settings');
    });
});
