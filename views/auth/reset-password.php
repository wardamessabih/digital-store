<?php
$pageTitle = 'إعادة تعيين كلمة المرور';
require_once __DIR__ . '/../../views/layouts/header.php';

$token = $_GET['token'] ?? '';
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-key fs-1 text-primary"></i>
                        <h2 class="mt-2">إعادة تعيين كلمة المرور</h2>
                        <p class="text-muted">أدخل كلمة المرور الجديدة</p>
                    </div>

                    <form id="resetForm">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
                        <div class="mb-4">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', 'eye1')">
                                    <i class="bi bi-eye" id="eye1"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', 'eye2')">
                                    <i class="bi bi-eye" id="eye2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check2-circle"></i> تغيير كلمة المرور
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
function togglePassword(inputId, iconId) {
    const password = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

document.getElementById('resetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    
    if (formData.get('password') !== formData.get('password_confirmation')) {
        showToast('كلمات المرور غير متطابقة', 'danger');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';
    
    fetch('/auth/reset-password', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('تم تغيير كلمة المرور بنجاح!', 'success');
            setTimeout(() => {
                window.location.href = '/auth/login';
            }, 1500);
        } else {
            showToast(data.message, 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> تغيير كلمة المرور';
        }
    })
    .catch(err => {
        showToast('حدث خطأ، يرجى المحاولة لاحقاً', 'danger');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> تغيير كلمة المرور';
    });
});
</script>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>