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
require_once MODELS_PATH . '/Customer.php';
require_once MODELS_PATH . '/PricingEngine.php';
require_once MODELS_PATH . '/Quote.php';
require_once MODELS_PATH . '/Cart.php';
require_once MODELS_PATH . '/Order.php';
require_once MODELS_PATH . '/Artwork.php';
require_once MODELS_PATH . '/PaymentService.php';
require_once MODELS_PATH . '/Invoice.php';

// Parse Request URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Remove subfolder path if executing in subfolder
if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

$url = isset($_GET['url']) ? trim($_GET['url'], '/') : trim($requestUri, '/');
$segments = explode('/', $url);
$route = $segments[0] ?? '';

// AJAX API Routes
if ($route === 'api') {
    $action = $segments[1] ?? '';
    
    if ($action === 'payment') {
        $subAction = $segments[2] ?? '';
        if ($subAction === 'webhook') {
            require_once MODELS_PATH . '/PaymentService.php';
            if (!PaymentService::isGatewayEnabled()) {
                json_response(['status' => 'disabled', 'message' => 'Online payment gateway is in configuration-pending / disabled state.'], 200);
            }
            json_response(['status' => 'received'], 200);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            json_response(['success' => false, 'message' => 'CSRF verification failed. Please refresh the page.'], 403);
        }

        // B2B Pricing, Orders & Configurator API Endpoints
        if ($action === 'b2b') {
            $subAction = $segments[2] ?? '';

            if ($subAction === 'calculate-price') {
                $productId = (int)($_POST['product_id'] ?? 0);
                $quantity = (int)($_POST['quantity'] ?? 100);
                $selectedOptions = $_POST['options'] ?? [];
                $customerId = is_customer_logged_in() ? (int)$_SESSION['customer_id'] : null;

                if (!$productId || $quantity <= 0) {
                    json_response(['success' => false, 'message' => 'Invalid product or quantity specified.'], 400);
                }

                $calculation = PricingEngine::calculatePrice($productId, $quantity, $selectedOptions, $customerId);
                json_response(['success' => true, 'data' => $calculation]);

            } elseif ($subAction === 'cart-add') {
                $productId = (int)($_POST['product_id'] ?? 0);
                $quantity = (int)($_POST['quantity'] ?? 100);
                $selectedOptions = $_POST['options'] ?? [];
                $orderName = trim($_POST['order_name'] ?? '');
                $pressline = trim($_POST['pressline'] ?? '');
                $notes = trim($_POST['notes'] ?? '');
                $artworkOption = $_POST['artwork_option'] ?? 'upload_later';
                $customerId = is_customer_logged_in() ? (int)$_SESSION['customer_id'] : null;

                $db = Database::getInstance();
                $stmt = $db->prepare("SELECT name FROM products WHERE id = :id");
                $stmt->execute(['id' => $productId]);
                $productName = $stmt->fetchColumn() ?: 'Custom Print Job';

                $calculation = PricingEngine::calculatePrice($productId, $quantity, $selectedOptions, $customerId);

                $availableOptions = PricingEngine::getProductOptions($productId);
                $configSnapshot = ['Quantity' => $quantity . ' units'];

                if (!empty($orderName)) {
                    $configSnapshot['Job Name'] = $orderName;
                }
                if (!empty($pressline)) {
                    $configSnapshot['Print Pressline'] = $pressline;
                }
                if (!empty($notes)) {
                    $configSnapshot['Special Instructions'] = $notes;
                }

                foreach ($availableOptions as $opt) {
                    $selValId = $selectedOptions[$opt['id']] ?? null;
                    if ($selValId && !empty($opt['values'])) {
                        foreach ($opt['values'] as $v) {
                            if ((int)$v['id'] === (int)$selValId) {
                                $configSnapshot[$opt['option_name']] = $v['value_name'];
                                break;
                            }
                        }
                    }
                }

                // Handle secure artwork upload if provided
                if ($artworkOption === 'upload_now' && isset($_FILES['artwork_file']) && $_FILES['artwork_file']['error'] === UPLOAD_ERR_OK) {
                    $up = upload_file($_FILES['artwork_file'], 'artworks');
                    if (!empty($up['filepath'])) {
                        $configSnapshot['Artwork Status'] = 'Uploaded Online';
                        $configSnapshot['Artwork File'] = $up['filepath'];
                    } else {
                        $configSnapshot['Artwork Status'] = 'Upload Failed: ' . ($up['error'] ?? 'Unknown error');
                    }
                } else {
                    $configSnapshot['Artwork Status'] = ucfirst(str_replace('_', ' ', $artworkOption));
                }

                if (Cart::add($productId, $productName, $quantity, $configSnapshot, $calculation, $customerId)) {
                    json_response(['success' => true, 'message' => 'Product configuration added to your B2B cart!']);
                } else {
                    json_response(['success' => false, 'message' => 'Failed to add item to cart.'], 500);
                }

            } elseif ($subAction === 'cart-remove') {
                $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
                $customerId = is_customer_logged_in() ? (int)$_SESSION['customer_id'] : null;

                if (Cart::remove($cartItemId, $customerId)) {
                    set_flash('success', 'Item removed from your B2B cart.');
                } else {
                    set_flash('danger', 'Failed to remove item.');
                }
                redirect_to(SITE_URL . '/dashboard/cart');

            } elseif ($subAction === 'order-place') {
                require_customer_login();
                $customerId = (int)$_SESSION['customer_id'];
                $cartItems = Cart::getItems($customerId);

                if (empty($cartItems)) {
                    set_flash('danger', 'Your cart is empty.');
                    redirect_to(SITE_URL . '/products');
                }

                // Determine Address
                $selectedAddressId = (int)($_POST['selected_address_id'] ?? 0);
                $shippingAddress = null;

                if ($selectedAddressId > 0) {
                    $addresses = Customer::getAddresses($customerId);
                    foreach ($addresses as $a) {
                        if ((int)$a['id'] === $selectedAddressId) {
                            $shippingAddress = $a;
                            break;
                        }
                    }
                }
                if (!$shippingAddress) {
                    $shippingAddress = $_POST['shipping'] ?? [
                        'address_line1' => $_SESSION['customer_address'] ?? 'Main Office',
                        'city' => $_SESSION['customer_city'] ?? 'New Delhi',
                        'state' => 'Delhi',
                        'pincode' => '110020'
                    ];
                }

                $itemsForOrder = [];
                foreach ($cartItems as $ci) {
                    $itemsForOrder[] = [
                        'product_id' => $ci['product_id'],
                        'product_name' => $ci['product_name_snapshot'],
                        'quantity' => $ci['quantity'],
                        'options' => [] // Options recalculated via stored configuration
                    ];
                }

                $paymentMethod = $_POST['payment_method'] ?? 'bank_transfer';

                // Check credit limit if credit chosen
                if ($paymentMethod === 'approved_credit') {
                    // Total calculated inside createOrder
                }

                $orderResult = Order::createOrder([
                    'customer_id' => $customerId,
                    'items' => $itemsForOrder,
                    'shipping_address' => $shippingAddress,
                    'billing_address' => $shippingAddress,
                    'payment_method' => $paymentMethod,
                    'customer_notes' => trim($_POST['customer_notes'] ?? '')
                ]);

                if ($orderResult) {
                    Cart::clear($customerId);
                    set_flash('success', 'Order #' . $orderResult['order_number'] . ' placed successfully! You can upload your artwork files now.');
                    redirect_to(SITE_URL . '/dashboard/orders?id=' . $orderResult['order_id']);
                } else {
                    set_flash('danger', 'Failed to place order. Please try again.');
                    redirect_to(SITE_URL . '/checkout');
                }

            } elseif ($subAction === 'artwork-upload') {
                require_customer_login();
                $customerId = (int)$_SESSION['customer_id'];
                $orderId = (int)($_POST['order_id'] ?? 0);
                $orderItemId = (int)($_POST['order_item_id'] ?? 0);
                $comment = trim($_POST['customer_comment'] ?? '');

                if (isset($_FILES['artwork_file'])) {
                    $res = Artwork::uploadArtwork($orderId, $orderItemId, $customerId, $_FILES['artwork_file'], $comment);
                    if ($res['success']) {
                        set_flash('success', $res['message']);
                    } else {
                        set_flash('danger', $res['message']);
                    }
                } else {
                    set_flash('danger', 'No file uploaded.');
                }

                redirect_to(SITE_URL . '/dashboard/orders?id=' . $orderId);

            } elseif ($subAction === 'quote-request') {
                if (!is_customer_logged_in()) {
                    json_response(['success' => false, 'message' => 'Please log in or register a B2B account to request formal quotations.'], 401);
                }

                $customerId = (int)$_SESSION['customer_id'];
                $source = $_POST['source'] ?? 'configurator';
                $quoteItems = [];
                $grandSubtotal = 0.00;
                $grandTax = 0.00;
                $grandTotal = 0.00;

                if ($source === 'cart') {
                    $cartItems = Cart::getItems($customerId);
                    if (empty($cartItems)) {
                        json_response(['success' => false, 'message' => 'Your B2B cart is empty.'], 400);
                    }
                    foreach ($cartItems as $ci) {
                        $breakdown = $ci['price_breakdown'];
                        $quoteItems[] = [
                            'product_id' => $ci['product_id'],
                            'product_name_snapshot' => $ci['product_name_snapshot'],
                            'quantity' => $ci['quantity'],
                            'configuration_json' => $ci['configuration'],
                            'price_breakdown_json' => $breakdown,
                            'unit_price' => round(($breakdown['final_total'] ?? $ci['subtotal']) / max(1, $ci['quantity']), 2),
                            'subtotal' => $breakdown['final_total'] ?? $ci['subtotal']
                        ];
                        $grandSubtotal += ($breakdown['net_subtotal'] ?? $ci['subtotal']);
                        $grandTax += ($breakdown['tax_amount'] ?? 0);
                        $grandTotal += ($breakdown['final_total'] ?? $ci['subtotal']);
                    }
                    Cart::clear($customerId);
                } else {
                    $productId = (int)($_POST['product_id'] ?? 0);
                    $quantity = (int)($_POST['quantity'] ?? 100);
                    $selectedOptions = $_POST['options'] ?? [];

                    $db = Database::getInstance();
                    $stmt = $db->prepare("SELECT name FROM products WHERE id = :id");
                    $stmt->execute(['id' => $productId]);
                    $productName = $stmt->fetchColumn() ?: 'Custom Print Job';

                    $calculation = PricingEngine::calculatePrice($productId, $quantity, $selectedOptions, $customerId);

                    $availableOptions = PricingEngine::getProductOptions($productId);
                    $configSnapshot = ['Quantity' => $quantity . ' units'];
                    foreach ($availableOptions as $opt) {
                        $selValId = $selectedOptions[$opt['id']] ?? null;
                        if ($selValId && !empty($opt['values'])) {
                            foreach ($opt['values'] as $v) {
                                if ((int)$v['id'] === (int)$selValId) {
                                    $configSnapshot[$opt['option_name']] = $v['value_name'];
                                    break;
                                }
                            }
                        }
                    }

                    $quoteItems[] = [
                        'product_id' => $productId,
                        'product_name_snapshot' => $productName,
                        'quantity' => $quantity,
                        'configuration_json' => $configSnapshot,
                        'price_breakdown_json' => $calculation,
                        'unit_price' => round($calculation['final_total'] / max(1, $quantity), 2),
                        'subtotal' => $calculation['final_total']
                    ];

                    $grandSubtotal = $calculation['net_subtotal'];
                    $grandTax = $calculation['tax_amount'];
                    $grandTotal = $calculation['final_total'];
                }

                $quoteId = Quote::create([
                    'customer_id' => $customerId,
                    'subtotal' => $grandSubtotal,
                    'discount' => 0.00,
                    'tax' => $grandTax,
                    'shipping' => 0.00,
                    'total' => $grandTotal,
                    'status' => 'requested',
                    'notes' => trim($_POST['notes'] ?? ''),
                    'items' => $quoteItems
                ]);

                if ($quoteId) {
                    json_response(['success' => true, 'message' => 'Formal quotation requested successfully! View and track it in your customer portal.']);
                } else {
                    json_response(['success' => false, 'message' => 'Failed to create quotation request.'], 500);
                }

            } elseif ($subAction === 'quote-accept') {
                require_customer_login();
                $quoteId = (int)($_POST['quote_id'] ?? 0);
                $customerId = (int)$_SESSION['customer_id'];

                if (Quote::updateStatus($quoteId, 'accepted', $customerId)) {
                    set_flash('success', 'Quotation accepted! Our press team will process your configuration into an order.');
                } else {
                    set_flash('danger', 'Failed to update quotation status.');
                }
                redirect_to(SITE_URL . '/dashboard/quotes?id=' . $quoteId);

            } elseif ($subAction === 'reorder') {
                require_customer_login();
                $customerId = (int)$_SESSION['customer_id'];
                $orderId = (int)($_POST['order_id'] ?? 0);

                $res = Order::reorderToCart($orderId, $customerId);
                if ($res['success']) {
                    set_flash('success', $res['message']);
                    redirect_to(SITE_URL . '/dashboard/cart');
                } else {
                    set_flash('danger', $res['message']);
                    redirect_to(SITE_URL . '/dashboard/orders');
                }

            } elseif ($subAction === 'payment-webhook') {
                require_once MODELS_PATH . '/PaymentService.php';
                if (!PaymentService::isGatewayEnabled()) {
                    json_response(['status' => 'disabled', 'message' => 'Online payment gateway is in configuration-pending / disabled state.'], 200);
                }
                json_response(['status' => 'received'], 200);

            } elseif ($subAction === 'quote-reject') {
                require_customer_login();
                $quoteId = (int)($_POST['quote_id'] ?? 0);
                $customerId = (int)$_SESSION['customer_id'];

                if (Quote::updateStatus($quoteId, 'rejected', $customerId)) {
                    set_flash('info', 'Quotation marked as rejected.');
                } else {
                    set_flash('danger', 'Failed to update quotation status.');
                }
                redirect_to(SITE_URL . '/dashboard/quotes?id=' . $quoteId);
            }
        }

        // Public Track Order / Quote Endpoint
        if ($action === 'track') {
            $refNo = trim($_POST['ref_no'] ?? '');
            if (empty($refNo)) {
                json_response(['success' => false, 'message' => 'Please enter a valid Reference or Order Number (e.g., MDP-2026-XXXXXX or MDQ-2026-XXXXXX).'], 400);
            }

            // Check if Order Number MDP-
            if (strpos(strtoupper($refNo), 'MDP-') === 0) {
                $order = Order::findByOrderNumber($refNo);
                if ($order) {
                    json_response([
                        'success' => true,
                        'data' => [
                            'ref_no' => $order['order_number'],
                            'customer_name' => $order['company_name'] ?: ($order['contact_person'] ?: 'B2B Client'),
                            'type' => 'PRODUCTION ORDER',
                            'service' => 'Commercial Print Order',
                            'status' => ucwords(str_replace('_', ' ', $order['status'])),
                            'created_at' => date('d M Y, h:i A', strtotime($order['placed_at'])),
                            'latest_note' => $order['tracking_number'] ? 'Dispatched via ' . e($order['courier_name']) . ' (AWB: ' . e($order['tracking_number']) . ')' : 'Order in press queue.'
                        ]
                    ]);
                }
            }

            // Fallback to Quote Lead Enquiry
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

        // Public Quick Quote & Lead Request API Endpoint
        if ($action === 'quote' || $action === 'enquiry') {
            $uploadedFile = null;
            if (isset($_FILES['artwork_file']) && $_FILES['artwork_file']['error'] === UPLOAD_ERR_OK) {
                $fileRes = upload_file($_FILES['artwork_file'], 'enquiries');
                if ($fileRes && empty($fileRes['error'])) {
                    $uploadedFile = [
                        'filename' => $fileRes['filename'],
                        'original_name' => $_FILES['artwork_file']['name'],
                        'filepath' => $fileRes['filepath'],
                        'file_size' => $_FILES['artwork_file']['size']
                    ];
                }
            }

            $refNo = Enquiry::create($_POST, $uploadedFile);
            if ($refNo) {
                json_response([
                    'success' => true,
                    'message' => 'Thank you! Your quotation request (Ref: #' . $refNo . ') has been logged successfully. Our press team will reach out to you shortly.',
                    'ref_no'  => $refNo
                ]);
            } else {
                json_response(['success' => false, 'message' => 'Failed to submit quote enquiry. Please try again.'], 500);
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
            $currentPortfolio = Portfolio::getBySlug($portfolioSlug);
            if (!$currentPortfolio) {
                http_response_code(404);
                $viewFile = VIEWS_PATH . '/404.php';
            } else {
                $pageTitle = e($currentPortfolio['title']) . ' | MD Printers Portfolio';
                $viewFile = VIEWS_PATH . '/portfolio/detail.php';
            }
        } else {
            $pageTitle = 'Print Work Showcase & Portfolio | MD Printers';
            $viewFile = VIEWS_PATH . '/portfolio/index.php';
        }
        break;

    case 'blog':
        if (!empty($segments[1])) {
            $blogSlug = $segments[1];
            $currentBlog = Blog::getBySlug($blogSlug);
            $currentArticle = $currentBlog;
            if (!$currentBlog) {
                http_response_code(404);
                $viewFile = VIEWS_PATH . '/404.php';
            } else {
                $pageTitle = e($currentBlog['title']) . ' | MD Printers Blog';
                $viewFile = VIEWS_PATH . '/blog/detail.php';
            }
        } else {
            $pageTitle = 'Printing Articles & Insights | MD Printers Blog';
            $viewFile = VIEWS_PATH . '/blog/index.php';
        }
        break;

    case 'order':
        $subAction = $segments[1] ?? 'categories';
        if ($subAction === 'category' && !empty($segments[2])) {
            $matchParams = ['slug' => $segments[2]];
            $pageTitle = 'Product Subcategories | MD Printers B2B Order';
            $viewFile = VIEWS_PATH . '/order/category_detail.php';
        } elseif ($subAction === 'group' && !empty($segments[2])) {
            $matchParams = ['slug' => $segments[2]];
            $pageTitle = 'Print Specifications & Variants | MD Printers B2B Order';
            $viewFile = VIEWS_PATH . '/order/group_detail.php';
        } elseif ($subAction === 'product' && !empty($segments[2])) {
            $matchParams = ['slug' => $segments[2]];
            $pageTitle = 'B2B Product Configurator & Pricing | MD Printers';
            $viewFile = VIEWS_PATH . '/order/configurator.php';
        } else {
            $pageTitle = 'Launch New B2B Order - Printing Categories | MD Printers';
            $viewFile = VIEWS_PATH . '/order/categories.php';
        }
        break;

    case 'quote':
    case 'request-quote':
        $pageTitle = 'Request Custom Printing Quotation | MD Printers';
        $viewFile = VIEWS_PATH . '/quote.php';
        break;

    case 'faq':
        $pageTitle = 'Frequently Asked Questions | MD Printers';
        $viewFile = VIEWS_PATH . '/faq.php';
        break;

    case 'privacy-policy':
        $pageTitle = 'Privacy Policy | MD Printers';
        $viewFile = VIEWS_PATH . '/privacy-policy.php';
        break;

    case 'terms':
        $pageTitle = 'Terms & Conditions | MD Printers';
        $viewFile = VIEWS_PATH . '/terms.php';
        break;

    case 'search':
        $pageTitle = 'Search Results | MD Printers';
        $viewFile = VIEWS_PATH . '/search.php';
        break;

    case 'checkout':
        require_customer_login();
        $pageTitle = 'B2B Order Checkout | MD Printers';
        $viewFile = VIEWS_PATH . '/customer/checkout.php';
        break;

    case 'contact':
        $pageTitle = 'Contact MD Printers | Printing Facility Location & Support';
        $viewFile = VIEWS_PATH . '/contact.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                set_flash('danger', 'CSRF security token failed. Please try again.');
                redirect_to(SITE_URL . '/register');
            }
            $reg = Customer::register($_POST);
            if ($reg['success']) {
                set_flash('success', $reg['message']);
                redirect_to(SITE_URL . '/login');
            } else {
                set_flash('danger', $reg['message']);
            }
        }
        $pageTitle = 'Register B2B Account | MD Printers Member Portal';
        $viewFile = VIEWS_PATH . '/auth/register.php';
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                set_flash('danger', 'CSRF security token failed. Please try again.');
                redirect_to(SITE_URL . '/login');
            }
            $log = Customer::login($_POST['email_or_mobile'] ?? '', $_POST['password'] ?? '');
            if ($log['success']) {
                customer_login_session($log['customer']);
                set_flash('success', 'Logged in successfully! Welcome to your MD Printers member portal.');
                redirect_to(SITE_URL . '/dashboard');
            } else {
                $flashType = ($log['status_code'] ?? '') === 'pending' ? 'info' : 'danger';
                set_flash($flashType, $log['message']);
            }
        }
        $pageTitle = 'Member Portal Login | MD Printers B2B';
        $viewFile = VIEWS_PATH . '/auth/login.php';
        break;

    case 'logout':
        customer_logout_session();
        set_flash('info', 'You have been safely logged out of your customer account.');
        redirect_to(SITE_URL . '/login');
        break;

    case 'dashboard':
        require_customer_login();
        $sub = $segments[1] ?? '';

        if ($sub === 'profile') {
            $pageTitle = 'Company Profile | Customer Portal | MD Printers';
            $viewFile = VIEWS_PATH . '/customer/profile.php';
        } elseif ($sub === 'addresses') {
            $pageTitle = 'Address Book | Customer Portal | MD Printers';
            $viewFile = VIEWS_PATH . '/customer/addresses.php';
        } elseif ($sub === 'quotes') {
            $pageTitle = 'My Quotations | Customer Portal | MD Printers';
            $viewFile = VIEWS_PATH . '/customer/quotes.php';
        } elseif ($sub === 'cart') {
            $pageTitle = 'B2B Printing Cart | Customer Portal | MD Printers';
            $viewFile = VIEWS_PATH . '/customer/cart.php';
        } elseif ($sub === 'orders') {
            if (isset($_GET['print_invoice'])) {
                $pageTitle = 'Tax Invoice | MD Printers';
                $viewFile = VIEWS_PATH . '/customer/invoice_print.php';
            } else {
                $pageTitle = 'My B2B Orders & Artworks | MD Printers';
                $viewFile = VIEWS_PATH . '/customer/orders.php';
            }
        } else {
            $pageTitle = 'Customer Member Dashboard | MD Printers';
            $viewFile = VIEWS_PATH . '/customer/dashboard.php';
        }
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
