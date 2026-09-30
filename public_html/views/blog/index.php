<?php
include INCLUDES_PATH . '/header.php';
$allPosts = Blog::getAll(['is_published' => 1]);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$totalPosts = count($allPosts);
$totalPages = max(1, (int)ceil($totalPosts / $perPage));
$posts = array_slice($allPosts, ($page - 1) * $perPage, $perPage);

$heroBadge    = 'TECHNICAL ARTICLES & GUIDES';
$heroTitle    = 'Commercial Print Insights';
$heroSubtitle = 'Master paper GSM weight selection, prepress bleed setup, CMYK color spaces, and packaging engineering.';
$heroIcon     = 'fas fa-book';
$breadcrumb   = ['Blog' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div style="text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
                <i class="fas fa-newspaper" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">No Blog Articles Found</h3>
                <p style="color: #64748b; margin-bottom: 20px;">No technical articles published yet.</p>
            </div>
        <?php else: ?>
            <div class="grid-2">
                <?php foreach ($posts as $p): ?>
                    <div class="card-feature" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 0; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div style="height: 240px; overflow: hidden; background: #f1f5f9;">
                            <img src="<?= media_url($p['featured_image']) ?>" alt="<?= e($p['title']) ?>" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 30px; display: flex; flex-direction: column; flex: 1;">
                            <span style="font-size: 0.8rem; font-weight: 800; color: var(--cmyk-cyan); text-transform: uppercase; margin-bottom: 8px;"><?= e($p['category_name'] ?? 'Prepress') ?></span>
                            <h3 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 14px; line-height: 1.3;"><?= e($p['title']) ?></h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px; flex: 1;"><?= e($p['excerpt']) ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                                <span style="font-size: 0.85rem; color: #94a3b8;"><i class="fas fa-user-edit"></i> <?= e($p['author']) ?></span>
                                <a href="<?= SITE_URL ?>/blog/<?= e($p['slug']) ?>" class="btn btn-outline btn-sm" style="border-color: var(--cmyk-cyan); color: var(--cmyk-cyan); font-weight: 700;">Read Article <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?= render_pagination($page, $totalPages, SITE_URL . '/blog') ?>
        <?php endif; ?>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>


