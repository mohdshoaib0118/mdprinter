<?php
require 'public_html/config/config.php';
require 'public_html/config/database.php';
$db = Database::getInstance();

$db->exec("UPDATE banners SET cta_primary_link = '/order/product/gold-foil-business-cards', cta_primary_text = 'CONFIGURE & ORDER' WHERE id = 1");
$db->exec("UPDATE banners SET cta_primary_link = '/order/product/custom-rigid-magnetic-box', cta_primary_text = 'EXPLORE PACKAGING', cta_secondary_link = '/request-quote' WHERE id = 2");
$db->exec("UPDATE banners SET cta_primary_link = '/services/t-shirt-printing', cta_primary_text = 'CUSTOMIZE T-SHIRTS' WHERE id = 3");
$db->exec("UPDATE banners SET cta_primary_link = '/services/acrylic-boards', cta_primary_text = 'VIEW SIGNAGE BOARDS' WHERE id = 4");

echo "UPDATED HERO BANNERS LINK MAPPINGS\n";
