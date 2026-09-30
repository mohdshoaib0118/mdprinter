<?php
require 'public_html/config/config.php';
require 'public_html/config/database.php';
$db = Database::getInstance();
echo "=== SERVICES ===\n";
print_r($db->query('SELECT id, name, slug FROM services')->fetchAll(PDO::FETCH_ASSOC));

echo "=== BANNERS ===\n";
print_r($db->query('SELECT id, title, cta_primary_text, cta_primary_link, cta_secondary_text, cta_secondary_link FROM banners')->fetchAll(PDO::FETCH_ASSOC));
