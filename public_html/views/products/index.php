<?php
include INCLUDES_PATH . '/header.php';
$categories = Product::getCategories();
$catSlug = $_GET['category'] ?? '';
$search = $_GET['q'] ?? '';

$filters = ['status' => 'published'];
if ($catSlug) $filters['category_slug'] = $catSlug;
if ($search) $filters['search'] = $search;

$allProducts = Product::getAll($filters, 100);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;
$totalProducts = count($allProducts);
$totalPages = max(1, (int)ceil($totalProducts / $perPage));
$products = array_slice($allProducts, ($page - 1) * $perPage, $perPage);

$heroBadge    = 'PRINT PRODUCTS CATALOGUE';
$heroTitle    = 'Commercial Print Products';
$heroSubtitle = 'Executive business cards, brochures, rigid gift boxes, custom packaging, T-shirts, mugs & acrylic signage.';
$heroIcon     = 'fas fa-boxes';
$breadcrumb   = ['Products' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 60px 0; background: #ffffff;">
    <div class="container">
        <!-- Search & Filter Bar -->
        <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between; margin-bottom: 40px; flex-wrap: wrap;">
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <a href="<?= SITE_URL ?>/products" class="btn <?= empty($catSlug) ? 'btn-primary' : 'btn-outline' ?> btn-sm">All Products</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= SITE_URL ?>/products?category=<?= e($cat['slug']) ?>" class="btn <?= $catSlug === $cat['slug'] ? 'btn-primary' : 'btn-outline' ?> btn-sm"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                <!-- Layout View Switcher -->
                <div class="view-toggle-group">
                    <button type="button" class="view-toggle-btn active" data-grid="grid-3" title="3 Columns View"><i class="fas fa-th-large"></i> 3 Grid</button>
                    <button type="button" class="view-toggle-btn" data-grid="grid-4" title="4 Columns View"><i class="fas fa-th"></i> 4 Grid</button>
                    <button type="button" class="view-toggle-btn" data-grid="view-list" title="List View"><i class="fas fa-list"></i> List View</button>
                </div>

                <form action="<?= SITE_URL ?>/products" method="GET" style="display: flex; gap: 10px; min-width: 260px;">
                    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search product name..." class="form-control" style="padding: 8px 14px; font-size: 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
                <i class="fas fa-search" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">No Products Found</h3>
                <p style="color: #64748b; margin-bottom: 20px;">Try adjusting your search criteria or browse all products.</p>
                <a href="<?= SITE_URL ?>/products" class="btn btn-primary btn-sm"><i class="fas fa-th"></i> View All Products</a>
            </div>
        <?php else: ?>
            <div class="grid-3 product-grid-container">
                <?php foreach ($products as $prod): ?>
                    <div class="card-feature" style="background: #ffffff; padding: 20px; border: 1px solid #cbd5e1; border-radius: 20px; box-shadow: var(--shadow-md); display: flex; flex-direction: column;">
                        <div style="height: 210px; border-radius: 14px; overflow: hidden; margin-bottom: 20px; background: #f1f5f9; flex-shrink: 0;">
                            <img src="<?= media_url($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-feature-body" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <span style="font-size: 0.78rem; color: var(--cmyk-cyan); font-weight: 800; text-transform: uppercase; display: block; margin-bottom: 6px;"><?= e($prod['category_name'] ?? 'Product') ?></span>
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; line-height: 1.4;"><?= e($prod['name']) ?></h3>
                            <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 20px; height: 42px; overflow: hidden; line-height: 1.5; flex-grow: 1;"><?= e($prod['short_desc']) ?></p>
                            <div style="display: flex; gap: 10px; margin-top: auto;">
                                <a href="<?= SITE_URL ?>/products/<?= e($prod['slug']) ?>" class="btn btn-outline btn-sm" style="flex: 1; border-color: #cbd5e1; color: #334155;">View Specs</a>
                                <a href="<?= SITE_URL ?>/request-quote?product=<?= urlencode($prod['name']) ?>" class="btn btn-primary btn-sm" style="flex: 1;">Quote</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?php 
                $queryParams = array_filter(['category' => $catSlug, 'q' => $search]);
                $pageBaseUrl = SITE_URL . '/products' . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');
                echo render_pagination($page, $totalPages, $pageBaseUrl);
            ?>
        <?php endif; ?>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

