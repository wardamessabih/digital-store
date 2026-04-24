<?php
$pageTitle = 'نسيت كلمة المرور';
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-key fs-1 text-primary"></i>
                        <h2 class="mt-2">نسيت كلمة المرور؟</h2>
                        <p class="text-muted">أدخل بريدك الإلكتروني لإعادة تعيين كلمة المرور</p>
                    </div>

                    <form action="/auth/forgot-password" method="POST" id="forgotForm">
                        <div class="mb-4">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="example@email.com" required autofocus>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-send"></i> إرسال رابط الاستعادة
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/auth/login" class="text-decoration-none">
                            <i class="bi bi-arrow-right"></i> العودة لتسجيل الدخول
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('forgotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري الإرسال...';
    
    fetch('/auth/forgot-password', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            submitBtn.innerHTML = '<i class="bi bi-check"></i> تم الإرسال';
        } else {
            showToast(data.message, 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send"></i> إرسال رابط الاستعادة';
        }
    })
    .catch(err => {
        showToast('حدث خطأ، يرجى المحاولة لاحقاً', 'danger');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send"></i> إرسال رابط الاستعادة';
    });
});
</script>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>