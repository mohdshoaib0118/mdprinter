<?php
include INCLUDES_PATH . '/header.php';
$query = trim($_GET['q'] ?? '');

$products = !empty($query) ? Product::getAll(['search' => $query, 'status' => 'published']) : [];
$services = !empty($query) ? Service::getAll(['search' => $query, 'status' => 'published']) : [];
$posts    = !empty($query) ? Blog::getAll(['search' => $query, 'is_published' => 1]) : [];

$heroBadge    = 'SITE SEARCH';
$heroTitle    = 'Search Results for "' . e($query) . '"';
$heroSubtitle = 'Results found across products, capabilities, and technical print articles.';
$heroIcon     = 'fas fa-search';
$breadcrumb   = ['Search' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <?php if (empty($query)): ?>
            <div style="text-align: center; color: #64748b; font-size: 1.1rem;">Please enter a search keyword in the header search box.</div>
        <?php elseif (empty($products) && empty($services) && empty($posts)): ?>
            <div style="text-align: center; color: #64748b; font-size: 1.1rem;">No matching printing products, services, or articles found for "<strong><?= e($query) ?></strong>".</div>
        <?php else: ?>
            <?php if (!empty($products)): ?>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; color: var(--cmyk-cyan);">Matching Products</h3>
                <div class="grid-4" style="margin-bottom: 50px;">
                    <?php foreach ($products as $p): ?>
                        <div class="card-feature" style="background: #ffffff; padding: 20px; border: 1px solid #e2e8f0; border-radius: 16px;">
                            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 10px;"><?= e($p['name']) ?></h4>
                            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 16px;"><?= e($p['short_desc']) ?></p>
                            <a href="<?= SITE_URL ?>/products/<?= e($p['slug']) ?>" class="btn btn-outline btn-sm" style="border-color: var(--cmyk-cyan); color: var(--cmyk-cyan); width: 100%;">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($services)): ?>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; color: var(--cmyk-magenta);">Matching Services</h3>
                <div class="grid-3" style="margin-bottom: 50px;">
                    <?php foreach ($services as $s): ?>
                        <div class="card-feature" style="background: #ffffff; padding: 24px; border: 1px solid #e2e8f0; border-radius: 16px;">
                            <h4 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 10px;"><?= e($s['name']) ?></h4>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 16px;"><?= e($s['short_desc']) ?></p>
                            <a href="<?= SITE_URL ?>/services/<?= e($s['slug']) ?>" class="btn btn-outline btn-sm" style="border-color: var(--cmyk-magenta); color: var(--cmyk-magenta); width: 100%;">Service Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

