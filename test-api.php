<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== API TEST ===\n";
echo "Session ID: " . session_id() . "\n";
echo "URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n\n";

echo "POST data:\n";
print_r($_POST);

echo "\n\n=== Testing Config ===\n";
require 'core/Config.php';
\Config::load('config.json');
echo "Config loaded\n";

echo "\n=== Testing Database ===\n";
require 'core/Database.php';
try {
    $db = \Core\Database::getInstance();
    echo "Database connected\n";
} catch(Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Product Model ===\n";
require 'app/models/Product.php';
$product = \App\Models\Product::find(1);
if ($product) {
    echo "Product found: " . $product['name'] . "\n";
} else {
    echo "Product NOT found\n";
}

echo "\n=== Adding to Cart ===\n";
$_SESSION['cart'] = [];
$_SESSION['cart'][1] = [
    'product_id' => 1,
    'quantity' => 1,
    'name' => 'Test Product'
];
echo "Cart count: " . count($_SESSION['cart']) . "\n";
