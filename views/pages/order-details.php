<?php
use Core\Helper;
$pageTitle = 'تفاصيل الطلب #' . $order['id'];
require_once __DIR__ . '/../layouts/header.php';

$deliveryData = $order['delivery_data'] ? json_decode($order['delivery_data'], true) : null;
?>

<section class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="/orders">مشترياتي</a></li>
            <li class="breadcrumb-item active">الطلب #<?= $order['id'] ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">الطلب #<?= $order['id'] ?></h4>
                    <span class="badge bg-<?= Helper::getStatusColor($order['status']) ?> fs-6">
                        <?= Helper::getStatusText($order['status']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="text-muted mb-1">تاريخ الطلب</p>
                            <p class="fw-bold"><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">طريقة الدفع</p>
                            <p class="fw-bold">
                                <?php
                                $paymentMethods = [
                                    'wallet' => 'المحفظة',
                                    'visa' => 'Visa',
                                    'crypto' => 'Crypto'
                                ];
                                echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                                ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">الإجمالي</p>
                            <p class="fw-bold text-primary fs-5"><?= Helper::formatPrice($order['total_price']) ?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>المنتج</h5>
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($order['product_image']): ?>
                                    <img src="<?= htmlspecialchars($order['product_image']) ?>" style="width: 80px; height: 80px; object-fit: cover;" class="rounded">
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($order['product_name']) ?></h6>
                                    <p class="text-muted mb-0">الكمية: <?= $order['quantity'] ?></p>
                                    <p class="text-muted mb-0">السعر: <?= Helper::formatPrice($order['total_price']) ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if ($order['custom_field_value']): ?>
                            <div class="col-md-6">
                                <h5>البيانات المطلوبة</h5>
                                <p class="mb-0"><?= htmlspecialchars($order['custom_field_value']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($order['notes']): ?>
                        <hr>
                        <div>
                            <h5>ملاحظات</h5>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($order['status'] === 'completed' && $deliveryData): ?>
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-check-circle"></i> تم تسليم المنتج
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">بيانات التوصيل</h5>
                        <?php if (is_array($deliveryData)): ?>
                            <?php foreach ($deliveryData as $key => $value): ?>
                                <div class="row mb-2">
                                    <div class="col-md-3 text-muted"><?= htmlspecialchars(ucfirst($key)) ?>:</div>
                                    <div class="col-md-9 fw-bold">
                                        <?php if (strpos(strtolower($key), 'code') !== false || strpos(strtolower($key), 'كود') !== false): ?>
                                            <code class="fs-5"><?= htmlspecialchars($value) ?></code>
                                            <button onclick="copyToClipboard('<?= htmlspecialchars(addslashes($value)) ?>')" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-clipboard"></i> نسخ
                                            </button>
                                        <?php elseif (strpos(strtolower($key), 'link') !== false || strpos(strtolower($key), 'رابط') !== false): ?>
                                            <a href="<?= htmlspecialchars($value) ?>" target="_blank" class="btn btn-sm btn-success">
                                                <i class="bi bi-box-arrow-up-right"></i> فتح الرابط
                                            </a>
                                        <?php elseif (strpos(strtolower($key), 'image') !== false || strpos(strtolower($key), 'صورة') !== false): ?>
                                            <img src="<?= htmlspecialchars($value) ?>" style="max-width: 200px;" class="img-thumbnail">
                                        <?php else: ?>
                                            <?= htmlspecialchars($value) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p><?= nl2br(htmlspecialchars($deliveryData)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($order['status'] === 'processing'): ?>
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-hourglass-split"></i> جاري المعالجة
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            <i class="bi bi-info-circle"></i>
                            طلبك قيد المعالجة سيتم تسليمه قريباً. شكراً لصبرك!
                        </p>
                    </div>
                </div>
            <?php elseif ($order['status'] === 'cancelled'): ?>
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-x-circle"></i> تم إلغاء الطلب
                    </div>
                    <div class="card-body">
                        <p class="mb-0">تم إلغاء هذا الطلب.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">ملخص الطلب</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>المنتج</span>
                        <span><?= htmlspecialchars($order['product_name']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الكمية</span>
                        <span><?= $order['quantity'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>طريقة الدفع</span>
                        <span><?= $paymentMethods[$order['payment_method']] ?? $order['payment_method'] ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">الإجمالي</span>
                        <span class="fw-bold text-primary fs-5"><?= Helper::formatPrice($order['total_price']) ?></span>
                    </div>
                </div>
            </div>

            <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <button onclick="cancelOrder(<?= $order['id'] ?>)" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle"></i> إلغاء الطلب
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
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

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('تم النسخ!', 'success');
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
