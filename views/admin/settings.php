<?php
$pageTitle = 'الإعدادات';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-gear"></i> الإعدادات</h2>
</div>

<form action="/admin/settings" method="POST">
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shop"></i> إعدادات المتجر</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">اسم المتجر</label>
                        <input type="text" name="app_name" class="form-control" value="<?= htmlspecialchars($settings['app_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رابط المتجر</label>
                        <input type="url" name="app_url" class="form-control" value="<?= htmlspecialchars($settings['app_url'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العملة</label>
                        <select name="currency" class="form-select">
                            <option value="SAR" <?= ($settings['currency'] ?? '') === 'SAR' ? 'selected' : '' ?>>ريال سعودي (ر.س)</option>
                            <option value="USD" <?= ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>دولار أمريكي ($)</option>
                            <option value="IQD" <?= ($settings['currency'] ?? '') === 'IQD' ? 'selected' : '' ?>>دينار عراقي</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> إعدادات الدفع</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Stripe Public Key</label>
                        <input type="text" name="stripe_key" class="form-control" value="<?= htmlspecialchars($settings['stripe_key'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stripe Secret Key</label>
                        <input type="password" name="stripe_secret" class="form-control" value="<?= htmlspecialchars($settings['stripe_secret'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Binance API</label>
                        <input type="text" name="binance_api" class="form-control" value="<?= htmlspecialchars($settings['binance_api'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-images"></i> إعدادات الرفع</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="use_cloudinary" class="form-check-input" id="cloudinary" value="1" <?= !empty($settings['use_cloudinary']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="cloudinary">استخدام Cloudinary</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cloudinary URL</label>
                        <input type="text" name="cloudinary_url" class="form-control" value="<?= htmlspecialchars($settings['cloudinary_url'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="use_s3" class="form-check-input" id="s3" value="1" <?= !empty($settings['use_s3']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="s3">استخدام AWS S3</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">S3 Bucket</label>
                        <input type="text" name="s3_bucket" class="form-control" value="<?= htmlspecialchars($settings['s3_bucket'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">S3 Key</label>
                        <input type="text" name="s3_key" class="form-control" value="<?= htmlspecialchars($settings['s3_key'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">S3 Secret</label>
                        <input type="password" name="s3_secret" class="form-control" value="<?= htmlspecialchars($settings['s3_secret'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear-wide"></i> إعدادات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="maintenance_mode" class="form-check-input" id="maintenance" value="1" <?= !empty($settings['maintenance_mode']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="maintenance">وضع الصيانة</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رسالة الصيانة</label>
                        <textarea name="maintenance_message" class="form-control" rows="2"><?= htmlspecialchars($settings['maintenance_message'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Analytics ID</label>
                        <input type="text" name="ga_id" class="form-control" value="<?= htmlspecialchars($settings['ga_id'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle"></i> حفظ الإعدادات
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
