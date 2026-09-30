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
            <!-- Blog Layout & View Switcher Bar -->
            <div class="blog-view-bar">
                <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">
                    <i class="fas fa-newspaper" style="color: var(--cmyk-cyan); margin-right: 6px;"></i>
                    Showing <span style="color: #0f172a; font-weight: 800;"><?= count($posts) ?></span> of <span style="color: #0f172a; font-weight: 800;"><?= $totalPosts ?></span> Technical Articles
                </div>

                <div class="blog-view-options">
                    <button type="button" class="blog-view-btn active" id="btnGridView" onclick="switchBlogView('grid')" title="Grid View (3 in a row)">
                        <i class="fas fa-th-large"></i> Grid View
                    </button>
                    <button type="button" class="blog-view-btn" id="btnListView" onclick="switchBlogView('list')" title="List View">
                        <i class="fas fa-list"></i> List View
                    </button>
                </div>
            </div>

            <!-- Blog Posts Container (3 in a row by default) -->
            <div id="blogGridContainer" class="blog-grid-container view-grid">
                <?php foreach ($posts as $p): ?>
                    <div class="card-blog">
                        <div class="blog-img-box">
                            <img src="<?= media_url($p['featured_image']) ?>" alt="<?= e($p['title']) ?>" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';">
                        </div>
                        <div class="blog-content-box">
                            <span style="font-size: 0.8rem; font-weight: 800; color: var(--cmyk-cyan); text-transform: uppercase; margin-bottom: 8px;"><?= e($p['category_name'] ?? 'Prepress') ?></span>
                            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; line-height: 1.35;"><?= e($p['title']) ?></h3>
                            <p style="color: #64748b; font-size: 0.92rem; line-height: 1.65; margin-bottom: 22px; flex: 1;"><?= e($p['excerpt']) ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: auto;">
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

<script>
function switchBlogView(mode) {
    var container = document.getElementById('blogGridContainer');
    var btnGrid = document.getElementById('btnGridView');
    var btnList = document.getElementById('btnListView');
    if (!container) return;

    if (mode === 'list') {
        container.classList.remove('view-grid');
        container.classList.add('view-list');
        btnGrid.classList.remove('active');
        btnList.classList.add('active');
        localStorage.setItem('blog_view_mode', 'list');
    } else {
        container.classList.remove('view-list');
        container.classList.add('view-grid');
        btnList.classList.remove('active');
        btnGrid.classList.add('active');
        localStorage.setItem('blog_view_mode', 'grid');
    }
}

// Restore user view preference on load
document.addEventListener('DOMContentLoaded', function() {
    var savedView = localStorage.getItem('blog_view_mode');
    if (savedView === 'list') {
        switchBlogView('list');
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>


