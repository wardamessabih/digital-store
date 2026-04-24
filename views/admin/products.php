<?php
$pageTitle = 'إدارة المنتجات';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="page-header mb-4">
            <h2><i class="bi bi-box-seam"></i> <?= $pageTitle ?></h2>
            <a href="/admin/products/create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> إضافة</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>التصنيف</th>
                            <th>السعر</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-1"></i><p>لا توجد منتجات</p></td></tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <?php if ($product['image_url']): ?>
                                            <img src="<?= htmlspecialchars($product['image_url']) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bi bi-image text-muted"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= $product['category_name'] ?? '-' ?></td>
                                    <td><?= number_format($product['price'], 2) ?> ر.س</td>
                                    <td><span class="badge bg-secondary"><?= $product['type'] ?></span></td>
                                    <td>
                                        <?php if ($product['is_active']): ?>
                                            <span class="badge bg-success">نشط</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">غير نشط</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/products/edit/<?= $product['id'] ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <a href="/product/<?= $product['id'] ?>" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                            <form action="/admin/products/delete/<?= $product['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('حذف؟')">
                                                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<style>
.page-header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 25px 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 25px; }
.page-header h2 { margin: 0; font-size: 1.5rem; font-weight: 700; }
.btn-primary { background: linear-gradient(135deg, #4f46e5, #8b5cf6); border: none; border-radius: 12px; padding: 12px 25px; font-weight: 700; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4); }
.table { font-size: 1rem; border-radius: 20px; overflow: hidden; }
.table thead { background: #f8fafc; }
.table th { font-size: 0.95rem; font-weight: 700; padding: 18px 20px; border: none; }
.table td { font-size: 1rem; vertical-align: middle; padding: 18px 20px; border-color: #f1f5f9; }
.table tbody tr:hover { background: #f8faff; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
