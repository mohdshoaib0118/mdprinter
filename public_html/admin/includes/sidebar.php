<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="<?= ADMIN_URL ?>" class="brand-logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="<?= SITE_URL ?>/assets/images/logo-official.png" alt="MD Printers" style="height: 38px; width: 38px; object-fit: contain; border-radius: 50%;">
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
            <a href="<?= ADMIN_URL ?>?view=orders" class="sidebar-link <?= ($adminView === 'orders') ? 'active' : '' ?>">
                <i class="fas fa-dolly" style="color: var(--cmyk-cyan);"></i> <span>B2B Production Orders</span>
                <?php
                    require_once MODELS_PATH . '/Order.php';
                    $pendingOrdersCount = count(Order::getAll(['status' => 'pending']));
                    if ($pendingOrdersCount > 0):
                ?>
                    <span style="background: #ef4444; color: #fff; font-size: 0.7rem; font-weight: 900; padding: 2px 7px; border-radius: 10px; margin-left: auto;"><?= $pendingOrdersCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=payments" class="sidebar-link <?= ($adminView === 'payments') ? 'active' : '' ?>">
                <i class="fas fa-file-invoice-dollar" style="color: var(--cmyk-magenta);"></i> <span>Payments & Ledger</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=notifications" class="sidebar-link <?= ($adminView === 'notifications') ? 'active' : '' ?>">
                <i class="fas fa-bell" style="color: var(--cmyk-yellow);"></i> <span>Notifications Log</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=banners" class="sidebar-link <?= ($adminView === 'banners') ? 'active' : '' ?>">
                <i class="fas fa-images" style="color: var(--cmyk-gold);"></i> <span>Hero Banners</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=customers" class="sidebar-link <?= ($adminView === 'customers') ? 'active' : '' ?>">
                <i class="fas fa-users-cog" style="color: var(--cmyk-cyan);"></i> <span>B2B Customers</span>
                <?php
                    require_once MODELS_PATH . '/Customer.php';
                    $pendingCustomersCount = Customer::countAll(['status' => 'pending']);
                    if ($pendingCustomersCount > 0):
                ?>
                    <span style="background: #eab308; color: #000; font-size: 0.7rem; font-weight: 900; padding: 2px 7px; border-radius: 10px; margin-left: auto;"><?= $pendingCustomersCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=quotations" class="sidebar-link <?= ($adminView === 'quotations') ? 'active' : '' ?>">
                <i class="fas fa-file-signature" style="color: var(--cmyk-magenta);"></i> <span>B2B Quotations</span>
                <?php
                    require_once MODELS_PATH . '/Quote.php';
                    $requestedQuotesCount = count(Quote::getAll(['status' => 'requested']));
                    if ($requestedQuotesCount > 0):
                ?>
                    <span style="background: #ef4444; color: #fff; font-size: 0.7rem; font-weight: 900; padding: 2px 7px; border-radius: 10px; margin-left: auto;"><?= $requestedQuotesCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=pricing" class="sidebar-link <?= ($adminView === 'pricing') ? 'active' : '' ?>">
                <i class="fas fa-tags" style="color: var(--cmyk-yellow);"></i> <span>Pricing Rules & Options</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=enquiries" class="sidebar-link <?= ($adminView === 'enquiries') ? 'active' : '' ?>">
                <i class="fas fa-file-invoice-dollar" style="color: var(--cmyk-cyan);"></i> <span>Quick Leads</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=categories" class="sidebar-link <?= ($adminView === 'categories') ? 'active' : '' ?>">
                <i class="fas fa-layer-group" style="color: var(--cmyk-cyan);"></i> <span>Printing Categories</span>
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>?view=product-groups" class="sidebar-link <?= ($adminView === 'product-groups') ? 'active' : '' ?>">
                <i class="fas fa-sitemap" style="color: var(--cmyk-magenta);"></i> <span>Product Subcategories</span>
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
