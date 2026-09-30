<?php
require_once __DIR__ . '/../public_html/config/config.php';
require_once __DIR__ . '/../public_html/config/database.php';
require_once __DIR__ . '/../public_html/models/Product.php';
require_once __DIR__ . '/../public_html/models/PricingEngine.php';

$db = Database::getInstance();
$products = $db->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $p) {
    echo "=========================================\n";
    echo "PRODUCT ID {$p['id']}: {$p['name']} ({$p['slug']})\n";
    echo "paper_options_json: {$p['paper_options_json']}\n";
    echo "finishing_options_json: {$p['finishing_options_json']}\n";
    
    $opts = PricingEngine::getProductOptions((int)$p['id']);
    echo "product_options count: " . count($opts) . "\n";
    foreach ($opts as $o) {
        echo "  - Option: {$o['option_name']} (Values: " . count($o['values']) . ")\n";
    }
    
    $slabs = PricingEngine::getQuantitySlabs((int)$p['id']);
    echo "pricing_slabs count: " . count($slabs) . "\n";
    foreach ($slabs as $s) {
        echo "  - Slab: Qty {$s['quantity']} => ₹{$s['base_price']}\n";
    }
}
