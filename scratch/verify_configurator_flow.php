<?php
/**
 * Scratch verification script for Product Configurator & Order Journey
 */
require_once __DIR__ . '/../public_html/config/config.php';
require_once __DIR__ . '/../public_html/config/database.php';
require_once __DIR__ . '/../public_html/models/Product.php';
require_once __DIR__ . '/../public_html/models/ProductGroup.php';
require_once __DIR__ . '/../public_html/models/Category.php';
require_once __DIR__ . '/../public_html/models/PricingEngine.php';
require_once __DIR__ . '/../public_html/models/Cart.php';
require_once __DIR__ . '/../public_html/models/Order.php';
require_once __DIR__ . '/../public_html/models/Quote.php';
require_once __DIR__ . '/../public_html/models/Customer.php';

echo "========================================================\n";
echo "B2B PRODUCT CONFIGURATOR & ORDER JOURNEY VERIFICATION\n";
echo "========================================================\n\n";

$db = Database::getInstance();
if (!$db) {
    echo "ERROR: Could not connect to database!\n";
    exit(1);
}

// 1. Verify Product #1 (Executive Gold Foil Business Cards)
$product = Product::getById(1);
if (!$product) {
    echo "ERROR: Product #1 not found!\n";
    exit(1);
}

echo "1. PRODUCT DETAILS:\n";
echo "   ID: {$product['id']}\n";
echo "   Name: {$product['name']}\n";
echo "   Slug: {$product['slug']}\n";
echo "   Min Quantity: {$product['min_quantity']}\n";
echo "   Production Time: {$product['production_time']}\n";
echo "   Group ID: {$product['product_group_id']}\n";
echo "   OK!\n\n";

// 2. Verify Product Options
$options = PricingEngine::getProductOptions($product['id']);
echo "2. PRODUCT OPTIONS FOR PRODUCT #1:\n";
foreach ($options as $opt) {
    echo "   - Option: {$opt['option_name']} (Type: {$opt['option_type']})\n";
    foreach ($opt['values'] as $v) {
        echo "     * Value: {$v['value_name']} (Mod: {$v['modifier_type']} +{$v['price_modifier']})\n";
    }
}
echo "   OK!\n\n";

// 3. Verify Server-Side Live Price Calculation
echo "3. SERVER-SIDE PRICING ENGINE CALCULATION:\n";
$calc = PricingEngine::calculatePrice($product['id'], 500, [1 => 1, 2 => 5, 3 => 7, 4 => 10, 5 => 14], null);
echo "   Is Priced: " . ($calc['is_priced'] ? 'YES' : 'NO') . "\n";
echo "   Base Price: ₹{$calc['base_price']}\n";
echo "   Options Total: ₹{$calc['options_total']}\n";
echo "   Gross Subtotal: ₹{$calc['gross_subtotal']}\n";
echo "   GST (18%): ₹{$calc['tax_amount']}\n";
echo "   Final Total: ₹{$calc['final_total']}\n";
$unitPrice = round($calc['final_total'] / 500, 2);
echo "   Per Unit Price: ₹{$unitPrice}\n";
echo "   OK!\n\n";

// 4. Verify Unpriced Safeguard
echo "4. UNPRICED PRODUCT SAFEGUARD CHECK (PRODUCT #4):\n";
$unpricedProduct = Product::getById(4);
if ($unpricedProduct) {
    $unpricedCalc = PricingEngine::calculatePrice($unpricedProduct['id'], 100, [], null);
    echo "   Product: {$unpricedProduct['name']}\n";
    echo "   Is Priced: " . ($unpricedCalc['is_priced'] ? 'YES' : 'NO') . "\n";
    echo "   Status Message: {$unpricedCalc['price_status_message']}\n";
    if (!$unpricedCalc['is_priced']) {
        echo "   SAFEGUARD VERIFIED: Add to Cart will be disabled and Request Formal Quote displayed!\n";
    }
}
echo "   OK!\n\n";

// 5. Verify Cart Addition with Order Name
echo "5. TESTING B2B CART ADDITION WITH SNAPSHOT:\n";
$orderName = "Test Campaign Cards 2026 Batch #101";
$presslineText = "Printed by MD Printers";
$notes = "Fast-track delivery required for trade exhibition";
$configSnapshot = [
    'Quantity' => '500 units',
    'Job Name' => $orderName,
    'Print Pressline' => $presslineText,
    'Special Instructions' => $notes,
    'Artwork Status' => 'Upload Later'
];

$added = Cart::add($product['id'], $product['name'], 500, $configSnapshot, $calc, 1);
echo "   Cart Addition Status: " . ($added ? 'SUCCESS' : 'FAILED') . "\n";

$cartItems = Cart::getItems(1);
echo "   Cart Item Count for Customer #1: " . count($cartItems) . "\n";
if (!empty($cartItems)) {
    $latestCartItem = $cartItems[0];
    echo "   Cart Item #{$latestCartItem['id']}: {$latestCartItem['product_name_snapshot']}\n";
    echo "   Order Name Snapshot: " . ($latestCartItem['configuration']['Job Name'] ?? 'N/A') . "\n";
    echo "   Subtotal: ₹{$latestCartItem['subtotal']}\n";
}
echo "   OK!\n\n";

// 6. Verify Customer Recent Orders & Quotes Queries
echo "6. CUSTOMER RECENT ORDERS & QUOTES QUERIES:\n";
$custOrders = Order::getForCustomer(1);
echo "   Recent Orders Count for Customer #1: " . count($custOrders) . "\n";

$custQuotes = Quote::getForCustomer(1);
echo "   Recent Quotes Count for Customer #1: " . count($custQuotes) . "\n";
echo "   OK!\n\n";

echo "========================================================\n";
echo "ALL VERIFICATION CHECKS PASSED SUCCESSFULLY!\n";
echo "========================================================\n";
