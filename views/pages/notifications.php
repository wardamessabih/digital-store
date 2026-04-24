<?php
use Core\Helper;
$pageTitle = 'الإشعارات';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bell"></i> الإشعارات</h1>
        <?php if (!empty($notifications)): ?>
            <a href="/notifications/mark-all-read" class="btn btn-outline-secondary">
                <i class="bi bi-check-all"></i> قراءة الكل
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-bell-slash fs-1 text-muted"></i>
                <h3 class="mt-3">لا توجد إشعارات</h3>
                <p class="text-muted">ستظهر هنا الإشعارات الجديدة</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <?php foreach ($notifications as $notif): ?>
                    <a href="/notifications/mark-read/<?= $notif['id'] ?>"
                       class="notification-item d-flex align-items-center p-3 border-bottom text-decoration-none <?= !$notif['is_read'] ? 'bg-light' : '' ?>"
                       style="color: inherit;">
                        <div class="flex-shrink-0 me-3">
                            <?php
                            $icons = [
                                'order' => 'bi-bag',
                                'wallet' => 'bi-wallet2',
                                'system' => 'bi-gear',
                                'promo' => 'bi-megaphone'
                            ];
                            $colors = [
                                'order' => 'primary',
                                'wallet' => 'success',
                                'system' => 'secondary',
                                'promo' => 'warning'
                            ];
                            ?>
                            <div class="rounded-circle bg-<?= $colors[$notif['type']] ?? 'secondary' ?> text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi <?= $icons[$notif['type']] ?? 'bi-bell' ?> fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-1 <?= !$notif['is_read'] ? 'fw-bold' : '' ?>">
                                    <?= htmlspecialchars($notif['title']) ?>
                                    <?php if (!$notif['is_read']): ?>
                                        <span class="badge bg-primary rounded-pill">جديد</span>
                                    <?php endif; ?>
                                </h6>
                                <small class="text-muted"><?= Helper::timeAgo($notif['created_at']) ?></small>
                            </div>
                            <p class="mb-0 text-muted"><?= htmlspecialchars($notif['message']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<style>
.notification-item:hover {
    background-color: #f8f9fa !important;
}
.notification-item:last-child {
    border-bottom: none !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
