<?php
/**
 * MD Printers - Dynamic XML Sitemap Generator
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

header("Content-Type: application/xml; charset=utf-8");

$db = getDB();

$products = $db->fetchAll("SELECT slug, updated_at FROM products WHERE is_active = 1");
$services = $db->fetchAll("SELECT slug, updated_at FROM services WHERE is_active = 1");
$portfolio = $db->fetchAll("SELECT slug, updated_at FROM portfolio");
$posts = $db->fetchAll("SELECT slug, created_at FROM blog_posts WHERE is_published = 1");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= SITE_URL ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/services</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/products</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/portfolio</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/blog</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/contact</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/request-quote</loc>
        <changefreq>weekly</changefreq>
        <priority>0.95</priority>
    </url>

    <?php foreach ($services as $s): ?>
    <url>
        <loc><?= SITE_URL ?>/services/<?= $s['slug'] ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>
    <?php endforeach; ?>

    <?php foreach ($products as $p): ?>
    <url>
        <loc><?= SITE_URL ?>/products/<?= $p['slug'] ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>
    <?php endforeach; ?>

    <?php foreach ($posts as $b): ?>
    <url>
        <loc><?= SITE_URL ?>/blog/<?= $b['slug'] ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>
