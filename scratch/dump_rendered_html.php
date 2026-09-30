<?php
$url = "http://localhost:8088/order/product/gold-foil-business-cards";
$html = file_get_contents($url);
echo "LENGTH: " . strlen($html) . "\n";
echo "FIRST 500 CHARS:\n" . substr($html, 0, 500) . "\n";
echo "LAST 500 CHARS:\n" . substr($html, -500) . "\n";

// Search for form elements
if (strpos($html, 'b2bOrderForm') !== false) {
    echo "b2bOrderForm FOUND AT INDEX " . strpos($html, 'b2bOrderForm') . "\n";
    $pos = strpos($html, 'b2bOrderForm');
    echo "SNIPPET:\n" . substr($html, $pos, 800) . "\n";
} else {
    echo "b2bOrderForm NOT FOUND!\n";
}
