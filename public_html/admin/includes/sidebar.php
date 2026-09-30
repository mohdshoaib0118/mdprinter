<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="<?= ADMIN_URL ?>" class="brand-logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="<?= SITE_URL ?>/assets/images/logo-icon.svg" alt="MD Printers" style="height: 36px; width: 36px;">
            <span style="font-weight: 900; color: #ffffff; font-size: 1.1rem; letter-spacing: 1px;">MD CONTROL</span>
        </a>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="<?= ADMIN_URL ?>?view=dashboard" class="sidebar-link <?= ($adminView === 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-chart-pie" style="color: var(--cmyk-cyan);"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=banners" class="sidebar-link <?= ($adminView === 'banners') ? 'active' : '' ?>">
                <i class="fas fa-images" style="color: var(--cmyk-gold);"></i> <span>Hero Banners</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=enquiries" class="sidebar-link <?= ($adminView === 'enquiries') ? 'active' : '' ?>">
                <i class="fas fa-file-invoice-dollar" style="color: var(--cmyk-magenta);"></i> <span>Quotes & Leads</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=products" class="sidebar-link <?= ($adminView === 'products') ? 'active' : '' ?>">
                <i class="fas fa-boxes" style="color: var(--cmyk-yellow);"></i> <span>Products Catalogue</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=services" class="sidebar-link <?= ($adminView === 'services') ? 'active' : '' ?>">
                <i class="fas fa-industry" style="color: var(--cmyk-cyan);"></i> <span>Print Services</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=portfolio" class="sidebar-link <?= ($adminView === 'portfolio') ? 'active' : '' ?>">
                <i class="fas fa-camera-retro" style="color: var(--cmyk-magenta);"></i> <span>Portfolio Projects</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=blog" class="sidebar-link <?= ($adminView === 'blog') ? 'active' : '' ?>">
                <i class="fas fa-newspaper" style="color: var(--cmyk-yellow);"></i> <span>Blog Articles</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=testimonials" class="sidebar-link <?= ($adminView === 'testimonials') ? 'active' : '' ?>">
                <i class="fas fa-quote-right" style="color: var(--cmyk-cyan);"></i> <span>Client Reviews</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=faqs" class="sidebar-link <?= ($adminView === 'faqs') ? 'active' : '' ?>">
                <i class="fas fa-question-circle" style="color: var(--cmyk-magenta);"></i> <span>FAQs</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=media" class="sidebar-link <?= ($adminView === 'media') ? 'active' : '' ?>">
                <i class="fas fa-folder-open" style="color: var(--cmyk-yellow);"></i> <span>Media Library</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=settings" class="sidebar-link <?= ($adminView === 'settings') ? 'active' : '' ?>">
                <i class="fas fa-sliders-h" style="color: var(--cmyk-cyan);"></i> <span>Site Settings</span>
            </a>
        </li>
    </ul>
</aside>

