<?php
include INCLUDES_PATH . '/header.php';
$specs = json_decode($currentProduct['specifications_json'] ?? '{}', true) ?: [];
$paperOptions = json_decode($currentProduct['paper_options_json'] ?? '[]', true) ?: [];
$finishingOptions = json_decode($currentProduct['finishing_options_json'] ?? '[]', true) ?: [];

$heroBadge    = 'PRODUCT SPECIFICATIONS';
$heroTitle    = $currentProduct['name'];
$heroSubtitle = $currentProduct['short_desc'] ?? 'High-precision commercial print product with custom paper and finishing options.';
$heroIcon     = 'fas fa-box';
$breadcrumb   = ['Products' => SITE_URL . '/products', $currentProduct['name'] => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 60px; align-items: start;">
            <div>
                <div style="height: 420px; border-radius: 20px; overflow: hidden; margin-bottom: 30px; border: 1px solid #e2e8f0; background: #0f172a; position: relative;">
                    <?= render_media_element($currentProduct['main_image'], $currentProduct['video_url'] ?? '', $currentProduct['name']) ?>
                </div>

                <h3 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Product Overview</h3>
                <div style="color: #475569; font-size: 1.05rem; line-height: 1.8; margin-bottom: 30px;">
                    <?= $currentProduct['full_desc'] ?>
                </div>

                <?php if (!empty($paperOptions)): ?>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Available Paper Stocks (GSM):</h4>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px;">
                        <?php foreach ($paperOptions as $paper): ?>
                            <span style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 10px; font-size: 0.9rem; font-weight: 700; color: var(--cmyk-cyan);">
                                <i class="fas fa-layer-group" style="margin-right: 6px;"></i> <?= e($paper) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($finishingOptions)): ?>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Special Finishing Options:</h4>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px;">
                        <?php foreach ($finishingOptions as $finish): ?>
                            <span style="background: rgba(238,0,131,0.06); border: 1px solid rgba(238,0,131,0.2); padding: 8px 16px; border-radius: 10px; font-size: 0.9rem; font-weight: 700; color: var(--cmyk-magenta);">
                                <i class="fas fa-magic" style="margin-right: 6px;"></i> <?= e($finish) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Quote Panel -->
            <div>
                <div style="background: #ffffff; padding: 36px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                    <h3 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 10px;">Configure Quote</h3>
                    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 24px;">Min. Order Quantity: <strong style="color: #0f172a;"><?= e($currentProduct['min_quantity']) ?> units</strong></p>

                    <a href="<?= SITE_URL ?>/request-quote?product=<?= urlencode($currentProduct['name']) ?>" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 16px; font-weight: 800;">
                        <i class="fas fa-calculator"></i> Launch Quotation Form
                    </a>

                    <a href="https://wa.me/<?= e(Setting::get('whatsapp_number', '918146141450')) ?>?text=<?= urlencode('Hello MD Printers, I am interested in ordering: ' . $currentProduct['name']) ?>" target="_blank" class="btn btn-magenta btn-lg" style="width: 100%; font-weight: 800;">
                        <i class="fab fa-whatsapp"></i> WhatsApp Quick Enquiry
                    </a>

                    <?php if (!empty($specs)): ?>
                        <div style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                            <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 14px;">Technical Details</h4>
                            <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem; padding: 0;">
                                <?php foreach ($specs as $k => $v): ?>
                                    <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                                        <span style="color: #64748b;"><?= e($k) ?>:</span>
                                        <strong style="color: #0f172a;"><?= e($v) ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

