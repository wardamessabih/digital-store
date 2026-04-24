<?php
use Core\Config;
use Core\Helper;
$pageTitle = 'الرئيسية';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="hero-section">
    <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <div class="hero-badge">
                    <i class="bi bi-star-fill"></i>
                    <span>خصم يصل إلى 50%</span>
                </div>
                <h1>مرحبا بك في <span class="text-gradient">المتجر الرقمي</span></h1>
                <p class="hero-subtitle">أفضل المنتجات الرقمية بأسعار مميزة - أكواد، اشتراكات، وألعاب بأسعار تنافسية</p>
                <form action="/products" method="GET" class="hero-search">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن منتج...">
                        <button type="submit" class="btn btn-light">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                <div class="hero-buttons">
                    <a href="/products" class="btn-cta">
                        <i class="bi bi-bag-check"></i>
                        <span>تسوق الآن</span>
                    </a>
                    <a href="/products?type=code" class="btn-outline">
                        <i class="bi bi-key"></i>
                        <span>عرض الأكواد</span>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 hero-image">
                <div class="hero-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=600" alt="Digital Store" class="img-fluid">
                </div>
                <div class="floating-card card-1">
                    <div class="card-icon bg-success bg-opacity-10">
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <div>
                        <span class="fw-bold d-block">توصيل فوري</span>
                        <small class="text-muted">24 ساعة</small>
                    </div>
                </div>
                <div class="floating-card card-2">
                    <div class="card-icon bg-warning bg-opacity-10">
                        <i class="bi bi-shield-fill text-warning"></i>
                    </div>
                    <div>
                        <span class="fw-bold d-block">دفع آمن 100%</span>
                        <small class="text-muted">مشفر بالكامل</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <h2 class="section-title"><i class="bi bi-grid-3x3-gap"></i> التصنيفات</h2>
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="/products?type=code" class="text-decoration-none">
                <div class="category-card">
                    <i class="bi bi-key text-primary"></i>
                    <h5>أكواد</h5>
                    <p>Steam, Google Play, iTunes</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/products?type=subscription" class="text-decoration-none">
                <div class="category-card">
                    <i class="bi bi-repeat text-success"></i>
                    <h5>اشتراكات</h5>
                    <p>Netflix, Spotify, VPN</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/products?type=game_id" class="text-decoration-none">
                <div class="category-card">
                    <i class="bi bi-controller text-danger"></i>
                    <h5>أرقام ألعاب</h5>
                    <p>PUBG, Free Fire, COD</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/products?type=credit" class="text-decoration-none">
                <div class="category-card">
                    <i class="bi bi-phone text-info"></i>
                    <h5>شحن رصيد</h5>
                    <p>زين, أورنج, موبيليس</p>
                </div>
            </a>
        </div>
    </div>

    <?php
    $featuredProducts = \App\Models\Product::all();
    $featuredProducts = array_slice($featuredProducts, 0, 8);
    ?>
    
    <?php if (!empty($featuredProducts)): ?>
    <h2 class="section-title"><i class="bi bi-lightning-fill"></i> أحدث المنتجات</h2>
    <div class="row g-4">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    <div class="position-relative">
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
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text"><?= Helper::truncate($product['description'] ?? '', 60) ?></p>
                    </div>
                    <div class="product-footer">
                        <span class="price-tag"><?= Helper::formatPrice($product['price']) ?></span>
                        <div class="btn-group-custom">
                            <a href="/product/<?= $product['id'] ?>" class="btn-action btn-view">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" onclick="addToCart(<?= $product['id'] ?>)" class="btn-action btn-buy">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
