<?php 
include INCLUDES_PATH . '/header.php'; 

$heroBadge    = 'DIRECTORY';
$heroTitle    = 'Website Sitemap';
$heroSubtitle = 'Complete directory of all pages, catalogues, and services on MD Printers.';
$heroIcon     = 'fas fa-sitemap';
$breadcrumb   = ['Sitemap' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px;">
        <div style="background: #f8fafc; padding: 32px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h3 style="color: var(--cmyk-cyan); font-size: 1.2rem; font-weight: 800; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-compass"></i> Main Navigation Pages
            </h3>
            <ul style="line-height: 2.2; font-weight: 700; color: #0f172a; list-style: none; padding: 0;">
                <li><a href="<?= SITE_URL ?>/" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Home Page</a></li>
                <li><a href="<?= SITE_URL ?>/about" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> About Us & Facility</a></li>
                <li><a href="<?= SITE_URL ?>/services" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Services & Press Suite</a></li>
                <li><a href="<?= SITE_URL ?>/products" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Products Catalogue</a></li>
                <li><a href="<?= SITE_URL ?>/portfolio" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Portfolio & Case Studies</a></li>
                <li><a href="<?= SITE_URL ?>/blog" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Blog & Print Guides</a></li>
                <li><a href="<?= SITE_URL ?>/faq" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Frequently Asked Questions</a></li>
                <li><a href="<?= SITE_URL ?>/contact" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Contact Us & Map</a></li>
                <li><a href="<?= SITE_URL ?>/request-quote" style="color: #0f172a; text-decoration: none;"><i class="fas fa-chevron-right" style="color: var(--cmyk-cyan); font-size: 0.75rem; margin-right: 8px;"></i> Request Custom Quote</a></li>
            </ul>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

