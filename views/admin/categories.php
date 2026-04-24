<?php
$pageTitle = 'إدارة التصنيفات';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="admin-categories py-5">
    <div class="container">
        <div class="page-header mb-4">
            <h1><i class="bi bi-folder"></i> <?= $pageTitle ?></h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-circle"></i> إضافة تصنيف
            </button>
        </div>

        <div class="categories-grid">
            <?php if (empty($categories)): ?>
                <div class="empty-state text-center py-5">
                    <i class="bi bi-folder-x"></i>
                    <h3>لا توجد تصنيفات</h3>
                    <p>ابدأ بإضافة أول تصنيف</p>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-folder"></i>
                        </div>
                        <div class="category-info">
                            <h3><?= htmlspecialchars($category['name']) ?></h3>
                            <p><?= htmlspecialchars($category['description'] ?? 'لا يوجد وصف') ?></p>
                            <span class="category-count"><?= $category['product_count'] ?? 0 ?> منتج</span>
                        </div>
                        <div class="category-actions">
                            <a href="/products?category=<?= $category['slug'] ?>" class="btn btn-sm btn-view" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-edit" onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', '<?= htmlspecialchars($category['description'] ?? '') ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="/admin/categories/delete/<?= $category['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذا التصنيف؟')">
                                <button type="submit" class="btn btn-sm btn-delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> إضافة تصنيف جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/categories/create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم التصنيف *</label>
                        <input type="text" name="name" class="form-control" required placeholder="مثال: ألعاب">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="وصف التصنيف"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> تعديل التصنيف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/categories/update" method="POST">
                <input type="hidden" name="id" id="editCategoryId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم التصنيف *</label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" id="editCategoryDesc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="bi fa-check"></i> حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, description) {
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryDesc').value = description;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>

<style>
.admin-categories {
    background: var(--gray-50);
    min-height: 100vh;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 25px 30px;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.page-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
}

.page-header h1 i {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-left: 10px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
}

.category-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}

.category-icon {
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
}

.category-icon i {
    font-size: 1.6rem;
    color: white;
}

.category-info {
    flex: 1;
}

.category-info h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 5px;
}

.category-info p {
    font-size: 0.95rem;
    color: var(--gray-500);
    margin-bottom: 8px;
}

.category-count {
    font-size: 0.85rem;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    color: var(--primary);
    padding: 5px 15px;
    border-radius: 25px;
    font-weight: 600;
}

.category-actions {
    display: flex;
    gap: 10px;
}

.category-actions .btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.3s ease;
}

.btn-edit { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
.btn-edit:hover { background: var(--primary); color: white; transform: scale(1.1); }
.btn-view { background: rgba(249, 115, 22, 0.1); color: #f97316; }
.btn-view:hover { background: #f97316; color: white; transform: scale(1.1); }
.btn-delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.btn-delete:hover { background: #ef4444; color: white; transform: scale(1.1); }

.empty-state {
    background: white;
    border-radius: 20px;
    padding: 80px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.empty-state i {
    font-size: 5rem;
    background: linear-gradient(135deg, var(--gray-200), var(--gray-300));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.empty-state h3 {
    color: var(--dark);
    margin-top: 20px;
    font-weight: 700;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
