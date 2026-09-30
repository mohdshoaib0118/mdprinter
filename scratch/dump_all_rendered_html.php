<?php
$urls = [
    'http://localhost:8088/order/product/gold-foil-business-cards',
    'http://localhost:8088/order/product/corporate-trifold-brochure',
    'http://localhost:8088/order/product/custom-rigid-magnetic-box',
    'http://localhost:8088/order/product/high-impact-flyers',
    'http://localhost:8088/order/product/presentation-folders'
];

foreach ($urls as $u) {
    echo "=========================================\n";
    echo "TESTING URL: $u\n";
    $html = file_get_contents($u);
    if (strpos($html, 'Fatal error') !== false || strpos($html, 'Parse error') !== false) {
        echo "ERROR DETECTED IN RENDERED HTML!\n";
    } else {
        echo "SUCCESS: 0 PHP ERRORS DETECTED!\n";
        echo "HTML Length: " . strlen($html) . " bytes\n";
    }
}
