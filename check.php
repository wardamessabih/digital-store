<?php
require_once __DIR__ . '/core/Database.php';

$db = \Core\Database::getInstance();
$products = $db->fetchAll("SELECT id, name, image_url FROM products");

echo "<h2>المنتجات:</h2>";
foreach ($products as $p) {
    echo "<p><strong>{$p['name']}</strong><br>";
    echo "image_url: <code>" . ($p['image_url'] ?: '<span style=\"color:red\">فارغ</span>') . "</code></p>";
}

echo "<h2>ملفات الرفع:</h2>";
$uploads = glob(__DIR__ . '/uploads/*');
foreach ($uploads as $f) {
    echo basename($f) . "<br>";
}
?>
