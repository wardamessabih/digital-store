<?php
use Core\Helper;
use Core\Wallet;
use Core\Auth;
use App\Payment\PaymentGateway;

$pageTitle = 'سلة التسوق';
require_once __DIR__ . '/../layouts/header.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

$paymentMethods = PaymentGateway::getSupportedMethods();
?>

<section class="container py-5">
    <h1 class="mb-4"><i class="bi bi-cart3"></i> سلة التسوق</h1>

    <?php if (empty($cart)): ?>
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-cart-x fs-1 text-muted"></i>
                <h3 class="mt-3">السلة فارغة</h3>
                <p class="text-muted">لم تقم بإضافة أي منتجات بعد</p>
                <a href="/products" class="btn btn-primary">
                    <i class="bi bi-bag"></i> تصفح المنتجات
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cart as $productId => $item): ?>
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-2">
                                    <?php if ($item['image']): ?>
                                        <img src="<?= htmlspecialchars($item['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                                    <?php if ($item['custom_field_required']): ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($item['custom_field_label']) ?>:
                                            <?= htmlspecialchars($item['custom_field'] ?? 'لم يُدخل') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2">
                                    <span class="price-tag"><?= Helper::formatPrice($item['price']) ?></span>
                                </div>
                                <div class="col-md-2">
                                    <span>الكمية: <?= $item['quantity'] ?></span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button onclick="removeFromCart(<?= $productId ?>)" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-4"><i class="bi bi-receipt"></i> ملخص الطلب</h4>

                        <div class="d-flex justify-content-between mb-2">
                            <span>عدد المنتجات:</span>
                            <span><?= count($cart) ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>الإجمالي:</span>
                            <span class="fw-bold fs-5 text-primary"><?= Helper::formatPrice($total) ?></span>
                        </div>

                        <hr>

                        <?php if (Auth::isLoggedIn()): ?>
                            <form id="checkoutForm">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">طريقة الدفع</label>
                                    
                                    <div class="payment-options">
                                        <?php foreach ($paymentMethods as $key => $method): ?>
                                            <label class="payment-option" data-method="<?= $key ?>">
                                                <input type="radio" name="payment" value="<?= $key ?>" <?= $key === 'wallet' ? 'checked' : '' ?>>
                                                <div class="payment-card">
                                                    <i class="bi <?= $method['icon'] ?>"></i>
                                                    <span><?= $method['name'] ?></span>
                                                    <?php if ($key === 'wallet'): ?>
                                                        <small class="text-muted">(<?= Helper::formatPrice(Wallet::getBalance(Auth::id())) ?>)</small>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Card Payment Form -->
                                <div id="visaPaymentForm" class="payment-form" style="display: none;">
                                    <div class="card p-3 mb-3 bg-light">
                                        <h6 class="mb-3"><i class="bi bi-credit-card"></i> بيانات البطاقة البنكية</h6>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small">رقم البطاقة</label>
                                            <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" id="cardNumber">
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label small">تاريخ الانتهاء</label>
                                                <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY" maxlength="5" id="cardExpiry">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">CVC</label>
                                                <input type="text" name="card_cvc" class="form-control" placeholder="123" maxlength="4" id="cardCvc">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small">اسم حامل البطاقة</label>
                                            <input type="text" name="card_holder" class="form-control" placeholder="الاسم كما يظهر على البطاقة">
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Payment Form -->
                                <div id="paypalPaymentForm" class="payment-form" style="display: none;">
                                    <div class="card p-3 mb-3 bg-light">
                                        <h6 class="mb-3"><i class="bi bi-paypal"></i> الدفع عبر PayPal</h6>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small">بريد PayPal</label>
                                            <input type="email" name="paypal_email" class="form-control" placeholder="example@paypal.com">
                                        </div>
                                        
                                        <div class="alert alert-info small">
                                            <i class="bi bi-info-circle"></i>
                                            سيتم إرسال طلب الدفع إلى بريدك PayPal
                                        </div>
                                    </div>
                                </div>

                                <!-- Brimoob Payment Form -->
                                <div id="brimoobPaymentForm" class="payment-form" style="display: none;">
                                    <div class="card p-3 mb-3 bg-light">
                                        <h6 class="mb-3"><i class="bi bi-phone"></i> الدفع عبر Brimoob</h6>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small">رقم هاتف Brimoob</label>
                                            <input type="tel" name="brimoob_phone" class="form-control" placeholder="07XXXXXXXX">
                                        </div>
                                        
                                        <div class="alert alert-info small">
                                            <i class="bi bi-info-circle"></i>
                                            سيتم إرسال رمز التأكيد إلى رقم هاتفك
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="bi bi-lock"></i> إتمام الطلب
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                يرجى <a href="/auth/login">تسجيل الدخول</a> لإتمام الطلب
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="/products" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-right"></i> متابعة التسوق
                    </a>
                </div>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-check text-success"></i>
                        دفع آمن 100%
                    </small>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<style>
.payment-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.payment-option {
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.payment-option .payment-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.payment-option input:checked + .payment-card {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
}

.payment-option .payment-card i {
    font-size: 1.5rem;
    color: #667eea;
}

.payment-option .payment-card span {
    font-weight: 600;
}

.payment-option:hover .payment-card {
    border-color: #667eea;
}

/* PayPal icon */
.bi-paypal::before {
    content: "P";
    font-family: Arial, sans-serif;
    font-weight: bold;
    font-style: normal;
    background: #003087;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 1.2rem;
}
</style>

<script>
document.querySelectorAll('input[name="payment"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.payment-form').forEach(form => {
            form.style.display = 'none';
        });
        
        const method = this.value;
        if (method === 'visa') {
            document.getElementById('visaPaymentForm').style.display = 'block';
        } else if (method === 'paypal') {
            document.getElementById('paypalPaymentForm').style.display = 'block';
        } else if (method === 'brimoob') {
            document.getElementById('brimoobPaymentForm').style.display = 'block';
        }
    });
});

// Card number formatting
document.getElementById('cardNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
    let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
    e.target.value = formatted;
});

// Expiry formatting
document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const formData = new FormData(this);
    formData.append('payment_method', paymentMethod);

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

    fetch('/direct-checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('تم إنشاء الطلب بنجاح!', 'success');
            setTimeout(() => {
                window.location.href = '/orders';
            }, 1500);
        } else {
            showToast(data.message || 'حدث خطأ', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-lock"></i> إتمام الطلب';
        }
    })
    .catch(err => {
        showToast('حدث خطأ، يرجى المحاولة لاحقاً', 'danger');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-lock"></i> إتمام الطلب';
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
