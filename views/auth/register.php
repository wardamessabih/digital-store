<?php
$pageTitle = 'إنشاء حساب';
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus fs-1 text-primary"></i>
                        <h2 class="mt-2">إنشاء حساب جديد</h2>
                        <p class="text-muted">انضم إلينا اليوم!</p>
                    </div>

                    <form action="/auth/register" method="POST" id="registerForm">
                        <div class="mb-4">
                            <label class="form-label">نوع الحساب</label>
                            <div class="d-flex gap-3">
                                <div class="form-check flex-fill p-3 border rounded text-center" style="cursor: pointer;" onclick="selectRole('user')">
                                    <input class="form-check-input" type="radio" name="role" id="roleUser" value="user" checked>
                                    <label class="form-check-label w-100" for="roleUser">
                                        <i class="bi bi-person fs-2 d-block mb-2"></i>
                                        <strong>مستخدم</strong>
                                        <p class="small text-muted mb-0">شراء المنتجات</p>
                                    </label>
                                </div>
                                <div class="form-check flex-fill p-3 border rounded text-center" style="cursor: pointer;" onclick="selectRole('admin')">
                                    <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin">
                                    <label class="form-check-label w-100" for="roleAdmin">
                                        <i class="bi bi-shield-check fs-2 d-block mb-2"></i>
                                        <strong>أدمن</strong>
                                        <p class="small text-muted mb-0">إدارة الموقع</p>
                                    </label>
                                </div>
                            </div>
                            <div id="adminCodeSection" class="mt-3" style="display: none;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">الاسم الكامل</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="محمد أحمد" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="6 أحرف على الأقل" required minlength="6">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', 'eye1')">
                                    <i class="bi bi-eye" id="eye1"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="أعد إدخال كلمة المرور" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', 'eye2')">
                                    <i class="bi bi-eye" id="eye2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    أوافق على <a href="#" class="text-decoration-none">الشروط والأحكام</a>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-person-plus"></i> إنشاء الحساب
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            لديك حساب بالفعل؟
                            <a href="/auth/login" class="text-decoration-none fw-bold">تسجيل الدخول</a>
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
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

function selectRole(role) {
    document.querySelector(`input[name="role"][value="${role}"]`).checked = true;
    const adminSection = document.getElementById('adminCodeSection');
    if (role === 'admin') {
        adminSection.innerHTML = `
            <div class="alert alert-warning">
                <div class="mb-2">
                    <label class="form-label"><i class="bi bi-key"></i> كود الأدمن</label>
                    <input type="text" name="admin_code" class="form-control" placeholder="أدخل كود الأدمن">
                </div>
                <small class="text-muted">أدخل كود خاص للتسجيل كأدمن (سيعطيه لك المسؤول)</small>
            </div>
        `;
        adminSection.style.display = 'block';
    } else {
        adminSection.innerHTML = '';
        adminSection.style.display = 'none';
    }
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const role = document.querySelector('input[name="role"]:checked').value;
    const adminCode = document.querySelector('input[name="admin_code"]')?.value;

    if (password !== confirm) {
        e.preventDefault();
        alert('كلمات المرور غير متطابقة!');
        return;
    }

    if (role === 'admin' && (!adminCode || adminCode.trim() === '')) {
        e.preventDefault();
        alert('يرجى إدخال كود الأدمن!');
        return;
    }
});
</script>

<style>
.form-check-input:checked + .form-check-label {
    border-color: var(--primary-color);
    background-color: rgba(99, 102, 241, 0.1);
}
.form-check-input:checked + label i {
    color: var(--primary-color);
}
</style>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
