<?php
require_once __DIR__ . '/../public_html/config/config.php';
require_once __DIR__ . '/../public_html/config/database.php';
require_once __DIR__ . '/../public_html/models/Product.php';
require_once __DIR__ . '/../public_html/models/PricingEngine.php';

$slug = 'high-impact-flyers';
$product = Product::getBySlug($slug);

echo "--- PRODUCT TABLE ROW ---\n";
print_r($product);

if ($product) {
    $pid = (int)$product['id'];
    
    echo "\n--- PRODUCT OPTIONS (PricingEngine::getProductOptions($pid)) ---\n";
    $options = PricingEngine::getProductOptions($pid);
    print_r($options);
    
    echo "\n--- PRODUCT PRICING SLABS (PricingEngine::getQuantitySlabs($pid)) ---\n";
    $slabs = PricingEngine::getQuantitySlabs($pid);
    print_r($slabs);
    
    echo "\n--- PRICING CALCULATION RESULT ---\n";
    $calc = PricingEngine::calculatePrice($pid, (int)($product['min_quantity'] ?? 100), [], null);
    print_r($calc);
}
