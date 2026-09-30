<?php
/**
 * MD Printers - Admin Panel Controller & Router
 */

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

$adminView = $_GET['view'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$adminTitle = ucfirst($adminView);
$viewPath = ADMIN_PATH . '/views/' . $adminView . '.php';

if ($adminView === 'enquiries') {
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
