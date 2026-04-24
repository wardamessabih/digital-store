<?php use Core\Config; ?>
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3"><i class="bi bi-shop me-2"></i> <?= Config::get('app.name', 'المتجر الرقمي') ?></h5>
                    <p class="text-white-50 mb-4">متجرك الرقمي الأول لشراء الأكواد والاشتراكات والخدمات الرقمية بأفضل الأسعار.</p>
                    <div class="social-links">
                        <a href="#" class="text-white"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-discord"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-telegram"></i></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-3">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-white-50">الرئيسية</a></li>
                        <li class="mb-2"><a href="/products" class="text-white-50">المنتجات</a></li>
                        <li class="mb-2"><a href="/orders" class="text-white-50">مشترياتي</a></li>
                        <li class="mb-2"><a href="/wallet" class="text-white-50">المحفظة</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-3">خدمة العملاء</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50">اتصل بنا</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">الأسئلة الشائعة</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">سياسة الاسترجاع</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">الشروط والأحكام</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white-50 my-4">
            <div class="text-center">
                <p class="mb-0 text-white-50">&copy; <?= date('Y') ?> <?= Config::get('app.name', 'المتجر الرقمي') ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/app.js"></script>

    <script>
        function updateCartBadge() {
            fetch('/direct-cart.php?action=count')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('cartBadge');
                    if (badge) badge.textContent = data.cart_count || 0;
                })
                .catch(err => console.log('Cart update error'));
        }

        function addToCart(productId, quantity = 1) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('action', 'add');

            fetch('/direct-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge();
                    showToast('تمت الإضافة للسلة بنجاح!', 'success');
                } else {
                    showToast(data.message || 'حدث خطأ', 'danger');
                }
            })
            .catch(err => {
                console.error('Cart error:', err);
                showToast('حدث خطأ', 'danger');
            });
        }

        function removeFromCart(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('action', 'remove');
            fetch('/direct-cart.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge();
                    location.reload();
                }
            });
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed`;
            toast.style.cssText = 'bottom: 20px; left: 20px; z-index: 9999;';
            toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }

        document.addEventListener('DOMContentLoaded', updateCartBadge);
    </script>
</body>
</html>
