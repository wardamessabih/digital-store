<?php
$pageTitle = 'الصفحة غير موجودة';
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<section class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card py-5">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                    <h1 class="display-1 fw-bold text-muted mt-3">404</h1>
                    <h3>الصفحة غير موجودة</h3>
                    <p class="text-muted">عذراً، الصفحة التي تبحث عنها غير موجودة.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="/" class="btn btn-primary">
                            <i class="bi bi-house"></i> العودة للرئيسية
                        </a>
                        <a href="/products" class="btn btn-outline-secondary">
                            <i class="bi bi-bag"></i> تصفح المنتجات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
