<?php
/**
 * MD Printers - B2B Order Journey: Group Detail (Products in Product Group Page)
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/ProductGroup.php';
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/PricingEngine.php';

$groupSlug = $matchParams['slug'] ?? '';
$group = ProductGroup::getBySlug($groupSlug);

if (!$group) {
    redirect_to(SITE_URL . '/order');
}

$category = Category::getById((int)$group['category_id']);

// Fetch products belonging to this group
$db = Database::getInstance();
$products = [];
if ($db) {
    $stmt = $db->prepare("SELECT p.* FROM products p WHERE p.product_group_id = :gid AND p.status = 'published' ORDER BY p.id ASC");
    $stmt->execute(['gid' => $group['id']]);
    $products = $stmt->fetchAll() ?: [];
}
?>

<div style="background: #f8fafc; min-height: 85vh; padding: 40px 0;">
    <div class="container">

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
            <ol style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; font-size: 0.88rem; font-weight: 700; color: #64748b;">
                <li><a href="<?= SITE_URL ?>/dashboard" style="color: #64748b; text-decoration: none;"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li><a href="<?= SITE_URL ?>/order" style="color: #64748b; text-decoration: none;">New Order</a></li>
                <?php if ($category): ?>
                    <li><span style="color: #cbd5e1;">/</span></li>
                    <li><a href="<?= SITE_URL ?>/order/category/<?= e($category['slug']) ?>" style="color: #64748b; text-decoration: none;"><?= e($category['name']) ?></a></li>
                <?php endif; ?>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li style="color: var(--cmyk-cyan);"><?= e($group['name']) ?></li>
            </ol>
        </nav>

        <!-- Group Header Banner -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 20px; padding: 36px; margin-bottom: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="background: rgba(0,174,239,0.15); color: var(--cmyk-cyan); padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 12px;">
                    Step 3 of 4: Select Product Variant
                </span>
                <h1 style="font-size: 2.2rem; font-weight: 900; margin: 0 0 8px 0; color: #ffffff;">
                    <?= e($group['name']) ?>
                </h1>
                <p style="color: #94a3b8; font-size: 1rem; margin: 0; max-width: 650px;">
                    <?= e($group['description'] ?: 'Choose from the specialized print variants below to begin trade configuration.') ?>
                </p>
            </div>
            <div>
                <?php if ($category): ?>
                    <a href="<?= SITE_URL ?>/order/category/<?= e($category['slug']) ?>" class="btn" style="background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); font-weight: 700; border-radius: 10px; padding: 10px 18px;">
                        <i class="fas fa-chevron-left"></i> Change Product Group
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                <?php foreach ($products as $prod): 
                    $slabs = PricingEngine::getQuantitySlabs($prod['id']);
                    $startingPrice = !empty($slabs) ? (float)$slabs[0]['base_price'] : 0;
                ?>
                    <div style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div>
                            <div style="height: 200px; background: #f1f5f9; position: relative; overflow: hidden;">
                                <img src="<?= media_url($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <span style="position: absolute; top: 12px; right: 12px; background: rgba(15,23,42,0.8); color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; backdrop-filter: blur(4px);">
                                    Min Qty: <?= number_format($prod['min_quantity']) ?>
                                </span>
                            </div>

                            <div style="padding: 24px;">
                                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; line-height: 1.3;">
                                    <?= e($prod['name']) ?>
                                </h3>
                                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 16px;">
                                    <?= e($prod['short_desc'] ?: 'High precision commercial printing specification.') ?>
                                </p>

                                <?php if (!empty($prod['specifications_json'])): 
                                    $specs = json_decode($prod['specifications_json'], true);
                                    if (is_array($specs)):
                                ?>
                                    <div style="background: #f8fafc; border-radius: 10px; padding: 12px; margin-bottom: 16px; font-size: 0.82rem; color: #475569;">
                                        <?php foreach (array_slice($specs, 0, 3) as $k => $v): ?>
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                <strong><?= e($k) ?>:</strong> <span><?= e($v) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; endif; ?>
                            </div>
                        </div>

                        <div style="padding: 0 24px 24px 24px; border-top: 1px solid #f1f5f9; margin-top: auto; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <?php if ($startingPrice > 0): ?>
                                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; display: block; text-transform: uppercase;">Starting From</span>
                                    <span style="font-size: 1.3rem; font-weight: 900; color: var(--cmyk-cyan);">₹<?= number_format($startingPrice, 2) ?></span>
                                <?php else: ?>
                                    <span style="font-size: 0.78rem; background: #fef3c7; color: #92400e; font-weight: 800; padding: 4px 10px; border-radius: 8px;">
                                        Quote Required
                                    </span>
                                <?php endif; ?>
                            </div>

                            <a href="<?= SITE_URL ?>/order/product/<?= e($prod['slug']) ?>" class="btn btn-primary" style="font-weight: 800; padding: 10px 18px; border-radius: 10px; font-size: 0.88rem; text-decoration: none;">
                                Configure & Order <i class="fas fa-sliders-h" style="margin-left: 6px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <i class="fas fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="color: #334155; font-weight: 800;">No Product Variants in this Group</h3>
                <p style="color: #64748b;">Please check back shortly or request a custom quotation.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
