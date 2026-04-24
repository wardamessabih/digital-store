<?php
use Core\Helper;
$pageTitle = 'المنتجات';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="container py-5">
    <h1 class="section-title"><i class="bi bi-grid-3x3-gap"></i> <?= $pageTitle ?></h1>

    <div class="row">
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="/products" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="ابحث..." value="<?= $_GET['search'] ?? '' ?>">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    <h6 class="mb-3 fw-bold"><i class="bi bi-folder me-2"></i> التصنيفات</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/products" class="text-decoration-none <?= !isset($_GET['category']) && !isset($_GET['type']) ? 'text-primary fw-bold' : 'text-secondary' ?>">
                                جميع المنتجات
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li class="mb-2">
                                <a href="/products?category=<?= $category['slug'] ?>" class="text-decoration-none <?= ($_GET['category'] ?? '') === $category['slug'] ? 'text-primary fw-bold' : 'text-secondary' ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <hr>

                    <h6 class="mb-3 fw-bold"><i class="bi bi-tag me-2"></i> الأنواع</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/products?type=code" class="text-decoration-none <?= ($_GET['type'] ?? '') === 'code' ? 'text-primary fw-bold' : 'text-secondary' ?>">
                                <i class="bi bi-key me-2"></i> أكواد
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/products?type=subscription" class="text-decoration-none <?= ($_GET['type'] ?? '') === 'subscription' ? 'text-primary fw-bold' : 'text-secondary' ?>">
                                <i class="bi bi-repeat me-2"></i> اشتراكات
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/products?type=game_id" class="text-decoration-none <?= ($_GET['type'] ?? '') === 'game_id' ? 'text-primary fw-bold' : 'text-secondary' ?>">
                                <i class="bi bi-controller me-2"></i> أرقام ألعاب
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/products?type=credit" class="text-decoration-none <?= ($_GET['type'] ?? '') === 'credit' ? 'text-primary fw-bold' : 'text-secondary' ?>">
                                <i class="bi bi-phone me-2"></i> شحن رصيد
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">لا توجد منتجات</h4>
                        <a href="/products" class="btn btn-primary mt-3">عرض الجميع</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card product-card h-100">
                                <?php if ($product['image_url']): ?>
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($product['category_name']): ?>
                                    <span class="category-badge"><?= htmlspecialchars($product['category_name']) ?></span>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-secondary"><?= Helper::truncate($product['description'] ?? '', 80) ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                        <span class="price-tag"><?= Helper::formatPrice($product['price']) ?></span>
                                        <div class="btn-group">
                                            <a href="/product/<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-primary btn-sm">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
