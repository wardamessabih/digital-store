<?php
$pageTitle = 'إدارة الطلبات';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-bag"></i> إدارة الطلبات</h2>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-auto">
                <a href="/admin/orders" class="btn <?= !$currentStatus ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    الكل
                </a>
                <a href="/admin/orders?status=pending" class="btn <?= $currentStatus === 'pending' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-hourglass"></i> معلقة
                </a>
                <a href="/admin/orders?status=processing" class="btn <?= $currentStatus === 'processing' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-gear"></i> قيد المعالجة
                </a>
                <a href="/admin/orders?status=completed" class="btn <?= $currentStatus === 'completed' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-check-circle"></i> مكتمل
                </a>
                <a href="/admin/orders?status=cancelled" class="btn <?= $currentStatus === 'cancelled' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-x-circle"></i> ملغي
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                        <th>طريقة الدفع</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                لا توجد طلبات
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= $order['id'] ?></strong></td>
                                <td>
                                    <div><?= htmlspecialchars($order['user_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($order['user_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td><?= $order['quantity'] ?></td>
                                <td class="fw-bold"><?= \Core\Helper::formatPrice($order['total_price']) ?></td>
                                <td>
                                    <?php
                                    $methods = [
                                        'wallet' => '<i class="bi bi-wallet2"></i> محفظة',
                                        'visa' => '<i class="bi bi-credit-card"></i> Visa',
                                        'crypto' => '<i class="bi bi-currency-bitcoin"></i> Crypto'
                                    ];
                                    echo $methods[$order['payment_method']] ?? $order['payment_method'];
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= \Core\Helper::getStatusColor($order['status']) ?>">
                                        <?= \Core\Helper::getStatusText($order['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="/admin/orders/view/<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
