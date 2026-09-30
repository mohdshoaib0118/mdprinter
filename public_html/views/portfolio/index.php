<?php
include INCLUDES_PATH . '/header.php';
$categories = Portfolio::getCategories();
$catSlug = $_GET['category'] ?? '';

$filters = ['status' => 'published'];
if (!empty($catSlug)) {
    $filters['category_slug'] = $catSlug;
}

$allPortfolioItems = Portfolio::getAll($filters);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$totalItems = count($allPortfolioItems);
$totalPages = max(1, (int)ceil($totalItems / $perPage));
$portfolioItems = array_slice($allPortfolioItems, ($page - 1) * $perPage, $perPage);

$heroBadge    = 'CASE STUDIES & SHOWCASE';
$heroTitle    = 'Printed Works Portfolio';
$heroSubtitle = 'High-definition packaging, offset corporate annual reports, luxury foil stationery, and retail installations.';
$heroIcon     = 'fas fa-camera';
$breadcrumb   = ['Portfolio' => ''];
$pageBaseUrl  = SITE_URL . '/portfolio' . (!empty($catSlug) ? '?category=' . urlencode($catSlug) : '');
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 60px 0; background: #ffffff;">
    <div class="container">
        <!-- Filter Tabs -->
        <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 50px; flex-wrap: wrap;">
            <a href="<?= SITE_URL ?>/portfolio" class="btn <?= empty($catSlug) ? 'btn-primary' : 'btn-outline' ?> btn-sm">All Projects</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= SITE_URL ?>/portfolio?category=<?= e($cat['slug']) ?>" class="btn <?= $catSlug === $cat['slug'] ? 'btn-primary' : 'btn-outline' ?> btn-sm"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($portfolioItems)): ?>
            <div style="text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
                <i class="fas fa-folder-open" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">No Case Studies Found</h3>
                <p style="color: #64748b; margin-bottom: 20px;">No portfolio projects found for this category.</p>
                <a href="<?= SITE_URL ?>/portfolio" class="btn btn-primary btn-sm"><i class="fas fa-th"></i> View All Projects</a>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($portfolioItems as $item): ?>
                    <div class="card-feature portfolio-item" data-category="<?= e($item['category_slug'] ?? 'general') ?>" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 0; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div style="height: 260px; overflow: hidden; background: #f1f5f9; position: relative;">
                            <img src="<?= media_url($item['image']) ?>" alt="<?= e($item['title']) ?>" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="width: 100%; height: 100%; object-fit: cover;">
                            <span style="position: absolute; top: 16px; left: 16px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); padding: 4px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; color: var(--cmyk-cyan); border: 1px solid #e2e8f0;">
                                <?= e($item['category_name'] ?? 'Case Study') ?>
                            </span>
                        </div>
                        <div style="padding: 24px;">
                            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; line-height: 1.4;"><?= e($item['title']) ?></h3>
                            <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 20px; line-height: 1.6;"><?= e($item['description']) ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                                <span><i class="fas fa-user-tie"></i> <?= e($item['client_name']) ?></span>
                                <a href="<?= SITE_URL ?>/portfolio/<?= e($item['slug']) ?>" style="color: var(--cmyk-cyan); font-weight: 800; text-decoration: none;">Read Case Study <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?= render_pagination($page, $totalPages, $pageBaseUrl) ?>
        <?php endif; ?>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

