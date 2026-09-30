<?php
/**
 * MD Printers - Admin Panel Controller & Router
 */

ob_start();
require_once dirname(__DIR__) . '/config/config.php';
require_once INCLUDES_PATH . '/functions.php';

// Auth Guard Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . ADMIN_URL . '/login.php');
    exit;
}

// Autoload Models
require_once MODELS_PATH . '/Setting.php';
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/Service.php';
require_once MODELS_PATH . '/Portfolio.php';
require_once MODELS_PATH . '/Blog.php';
require_once MODELS_PATH . '/Testimonial.php';
require_once MODELS_PATH . '/Faq.php';
require_once MODELS_PATH . '/Enquiry.php';
require_once MODELS_PATH . '/Banner.php';
require_once MODELS_PATH . '/Customer.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/ProductGroup.php';
require_once MODELS_PATH . '/PricingEngine.php';
require_once MODELS_PATH . '/Quote.php';
require_once MODELS_PATH . '/Cart.php';
require_once MODELS_PATH . '/Order.php';
require_once MODELS_PATH . '/Artwork.php';
require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/PaymentService.php';
require_once MODELS_PATH . '/NotificationService.php';

$adminView = $_GET['view'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$adminTitle = ucfirst($adminView);
$viewPath = ADMIN_PATH . '/views/' . $adminView . '.php';

if ($adminView === 'orders') {
    if ($action === 'pdf_invoice' && $id > 0) {
        $order = Order::findById($id);
        if ($order) {
            Invoice::streamPdf($order, true);
            exit;
        }
    }
    $adminTitle = 'B2B Production Orders';
    $viewPath = ($action === 'detail') ? ADMIN_PATH . '/views/orders/detail.php' : ADMIN_PATH . '/views/orders/index.php';
} elseif ($adminView === 'categories') {
    $adminTitle = 'Printing Categories Management';
    $viewPath = ADMIN_PATH . '/views/categories/index.php';
} elseif ($adminView === 'product-groups') {
    $adminTitle = 'Product Groups / Subcategories';
    $viewPath = ADMIN_PATH . '/views/product-groups/index.php';
} elseif ($adminView === 'payments') {
    $adminTitle = 'B2B Payments & Credit Ledger';
    $viewPath = ADMIN_PATH . '/views/payments/index.php';
} elseif ($adminView === 'notifications') {
    $adminTitle = 'Notification Audit Logs';
    $viewPath = ADMIN_PATH . '/views/notifications/index.php';
} elseif ($adminView === 'customers') {
    $adminTitle = 'B2B Customers Management';
    $viewPath = ($action === 'detail') ? ADMIN_PATH . '/views/customers/detail.php' : ADMIN_PATH . '/views/customers/index.php';
} elseif ($adminView === 'quotations') {
    $adminTitle = 'Formal B2B Quotations';
    $viewPath = ($action === 'detail') ? ADMIN_PATH . '/views/quotations/detail.php' : ADMIN_PATH . '/views/quotations/index.php';
} elseif ($adminView === 'pricing') {
    $adminTitle = 'B2B Pricing Rules & Options Engine';
    $viewPath = ADMIN_PATH . '/views/pricing/index.php';
} elseif ($adminView === 'enquiries') {
    $viewPath = ($action === 'detail') ? ADMIN_PATH . '/views/enquiries/detail.php' : ADMIN_PATH . '/views/enquiries/index.php';
} elseif ($adminView === 'banners') {
    $adminTitle = 'Hero Banner Sliders';
    $viewPath = ($action === 'create' || $action === 'edit') ? ADMIN_PATH . '/views/banners/edit.php' : (($action === 'delete') ? ADMIN_PATH . '/views/banners/delete.php' : ADMIN_PATH . '/views/banners/index.php');
} elseif ($adminView === 'products') {
    $viewPath = ($action === 'create' || $action === 'edit') ? ADMIN_PATH . '/views/products/edit.php' : ADMIN_PATH . '/views/products/index.php';
} elseif ($adminView === 'services') {
    $viewPath = ($action === 'create' || $action === 'edit') ? ADMIN_PATH . '/views/services/edit.php' : ADMIN_PATH . '/views/services/index.php';
} elseif ($adminView === 'portfolio') {
    $viewPath = ($action === 'create' || $action === 'edit') ? ADMIN_PATH . '/views/portfolio/edit.php' : ADMIN_PATH . '/views/portfolio/index.php';
} elseif ($adminView === 'blog') {
    $viewPath = ($action === 'create' || $action === 'edit') ? ADMIN_PATH . '/views/blog/edit.php' : ADMIN_PATH . '/views/blog/index.php';
} elseif ($adminView === 'testimonials') {
    $viewPath = ADMIN_PATH . '/views/testimonials/index.php';
} elseif ($adminView === 'faqs') {
    $viewPath = ADMIN_PATH . '/views/faqs/index.php';
} elseif ($adminView === 'media') {
    $viewPath = ADMIN_PATH . '/views/media/index.php';
}

include ADMIN_PATH . '/includes/header.php';

if (file_exists($viewPath)) {
    require $viewPath;
} else {
    require ADMIN_PATH . '/views/dashboard.php';
}

include ADMIN_PATH . '/includes/footer.php';
