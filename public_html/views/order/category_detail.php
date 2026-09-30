<?php
/**
 * MD Printers - B2B Order Journey: Category Detail (Product Groups / Subcategories Page)
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/ProductGroup.php';
require_once MODELS_PATH . '/Product.php';

$catSlug = $matchParams['slug'] ?? '';
$category = Category::getBySlug($catSlug);

if (!$category) {
    redirect_to(SITE_URL . '/order');
}

$productGroups = ProductGroup::getAll([
    'category_id' => $category['id'],
    'status' => 'active'
]);

// Also fetch unmapped products directly under this category
$directProducts = Product::getAll([
    'category_slug' => $catSlug,
    'status' => 'published'
]);
?>

<div style="background: #f8fafc; min-height: 85vh; padding: 40px 0;">
    <div class="container">

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
            <ol style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; font-size: 0.88rem; font-weight: 700; color: #64748b;">
                <li><a href="<?= SITE_URL ?>/dashboard" style="color: #64748b; text-decoration: none;"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li><a href="<?= SITE_URL ?>/order" style="color: #64748b; text-decoration: none;">New Order</a></li>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li style="color: var(--cmyk-cyan);"><?= e($category['name']) ?></li>
            </ol>
        </nav>

        <!-- Category Banner Header -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 20px; padding: 36px; margin-bottom: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="background: rgba(0,174,239,0.15); color: var(--cmyk-cyan); padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 12px;">
                    Step 2 of 4: Product Group / Subcategory
                </span>
                <h1 style="font-size: 2.2rem; font-weight: 900; margin: 0 0 8px 0; color: #ffffff;">
                    <?= e($category['name']) ?>
                </h1>
                <p style="color: #94a3b8; font-size: 1rem; margin: 0; max-width: 650px;">
                    <?= e($category['description'] ?: 'Select your desired paper weight, material specification or product group below.') ?>
                </p>
            </div>
            <div>
                <a href="<?= SITE_URL ?>/order" class="btn" style="background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); font-weight: 700; border-radius: 10px; padding: 10px 18px;">
                    <i class="fas fa-chevron-left"></i> Change Category
                </a>
            </div>
        </div>

        <!-- Product Groups / Subcategories Section -->
        <?php if (!empty($productGroups)): ?>
            <h3 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin-bottom: 20px;">
                Select Product Group / GSM Specification
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; margin-bottom: 40px;">
                <?php foreach ($productGroups as $group): ?>
                    <div style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: transform 0.2s ease, box-shadow 0.2s ease; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <span style="background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: 12px; text-transform: uppercase;">
                                    Min Qty: <?= number_format($group['min_quantity']) ?> units
                                </span>
                                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700;">
                                    <?= (int)$group['product_count'] ?> Products
                                </span>
                            </div>

                            <h3 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">
                                <?= e($group['name']) ?>
                            </h3>
                            <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">
                                <?= e($group['description'] ?: 'High quality printing options with custom trade specifications.') ?>
                            </p>
                        </div>

                        <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; text-align: right;">
                            <a href="<?= SITE_URL ?>/order/group/<?= e($group['slug']) ?>" class="btn btn-primary" style="font-weight: 800; padding: 10px 20px; border-radius: 10px; font-size: 0.88rem; text-decoration: none; width: 100%; display: justify-content: center; text-align: center;">
                                Explore Products <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Direct Products Section -->
        <?php if (!empty($directProducts)): ?>
            <h3 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin-bottom: 20px;">
                Available Products in <?= e($category['name']) ?>
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                <?php foreach ($directProducts as $prod): ?>
                    <div style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="height: 180px; background: #f1f5f9; overflow: hidden; position: relative;">
                                <img src="<?= media_url($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="padding: 20px;">
                                <h4 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">
                                    <?= e($prod['name']) ?>
                                </h4>
                                <p style="color: #64748b; font-size: 0.88rem; line-height: 1.4; margin-bottom: 14px;">
                                    <?= e($prod['short_desc'] ?: 'Custom commercial printing product.') ?>
                                </p>
                            </div>
                        </div>

                        <div style="padding: 0 20px 20px 20px;">
                            <a href="<?= SITE_URL ?>/order/product/<?= e($prod['slug']) ?>" class="btn btn-primary" style="width: 100%; text-align: center; justify-content: center; font-weight: 800; padding: 10px; border-radius: 10px;">
                                Configure & Order <i class="fas fa-sliders-h" style="margin-left: 6px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($productGroups) && empty($directProducts)): ?>
            <div style="text-align: center; padding: 60px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <i class="fas fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="color: #334155; font-weight: 800;">No Products Configured in this Category Yet</h3>
                <p style="color: #64748b;">Please check back shortly or request a custom quote.</p>
                <a href="<?= SITE_URL ?>/request-quote" class="btn btn-primary" style="font-weight: 800; border-radius: 10px;">Request Quote</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
