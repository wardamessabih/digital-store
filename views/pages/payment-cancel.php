<?php
$pageTitle = 'تم إلغاء الدفع';
require_once __DIR__ . '/../layouts/header.php';

$orderId = (int)($_GET['order_id'] ?? 0);
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="bi bi-x-lg text-white fs-1"></i>
                        </div>
                    </div>

                    <h2 class="mb-3">تم إلغاء الدفع</h2>
                    <p class="text-muted mb-4">لم يتم إتمام عملية الدفع. يمكنك المحاولة مرة أخرى.</p>

                    <?php if ($orderId): ?>
                        <div class="bg-light rounded p-3 mb-4">
                            <small class="text-muted">رقم الطلب: #<?= $orderId ?></small>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="/cart" class="btn btn-primary">
                            <i class="bi bi-cart3"></i> العودة للسلة
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
