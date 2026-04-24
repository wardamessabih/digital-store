<?php
use Core\Helper;
use Core\Wallet;
use Core\Auth;
$pageTitle = 'حسابي';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <span class="text-white fs-1"><?= mb_substr($user['name'], 0, 1) ?></span>
                    </div>
                    <h4 class="mt-3 mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                    <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                        <?= $user['role'] === 'admin' ? 'مدير' : 'مستخدم' ?>
                    </span>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-wallet2"></i> رصيد المحفظة</h5>
                    <h3 class="text-primary"><?= Helper::formatPrice(\Core\Wallet::getBalance($user['id'])) ?></h3>
                    <a href="/wallet" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="bi bi-plus-circle"></i> شحن المحفظة
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> معلومات الحساب</h5>
                </div>
                <div class="card-body">
                    <form action="/profile/update" method="POST" id="profileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                <small class="text-muted">لا يمكن تغيير البريد الإلكتروني</small>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3"><i class="bi bi-lock"></i> تغيير كلمة المرور</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" name="new_password" class="form-control" minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" name="new_password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> النشاط الأخير</h5>
                </div>
                <div class="card-body">
                    <?php
                    $recentOrders = \App\Models\Order::getUserOrders($user['id'], 5);
                    ?>
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-muted text-center mb-0">لا يوجد نشاط حديث</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($recentOrders as $order): ?>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <strong><?= htmlspecialchars($order['product_name']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= Helper::getStatusColor($order['status']) ?>">
                                            <?= Helper::getStatusText($order['status']) ?>
                                        </span>
                                        <br>
                                        <small><?= Helper::formatPrice($order['total_price']) ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const newPassword = document.querySelector('input[name="new_password"]').value;
    const confirmPassword = document.querySelector('input[name="new_password_confirmation"]').value;

    if (newPassword && newPassword !== confirmPassword) {
        e.preventDefault();
        alert('كلمات المرور غير متطابقة!');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
