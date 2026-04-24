# Digital Store Platform - متجر رقمي متكامل

منصة متجر رقمي متكاملة لبيع الأكواد والاشتراكات والخدمات الرقمية.

## المميزات

- 🛒 إدارة المنتجات (أكواد، اشتراكات، رصيد، أرقام ألعاب)
- 👥 نظام المستخدمين (تسجيل، تسجيل دخول، أدوار)
- 💰 المحفظة الداخلية
- 💳 طرق دفع متعددة (المحفظة، Visa، Crypto)
- 📦 إدارة الطلبات
- 🔔 نظام الإشعارات
- 📊 لوحة تحكم متكاملة للأدمن
- 📱 تصميم متجاوب (Responsive)
- 🌐 دعم اللغة العربية (RTL)

## المتطلبات

- PHP 8.0 أو أعلى
- MySQL 5.7 أو أعلى
- Apache/Nginx
- Composer (لإدارة الحزم)

## التثبيت

### 1. استنساخ المشروع

```bash
git clone <repo-url> digital-store
cd digital-store
```

### 2. إنشاء قاعدة البيانات

```sql
CREATE DATABASE digital_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

ثم استورد ملف `database/schema.sql`:

```bash
mysql -u root -p digital_store < database/schema.sql
```

### 3. تخصيص الإعدادات

عدّل ملف `config.json`:

```json
{
    "database": {
        "host": "localhost",
        "name": "digital_store",
        "user": "root",
        "pass": "your_password"
    },
    "app": {
        "name": "المتجر الرقمي",
        "url": "http://localhost"
    },
    "security": {
        "jwt_secret": "your-secret-key-here"
    }
}
```

### 4. تشغيل البيانات التجريبية (اختياري)

```bash
php database/Seeder.php
```

### 5. تشغيل الخادم

```bash
php -S localhost:8000
```

أو استخدم XAMPP/WAMP وقم بتوجيه المجلد لمجلد المشروع.

## بيانات الدخول

### الأدمن
- البريد: `admin@example.com`
- كلمة المرور: `admin123`

### المستخدم
- البريد: `user@example.com`
- كلمة المرور: `user123`

## هيكل المشروع

```
digital-store/
├── core/                  # الملفات الأساسية
│   ├── Auth.php          # نظام المصادقة
│   ├── Database.php       # قاعدة البيانات
│   ├── Helper.php         # دوال مساعدة
│   ├── Notification.php   # الإشعارات
│   ├── Router.php         # التوجيه
│   ├── Session.php        # الجلسات
│   ├── Wallet.php         # المحفظة
│   └── config.php         # الإعدادات
├── app/
│   └── models/           # نماذج البيانات
│       ├── Product.php
│       ├── Order.php
│       └── Category.php
├── routes/               # التوجيه
│   ├── web.php
│   ├── api.php
│   └── admin.php
├── views/                # الواجهات
│   ├── layouts/
│   ├── pages/
│   ├── auth/
│   ├── admin/
│   └── errors/
├── database/             # قاعدة البيانات
│   ├── schema.sql
│   └── Seeder.php
├── uploads/             # الملفات المرفوعة
├── index.php            # نقطة الدخول
├── config.json          # الإعدادات
└── README.md
```

## API Endpoints

### المنتجات
- `GET /api/products` - جلب جميع المنتجات
- `GET /api/product/{id}` - جلب منتج واحد
- `GET /api/products?type=code` - تصفية حسب النوع

### السلة
- `POST /api/cart/add` - إضافة للسلة
- `POST /api/cart/remove` - حذف من السلة
- `GET /api/cart` - جلب السلة

### الطلبات
- `POST /api/order/create` - إنشاء طلب
- `POST /api/order/cancel/{id}` - إلغاء طلب

### المستخدم
- `GET /api/user/notifications` - الإشعارات
- `GET /api/user/wallet` - رصيد المحفظة

## طرق الدفع

### 1. المحفظة الداخلية
- يتم خصم المبلغ من رصيد المستخدم
- يتطلب شحن المحفظة أولاً

### 2. Visa/MasterCard
- يتم استخدام Stripe (يتطلب مفاتيح API)

### 3. Crypto
- يتم استخدام Binance Pay (يتطلب API Key)

## لوحة التحكم

تتضمن:
- 📊 Dashboard مع إحصائيات
- 📦 إدارة المنتجات
- 🛒 إدارة الطلبات
- 💰 إدارة شحن المحفظة
- 👥 إدارة المستخدمين
- ⚙️ الإعدادات

## الأمان

- تشفير كلمات المرور باستخدام bcrypt
- حماية من SQL Injection عبر PDO Prepared Statements
- التحقق من الصلاحيات
- تنظيف المدخلات

## التخصيص

### إضافة طريقة دفع جديدة

1. أضف الحقل في جدول `orders`
2. أنشئ صفحة الدفع في `views/payment/`
3. أضف الـ endpoint في `routes/api.php`

### إضافة نوع منتج جديد

1. أضف النوع في جدول `products`
2. حدّث الواجهة حسب الحاجة

## المساهمة

نرحب بالمساهمات! يرجى فتح Issue أو Pull Request.

## الترخيص

MIT License

---

تم التطوير بـ ❤️ باستخدام PHP + Bootstrap
