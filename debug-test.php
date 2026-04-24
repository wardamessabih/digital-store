<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
echo "=== TEST START ===\n\n";

// 1. Test Config
echo "1. Loading Config...\n";
require 'core/Config.php';
use Core\Config;
Config::load('config.json');
echo "Config OK\n";
echo "DB Host: " . Config::get('database.host') . "\n";
echo "DB Name: " . Config::get('database.name') . "\n\n";

// 2. Test Database
echo "2. Testing Database...\n";
require 'core/Database.php';
use Core\Database;
try {
    $db = Database::getInstance();
    echo "Database connected OK\n\n";
} catch(Exception $e) {
    echo "Database ERROR: " . $e->getMessage() . "\n\n";
    exit;
}

// 3. Check tables
echo "3. Checking tables...\n";
$tables = $db->fetchAll("SHOW TABLES");
echo "Tables found: " . count($tables) . "\n";
foreach ($tables as $t) {
    $tableName = array_values($t)[0];
    $count = $db->fetch("SELECT COUNT(*) as c FROM $tableName");
    echo "  - $tableName: " . $count['c'] . " rows\n";
}

echo "\n4. Testing Product::find(1)...\n";
require 'app/models/Product.php';
use App\Models\Product;
$product = Product::find(1);
if ($product) {
    echo "SUCCESS: Product found - " . $product['name'] . "\n";
} else {
    echo "Product NOT found with id=1\n";
    
    $all = Product::all();
    echo "Total products: " . count($all) . "\n";
    
    if (count($all) > 0) {
        echo "First product: " . $all[0]['name'] . " (id=" . $all[0]['id'] . ")\n";
    }
}

echo "\n=== TEST END ===";
echo "</pre>";
