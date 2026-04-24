<?php
use Core\Auth;
use Core\Helper;
use Core\Wallet;
use Core\Notification;

$pageTitle = 'لوحة التحكم';
require_once __DIR__ . '/../layouts/header.php';

$user = Auth::user();
$userId = Auth::id();
$balance = Wallet::getBalance($userId);
$orders = \App\Models\Order::getUserOrders($userId);
$recentOrders = array_slice($orders, 0, 5);
$notifications = Notification::getUserNotifications($userId, 5);
$unreadCount = Notification::getUnreadCount($userId);

$stats = [
    'total_orders' => count($orders),
    'completed_orders' => count(array_filter($orders, fn($o) => $o['status'] === 'completed')),
    'pending_orders' => count(array_filter($orders, fn($o) => $o['status'] === 'pending')),
    'wallet_balance' => $balance
];
?>

<section class="container py-5">
    <!-- Welcome Section -->
    <div class="welcome-section mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">مرحباً <?= htmlspecialchars($user['name']) ?> 👋</h1>
                <p class="text-muted mb-0">اهلاً بك في لوحة التحكم الخاصة بك</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="wallet-balance">
                    <small class="text-muted">رصيد المحفظة</small>
                    <h3 class="mb-0 text-primary"><?= Helper::formatPrice($balance) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <a href="/orders" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10">
                        <i class="bi bi-bag text-primary"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total_orders'] ?></h3>
                        <p class="mb-0">إجمالي الطلبات</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="/orders?status=completed" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['completed_orders'] ?></h3>
                        <p class="mb-0">طلبات مكتملة</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="/orders?status=pending" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon bg-warning bg-opacity-10">
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['pending_orders'] ?></h3>
                        <p class="mb-0">طلبات معلقة</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="/wallet" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-icon bg-info bg-opacity-10">
                        <i class="bi bi-wallet2 text-info"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($balance, 0) ?></h3>
                        <p class="mb-0">ر.س الرصيد</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bag me-2"></i>طلباتي الأخيرة</h5>
                    <a href="/orders" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bag-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">لا توجد طلبات حتى الآن</p>
                            <a href="/products" class="btn btn-primary btn-sm">تصفح المنتجات</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>المنتج</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="/order/<?= $order['id'] ?>" class="text-primary fw-bold">
                                                    #<?= $order['id'] ?>
                                                </a>
                                            </td>
                                            <td><?= Helper::truncate($order['product_name'] ?? 'منتج', 30) ?></td>
                                            <td class="fw-bold"><?= Helper::formatPrice($order['total_price']) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match($order['status']) {
                                                    'completed' => 'success',
                                                    'pending', 'processing' => 'warning',
                                                    'cancelled', 'refunded' => 'danger',
                                                    default => 'secondary'
                                                };
                                                $statusText = match($order['status']) {
                                                    'pending' => 'قيد الانتظار',
                                                    'processing' => 'جاري المعالجة',
                                                    'completed' => 'مكتمل',
                                                    'cancelled' => 'ملغي',
                                                    'refunded' => 'مرتجع',
                                                    default => $order['status']
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications & Quick Actions -->
        <div class="col-lg-4">
            <!-- Notifications -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bell me-2"></i>الإشعارات</h5>
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge bg-danger"><?= $unreadCount ?> جديد</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash fs-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">لا توجد إشعارات</p>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($notifications as $notif): ?>
                                <li class="list-group-item <?= !$notif['is_read'] ? 'bg-light' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="d-block"><?= htmlspecialchars($notif['title']) ?></strong>
                                            <small class="text-muted"><?= htmlspecialchars($notif['message']) ?></small>
                                        </div>
                                        <small class="text-muted"><?= Helper::timeAgo($notif['created_at']) ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="card-footer bg-white text-center">
                            <a href="/notifications" class="btn btn-sm btn-outline-secondary">عرض الكل</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/products" class="btn btn-primary">
                            <i class="bi bi-bag-plus me-2"></i>تصفح المنتجات
                        </a>
                        <a href="/wallet" class="btn btn-outline-primary">
                            <i class="bi bi-wallet2 me-2"></i>شحن المحفظة
                        </a>
                        <a href="/profile" class="btn btn-outline-secondary">
                            <i class="bi bi-person me-2"></i>تعديل الملف الشخصي
                        </a>
                        <?php if (Auth::isAdmin()): ?>
                            <a href="/admin" class="btn btn-dark">
                                <i class="bi bi-speedometer2 me-2"></i>لوحة تحكم الأدمن
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.welcome-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 20px;
    color: white;
}

.welcome-section h1 {
    color: white;
}

.wallet-balance {
    background: rgba(255,255,255,0.2);
    padding: 15px 25px;
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.wallet-balance .text-primary {
    color: white !important;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #eee;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.8rem;
}

.stat-info h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-info p {
    color: #64748b;
    font-size: 0.9rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
