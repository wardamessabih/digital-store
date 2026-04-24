<?php
$pageTitle = 'تفاصيل الطلب #' . $order['id'];
require_once __DIR__ . '/../layouts/header.php';

$deliveryData = $order['delivery_data'] ? json_decode($order['delivery_data'], true) : null;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-bag"></i> الطلب #<?= $order['id'] ?></h2>
    <a href="/admin/orders" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right"></i> العودة للطلبات
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">معلومات الطلب</h5>
                <span class="badge bg-<?= \Core\Helper::getStatusColor($order['status']) ?> fs-6">
                    <?= \Core\Helper::getStatusText($order['status']) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="text-muted mb-1">رقم الطلب</p>
                        <p class="fw-bold">#<?= $order['id'] ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">تاريخ الإنشاء</p>
                        <p class="fw-bold"><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">طريقة الدفع</p>
                        <p class="fw-bold">
                            <?php
                            $methods = [
                                'wallet' => 'المحفظة',
                                'visa' => 'Visa',
                                'crypto' => 'Crypto'
                            ];
                            echo $methods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </p>
                    </div>
                </div>

                <hr>

                <h5>المنتج</h5>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <?php if ($order['product_image']): ?>
                        <img src="<?= htmlspecialchars($order['product_image']) ?>" style="width: 80px; height: 80px; object-fit: cover;" class="rounded">
                    <?php endif; ?>
                    <div>
                        <h6 class="mb-1"><?= htmlspecialchars($order['product_name']) ?></h6>
                        <p class="text-muted mb-0">الكمية: <?= $order['quantity'] ?></p>
                    </div>
                </div>

                <?php if ($order['custom_field_value']): ?>
                    <div class="alert alert-info">
                        <strong>البيانات المطلوبة:</strong> <?= htmlspecialchars($order['custom_field_value']) ?>
                    </div>
                <?php endif; ?>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>معلومات المستخدم</h5>
                        <p class="mb-1"><strong>الاسم:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
                        <p class="mb-0"><strong>البريد:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>الإجمالي</h5>
                        <h3 class="text-primary mb-0"><?= \Core\Helper::formatPrice($order['total_price']) ?></h3>
                    </div>
                </div>

                <?php if ($order['notes']): ?>
                    <hr>
                    <div>
                        <h5>ملاحظات</h5>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($order['status'] === 'completed' && $deliveryData): ?>
                    <hr>
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle"></i> بيانات التسليم</h5>
                        <?php if (is_array($deliveryData)): ?>
                            <?php foreach ($deliveryData as $key => $value): ?>
                                <div class="row mb-2">
                                    <div class="col-md-3 text-muted"><?= htmlspecialchars($key) ?>:</div>
                                    <div class="col-md-9 fw-bold"><?= htmlspecialchars($value) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <pre class="mb-0"><?= htmlspecialchars($deliveryData) ?></pre>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> تحديث الحالة</h5>
            </div>
            <div class="card-body">
                <form action="/admin/orders/update-status/<?= $order['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>قيد المعالجة</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>مكتمل</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>ملغي</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">بيانات التسليم (للمكتمل)</label>
                        <textarea name="delivery_data" class="form-control font-monospace" rows="4" placeholder='{"code": "YOUR-CODE", "link": "https://..."}'><?= htmlspecialchars($order['delivery_data'] ?? '') ?></textarea>
                        <small class="text-muted">أدخل بيانات المنتج للعميل</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> تحديث الحالة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-warning"><i class="bi bi-exclamation-triangle"></i> إجراءات سريعة</h6>
                    <div class="d-grid gap-2">
                        <form action="/admin/orders/update-status/<?= $order['id'] ?>" method="POST">
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="bi bi-gear"></i> تحويل للمعالجة
                            </button>
                        </form>
                        <form action="/admin/orders/update-status/<?= $order['id'] ?>" method="POST">
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('هل أنت متأكد؟')">
                                <i class="bi bi-x-circle"></i> إلغاء الطلب
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
