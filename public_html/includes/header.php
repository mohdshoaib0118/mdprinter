<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary SEO Meta Tags -->
    <title><?= e($pageTitle ?? Setting::get('default_meta_title', 'MD Printers | Commercial Printing & Custom Packaging Press')) ?></title>
    <meta name="title" content="<?= e($pageTitle ?? Setting::get('default_meta_title', 'MD Printers')) ?>">
    <meta name="description" content="<?= e($metaDescription ?? Setting::get('default_meta_description', 'MD Printers is India premier commercial print press offering G7 color precision offset printing, custom rigid packaging boxes, executive gold foil business cards, corporate T-shirts, mugs, and LED acrylic signage.')) ?>">
    <meta name="keywords" content="<?= e($metaKeywords ?? Setting::get('default_meta_keywords', 'MD Printers, commercial printing, offset printing, digital printing press, custom packaging boxes, gold foil visiting cards, t-shirt printing, mug printing, acrylic boards, Delhi printers')) ?>">
    <meta name="author" content="MD Printers">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="theme-color" content="#0085b2">
    <link rel="canonical" href="<?= SITE_URL . e($_SERVER['REQUEST_URI'] ?? '') ?>">

    <!-- Open Graph / Facebook / LinkedIn Meta Tags -->
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MD Printers">
    <meta property="og:title" content="<?= e($pageTitle ?? 'MD Printers | Commercial Printing & Packaging Solutions') ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? 'High-precision offset printing, custom packaging boxes, executive visiting cards, corporate T-shirts, ceramic mugs & LED acrylic boards with fast delivery.') ?>">
    <meta property="og:url" content="<?= SITE_URL . e($_SERVER['REQUEST_URI'] ?? '') ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/logo.svg">
    <meta property="og:image:alt" content="MD Printers Official Logo">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle ?? 'MD Printers | Commercial Printing Press') ?>">
    <meta name="twitter:description" content="<?= e($metaDescription ?? 'Commercial printing, custom packaging, business cards, T-shirts, ceramic mugs, and acrylic signage with 24-hour express runs.') ?>">
    <meta name="twitter:image" content="<?= SITE_URL ?>/assets/images/logo.svg">

    <!-- Schema.org JSON-LD LocalBusiness & PrintShop Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "LocalBusiness",
          "@id": "<?= SITE_URL ?>/#organization",
          "name": "MD Printers",
          "url": "<?= SITE_URL ?>",
          "logo": "<?= SITE_URL ?>/assets/images/logo.svg",
          "image": "<?= SITE_URL ?>/assets/images/logo.svg",
          "description": "Commercial print production press providing offset printing, rigid packaging, corporate apparel, mugs, and signages.",
          "telephone": "+91 81461 41450",
          "email": "info@mdprinters.com",
          "priceRange": "$$",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "Industrial Area, Phase II, Commercial Print Zone",
            "addressLocality": "New Delhi",
            "addressRegion": "Delhi",
            "postalCode": "110020",
            "addressCountry": "IN"
          },
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": 28.6129,
            "longitude": 77.2167
          },
          "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            "opens": "09:00",
            "closes": "20:00"
          },
          "sameAs": [
            "https://facebook.com/mdprinters",
            "https://instagram.com/mdprinters",
            "https://linkedin.com/company/mdprinters"
          ]
        },
        {
          "@type": "WebSite",
          "@id": "<?= SITE_URL ?>/#website",
          "url": "<?= SITE_URL ?>",
          "name": "MD Printers",
          "publisher": {
            "@id": "<?= SITE_URL ?>/#organization"
          },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "<?= SITE_URL ?>/products?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>

    <!-- Google Fonts & FontAwesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- International Telephone Input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">

    <!-- CSS Assets -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
</head>
<body class="cmyk-bg-pattern">

<!-- LUXURY BRAND PRELOADER -->
<div id="site-preloader" class="site-preloader">
    <div class="preloader-inner">
        <div class="preloader-spinner-box">
            <div class="cmyk-ring cyan"></div>
            <div class="cmyk-ring magenta"></div>
            <div class="cmyk-ring yellow"></div>
            <div class="cmyk-ring gold"></div>
            <div class="preloader-center-logo">
                <img src="<?= SITE_URL ?>/assets/images/logo-official.png" alt="MD Printers Logo" style="width: 34px; height: 34px; object-fit: contain; border-radius: 50%;">
            </div>
        </div>
        <div class="preloader-brand-title">MD PRINTERS</div>
        <div class="preloader-subtext">COMMERCIAL PRINT PRESS</div>
        <div class="preloader-bar-wrap">
            <div class="preloader-bar-fill"></div>
        </div>
    </div>
</div>
<script>
(function() {
    function hideLoader() {
        var loader = document.getElementById('site-preloader');
        if (loader) {
            loader.classList.add('loaded');
            setTimeout(function() { loader.style.display = 'none'; }, 400);
        }
    }
    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader);
        setTimeout(hideLoader, 800);
    }
})();
</script>

