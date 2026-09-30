<?php
require_once __DIR__ . '/../public_html/config/config.php';
require_once __DIR__ . '/../public_html/config/database.php';
require_once __DIR__ . '/../public_html/models/Product.php';

$products = Product::getAll(['status' => 'published'], 20);

echo "========================================================\n";
echo "CONFIGURATOR FORM VISIBILITY TEST FOR ALL PRODUCTS\n";
echo "========================================================\n\n";

foreach ($products as $p) {
    $url = "http://localhost:8088/order/product/" . $p['slug'];
    $html = @file_get_contents($url);
    
    echo "PRODUCT #{$p['id']} [{$p['slug']}]:\n";
    if ($html === false) {
        echo "   HTTP FETCH FAILED!\n";
        continue;
    }
    
    $hasForm = strpos($html, 'id="b2bOrderForm"') !== false;
    $hasOrderName = strpos($html, 'name="order_name"') !== false;
    $hasQty = strpos($html, 'name="quantity"') !== false;
    $hasArtwork = strpos($html, 'name="artwork_option"') !== false;
    $hasPriceOrBanner = (strpos($html, 'lblFinalTotal') !== false || strpos($html, 'unpricedWarningBanner') !== false);
    
    echo "   Form Container: " . ($hasForm ? 'YES' : 'NO') . "\n";
    echo "   Order Name Field: " . ($hasOrderName ? 'YES' : 'NO') . "\n";
    echo "   Quantity Field: " . ($hasQty ? 'YES' : 'NO') . "\n";
    echo "   Artwork Field: " . ($hasArtwork ? 'YES' : 'NO') . "\n";
    echo "   Price/Quote Panel: " . ($hasPriceOrBanner ? 'YES' : 'NO') . "\n";
    
    if ($hasForm && $hasOrderName && $hasQty && $hasArtwork && $hasPriceOrBanner) {
        echo "   STATUS: FULLY VISIBLE & FUNCTIONAL!\n\n";
    } else {
        echo "   STATUS: INCOMPLETE OR BLANK!\n\n";
    }
}
