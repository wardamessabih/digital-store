<?php
use Core\Helper;
$pageTitle = 'المحفظة';
require_once __DIR__ . '/../layouts/header.php';
?>

<section class="container py-5">
    <h1 class="mb-4"><i class="bi bi-wallet2"></i> المحفظة</h1>

    <div class="row">
        <div class="col-lg-4">
            <div class="card bg-gradient text-white mb-4" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                <div class="card-body text-center">
                    <i class="bi bi-wallet-fill fs-1 mb-3"></i>
                    <h3 class="mb-2">الرصيد الحالي</h3>
                    <h2 class="display-4 fw-bold mb-0"><?= Helper::formatPrice($balance) ?></h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-plus-circle"></i> شحن المحفظة</h5>
                    <hr>
                    <form action="/wallet/request" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">المبلغ (ر.س)</label>
                            <input type="number" name="amount" class="form-control" min="1" step="0.01" required placeholder="مثال: 100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">طريقة الدفع</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="zain_cash">Zain Cash</option>
                                <option value="asia_hawala">Asia Hawala</option>
                                <option value="western_union">Western Union</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رقم الحوالة / المرجع</label>
                            <input type="text" name="transaction_id" class="form-control" placeholder="رقم الحوالة">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">صورة الفاتورة / الإيصال</label>
                            <input type="file" name="invoice_image" class="form-control" accept="image/*,.pdf">
                            <small class="text-muted">اختياري - يرجى إرفاق صورة الإيصال</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> إرسال طلب الشحن
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> سجل الشحنات</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($requests)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">لا توجد طلبات شحن سابقة</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>المبلغ</th>
                                        <th>الطريقة</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $req): ?>
                                        <tr>
                                            <td><?= $req['id'] ?></td>
                                            <td class="fw-bold"><?= Helper::formatPrice($req['amount']) ?></td>
                                            <td>
                                                <?php
                                                $methods = [
                                                    'bank_transfer' => 'تحويل بنكي',
                                                    'zain_cash' => 'Zain Cash',
                                                    'asia_hawala' => 'Asia Hawala',
                                                    'western_union' => 'Western Union'
                                                ];
                                                echo $methods[$req['payment_method']] ?? $req['payment_method'];
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= Helper::getStatusColor($req['status']) ?>">
                                                    <?= Helper::getStatusText($req['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('Y-m-d', strtotime($req['created_at'])) ?></td>
                                            <td>
                                                <?php if ($req['admin_notes']): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="showNotes('<?= htmlspecialchars(addslashes($req['admin_notes'])) ?>')">
                                                        <i class="bi bi-chat-left-text"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($req['invoice_image_url']): ?>
                                                    <a href="<?= htmlspecialchars($req['invoice_image_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-image"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5><i class="bi bi-info-circle"></i> كيفية الشحن</h5>
                    <ol class="mb-0">
                        <li>اختر طريقة الدفع المناسبة</li>
                        <li>أدخل المبلغ المراد شحنه</li>
                        <li>أرسل المبلغ عبر الطريقة المختارة</li>
                        <li>أدخل رقم الحوالة/المرجع</li>
                        <li>أرفق صورة الإيصال (اختياري)</li>
                        <li>انتظر حتى يتم مراجعة طلبك (خلال 24 ساعة)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ملاحظات الأدمن</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notesContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
function showNotes(notes) {
    document.getElementById('notesContent').textContent = notes;
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
