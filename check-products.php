<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'core/Config.php';
require 'core/Database.php';

use Core\Config;
use Core\Database;

Config::load('config.json');
$db = Database::getInstance();

echo "Products list:\n";
$products = $db->fetchAll('SELECT id, name FROM products ORDER BY id');
foreach ($products as $p) {
    echo $p['id'] . ': ' . $p['name'] . "\n";
}

echo "\n\nTrying Product::find(22):\n";
require 'app/models/Product.php';
$product = \App\Models\Product::find(22);
if ($product) {
    echo "Found: " . $product['name'] . "\n";
} else {
    echo "Not found\n";
}
