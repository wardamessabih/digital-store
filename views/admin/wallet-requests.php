<?php
$pageTitle = 'طلبات شحن المحفظة';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-wallet2"></i> طلبات شحن المحفظة</h2>
</div>

<?php if (!empty($pendingRequests)): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
        <div>
            <strong>تنبيه:</strong> يوجد <?= count($pendingRequests) ?> طلبات معلقة تحتاج مراجعة
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-hourglass"></i> طلبات معلقة (<?= count($pendingRequests) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pendingRequests)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <p class="mt-2 mb-0">لا توجد طلبات معلقة</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المستخدم</th>
                                    <th>المبلغ</th>
                                    <th>الطريقة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingRequests as $req): ?>
                                    <tr>
                                        <td><?= $req['id'] ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($req['user_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($req['user_email']) ?></small>
                                        </td>
                                        <td class="fw-bold text-success"><?= \Core\Helper::formatPrice($req['amount']) ?></td>
                                        <td><?= htmlspecialchars($req['payment_method']) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <form action="/admin/wallet-requests/approve/<?= $req['id'] ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="btn btn-success" title="موافقة">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                                <button class="btn btn-danger" onclick="showRejectModal(<?= $req['id'] ?>)" title="رفض">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                                <?php if ($req['invoice_image_url']): ?>
                                                    <a href="<?= htmlspecialchars($req['invoice_image_url']) ?>" target="_blank" class="btn btn-outline-primary">
                                                        <i class="bi bi-image"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> كل الطلبات</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المستخدم</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allRequests)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">لا توجد طلبات</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allRequests as $req): ?>
                                    <tr>
                                        <td><?= $req['id'] ?></td>
                                        <td><?= htmlspecialchars($req['user_name']) ?></td>
                                        <td class="fw-bold"><?= \Core\Helper::formatPrice($req['amount']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= \Core\Helper::getStatusColor($req['status']) ?>">
                                                <?= \Core\Helper::getStatusText($req['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('Y-m-d', strtotime($req['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="rejectForm">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger"></i> رفض الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض (اختياري)</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="أدخل سبب الرفض..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(requestId) {
    document.getElementById('rejectForm').action = '/admin/wallet-requests/reject/' + requestId;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
