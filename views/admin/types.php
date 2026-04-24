<?php
$pageTitle = 'أنواع المنتجات';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="admin-types py-5">
    <div class="container">
        <div class="page-header mb-4">
            <h1><i class="bi bi-tags"></i> <?= $pageTitle ?></h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                <i class="bi bi-plus-circle"></i> إضافة نوع جديد
            </button>
        </div>

        <div class="types-grid">
            <?php foreach ($types as $type): ?>
                <div class="type-card">
                    <div class="type-icon" style="background: <?= $type['color'] ?>;">
                        <i class="bi <?= $type['icon'] ?>"></i>
                    </div>
                    <div class="type-info">
                        <h3><?= htmlspecialchars($type['label']) ?></h3>
                        <code><?= $type['value'] ?></code>
                        <span class="type-count"><?= $type['count'] ?> منتج</span>
                    </div>
                    <div class="type-actions">
                        <button class="btn btn-sm btn-edit" onclick="editType('<?= $type['value'] ?>')">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="modal fade" id="addTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> إضافة نوع جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/types/create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">القيمة (بالإنجليزية) *</label>
                        <input type="text" name="value" class="form-control" required placeholder="مثال: software" pattern="[a-z_]+">
                        <small class="text-muted">حروف إنجليزية صغيرة وشرطة سفلية فقط</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم العربي *</label>
                        <input type="text" name="label" class="form-control" required placeholder="مثال: برامج">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الأيقونة</label>
                        <select name="icon" class="form-select">
                            <option value="bi-key">🔑 كود</option>
                            <option value="bi-repeat">🔄 اشتراك</option>
                            <option value="bi-controller">🎮 لعبة</option>
                            <option value="bi-phone">📱 هاتف</option>
                            <option value="bi-link-45deg">🔗 رابط</option>
                            <option value="bi-credit-card">💳 رصيد</option>
                            <option value="bi-download">📥 تحميل</option>
                            <option value="bi-globe">🌐 موقع</option>
                            <option value="bi-shield">🛡️ حماية</option>
                            <option value="bi-package">📦 برنامج</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اللون</label>
                        <div class="color-options">
                            <input type="radio" name="color" value="#6366f1" checked>
                            <input type="radio" name="color" value="#8b5cf6">
                            <input type="radio" name="color" value="#ec4899">
                            <input type="radio" name="color" value="#ef4444">
                            <input type="radio" name="color" value="#f97316">
                            <input type="radio" name="color" value="#eab308">
                            <input type="radio" name="color" value="#22c55e">
                            <input type="radio" name="color" value="#14b8a6">
                            <input type="radio" name="color" value="#06b6d4">
                            <input type="radio" name="color" value="#3b82f6">
                        </div>
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

<style>
.admin-types {
    background: #f5f7fa;
    min-height: 100vh;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 20px 30px;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.page-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a2e;
}

.page-header h1 i {
    color: #6366f1;
    margin-left: 10px;
}

.types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.type-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.type-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.type-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.type-icon i {
    font-size: 1.5rem;
    color: white;
}

.type-info {
    flex: 1;
}

.type-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 5px;
}

.type-info code {
    display: block;
    font-size: 0.85rem;
    color: #888;
    margin-bottom: 8px;
}

.type-count {
    font-size: 0.8rem;
    background: #f0f0ff;
    color: #6366f1;
    padding: 3px 12px;
    border-radius: 20px;
}

.type-actions .btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
}

.btn-edit { background: #f0f0ff; color: #6366f1; }

.color-options {
    display: flex;
    gap: 10px;
}

.color-options input[type="radio"] {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
