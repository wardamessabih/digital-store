<?php
use Core\Helper;
$pageTitle = $product['name'];
require_once __DIR__ . '/../layouts/header.php';

$typeLabels = [
    'code' => 'كود',
    'subscription' => 'اشتراك',
    'game_id' => 'رقم لعبة',
    'phone' => 'هاتف',
    'link' => 'رابط',
    'credit' => 'رصيد'
];

$typeIcons = [
    'code' => 'bi-key',
    'subscription' => 'bi-repeat',
    'game_id' => 'bi-controller',
    'phone' => 'bi-phone',
    'link' => 'bi-link',
    'credit' => 'bi-currency-dollar'
];
?>

<section class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="/products">المنتجات</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <?php if ($product['image_url']): ?>
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img" alt="<?= htmlspecialchars($product['name']) ?>">
                <?php else: ?>
                    <div class="card-img d-flex align-items-center justify-content-center bg-light" style="height: 400px;">
                        <i class="bi bi-image text-muted" style="font-size: 8rem;"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <?php if ($product['category_name']): ?>
                        <a href="/products?category=<?= $product['category']['slug'] ?? '' ?>" class="badge bg-primary mb-2">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </a>
                    <?php endif; ?>

                    <span class="badge bg-secondary mb-2">
                        <i class="<?= $typeIcons[$product['type']] ?? 'bi-box' ?>"></i>
                        <?= $typeLabels[$product['type']] ?? $product['type'] ?>
                    </span>

                    <h2 class="mb-3"><?= htmlspecialchars($product['name']) ?></h2>

                    <div class="price-tag fs-2 mb-4">
                        <?= Helper::formatPrice($product['price']) ?>
                    </div>

                    <div class="mb-4">
                        <h5><i class="bi bi-info-circle"></i> الوصف</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'] ?? 'لا يوجد وصف')) ?></p>
                    </div>

                    <?php if ($product['requires_custom_field']): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-lg"></i>
                            <strong>ملاحظة:</strong> هذا المنتج يتطلب إدخال <?= htmlspecialchars($product['custom_field_label'] ?? 'بيانات') ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($product['stock'] !== -1 && $product['stock'] <= 5): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            تبقى <?= $product['stock'] ?> قطع فقط!
                        </div>
                    <?php endif; ?>

                    <form action="/api/cart/add" method="POST" id="addToCartForm">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <?php if ($product['requires_custom_field']): ?>
                            <div class="mb-3">
                                <label class="form-label"><?= htmlspecialchars($product['custom_field_label'] ?? 'البيانات المطلوبة') ?></label>
                                <?php if (strpos(strtolower($product['custom_field_label'] ?? ''), 'id') !== false || strpos(strtolower($product['custom_field_label'] ?? ''), 'ايدي') !== false): ?>
                                    <input type="text" name="custom_field" class="form-control" placeholder="مثال: 123456789" required>
                                <?php elseif (strpos(strtolower($product['custom_field_label'] ?? ''), 'email') !== false || strpos(strtolower($product['custom_field_label'] ?? ''), 'بريد') !== false): ?>
                                    <input type="email" name="custom_field" class="form-control" placeholder="example@email.com" required>
                                <?php else: ?>
                                    <input type="text" name="custom_field" class="form-control" required>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">الكمية</label>
                            <div class="input-group" style="width: 150px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="decrementQty()">-</button>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="10">
                                <button type="button" class="btn btn-outline-secondary" onclick="incrementQty()">+</button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus"></i> أضف للسلة
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <div class="d-flex gap-4 text-muted">
                            <div>
                                <i class="bi bi-lightning-charge"></i> توصيل فوري
                            </div>
                            <div>
                                <i class="bi bi-shield-check"></i> ضمان正品
                            </div>
                            <div>
                                <i class="bi bi-headset"></i> دعم 24/7
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function incrementQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) < 10) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'add');

    fetch('/direct-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartBadge();
            showToast('تمت الإضافة للسلة بنجاح!', 'success');
        } else {
            showToast(data.message || 'حدث خطأ', 'danger');
        }
    })
    .catch(err => {
        showToast('حدث خطأ أثناء الإضافة', 'danger');
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
