<?php
/**
 * MD Printers - B2B Order Journey: Printing Categories Selection Page
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/ProductGroup.php';
require_once MODELS_PATH . '/Product.php';

Category::initTables();
ProductGroup::initTables();

$categories = Category::getAll(['status' => 'active']);
?>

<div style="background: #f8fafc; min-height: 85vh; padding: 40px 0;">
    <div class="container">

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
            <ol style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; font-size: 0.88rem; font-weight: 700; color: #64748b;">
                <li><a href="<?= SITE_URL ?>/dashboard" style="color: #64748b; text-decoration: none;"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li style="color: var(--cmyk-cyan);">Launch New Order (Printing Categories)</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 20px; padding: 36px; margin-bottom: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="background: rgba(0,174,239,0.15); color: var(--cmyk-cyan); padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 12px;">
                    Step 1 of 4: Category Selection
                </span>
                <h1 style="font-size: 2.2rem; font-weight: 900; margin: 0 0 8px 0; color: #ffffff;">Launch New B2B Order / Quote</h1>
                <p style="color: #94a3b8; font-size: 1rem; margin: 0; max-width: 650px;">
                    Select a printing service category below to explore product groups, custom GSM paper stocks, and dynamic trade configurators.
                </p>
            </div>
            <div>
                <div style="position: relative; min-width: 280px;">
                    <input type="text" id="catSearchInput" onkeyup="filterCategories()" placeholder="Search printing services..." style="width: 100%; padding: 14px 20px 14px 44px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); color: #ffffff; font-weight: 600; font-size: 0.9rem; outline: none;">
                    <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                </div>
            </div>
        </div>

        <!-- Printing Categories Grid -->
        <div id="categoriesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="category-card-item" data-title="<?= strtolower(e($cat['name'])) ?>" data-desc="<?= strtolower(e($cat['description'])) ?>" style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: transform 0.25s ease, box-shadow 0.25s ease; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, rgba(0,174,239,0.1), rgba(236,0,140,0.1)); color: var(--cmyk-cyan); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                                    <i class="fas <?= e($cat['icon'] ?: 'fa-print') ?>"></i>
                                </div>
                                <span style="background: #f1f5f9; color: #475569; font-size: 0.78rem; font-weight: 800; padding: 4px 10px; border-radius: 12px;">
                                    <?= (int)$cat['group_count'] ?> Subcategories
                                </span>
                            </div>

                            <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; line-height: 1.3;">
                                <?= e($cat['name']) ?>
                            </h3>
                            <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">
                                <?= e($cat['description'] ?: 'High precision commercial printing services with custom paper weights and trade finishes.') ?>
                            </p>
                        </div>

                        <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.82rem; color: #64748b; font-weight: 700;">
                                <?= (int)$cat['product_count'] ?> Products
                            </span>
                            <a href="<?= SITE_URL ?>/order/category/<?= e($cat['slug']) ?>" class="btn btn-primary" style="font-weight: 800; padding: 10px 20px; border-radius: 10px; font-size: 0.88rem; text-decoration: none;">
                                Select Category <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                    <i class="fas fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                    <h3 style="color: #334155; font-weight: 800;">No Printing Categories Available</h3>
                    <p style="color: #64748b;">Please contact support or check back shortly.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function filterCategories() {
    var q = document.getElementById('catSearchInput').value.toLowerCase();
    var cards = document.querySelectorAll('.category-card-item');
    cards.forEach(function(card) {
        var title = card.getAttribute('data-title') || '';
        var desc = card.getAttribute('data-desc') || '';
        if (title.indexOf(q) !== -1 || desc.indexOf(q) !== -1) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
