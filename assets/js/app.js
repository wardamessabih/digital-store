/**
 * Digital Store Platform - Main JavaScript
 */

const StoreApp = {
    cart: {
        items: JSON.parse(localStorage.getItem('cart') || '[]'),

        add(productId, quantity = 1, customField = null) {
            const product = this.items.find(item => item.productId === productId);

            if (product) {
                product.quantity += quantity;
            } else {
                this.items.push({ productId, quantity, customField });
            }

            this.save();
            this.updateBadge();
            this.showNotification('تمت الإضافة للسلة', 'success');
        },

        remove(productId) {
            this.items = this.items.filter(item => item.productId !== productId);
            this.save();
            this.updateBadge();
        },

        clear() {
            this.items = [];
            this.save();
            this.updateBadge();
        },

        getItems() {
            return this.items;
        },

        getCount() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },

        save() {
            localStorage.setItem('cart', JSON.stringify(this.items));
        },

        updateBadge() {
            const badge = document.querySelector('.cart-count');
            if (badge) {
                badge.textContent = this.getCount();
            }
        },

        showNotification(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed`;
            toast.style.cssText = 'bottom: 20px; left: 20px; z-index: 9999;';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
    },

    notifications: {
        lastId: 0,
        sseConnection: null,

        connect() {
            if (typeof EventSource !== 'undefined') {
                this.sseConnection = new EventSource('/api/notifications-sse?last_id=' + this.lastId);

                this.sseConnection.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    if (data.notifications && data.notifications.length > 0) {
                        this.handleNewNotifications(data.notifications);
                        this.updateBadge(data.unread_count);
                    }
                };

                this.sseConnection.onerror = () => {
                    setTimeout(() => this.connect(), 30000);
                };
            }
        },

        disconnect() {
            if (this.sseConnection) {
                this.sseConnection.close();
            }
        },

        handleNewNotifications(notifications) {
            notifications.forEach(notif => {
                if (notif.id > this.lastId) {
                    this.lastId = notif.id;
                    StoreApp.cart.showNotification(notif.title + ': ' + notif.message, 'info');
                }
            });
        },

        updateBadge(count) {
            const badge = document.querySelector('.notification-badge');
            if (badge && count !== undefined) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'block' : 'none';
            }
        },

        async markAsRead(id) {
            try {
                await fetch('/api/user/notifications/read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'notification_id=' + id
                });
            } catch (e) {
                console.error('Error marking notification as read');
            }
        }
    },

    init() {
        this.cart.updateBadge();

        if (document.querySelector('.notification-badge')) {
            this.notifications.connect();
        }

        this.initForms();
        this.initLazyLoad();
    },

    initForms() {
        document.querySelectorAll('form[data-ajax]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const url = form.action || window.location.href;
                const method = form.method || 'POST';

                try {
                    const response = await fetch(url, {
                        method,
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.cart.showNotification(data.message || 'تمت العملية بنجاح', 'success');
                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 1000);
                        }
                    } else {
                        this.cart.showNotification(data.message || 'حدث خطأ', 'danger');
                    }
                } catch (e) {
                    this.cart.showNotification('حدث خطأ في الاتصال', 'danger');
                }
            });
        });
    },

    initLazyLoad() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => observer.observe(img));
        }
    }
};

document.addEventListener('DOMContentLoaded', () => StoreApp.init());
