<?php
$pageTitle = 'إدارة المستخدمين';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> إدارة المستخدمين</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>الرصيد</th>
                        <th>عدد الطلبات</th>
                        <th>إجمالي المشتريات</th>
                        <th>تاريخ التسجيل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                لا توجد مستخدمين
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <?= mb_substr($user['name'], 0, 1) ?>
                                        </div>
                                        <span><?= htmlspecialchars($user['name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">أدمن</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">مستخدم</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= \Core\Helper::formatPrice($user['wallet_balance']) ?></td>
                                <td><?= $user['orders_count'] ?></td>
                                <td><?= \Core\Helper::formatPrice($user['total_spent'] ?? 0) ?></td>
                                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
