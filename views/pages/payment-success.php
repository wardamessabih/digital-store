<?php
$pageTitle = 'تم الدفع بنجاح';
require_once __DIR__ . '/../layouts/header.php';

$sessionId = $_GET['session_id'] ?? '';
$orderId = (int)($_GET['order_id'] ?? 0);

if ($sessionId && $orderId) {
    $order = \App\Models\Order::find($orderId);
}
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="bi bi-check-lg text-white fs-1"></i>
                        </div>
                    </div>

                    <h2 class="mb-3">تم الدفع بنجاح!</h2>
                    <p class="text-muted mb-4">شكراً لك! تم استلام طلبك بنجاح.</p>

                    <?php if (isset($order) && $order): ?>
                        <div class="bg-light rounded p-3 mb-4">
                            <h5>رقم الطلب: #<?= $order['id'] ?></h5>
                            <p class="mb-1">المنتج: <?= htmlspecialchars($order['product_name']) ?></p>
                            <p class="mb-0 fw-bold text-primary">المبلغ: <?= \Core\Helper::formatPrice($order['total_price']) ?></p>
                        </div>

                        <p class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            سيتم إرسال إشعار إليك عند بدء معالجة الطلب
                        </p>
                    <?php endif; ?>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="/orders" class="btn btn-primary">
                            <i class="bi bi-bag"></i> مشترياتي
                        </a>
                        <a href="/" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
