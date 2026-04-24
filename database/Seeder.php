<?php

require_once dirname(__DIR__) . '/core/Config.php';
require_once dirname(__DIR__) . '/core/Database.php';
require_once dirname(__DIR__) . '/core/Helper.php';

use Core\Database;
use Core\Helper;

class Seeder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function run() {
        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedSettings();

        echo "تم إنشاء البيانات التجريبية بنجاح!\n";
    }

    private function seedUsers() {
        $this->db->query('DELETE FROM users');

        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $userPassword = password_hash('user123', PASSWORD_BCRYPT);

        $this->db->insert('users', [
            'name' => 'مدير النظام',
            'email' => 'admin@example.com',
            'password' => $adminPassword,
            'role' => 'admin',
            'wallet_balance' => 1000.00
        ]);

        $this->db->insert('users', [
            'name' => 'مستخدم تجريبي',
            'email' => 'user@example.com',
            'password' => $userPassword,
            'role' => 'user',
            'wallet_balance' => 500.00
        ]);

        echo "- تم إنشاء المستخدمين\n";
    }

    private function seedCategories() {
        $this->db->query('DELETE FROM categories');

        $categories = [
            ['name' => 'أكواد Steam', 'description' => 'أكواد تفعيل لألعاب Steam'],
            ['name' => 'بطاقات Google Play', 'description' => 'بطاقات هدايا Google Play'],
            ['name' => 'اشتراكات نتفلكس', 'description' => 'اشتراكات نتفلكس بمختلف الخطط'],
            ['name' => 'VPN', 'description' => 'خدمات VPN'],
            ['name' => 'أرقام ألعاب', 'description' => 'أرقام تعريف للألعاب'],
            ['name' => 'شحن رصيد', 'description' => 'شحن رصيد الهاتف']
        ];

        foreach ($categories as $cat) {
            $slug = Helper::slugify($cat['name']);
            $this->db->insert('categories', [
                'name' => $cat['name'],
                'slug' => $slug,
                'description' => $cat['description'],
                'is_active' => 1
            ]);
        }

        echo "- تم إنشاء التصنيفات\n";
    }

    private function seedProducts() {
        $this->db->query('DELETE FROM products');

        $products = [
            [
                'category_id' => 1,
                'name' => 'كود تفعيل Steam - 50$',
                'description' => 'كود تفعيل Steam بقيمة 50 دولار. يعمل على جميع الحسابات.',
                'price' => 180.00,
                'type' => 'code',
                'image_url' => 'https://images.unsplash.com/photo-1612287230202-1ff1d7d410e6?w=400',
                'delivery_data' => '{"code": "%code%"}',
                'requires_custom_field' => 0,
                'is_active' => 1
            ],
            [
                'category_id' => 1,
                'name' => 'كود تفعيل Steam - 100$',
                'description' => 'كود تفعيل Steam بقيمة 100 دولار.',
                'price' => 350.00,
                'type' => 'code',
                'image_url' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=400',
                'delivery_data' => '{"code": "%code%"}',
                'requires_custom_field' => 0,
                'is_active' => 1
            ],
            [
                'category_id' => 2,
                'name' => 'بطاقة Google Play - 10$',
                'description' => 'بطاقة هدايا Google Play بقيمة 10 دولارات.',
                'price' => 38.00,
                'type' => 'code',
                'image_url' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400',
                'delivery_data' => '{"code": "%code%"}',
                'requires_custom_field' => 0,
                'is_active' => 1
            ],
            [
                'category_id' => 2,
                'name' => 'بطاقة Google Play - 25$',
                'description' => 'بطاقة هدايا Google Play بقيمة 25 دولار.',
                'price' => 90.00,
                'type' => 'code',
                'image_url' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400',
                'delivery_data' => '{"code": "%code%"}',
                'requires_custom_field' => 0,
                'is_active' => 1
            ],
            [
                'category_id' => 3,
                'name' => 'اشتراك Netflix شهر - أساسية',
                'description' => 'اشتراك Netflix شهر واحد - الخطة الأساسية (HD).',
                'price' => 45.00,
                'type' => 'subscription',
                'image_url' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=400',
                'delivery_data' => '{"email": "%email%", "password": "%password%"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'بريدك الإلكتروني',
                'is_active' => 1
            ],
            [
                'category_id' => 3,
                'name' => 'اشتراك Netflix شهر - قياسية',
                'description' => 'اشتراك Netflix شهر واحد - الخطة القياسية (Full HD).',
                'price' => 75.00,
                'type' => 'subscription',
                'image_url' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=400',
                'delivery_data' => '{"email": "%email%", "password": "%password%"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'بريدك الإلكتروني',
                'is_active' => 1
            ],
            [
                'category_id' => 4,
                'name' => 'NordVPN - سنة',
                'description' => 'اشتراك NordVPN لمدة سنة كاملة.',
                'price' => 120.00,
                'type' => 'subscription',
                'image_url' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400',
                'delivery_data' => '{"username": "%username%", "password": "%password%"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'اسم المستخدم',
                'is_active' => 1
            ],
            [
                'category_id' => 5,
                'name' => 'شحن PUBG Mobile - 60 UC',
                'description' => 'شحن 60 UC لحساب PUBG Mobile الخاص بك.',
                'price' => 8.00,
                'type' => 'game_id',
                'image_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400',
                'delivery_data' => '{"uc": "60"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'معرف اللاعب (Player ID)',
                'is_active' => 1
            ],
            [
                'category_id' => 5,
                'name' => 'شحن PUBG Mobile - 600 UC',
                'description' => 'شحن 600 UC + 60 UC مجاناً.',
                'price' => 70.00,
                'type' => 'game_id',
                'image_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400',
                'delivery_data' => '{"uc": "660"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'معرف اللاعب (Player ID)',
                'is_active' => 1
            ],
            [
                'category_id' => 5,
                'name' => 'شحن Free Fire - 100 نقطة',
                'description' => 'شحن 100 نقطة لحساب Free Fire.',
                'price' => 5.00,
                'type' => 'game_id',
                'image_url' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400',
                'delivery_data' => '{"points": "100"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'معرف اللاعب (UID)',
                'is_active' => 1
            ],
            [
                'category_id' => 6,
                'name' => 'شحن رصيد زين - 5$',
                'description' => 'شحن رصيد زين عراقي بقيمة 5 دولار.',
                'price' => 6.00,
                'type' => 'credit',
                'image_url' => 'https://images.unsplash.com/photo-1596558450268-9c27524ba856?w=400',
                'delivery_data' => '{"amount": "5$"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'رقم الهاتف',
                'is_active' => 1
            ],
            [
                'category_id' => 6,
                'name' => 'شحن رصيد زين - 10$',
                'description' => 'شحن رصيد زين عراقي بقيمة 10 دولار.',
                'price' => 11.00,
                'type' => 'credit',
                'image_url' => 'https://images.unsplash.com/photo-1596558450268-9c27524ba856?w=400',
                'delivery_data' => '{"amount": "10$"}',
                'requires_custom_field' => 1,
                'custom_field_label' => 'رقم الهاتف',
                'is_active' => 1
            ]
        ];

        foreach ($products as $product) {
            $this->db->insert('products', $product);
        }

        echo "- تم إنشاء المنتجات\n";
    }

    private function seedSettings() {
        $this->db->query('DELETE FROM settings');

        $settings = [
            ['setting_key' => 'app_name', 'setting_value' => 'المتجر الرقمي'],
            ['setting_key' => 'currency', 'setting_value' => 'SAR'],
            ['setting_key' => 'contact_email', 'setting_value' => 'support@example.com'],
            ['setting_key' => 'contact_phone', 'setting_value' => '+966123456789']
        ];

        foreach ($settings as $setting) {
            $this->db->insert('settings', $setting);
        }

        echo "- تم إنشاء الإعدادات\n";
    }
}

if (php_sapi_name() === 'cli' || isset($_GET['run'])) {
    require_once __DIR__ . '/../core/Database.php';
    require_once __DIR__ . '/../core/Helper.php';

    $seeder = new Seeder();
    $seeder->run();
}
