<?php
require_once __DIR__ . '/../public_html/config/config.php';
require_once CONFIG_PATH . '/database.php';

$db = Database::getInstance();

echo "=== DATABASE PRICING & CATEGORY AUDIT ===\n\n";

$cats = $db->query("SELECT id, name, slug FROM product_categories")->fetchAll();
echo "Categories in DB (" . count($cats) . "):\n";
foreach ($cats as $c) {
    echo "  - [ID {$c['id']}] {$c['name']} ({$c['slug']})\n";
}

echo "\nProduct Groups in DB:\n";
$groups = $db->query("SELECT g.id, g.category_id, g.name, g.slug, c.name as cat_name FROM product_groups g LEFT JOIN product_categories c ON g.category_id = c.id")->fetchAll();
echo "Total Product Groups: " . count($groups) . "\n";
foreach ($groups as $g) {
    echo "  - [ID {$g['id']}] Group: {$g['name']} | Category: {$g['cat_name']}\n";
}

echo "\nProducts in DB:\n";
$prods = $db->query("SELECT p.id, p.category_id, p.product_group_id, p.name, p.slug, p.min_quantity FROM products p")->fetchAll();
echo "Total Products: " . count($prods) . "\n";
foreach ($prods as $p) {
    echo "  - [ID {$p['id']}] {$p['name']} | Category ID: {$p['category_id']} | Group ID: " . ($p['product_group_id'] ?? 'NULL') . "\n";
}

echo "\nPricing Slabs in DB:\n";
$slabs = $db->query("SELECT product_id, COUNT(*) as cnt FROM product_pricing_slabs GROUP BY product_id")->fetchAll();
foreach ($slabs as $s) {
    echo "  - Product ID {$s['product_id']}: {$s['cnt']} slabs\n";
}

echo "\nProduct Options in DB:\n";
$opts = $db->query("SELECT product_id, COUNT(*) as cnt FROM product_options GROUP BY product_id")->fetchAll();
foreach ($opts as $o) {
    echo "  - Product ID {$o['product_id']}: {$o['cnt']} options\n";
}
