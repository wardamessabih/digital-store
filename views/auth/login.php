<?php
$pageTitle = 'تسجيل الدخول';
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle fs-1 text-primary"></i>
                        <h2 class="mt-2">تسجيل الدخول</h2>
                        <p class="text-muted">مرحباً بك مجدداً!</p>
                    </div>

                    <form action="/auth/login" method="POST">
                        <div class="mb-4">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="example@email.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">تذكرني</label>
                            </div>
                            <a href="/auth/forgot-password" class="text-decoration-none">نسيت كلمة المرور؟</a>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> تسجيل الدخول
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            ليس لديك حساب؟
                            <a href="/auth/register" class="text-decoration-none fw-bold">إنشاء حساب جديد</a>
                        </p>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="/" class="text-decoration-none">
                            <i class="bi bi-house"></i> العودة للرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');

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
</script>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
