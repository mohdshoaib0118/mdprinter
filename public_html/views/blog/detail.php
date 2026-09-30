<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'TECHNICAL ARTICLE';
$heroTitle    = $currentArticle['title'];
$heroSubtitle = 'Written by ' . ($currentArticle['author'] ?? 'MD Printers Prepress Team') . ' • Category: ' . ($currentArticle['category_name'] ?? 'Print Insights');
$heroIcon     = 'fas fa-book-open';
$breadcrumb   = ['Blog' => SITE_URL . '/blog', $currentArticle['title'] => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div style="max-width: 840px; margin: 0 auto;">
            <div style="height: 420px; border-radius: 20px; overflow: hidden; margin-bottom: 40px; border: 1px solid #e2e8f0; background: #0f172a; position: relative;">
                <?= render_media_element($currentArticle['featured_image'], $currentArticle['video_url'] ?? '', $currentArticle['title']) ?>
            </div>

            <div style="font-size: 1.1rem; line-height: 1.85; color: #334155; margin-bottom: 50px;">
                <?= $currentArticle['content'] ?>
            </div>

            <div style="background: #f8fafc; padding: 36px; border-radius: 20px; border: 1px solid #e2e8f0; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <h4 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Need Technical Guidance on Your Print Artwork?</h4>
                <p style="font-size: 1rem; color: #64748b; margin-bottom: 24px;">Our prepress engineers check vector resolutions, bleed margins, and CMYK profiles before press runs.</p>
                <a href="<?= SITE_URL ?>/request-quote" class="btn btn-primary btn-lg" style="font-weight: 800;"><i class="fas fa-file-upload"></i> Upload Artwork & Request Quote</a>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

