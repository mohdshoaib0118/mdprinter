<?php
include INCLUDES_PATH . '/header.php';
$features = json_decode($currentService['features_json'] ?? '[]', true) ?: [];
$specs = json_decode($currentService['specifications_json'] ?? '{}', true) ?: [];

$heroBadge    = 'SERVICE SPECIFICATIONS';
$heroTitle    = $currentService['name'];
$heroSubtitle = $currentService['short_desc'] ?? 'Commercial press capability engineered for corporate and retail applications.';
$heroIcon     = $currentService['icon'] ?? 'fas fa-cog';
$breadcrumb   = ['Services' => SITE_URL . '/services', $currentService['name'] => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 50px;">
            <div>
                <div style="height: 380px; border-radius: 20px; overflow: hidden; margin-bottom: 40px; border: 1px solid #e2e8f0; background: #0f172a; position: relative;">
                    <?= render_media_element($currentService['main_image'], $currentService['video_url'] ?? '', $currentService['name']) ?>
                </div>

                <div style="color: #475569; font-size: 1.05rem; line-height: 1.8; margin-bottom: 40px;">
                    <?= $currentService['full_desc'] ?>
                </div>

                <?php if (!empty($features)): ?>
                    <h3 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 20px;">Service Highlights</h3>
                    <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 40px;">
                        <?php foreach ($features as $ft): ?>
                            <div style="background: #f8fafc; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-check-circle" style="color: var(--cmyk-cyan); font-size: 1.2rem;"></i>
                                <span style="font-weight: 700; font-size: 0.95rem; color: #0f172a;"><?= e($ft) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <div style="background: #f8fafc; padding: 30px; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <h4 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">Technical Specs</h4>
                    <?php if (!empty($specs)): ?>
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 14px; font-size: 0.9rem; padding: 0;">
                            <?php foreach ($specs as $key => $val): ?>
                                <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                    <span style="color: #64748b;"><?= e($key) ?>:</span>
                                    <strong style="color: #0f172a; text-align: right;"><?= e($val) ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div style="background: linear-gradient(135deg, rgba(0,133,178,0.08), rgba(238,0,131,0.08)); padding: 30px; border-radius: 20px; border: 1px solid rgba(0,133,178,0.2); text-align: center;">
                    <h4 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Request Service Quote</h4>
                    <p style="font-size: 0.9rem; color: #475569; margin-bottom: 20px;">Get exact pricing based on paper stock, dimensions, and quantities.</p>
                    <a href="<?= SITE_URL ?>/request-quote?service=<?= urlencode($currentService['name']) ?>" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 800;"><i class="fas fa-calculator"></i> Calculate Quote</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Major Capabilities & Plant Infrastructure Section -->
<section style="padding: 70px 0; background: #f8fafc; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div style="text-align: center; max-width: 700px; margin: 0 auto 45px auto;">
            <span style="background: rgba(0,133,178,0.1); color: var(--cmyk-cyan); padding: 4px 14px; border-radius: 20px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase;">
                <i class="fas fa-cogs"></i> PLANT INFRASTRUCTURE
            </span>
            <h2 style="font-size: 2.1rem; font-weight: 900; color: #0f172a; margin-top: 10px; margin-bottom: 12px;">Major Press Capabilities</h2>
            <p style="color: #64748b; font-size: 0.98rem; line-height: 1.6;">
                Engineered with European offset machinery, digital VDP presses, and automated post-press finishing systems for enterprise clients.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
            <div style="background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0,133,178,0.1); color: var(--cmyk-cyan); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 18px;">
                    <i class="fas fa-print"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Multi-Color Commercial Offset</h3>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin-bottom: 14px;">
                    Heidelberg Speedmaster presses with G7 spectral color density control, online aqueous varnishing, and 18,000 sheets/hour throughput.
                </p>
                <span style="font-size: 0.8rem; font-weight: 800; color: var(--cmyk-cyan);">• Up to 450 GSM Substrate Board</span>
            </div>

            <div style="background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(238,0,131,0.1); color: var(--cmyk-magenta); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 18px;">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Variable Digital Press (VDP)</h3>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin-bottom: 14px;">
                    HP Indigo & Konica Minolta production presses for short runs, variable data barcode printing, and personalized direct mailers.
                </p>
                <span style="font-size: 0.8rem; font-weight: 800; color: var(--cmyk-magenta);">• Same-Day Turnaround Available</span>
            </div>

            <div style="background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(201,158,80,0.1); color: var(--cmyk-gold); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 18px;">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Rigid Box & Packaging Automation</h3>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin-bottom: 14px;">
                    CAD sample prototyping, automated folding carton converting, die-cutting, window patching, and magnetic gift box wrapping.
                </p>
                <span style="font-size: 0.8rem; font-weight: 800; color: var(--cmyk-gold);">• Custom Structural Prototyping</span>
            </div>

            <div style="background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,222,0,0.25); color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 18px;">
                    <i class="fas fa-magic"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">3D Spot UV & Foil Stamping</h3>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin-bottom: 14px;">
                    Digital hot foil stamping (Metallic Gold, Rose Gold, Silver), velvet soft-touch lamination, and raised tactile 3D spot UV effects.
                </p>
                <span style="font-size: 0.8rem; font-weight: 800; color: #b45309;">• Tactile Metallic Embellishments</span>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

