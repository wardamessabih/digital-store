<?php
$pageTitle = 'لوحة التحكم';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> لوحة التحكم</h2>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">إجمالي الطلبات</h6>
                        <h3 class="mb-0"><?= number_format($stats['total_orders']) ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-bag text-primary fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/orders" class="text-decoration-none">عرض التفاصيل <i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">طلبات معلقة</h6>
                        <h3 class="mb-0"><?= number_format($stats['pending_orders']) ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/orders?status=pending" class="text-decoration-none">عرض التفاصيل <i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">المبيعات</h6>
                        <h3 class="mb-0"><?= number_format($stats['total_revenue'], 2) ?> ر.س</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-currency-dollar text-success fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/orders?status=completed" class="text-decoration-none">عرض التفاصيل <i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">المنتجات</h6>
                        <h3 class="mb-0"><?= number_format($productCount) ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-box-seam text-info fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/products" class="text-decoration-none">عرض التفاصيل <i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> آخر الطلبات</h5>
                <a href="/admin/orders" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المستخدم</th>
                                <th>المنتج</th>
                                <th>الإجمالي</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">لا توجد طلبات</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><a href="/admin/orders/view/<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                        <td><?= \Core\Helper::formatPrice($order['total_price']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= \Core\Helper::getStatusColor($order['status']) ?>">
                                                <?= \Core\Helper::getStatusText($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-wallet2"></i> طلبات الشحن المعلقة</h5>
                <a href="/admin/wallet-requests" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pendingRequests)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-1"></i>
                        <p class="mb-0 mt-2">لا توجد طلبات معلقة</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach (array_slice($pendingRequests, 0, 5) as $request): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($request['user_name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= \Core\Helper::formatPrice($request['amount']) ?></small>
                                </div>
                                <a href="/admin/wallet-requests" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body text-center">
                <i class="bi bi-people text-primary fs-1"></i>
                <h4 class="mt-2"><?= number_format($userCount) ?></h4>
                <p class="text-muted mb-0">إجمالي المستخدمين</p>
                <a href="/admin/users" class="btn btn-sm btn-outline-primary mt-2">عرض المستخدمين</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
