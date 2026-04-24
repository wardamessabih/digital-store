<?php
$pageTitle = isset($isEdit) ? 'تعديل المنتج' : 'إضافة منتج جديد';
require_once __DIR__ . '/../layouts/header.php';

$product = $product ?? null;
?>

<div class="page-header">
    <h2><i class="bi bi-<?= isset($isEdit) ? 'pencil-square' : 'plus-circle' ?>"></i> <?= $pageTitle ?></h2>
    <a href="/admin/products" class="btn btn-secondary"><i class="bi bi-arrow-right"></i> العودة</a>
</div>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-info-circle"></i> معلومات المنتج</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">اسم المنتج *</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">التصنيف</label>
                            <div class="input-group">
                                <select name="category_id" class="form-select" id="categorySelect">
                                    <option value="">بدون تصنيف</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-primary" onclick="openCategoryModal()"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع المنتج *</label>
                            <div class="input-group">
                                <select name="type" class="form-select" id="typeSelect" required>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= $type['value'] ?>" <?= ($product['type'] ?? '') === $type['value'] ? 'selected' : '' ?>><?= $type['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-primary" onclick="openTypeModal()"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">السعر (ر.س) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?= $product['price'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المخزون</label>
                            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? -1 ?>">
                            <small class="text-muted">أدخل -1 للمخزون غير محدود</small>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">منتج نشط</label>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-truck"></i> إعدادات التسليم</h5></div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="requires_custom_field" class="form-check-input" id="requiresCustom" value="1" <?= ($product['requires_custom_field'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="requiresCustom">يتطلب بيانات من العميل</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">عنوان حقل البيانات المطلوبة</label>
                        <input type="text" name="custom_field_label" class="form-control" placeholder="مثال: معرف اللاعب" value="<?= htmlspecialchars($product['custom_field_label'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">قالب بيانات التسليم (JSON)</label>
                        <textarea name="delivery_data" class="form-control font-monospace" rows="3"><?= htmlspecialchars($product['delivery_data'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-image"></i> صورة المنتج</h5></div>
                <div class="card-body text-center">
                    <div class="image-preview mb-3" id="imagePreview">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid rounded">
                        <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;"><i class="bi bi-image text-muted" style="font-size: 3rem;"></i></div>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="image" class="form-control mb-2" id="imageInput" accept="image/*">
                    <small class="text-muted">JPG, PNG, GIF - 50MB</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-circle"></i> <?= isset($isEdit) ? 'حفظ التغييرات' : 'إضافة المنتج' ?></button>
            <a href="/admin/products" class="btn btn-secondary w-100">إلغاء</a>
        </div>
    </div>
</form>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-folder-plus"></i> إضافة تصنيف</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="/admin/categories/create" method="POST" id="categoryForm">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">الاسم *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إضافة</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="typeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-tag-plus"></i> إضافة نوع</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="/admin/types/create" method="POST" id="typeForm">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">القيمة (إنجليزي) *</label><input type="text" name="value" class="form-control" required placeholder="software"></div>
                    <div class="mb-3"><label class="form-label">الاسم العربي *</label><input type="text" name="label" class="form-control" required placeholder="برامج"></div>
                    <div class="mb-3"><label class="form-label">اللون</label><input type="color" name="color" class="form-control" value="#4f46e5"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إضافة</button></div>
            </form>
        </div>
    </div>
</div>

<script>
function openCategoryModal() { new bootstrap.Modal(document.getElementById('categoryModal')).show(); }
function openTypeModal() { new bootstrap.Modal(document.getElementById('typeModal')).show(); }
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) { document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded">'; };
        reader.readAsDataURL(file);
    }
});
document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, { method: 'POST', body: new FormData(this) })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const opt = new Option(data.category.name, data.category.id, false, true);
            document.getElementById('categorySelect').add(opt);
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            this.reset();
        }
    });
});
document.getElementById('typeForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(this.action, { method: 'POST', body: new FormData(this) })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const opt = new Option(data.type.label, data.type.value, false, true);
            document.getElementById('typeSelect').add(opt);
            bootstrap.Modal.getInstance(document.getElementById('typeModal')).hide();
            this.reset();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
