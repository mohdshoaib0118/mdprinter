<?php
/**
 * MD Printers - Front Controller & Universal Router
 */

require_once __DIR__ . '/config/config.php';
require_once INCLUDES_PATH . '/functions.php';

// Autoload Models
require_once MODELS_PATH . '/Banner.php';
require_once MODELS_PATH . '/Setting.php';
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/Service.php';
require_once MODELS_PATH . '/Portfolio.php';
require_once MODELS_PATH . '/Blog.php';
require_once MODELS_PATH . '/Testimonial.php';
require_once MODELS_PATH . '/Faq.php';
require_once MODELS_PATH . '/Enquiry.php';

// Parse Request URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Remove subfolder path if executing in subfolder (e.g. /scratch/md-printers)
if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

$url = isset($_GET['url']) ? trim($_GET['url'], '/') : trim($requestUri, '/');
$segments = explode('/', $url);
$route = $segments[0] ?? '';

// AJAX API Routes
if ($route === 'api') {
    $action = $segments[1] ?? '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            json_response(['success' => false, 'message' => 'CSRF verification failed. Please refresh the page.'], 403);
        }

        if ($action === 'quote') {
            $customerName = trim($_POST['customer_name'] ?? '');
            $email        = trim($_POST['email'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');

            if (empty($customerName) || empty($phone)) {
                json_response(['success' => false, 'message' => 'Please fill in required fields (Customer Name & Phone).'], 400);
            }

            $uploadedFile = null;
            if (isset($_FILES['artwork_file']) && $_FILES['artwork_file']['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = upload_file($_FILES['artwork_file'], 'enquiries');
                if (isset($uploadedFile['error'])) {
                    json_response(['success' => false, 'message' => $uploadedFile['error']], 400);
                }
            }

            $_POST['sent_to_whatsapp'] = 1;
            $refNo = Enquiry::create($_POST, $uploadedFile);

            if ($refNo) {
                // Construct WhatsApp prefilled message
                $waText  = "*NEW PRINTING QUOTE ENQUIRY [{$refNo}]*\n\n";
                $waText .= "👤 *Name:* " . $customerName . "\n";
                $waText .= "📱 *Phone:* " . $phone . "\n";
                if (!empty($email)) $waText .= "✉️ *Email:* " . $email . "\n";
                if (!empty($_POST['product_service'])) $waText .= "🖨️ *Service:* " . $_POST['product_service'] . "\n";
                if (!empty($_POST['quantity'])) $waText .= "📦 *Quantity:* " . $_POST['quantity'] . "\n";
                if (!empty($_POST['size'])) $waText .= "📏 *Size:* " . $_POST['size'] . "\n";
                if (!empty($_POST['material_gsm'])) $waText .= "📄 *Material:* " . $_POST['material_gsm'] . "\n";
                if (!empty($_POST['message'])) $waText .= "💬 *Notes:* " . $_POST['message'] . "\n";

                $waNumber = Setting::get('whatsapp_number', '918146141450');
                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $waNumber) . "?text=" . urlencode($waText);

                json_response([
                    'success'      => true,
                    'message'      => 'Thank you! Your quote request has been saved and sent to WhatsApp.',
                    'ref_no'       => $refNo,
                    'whatsapp_url' => $waUrl
                ]);
            } else {
                json_response(['success' => false, 'message' => 'Failed to process request. Please try again.'], 500);
            }
        } elseif ($action === 'contact') {
            $customerName = trim($_POST['customer_name'] ?? '');
            $email        = trim($_POST['email'] ?? '');
            $message      = trim($_POST['message'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');

            if (empty($customerName) || (empty($email) && empty($phone))) {
                json_response(['success' => false, 'message' => 'Name and Email/Phone are required.'], 400);
            }

            $_POST['type'] = 'contact';
            $_POST['sent_to_whatsapp'] = 1;
            $refNo = Enquiry::create($_POST);

            if ($refNo) {
                $waText  = "*NEW CONTACT ENQUIRY [{$refNo}]*\n\n";
                $waText .= "👤 *Name:* " . $customerName . "\n";
                if (!empty($phone)) $waText .= "📱 *Phone:* " . $phone . "\n";
                if (!empty($email)) $waText .= "✉️ *Email:* " . $email . "\n";
                if (!empty($message)) $waText .= "💬 *Message:* " . $message . "\n";

                $waNumber = Setting::get('whatsapp_number', '918146141450');
                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $waNumber) . "?text=" . urlencode($waText);

                json_response([
                    'success'      => true,
                    'message'      => 'Message sent successfully! Our team will contact you shortly.',
                    'ref_no'       => $refNo,
                    'whatsapp_url' => $waUrl
                ]);
            } else {
                json_response(['success' => false, 'message' => 'Database error. Please try again.'], 500);
            }
        } elseif ($action === 'callback') {
            $customerName = trim($_POST['customer_name'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');
            $preferredTime= trim($_POST['preferred_time'] ?? 'Immediate Call');

            if (empty($customerName) || empty($phone)) {
                json_response(['success' => false, 'message' => 'Name and Phone number are required for Callback.'], 400);
            }

            $_POST['type'] = 'quote';
            $_POST['product_service'] = 'Instant Callback Request (' . $preferredTime . ')';
            $_POST['sent_to_whatsapp'] = 1;
            $refNo = Enquiry::create($_POST);

            if ($refNo) {
                $waText  = "*INSTANT CALLBACK REQUEST [{$refNo}]*\n\n";
                $waText .= "👤 *Name:* " . $customerName . "\n";
                $waText .= "📱 *Phone:* " . $phone . "\n";
                $waText .= "⏰ *Preferred Time:* " . $preferredTime . "\n";

                $waNumber = Setting::get('whatsapp_number', '918146141450');
                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $waNumber) . "?text=" . urlencode($waText);

                json_response([
                    'success'      => true,
                    'message'      => 'Callback request registered! Press specialist will call you shortly.',
                    'ref_no'       => $refNo,
                    'whatsapp_url' => $waUrl
                ]);
            } else {
                json_response(['success' => false, 'message' => 'Failed to register callback. Please try again.'], 500);
            }
        } elseif ($action === 'sample-kit') {
            $customerName = trim($_POST['customer_name'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');
            $address      = trim($_POST['address'] ?? '');

            if (empty($customerName) || empty($phone) || empty($address)) {
                json_response(['success' => false, 'message' => 'Name, Phone, and Delivery Address are required.'], 400);
            }

            $_POST['type'] = 'quote';
            $_POST['product_service'] = 'Sample Kit Request Box';
            $_POST['message'] = "Delivery Address: " . $address . "\n" . ($_POST['message'] ?? '');
            $_POST['sent_to_whatsapp'] = 1;
            $refNo = Enquiry::create($_POST);

            if ($refNo) {
                $waText  = "*PRINT SAMPLE KIT REQUEST [{$refNo}]*\n\n";
                $waText .= "👤 *Name:* " . $customerName . "\n";
                $waText .= "📱 *Phone:* " . $phone . "\n";
                $waText .= "📍 *Address:* " . $address . "\n";

                $waNumber = Setting::get('whatsapp_number', '918146141450');
                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $waNumber) . "?text=" . urlencode($waText);

                json_response([
                    'success'      => true,
                    'message'      => 'Sample Kit Request received! Our press team will dispatch your kit.',
                    'ref_no'       => $refNo,
                    'whatsapp_url' => $waUrl
                ]);
            } else {
                json_response(['success' => false, 'message' => 'Failed to register request. Please try again.'], 500);
            }
        } elseif ($action === 'track') {
            $refNo = trim($_POST['ref_no'] ?? '');
            if (empty($refNo)) {
                json_response(['success' => false, 'message' => 'Please enter a valid Reference Number (e.g., MDQ-2026-XXXXXX).'], 400);
            }

            $enquiry = Enquiry::getByRefNo($refNo);
            if ($enquiry) {
                json_response([
                    'success' => true,
                    'data'    => [
                        'ref_no'        => $enquiry['ref_no'],
                        'customer_name' => $enquiry['customer_name'],
                        'type'          => strtoupper($enquiry['type']),
                        'service'       => $enquiry['product_service'] ?? 'General Print Request',
                        'status'        => $enquiry['status'],
                        'created_at'    => date('d M Y, h:i A', strtotime($enquiry['created_at'])),
                        'latest_note'   => $enquiry['latest_note'] ?? 'Enquiry logged in press CRM queue.'
                    ]
                ]);
            } else {
                json_response(['success' => false, 'message' => 'No order or quote found with reference number "' . e($refNo) . '". Please check and try again.'], 444);
            }
        }
    }
    json_response(['success' => false, 'message' => 'Invalid API Endpoint'], 404);
}

// Sitemap Generator Route
if ($route === 'sitemap' || $requestUri === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    $pages = ['', 'about', 'services', 'products', 'portfolio', 'blog', 'request-quote', 'contact', 'faq', 'privacy-policy', 'terms'];
    foreach ($pages as $p) {
        $loc = SITE_URL . ($p ? '/' . $p : '');
        echo "<url><loc>{$loc}</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>";
    }

    $services = Service::getAll(['status' => 'published']);
    foreach ($services as $s) {
        echo "<url><loc>" . SITE_URL . "/services/" . e($s['slug']) . "</loc><changefreq>monthly</changefreq><priority>0.9</priority></url>";
    }

    $products = Product::getAll(['status' => 'published']);
    foreach ($products as $pr) {
        echo "<url><loc>" . SITE_URL . "/products/" . e($pr['slug']) . "</loc><changefreq>monthly</changefreq><priority>0.9</priority></url>";
    }

    $posts = Blog::getAll(['is_published' => 1]);
    foreach ($posts as $b) {
        echo "<url><loc>" . SITE_URL . "/blog/" . e($b['slug']) . "</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>";
    }

    echo '</urlset>';
    exit;
}

// Admin Panel Router Delegation
if ($route === 'admin') {
    require ADMIN_PATH . '/index.php';
    exit;
}

// Public Frontend Routing Engine
$pageTitle       = Setting::get('default_meta_title', 'MD Printers | Premium Printing');
$metaDescription = Setting::get('default_meta_description', 'Commercial Printing, Packaging, & Digital Printing.');
$metaKeywords    = Setting::get('default_meta_keywords', 'printing, offset, packaging');

switch ($route) {
    case '':
    case 'home':
        $viewFile = VIEWS_PATH . '/home.php';
        break;

    case 'about':
        $pageTitle = 'About MD Printers | Commercial Print Facility & Capabilities';
        $viewFile = VIEWS_PATH . '/about.php';
        break;

    case 'services':
        if (!empty($segments[1])) {
            $serviceSlug = $segments[1];
            $currentService = Service::getBySlug($serviceSlug);
            if (!$currentService) {
                http_response_code(404);
                $viewFile = VIEWS_PATH . '/404.php';
            } else {
                $pageTitle = e($currentService['name']) . ' | MD Printers Services';
                $metaDescription = e($currentService['short_desc']);
                $viewFile = VIEWS_PATH . '/services/detail.php';
            }
        } else {
            $pageTitle = 'Printing Services | Commercial, Digital, & Packaging | MD Printers';
            $viewFile = VIEWS_PATH . '/services/index.php';
        }
        break;

    case 'products':
        if (!empty($segments[1])) {
            $productSlug = $segments[1];
            $currentProduct = Product::getBySlug($productSlug);
            if (!$currentProduct) {
                http_response_code(404);
                $viewFile = VIEWS_PATH . '/404.php';
            } else {
                $pageTitle = e($currentProduct['name']) . ' | MD Printers Catalogue';
                $metaDescription = e($currentProduct['short_desc']);
                $viewFile = VIEWS_PATH . '/products/detail.php';
            }
        } else {
            $pageTitle = 'Print Products Catalogue | Business Cards, Packaging, Flyers';
            $viewFile = VIEWS_PATH . '/products/index.php';
        }
        break;

    case 'portfolio':
        if (!empty($segments[1])) {
            $portfolioSlug = $segments[1];
            $currentProject = Portfolio::getBySlug($portfolioSlug);
            if (!$currentProject) {
                http_response_code(404);
                $viewFile = VIEWS_PATH . '/404.php';
            } else {
                $pageTitle = e($currentProject['title']) . ' | Case Study | MD Printers';
                $viewFile = VIEWS_PATH . '/portfolio/detail.php';
            }
        } else {
            $pageTitle = 'Print Project Portfolio & Case Studies | MD Printers';
            $viewFile = VIEWS_PATH . '/portfolio/index.php';
        }
        break;

    case 'blog':
        if (!empty($segments[1])) {
            $articleSlug = $segments[1];
            $currentArticle = Blog::getBySlug($articleSlug);
            if (!$currentArticle) {
                http_response_code(404);
                $viewFile = VIEWS_PATH . '/404.php';
            } else {
                $pageTitle = e($currentArticle['title']) . ' | MD Printers Insights';
                $metaDescription = e($currentArticle['excerpt']);
                $viewFile = VIEWS_PATH . '/blog/detail.php';
            }
        } else {
            $pageTitle = 'Commercial Print & Packaging Insights Blog | MD Printers';
            $viewFile = VIEWS_PATH . '/blog/index.php';
        }
        break;

    case 'quote':
    case 'request-quote':
        $pageTitle = 'Request Custom Print Quotation | Instant Calculator | MD Printers';
        $viewFile = VIEWS_PATH . '/quote.php';
        break;

    case 'contact':
        $pageTitle = 'Contact MD Printers | Printing Facility Location & Support';
        $viewFile = VIEWS_PATH . '/contact.php';
        break;

    case 'faq':
        $pageTitle = 'Frequently Asked Questions | MD Printers Technical Support';
        $viewFile = VIEWS_PATH . '/faq.php';
        break;

    case 'search':
        $pageTitle = 'Search Printing Products & Services | MD Printers';
        $viewFile = VIEWS_PATH . '/search.php';
        break;

    case 'privacy-policy':
        $pageTitle = 'Privacy Policy | MD Printers';
        $viewFile = VIEWS_PATH . '/privacy-policy.php';
        break;

    case 'terms':
        $pageTitle = 'Terms & Conditions | MD Printers';
        $viewFile = VIEWS_PATH . '/terms.php';
        break;

    default:
        http_response_code(404);
        $viewFile = VIEWS_PATH . '/404.php';
        break;
}

// Render the selected view
if (file_exists($viewFile)) {
    require $viewFile;
} else {
    http_response_code(404);
    echo "<h1>404 Page Not Found</h1>";
}
