<?php
use Core\Helper;
$pageTitle = 'مشترياتي';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="container py-5">
    <h1 class="mb-4"><i class="bi bi-bag"></i> مشترياتي</h1>

    <?php if (empty($orders)): ?>
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-bag-x fs-1 text-muted"></i>
                <h3 class="mt-3">لا توجد طلبات</h3>
                <p class="text-muted">لم تقم بأي طلب بعد</p>
                <a href="/products" class="btn btn-primary">
                    <i class="bi bi-bag"></i> تصفح المنتجات
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>الإجمالي</th>
                                <th>حالة الدفع</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <a href="/order/<?= $order['id'] ?>" class="text-decoration-none fw-bold">
                                            #<?= $order['id'] ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($order['product_image']): ?>
                                                <img src="<?= htmlspecialchars($order['product_image']) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($order['product_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= $order['quantity'] ?></td>
                                    <td class="fw-bold"><?= Helper::formatPrice($order['total_price']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= Helper::getStatusColor($order['status']) ?>">
                                            <?= Helper::getStatusText($order['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <a href="/order/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> عرض
                                        </a>
                                        <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                                            <button onclick="cancelOrder(<?= $order['id'] ?>)" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle"></i> إلغاء
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
function cancelOrder(orderId) {
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) return;

    const formData = new FormData();

    fetch(`/api/order/cancel/${orderId}`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('تم إلغاء الطلب بنجاح', 'success');
            location.reload();
        } else {
            showToast(data.message || 'حدث خطأ', 'danger');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
