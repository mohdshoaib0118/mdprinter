<?php
/**
 * MD Printers - Admin Sidebar Layout
 */
$adminUser = current_admin_user();
$currentAdminPage = $adminActivePage ?? 'dashboard';
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="<?= SITE_URL ?>/assets/images/logo-icon.svg" alt="MD" style="width: 34px; height: 34px; object-fit: contain;">
        <span>MD CONTROL</span>
    </div>

    <div class="sidebar-menu">
        <a href="<?= ADMIN_URL ?>/dashboard.php" class="menu-item <?= $currentAdminPage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="menu-category">LEAD MANAGEMENT</div>
        <a href="<?= ADMIN_URL ?>/enquiries/index.php" class="menu-item <?= $currentAdminPage === 'enquiries' ? 'active' : '' ?>">
            <i class="fas fa-file-invoice-dollar"></i> Enquiries & Quotes
        </a>

        <div class="menu-category">CATALOGUE MANAGEMENT</div>
        <a href="<?= ADMIN_URL ?>/products/index.php" class="menu-item <?= $currentAdminPage === 'products' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="<?= ADMIN_URL ?>/services/index.php" class="menu-item <?= $currentAdminPage === 'services' ? 'active' : '' ?>">
            <i class="fas fa-cogs"></i> Print Services
        </a>
        <a href="<?= ADMIN_URL ?>/portfolio/index.php" class="menu-item <?= $currentAdminPage === 'portfolio' ? 'active' : '' ?>">
            <i class="fas fa-camera"></i> Portfolio Case Studies
        </a>

        <div class="menu-category">CONTENT & CMS</div>
        <a href="<?= ADMIN_URL ?>/banners/index.php" class="menu-item <?= $currentAdminPage === 'banners' ? 'active' : '' ?>">
            <i class="fas fa-images"></i> Hero Banners
        </a>
        <a href="<?= ADMIN_URL ?>/blog/index.php" class="menu-item <?= $currentAdminPage === 'blog' ? 'active' : '' ?>">
            <i class="fas fa-newspaper"></i> Blog Articles
        </a>
        <a href="<?= ADMIN_URL ?>/testimonials/index.php" class="menu-item <?= $currentAdminPage === 'testimonials' ? 'active' : '' ?>">
            <i class="fas fa-star"></i> Testimonials
        </a>
        <a href="<?= ADMIN_URL ?>/faqs/index.php" class="menu-item <?= $currentAdminPage === 'faqs' ? 'active' : '' ?>">
            <i class="fas fa-question-circle"></i> FAQs
        </a>
        <a href="<?= ADMIN_URL ?>/media/index.php" class="menu-item <?= $currentAdminPage === 'media' ? 'active' : '' ?>">
            <i class="fas fa-images"></i> Media Library
        </a>

        <div class="menu-category">SYSTEM & SETTINGS</div>
        <a href="<?= ADMIN_URL ?>/settings/index.php" class="menu-item <?= $currentAdminPage === 'settings' ? 'active' : '' ?>">
            <i class="fas fa-sliders-h"></i> Site Settings & CMS
        </a>
        <a href="<?= SITE_URL ?>" target="_blank" class="menu-item">
            <i class="fas fa-external-link-alt"></i> View Live Website
        </a>
        <a href="<?= ADMIN_URL ?>/logout.php" class="menu-item" style="color: #F87171;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>