<!-- SVG Filter for Gooey Liquid Blob Buttons -->
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="display: block; height: 0; width: 0; position: absolute; pointer-events: none;" aria-hidden="true">
  <defs>
    <filter id="goo">
      <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
      <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
      <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
    </filter>
  </defs>
</svg>

<!-- Top Utility Bar -->
<div class="top-utility-bar">
    <div class="container">
        <div class="top-utility-inner">
            <div class="top-utility-left">
                <span class="top-util-item">
                    <i class="fas fa-phone-alt icon-cyan"></i> Hotline: 
                    <a href="tel:8146141450" class="top-util-link">+91 81461 41450</a> 
                    <span class="divider">|</span> 
                    <a href="tel:9878629214" class="top-util-link">+91 98786 29214</a>
                </span>
                <span class="top-badge-express desktop-only">
                    <span class="live-pulse"></span> <i class="fas fa-bolt"></i> Express 24-Hour Press Run
                </span>
            </div>

            <div class="top-utility-right">
                <button type="button" data-modal="track-order" class="top-util-btn btn-track desktop-only">
                    <i class="fas fa-search-location"></i> Track Order
                </button>
                <button type="button" data-modal="sample-kit" class="top-util-btn btn-sample desktop-only">
                    <i class="fas fa-box-open"></i> Request Free Sample Kit
                </button>
                
                <?php if (is_customer_logged_in()): ?>
                    <a href="<?= SITE_URL ?>/dashboard" class="top-util-btn desktop-only" style="background: rgba(0, 168, 232, 0.15); color: #00a8e8; font-weight: 800; border: 1px solid rgba(0, 168, 232, 0.3);">
                        <i class="fas fa-user-circle"></i> <?= e($_SESSION['customer_name'] ?? 'My Account') ?>
                    </a>
                    <a href="<?= SITE_URL ?>/logout" class="top-util-btn desktop-only" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 700;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login" class="top-util-btn desktop-only" style="background: rgba(0, 168, 232, 0.15); color: #00a8e8; font-weight: 800; border: 1px solid rgba(0, 168, 232, 0.3);">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="<?= SITE_URL ?>/register" class="top-util-btn desktop-only" style="background: rgba(255, 183, 3, 0.2); color: #b45309; font-weight: 800; border: 1px solid rgba(255, 183, 3, 0.4);">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>

                <?php $waLink = Setting::get('whatsapp_url') ?: (Setting::get('whatsapp_number') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', Setting::get('whatsapp_number')) : 'https://wa.me/918146141450'); ?>
                <a href="<?= e($waLink) ?>" target="_blank" class="top-util-btn btn-whatsapp">
                    <i class="fab fa-whatsapp"></i> WhatsApp Quote
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Header Navigation -->
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <a href="<?= SITE_URL ?>/" class="brand-logo" aria-label="MD Printers Home" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                <img src="<?= SITE_URL ?>/assets/images/logo-official.png" alt="MD Printers" class="brand-logo-img" style="height: 50px; width: 50px; object-fit: contain;">
                <div style="display: flex; flex-direction: column; line-height: 1.1;">
                    <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.3rem; font-weight: 900; color: #0f172a; letter-spacing: 0.5px; text-transform: uppercase;">
                        MD <span style="color: var(--cmyk-cyan, #00aeef);">PRINTERS</span>
                    </span>
                    <span style="font-size: 0.65rem; font-weight: 800; color: #64748b; letter-spacing: 1.4px; text-transform: uppercase;">
                        Design • Print • Advertising
                    </span>
                </div>
            </a>

            <ul class="nav-menu">
                <li><a href="<?= SITE_URL ?>/" class="nav-link <?= ($route === '' || $route === 'home') ? 'active' : '' ?>"><i class="fas fa-home mobile-only"></i> Home</a></li>
                <li><a href="<?= SITE_URL ?>/about" class="nav-link <?= $route === 'about' ? 'active' : '' ?>"><i class="fas fa-info-circle mobile-only"></i> About Us</a></li>
                <li><a href="<?= SITE_URL ?>/services" class="nav-link <?= $route === 'services' ? 'active' : '' ?>"><i class="fas fa-cogs mobile-only"></i> Services</a></li>
                <li><a href="<?= SITE_URL ?>/products" class="nav-link <?= $route === 'products' ? 'active' : '' ?>"><i class="fas fa-box-open mobile-only"></i> Products</a></li>
                <li><a href="<?= SITE_URL ?>/portfolio" class="nav-link <?= $route === 'portfolio' ? 'active' : '' ?>"><i class="fas fa-images mobile-only"></i> Portfolio</a></li>
                <li><a href="<?= SITE_URL ?>/blog" class="nav-link <?= $route === 'blog' ? 'active' : '' ?>"><i class="fas fa-newspaper mobile-only"></i> Blog</a></li>
                <li><a href="<?= SITE_URL ?>/contact" class="nav-link <?= $route === 'contact' ? 'active' : '' ?>"><i class="fas fa-envelope mobile-only"></i> Contact</a></li>
                
                <!-- Customer Auth Actions in Mobile Menu -->
                <li class="mobile-only" style="padding-top: 14px; border-top: 1px solid #e2e8f0; margin-top: 10px;">
                    <?php if (is_customer_logged_in()): ?>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?= SITE_URL ?>/dashboard" class="btn btn-primary btn-sm" style="flex: 1; font-weight: 700;"><i class="fas fa-th-large"></i> Dashboard</a>
                            <a href="<?= SITE_URL ?>/logout" class="btn btn-outline btn-sm" style="flex: 1; font-weight: 700; color: #ef4444; border-color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?= SITE_URL ?>/login" class="btn btn-primary btn-sm" style="flex: 1; font-weight: 700;"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <a href="<?= SITE_URL ?>/register" class="btn btn-magenta btn-sm" style="flex: 1; font-weight: 700;"><i class="fas fa-user-plus"></i> Register</a>
                        </div>
                    <?php endif; ?>
                </li>

                <li class="mobile-only" style="padding-top: 10px;">
                    <div style="display: flex; gap: 10px;">
                        <button type="button" data-modal="track-order" class="btn btn-outline btn-sm" style="flex: 1; font-weight: 700;"><i class="fas fa-search-location"></i> Track Order</button>
                        <button type="button" data-modal="sample-kit" class="btn btn-magenta btn-sm" style="flex: 1; font-weight: 700;"><i class="fas fa-box"></i> Sample Kit</button>
                    </div>
                </li>
            </ul>

            <div class="header-actions">
                <?php if (is_customer_logged_in()): ?>
                    <a href="<?= SITE_URL ?>/dashboard" class="btn btn-outline btn-sm desktop-only" style="border-color: var(--cmyk-cyan); color: var(--cmyk-cyan); font-weight: 800;">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login" class="btn btn-outline btn-sm desktop-only" style="border-color: var(--cmyk-cyan); color: var(--cmyk-cyan); font-weight: 800;">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>

                <button type="button" data-modal="callback" class="btn btn-outline btn-sm desktop-only" style="border-color: var(--cmyk-cyan); color: var(--cmyk-cyan); font-weight: 700;"><i class="fas fa-phone-alt"></i> Call Us</button>
                <button type="button" data-modal="quick-quote" class="btn btn-primary btn-sm desktop-only"><i class="fas fa-file-invoice-dollar"></i> Get Quote</button>
                <button class="mobile-toggle" aria-label="Toggle Menu"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </div>
</header>

<!-- Dynamic Quick Service Circle Bar -->
<?php require_once INCLUDES_PATH . '/quick-services-bar.php'; ?>
