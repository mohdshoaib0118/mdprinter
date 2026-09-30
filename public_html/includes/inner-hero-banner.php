<?php
/**
 * MD Printers - Inner Page Dynamic Hero Banner Component
 */
$heroBadge    = $heroBadge ?? 'MD PRINTERS OFFICIAL';
$heroTitle    = $heroTitle ?? 'Commercial Printing Solutions';
$heroSub      = $heroSubtitle ?? 'High-precision offset printing, digital graphics, and custom packaging.';
$heroIcon     = $heroIcon ?? 'fas fa-print';
$breadcrumb   = $breadcrumb ?? [];
?>

<section class="inner-hero-banner" style="padding: 65px 0 55px 0; background: linear-gradient(135deg, #090d16 0%, #0f172a 60%, #1e293b 100%); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: relative; color: #ffffff; overflow: hidden;">
    <div style="position: absolute; inset: 0; background-image: radial-gradient(rgba(0, 168, 232, 0.15) 1px, transparent 1px); background-size: 28px 28px; opacity: 0.6; pointer-events: none;"></div>
    <div class="container" style="text-align: center; position: relative; z-index: 2;">
        <?php if (!empty($breadcrumb)): ?>
            <div style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 18px; font-weight: 600;">
                <a href="<?= SITE_URL ?>/" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#00a8e8'" onmouseout="this.style.color='#94a3b8'">Home</a>
                <?php foreach ($breadcrumb as $name => $url): ?>
                    <span style="margin: 0 8px; color: #475569;">/</span>
                    <?php if ($url): ?>
                        <a href="<?= e($url) ?>" style="color: #94a3b8; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#00a8e8'" onmouseout="this.style.color='#94a3b8'"><?= e($name) ?></a>
                    <?php else: ?>
                        <span style="color: var(--cmyk-cyan); font-weight: 800;"><?= e($name) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(0, 168, 232, 0.15); border: 1px solid rgba(0, 168, 232, 0.35); padding: 6px 16px; border-radius: 20px; color: #00a8e8; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; margin-bottom: 18px; letter-spacing: 0.8px; box-shadow: 0 4px 14px rgba(0, 168, 232, 0.2);">
            <i class="<?= e($heroIcon) ?>"></i> <?= e($heroBadge) ?>
        </div>

        <h1 class="inner-hero-title" style="font-size: 2.8rem; font-weight: 900; color: #ffffff; margin-bottom: 16px; line-height: 1.18; letter-spacing: -0.8px; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
            <?= e($heroTitle) ?>
        </h1>

        <p style="font-size: 1.12rem; color: #cbd5e1; max-width: 740px; margin: 0 auto; line-height: 1.65; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">
            <?= e($heroSub) ?>
        </p>
    </div>
</section>
