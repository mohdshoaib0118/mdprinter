<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'CASE STUDY DETAILS';
$heroTitle    = $currentProject['title'];
$heroSubtitle = 'Client: ' . ($currentProject['client_name'] ?? 'Corporate Brand') . ' • Category: ' . ($currentProject['category_name'] ?? 'Print Project');
$heroIcon     = 'fas fa-camera';
$breadcrumb   = ['Portfolio' => SITE_URL . '/portfolio', $currentProject['title'] => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="height: 480px; border-radius: 20px; overflow: hidden; margin-bottom: 40px; border: 1px solid #e2e8f0; background: #0f172a; position: relative;">
                <?= render_media_element($currentProject['image'], $currentProject['video_url'] ?? '', $currentProject['title']) ?>
            </div>

            <div style="background: #f8fafc; padding: 30px; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 40px; display: flex; gap: 30px; flex-wrap: wrap; justify-content: space-between;">
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">CATEGORY</span>
                    <strong style="font-size: 1rem; color: var(--cmyk-cyan);"><?= e($currentProject['category_name'] ?? 'Print Project') ?></strong>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">SERVICES EXECUTED</span>
                    <strong style="font-size: 1rem; color: #0f172a;"><?= e($currentProject['services_used'] ?? 'Offset & Finishing') ?></strong>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">CLIENT</span>
                    <strong style="font-size: 1rem; color: #0f172a;"><?= e($currentProject['client_name']) ?></strong>
                </div>
            </div>

            <h3 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Case Study Overview</h3>
            <div style="font-size: 1.08rem; color: #475569; line-height: 1.8; margin-bottom: 40px;">
                <p><?= e($currentProject['description']) ?></p>
            </div>

            <div style="text-align: center; border-top: 1px solid #e2e8f0; padding-top: 40px;">
                <h4 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Inspired by this Project?</h4>
                <a href="<?= SITE_URL ?>/request-quote" class="btn btn-primary btn-lg"><i class="fas fa-calculator"></i> Request Similar Custom Quote</a>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

