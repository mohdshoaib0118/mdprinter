<?php
include INCLUDES_PATH . '/header.php';
$allServices = Service::getAll(['status' => 'published']);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$totalServices = count($allServices);
$totalPages = max(1, (int)ceil($totalServices / $perPage));
$services = array_slice($allServices, ($page - 1) * $perPage, $perPage);

$heroBadge    = 'CAPABILITIES & PRESS SUITE';
$heroTitle    = 'Commercial Printing Services';
$heroSubtitle = 'Precision multi-color offset, on-demand digital press suites, custom rigid packaging, and high-end stationery.';
$heroIcon     = 'fas fa-th-large';
$breadcrumb   = ['Services' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <?php if (empty($services)): ?>
            <div style="text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
                <i class="fas fa-cogs" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">No Services Found</h3>
                <p style="color: #64748b; margin-bottom: 20px;">No commercial printing services available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($services as $srv): ?>
                    <div class="card-feature" style="background: #ffffff; padding: 24px; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div style="height: 200px; border-radius: 12px; overflow: hidden; margin-bottom: 24px; background: #f1f5f9;">
                            <img src="<?= media_url($srv['main_image']) ?>" alt="<?= e($srv['name']) ?>" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-icon" style="margin-top: -45px; position: relative; z-index: 2; border: 3px solid #ffffff; width: 48px; height: 48px; border-radius: 50%; background: var(--cmyk-cyan); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"><i class="<?= e($srv['icon']) ?>"></i></div>
                        <h3 style="font-size: 1.3rem; margin: 16px 0 12px 0; font-weight: 800; color: #0f172a;"><?= e($srv['name']) ?></h3>
                        <p style="color: #64748b; font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px; height: 60px; overflow: hidden;"><?= e($srv['short_desc']) ?></p>
                        <a href="<?= SITE_URL ?>/services/<?= e($srv['slug']) ?>" class="btn btn-outline btn-sm" style="width: 100%; border-color: var(--cmyk-cyan); color: var(--cmyk-cyan); font-weight: 700;">Service Specifications <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?= render_pagination($page, $totalPages, SITE_URL . '/services') ?>
        <?php endif; ?>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>


