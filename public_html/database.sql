-- ============================================================
-- MD PRINTERS - PRODUCTION MYSQL DATABASE SCHEMA & SEED DATA
-- Core PHP + MySQL Implementation
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `enquiry_notes`;
DROP TABLE IF EXISTS `enquiry_files`;
DROP TABLE IF EXISTS `enquiries`;
DROP TABLE IF EXISTS `blog_posts`;
DROP TABLE IF EXISTS `blog_categories`;
DROP TABLE IF EXISTS `portfolio`;
DROP TABLE IF EXISTS `portfolio_categories`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `product_categories`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `service_categories`;
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `stats`;
DROP TABLE IF EXISTS `admins`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. ADMINS TABLE
-- ------------------------------------------------------------
CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('superadmin', 'admin', 'editor') NOT NULL DEFAULT 'admin',
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default password: admin123 (Hashed using PASSWORD_BCRYPT)
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'MD Printers Admin', 'admin@mdprinters.com', '$2y$10$wT0lGzO0M1q6Q7VfPZ4m6.5i6/E5wD5T3E3zV7vN3lZ4m6.5i6/E5', 'superadmin');

-- ------------------------------------------------------------
-- 2. SITE SETTINGS TABLE (CMS Key-Value Store)
-- ------------------------------------------------------------
CREATE TABLE `site_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'MD Printers', 'general'),
('site_tagline', 'Print That Makes Your Brand Impossible to Ignore', 'general'),
('contact_email', 'info@mdprinters.com', 'contact'),
('quote_email', 'quotes@mdprinters.com', 'contact'),
('contact_phone', '+91 81461 41450, +91 98786 29214', 'contact'),
('whatsapp_number', '918146141450', 'contact'),
('business_address', 'Back side Chandigarh Sweets, Plot No. 360, Industrial Area Phase I, Chandigarh, 160002', 'contact'),
('business_hours', 'Mon - Sat: 9:00 AM - 8:00 PM', 'contact'),
('google_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.56847841961!2d77.21672131508205!3d28.61291298242491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfd37b741d057%3A0xc9942a2228f41940!2sConnaught%20Place!5e0!3m2!1sen!2sin!4v1625000000000!5m2!1sen!2sin', 'contact'),
('facebook_url', 'https://facebook.com/mdprinters', 'social'),
('instagram_url', 'https://instagram.com/mdprinters', 'social'),
('linkedin_url', 'https://linkedin.com/company/mdprinters', 'social'),
('whatsapp_cta_message', 'Hello MD Printers, I would like to request a quotation for printing services.', 'contact'),
('default_meta_title', 'MD Printers | Premium Commercial Printing & Packaging Solutions', 'seo'),
('default_meta_description', 'MD Printers provides high-precision digital printing, offset printing, custom packaging, brochure printing, and corporate stationery with fast delivery.', 'seo'),
('default_meta_keywords', 'commercial printing, offset printing, digital printing, packaging boxes, business cards, brochure printing, MD printers', 'seo'),
('smtp_host', 'smtp.mailtrap.io', 'smtp'),
('smtp_port', '2525', 'smtp'),
('smtp_user', '', 'smtp'),
('smtp_pass', '', 'smtp'),
('smtp_encryption', 'tls', 'smtp'),
('hero_title', 'Print That Makes Your Brand Impossible to Ignore.', 'homepage'),
('hero_subtitle', 'Engineering precision offset printing, custom luxury packaging, digital graphics, and corporate stationery with ultra-fast turnaround.', 'homepage'),
('hero_cta_primary', 'Request a Quote', 'homepage'),
('hero_cta_secondary', 'Explore Printing Services', 'homepage');

-- ------------------------------------------------------------
-- 2. BANNERS TABLE (DYNAMIC HERO SLIDERS)
-- ------------------------------------------------------------
CREATE TABLE `banners` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `badge` VARCHAR(150) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` TEXT NOT NULL,
  `cta_primary_text` VARCHAR(100) DEFAULT 'Get Quote',
  `cta_primary_link` VARCHAR(255) DEFAULT '#',
  `cta_secondary_text` VARCHAR(100) DEFAULT 'Call Us',
  `cta_secondary_link` VARCHAR(255) DEFAULT '#',
  `bg_gradient` VARCHAR(255) DEFAULT 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))',
  `image` VARCHAR(255) NULL,
  `sort_order` INT DEFAULT 0,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `banners` (`id`, `badge`, `title`, `subtitle`, `cta_primary_text`, `cta_primary_link`, `cta_secondary_text`, `cta_secondary_link`, `bg_gradient`, `image`, `sort_order`, `status`) VALUES
(1, '🔥 BEST SELLER • STARTING @ ₹199 / 100 CARDS', 'Executive Gold Foil Business Cards', 'Premium 400 GSM Silk Artboard coated with Velvet Soft-Touch Lamination & raised metallic gold foil stamping.', 'Order Visiting Cards', 'http://localhost/md-printers/services/visiting-cards', 'Call: 81461 41450', 'tel:8146141450', 'linear-gradient(135deg, rgba(201,158,80,0.12), rgba(0,133,178,0.08))', 'assets/images/products/gold-foil-card.jpg', 1, 'published'),
(2, '📦 CUSTOM PACKAGING • STARTING @ ₹12.50 / BOX', 'Custom Rigid Packaging & Product Boxes', 'Bespoke folding cartons, rigid magnetic gift boxes, cosmetic sleeves, and e-commerce shipping boxes with CAD prototyping.', 'Explore Packaging', 'http://localhost/md-printers/services/packaging-boxes', 'Request Sample Kit', 'http://localhost/md-printers/request-quote', 'linear-gradient(135deg, rgba(238,0,131,0.1), rgba(0,133,178,0.08))', 'assets/images/services/packaging-printing.jpg', 2, 'published'),
(3, 'shirt CORPORATE APPAREL • STARTING @ ₹299 / SHIRT', 'Custom Corporate T-Shirts & Uniforms', '100% Bio-Washed Combed Cotton T-Shirts printed with high-density DTG, plastisol screen print & computerized embroidery.', 'Customize T-Shirts', 'http://localhost/md-printers/services/t-shirt-printing', 'WhatsApp Us', 'https://wa.me/918146141450', 'linear-gradient(135deg, rgba(0,133,178,0.12), rgba(255,222,0,0.1))', 'assets/images/services/tshirt-printing.jpg', 3, 'published'),
(4, '✨ ACRYLIC SIGNAGE • STARTING @ ₹899 / SQ FT', '3D LED Acrylic Boards & Name Plates', 'Imported Cast Acrylic Sheets with 3D laser-cut letters, Samsung LED module backlight, and stainless steel stand-offs.', 'View Signage Boards', 'http://localhost/md-printers/services/acrylic-boards', 'Call: 81461 41450', 'tel:8146141450', 'linear-gradient(135deg, rgba(238,0,131,0.1), rgba(201,158,80,0.12))', 'assets/images/services/acrylic-boards.jpg', 4, 'published');

-- ------------------------------------------------------------
-- 3. STATS TABLE
-- ------------------------------------------------------------
CREATE TABLE `stats` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `counter_value` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(50) NOT NULL DEFAULT 'fas fa-award',
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stats` (`id`, `title`, `counter_value`, `icon`, `sort_order`) VALUES
(1, 'Years of Print Mastery', '18+', 'fas fa-calendar-alt', 1),
(2, 'Projects Delivered', '25,000+', 'fas fa-boxes', 2),
(3, 'Corporate & Retail Clients', '3,400+', 'fas fa-building', 3),
(4, 'Color Accuracy Rate', '99.8%', 'fas fa-print', 4);

-- ------------------------------------------------------------
-- 4. SERVICE CATEGORIES & SERVICES TABLE
-- ------------------------------------------------------------
CREATE TABLE `service_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `service_categories` (`id`, `name`, `slug`) VALUES
(1, 'Commercial Press', 'commercial-press'),
(2, 'Packaging & Boxes', 'packaging-boxes'),
(3, 'Marketing Materials', 'marketing-materials'),
(4, 'Corporate Stationery', 'corporate-stationery');

CREATE TABLE `services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'fas fa-print',
  `short_desc` TEXT NOT NULL,
  `full_desc` LONGTEXT NOT NULL,
  `main_image` VARCHAR(255) NULL,
  `features_json` TEXT NULL,
  `specifications_json` TEXT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `meta_title` VARCHAR(255) NULL,
  `meta_desc` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `service_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`id`, `category_id`, `name`, `slug`, `icon`, `short_desc`, `full_desc`, `main_image`, `features_json`, `specifications_json`, `is_featured`, `status`) VALUES
(1, 1, 'Printing', 'printing', 'fas fa-print', 'High-volume commercial offset & digital printing with G7 certified CMYK color density for books, brochures, catalogs, and flyers.', '<p>Our commercial printing press suite delivers razor-sharp text clarity, vibrant spot colors (Pantone PMS matching), and smooth solid coverage across large print quantities. Operating Heidelberg and HP Indigo presses, MD Printers ensures rapid turnarounds for corporate annual reports, product catalogs, brochures, and promotional literature.</p>', 'assets/images/services/offset-printing.jpg', '["G7 Color Certified", "Pantone Spot Matching", "High-Volume Heidelberg Press", "Substrate range: 70 - 450 GSM"]', '{"Press": "Heidelberg Speedmaster XL 106", "Resolution": "2400 DPI CTP", "Color Modes": "CMYK + 2 Spot Colors Inline"}', 1, 'published'),
(2, 1, 'Visiting Cards', 'visiting-cards', 'fas fa-address-card', 'Executive business cards with metallic gold foil, raised 3D spot UV, velvet touch lamination, and heavy 400+ GSM artboard.', '<p>Make your networking impression unforgettable. MD Printers crafts executive visiting cards using 400 GSM silk artboard, velvet soft-touch coating, raised metallic foil stamping (gold, rose gold, silver), and 3D spot UV highlights on logos.</p>', 'assets/images/services/business-cards.jpg', '["400 GSM Heavy Artboard", "Gold / Silver Metallic Foil", "Raised 3D Spot UV", "Round Corners or Die-Cut Slits"]', '{"Dimensions": "3.5 x 2.0 inches", "Paper Weight": "400 GSM Silk", "Finishing": "Velvet Touch + Metallic Foil"}', 1, 'published'),
(3, 1, 'T-Shirt Printing', 't-shirt-printing', 'fas fa-tshirt', 'Custom corporate t-shirts, polo shirts, screen printing, DTG high-density prints, and embroidered staff uniforms.', '<p>Transform your workforce into brand ambassadors. MD Printers offers custom t-shirt printing utilizing Direct-to-Garment (DTG), plastisol screen printing, heat transfer vinyl, and high-precision computer embroidery on premium combed cotton tees.</p>', 'assets/images/services/tshirt-printing.jpg', '["100% Bio-Washed Combed Cotton", "High-Density DTG & Screen Print", "Precision Computerized Embroidery", "Durable Fade-Resistant Inks"]', '{"Fabric": "180 - 240 GSM Cotton", "Print Methods": "DTG, Screen Print, Embroidery", "Sizes": "S to 5XL"}', 1, 'published'),
(4, 1, 'Mug Printing', 'mug-printing', 'fas fa-mug-hot', 'Custom ceramic coffee mugs, magic heat-sensitive mugs, travel tumblers, and corporate branded promotional drinkware.', '<p>High-grade dye-sublimation ceramic mugs printed with vibrant full-color wrap graphics. Dishwasher safe, microwave safe, and engineered for long-lasting corporate gifting.</p>', 'assets/images/services/mug-printing.jpg', '["11 oz & 15 oz Ceramic Mugs", "HD Sublimation Full-Wrap Print", "Magic Heat-Changing Option", "Individual Gift Box Packaging"]', '{"Capacity": "325 ml / 11 oz", "Material": "Grade-A Ceramic", "Dishwasher Safe": "Yes"}', 1, 'published'),
(5, 1, 'Badges', 'badges', 'fas fa-certificate', 'Custom button badges, metallic lapel pins, magnetic event badges, and corporate identification pins.', '<p>Custom promotional button badges, enamel metal pins, and magnetic name badges for corporate events, trade shows, school badges, and campaign branding.</p>', 'assets/images/services/badges.jpg', '["Glossy Mylar Protective Film", "Safety Pin & Magnetic Backing", "Custom Die-Cut Shapes", "Enamel Metal & Plastic Badges"]', '{"Sizes": "44mm, 58mm, 75mm", "Backing": "Pin, Magnet, Clip", "Finish": "High Gloss Mylar"}', 1, 'published'),
(6, 1, 'Stickers', 'stickers', 'fas fa-sticky-note', 'Waterproof vinyl stickers, die-cut product labels, foil stamped sticker sheets, and clear window decals.', '<p>Custom printed adhesive stickers, product packaging labels, roll stickers, kiss-cut sheets, holographic stickers, and waterproof outdoor vinyl decals.</p>', 'assets/images/services/stickers.jpg', '["Waterproof & UV Resistant Vinyl", "Die-Cut & Kiss-Cut Shapes", "Holographic & Metallic Foil Options", "Roll Labels for Auto Applicators"]', '{"Material": "White Vinyl, Clear PVC, Metallic Kraft", "Adhesive": "Strong Permanent", "UV Protection": "3 Years Outdoor"}', 1, 'published'),
(7, 1, 'ID Cards', 'id-cards', 'fas fa-id-badge', 'High-durability PVC employee ID cards, RFID smart cards, visitor passes, and barcode student access cards.', '<p>CR80 standard thermal plastic PVC ID cards with high-definition thermal printing, magnetic stripes, RFID chip embedding, QR/barcodes, and protective holographic overlays.</p>', 'assets/images/services/id-cards.jpg', '["Heavy-Duty 30 Mil PVC Plastic", "HD Re-transfer Thermal Printing", "RFID & Magnetic Stripe Options", "Barcode & Holographic Security"]', '{"Card Standard": "CR80 (85.6 x 54 mm)", "Thickness": "0.76mm / 30 Mil", "Printing": "Dual Sided HD"}', 1, 'published'),
(8, 1, 'Lanyards', 'lanyards', 'fas fa-ribbon', 'Custom satin lanyard straps, screen-printed lanyard ribbons, breakaway safety clips, and heavy-duty metal hooks.', '<p>Custom woven and satin polyester lanyards for corporate staff, exhibitions, and security access. Features vibrant sublimation printing with safety breakaway buckles and swivel hooks.</p>', 'assets/images/services/lanyards.jpg', '["Premium Satin & Polyester Ribbon", "Full-Color Sublimation Printing", "Breakaway Safety Lock", "Heavy-Duty Dog Hook & Oval Clip"]', '{"Widths": "12mm, 16mm, 20mm", "Length": "900mm Standard", "Attachment": "Metal Hook, Badge Reel"}', 1, 'published'),
(9, 1, 'Name Plates', 'name-plates', 'fas fa-signature', 'Executive brass & stainless steel door name plates, acrylic desk name signs, and laser-engraved office plaques.', '<p>Laser-engraved acrylic, brushed metal stainless steel, and gold brass name plates for executive desks, office doors, conference rooms, and reception counters.</p>', 'assets/images/services/name-plates.jpg', '["Laser Engraved Metal & Acrylic", "Brushed Gold & Silver Stainless Steel", "Beveled Edge Glass / Acrylic Desk Holders", "Weatherproof UV Printing"]', '{"Materials": "Acrylic, Brass, Stainless Steel", "Mounting": "Adhesive Tape, Stand-off Bolts"}', 1, 'published'),
(10, 1, 'Acrylic Boards', 'acrylic-boards', 'fas fa-shapes', 'Cast acrylic LED light boards, 3D raised letter signage, clear acrylic display panels, and shopfront signboards.', '<p>High-grade cast acrylic board printing and 3D laser-cut letter signages. Ideal for corporate office receptions, store branding, illuminated LED backlight signs, and directional wayfinding panels.</p>', 'assets/images/services/acrylic-boards.jpg', '["Imported Cast Acrylic Sheets", "3D Raised Laser-Cut Letters", "Backlit LED Module Lighting", "Stainless Steel Stand-off Mounts"]', '{"Thickness": "3mm, 5mm, 8mm, 10mm", "Lighting": "Samsung LED Backlight Modules"}', 1, 'published'),
(11, 1, 'Corporate Printing', 'corporate-printing', 'fas fa-briefcase', 'Corporate stationery kits, custom presentation folders, annual reports, branded diaries, calendars, and welcome kits.', '<p>Complete corporate printing solutions including pocket presentation folders, custom foil-stamped notebooks, desk calendars, company profile brochures, and executive welcome gift hampers.</p>', 'assets/images/services/corporate-printing.jpg', '["Complete Brand Welcome Kits", "Die-Cut Pocket Folders", "Custom Executive Leatherette Diaries", "Table Top Desk Calendars"]', '{"Kit Contents": "Folder, Notebook, Pen, Card, USB", "Packaging": "Custom Luxury Box"}', 1, 'published');

-- ------------------------------------------------------------
-- 5. PRODUCT CATEGORIES & PRODUCTS TABLE
-- ------------------------------------------------------------
CREATE TABLE `product_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Business Cards', 'business-cards', 'Executive, textured, velvet touch, and foil stamped business cards.'),
(2, 'Brochures & Flyers', 'brochures-flyers', 'Corporate flyers, tri-fold brochures, booklets, and leaflets.'),
(3, 'Packaging & Boxes', 'packaging-boxes', 'Custom corrugated boxes, rigid gift boxes, product sleeves, and mailers.'),
(4, 'Posters & Banners', 'posters-banners', 'Large format vinyl banners, posters, roll-up standees, and signage.'),
(5, 'Corporate Stationery', 'stationery', 'Letterheads, envelopes, sticky notes, ID cards, and folders.');

CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `short_desc` TEXT NOT NULL,
  `full_desc` LONGTEXT NOT NULL,
  `main_image` VARCHAR(255) NULL,
  `gallery_json` TEXT NULL,
  `specifications_json` TEXT NULL,
  `paper_options_json` TEXT NULL,
  `finishing_options_json` TEXT NULL,
  `min_quantity` INT DEFAULT 100,
  `is_featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `meta_title` VARCHAR(255) NULL,
  `meta_desc` TEXT NULL,
  `meta_keywords` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `product_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_desc`, `full_desc`, `main_image`, `gallery_json`, `specifications_json`, `paper_options_json`, `finishing_options_json`, `min_quantity`, `is_featured`, `status`) VALUES
(1, 1, 'Executive Gold Foil Business Cards', 'gold-foil-business-cards', 'Luxury 400 GSM velvet touch business cards with metallic gold or silver foil precision stamping.', '<p>Elevate networking into a lasting brand impression. Our executive foil business cards feature 400 GSM heavy artboard coated with silk velvet lamination, topped with raised metallic foil stamping on text or logos.</p>', 'assets/images/products/gold-foil-card.jpg', '["assets/images/products/gold-foil-card-1.jpg", "assets/images/products/gold-foil-card-2.jpg"]', '{"Dimensions": "3.5 x 2.0 inches (Standard US/UK)", "Paper Weight": "400 GSM Silk Board", "Corners": "Square or 3mm Round Corners"}', '["400 GSM Silk Artboard", "350 GSM Cotton Textured", "600 GSM Triplex Colored Core"]', '["Metallic Gold Foil", "Rose Gold Foil", "Silver Foil", "3D Raised Spot UV"]', 100, 1, 'published'),

(2, 2, 'Luxury Corporate Tri-Fold Brochure', 'corporate-trifold-brochure', 'Professional 250 GSM tri-fold brochure with crisp scoring, high-gloss or matte lamination.', '<p>Present your company overview, service offerings, and product lines with clean visual typography on 250 GSM premium art paper with precision machine scoring for zero spine cracking.</p>', 'assets/images/products/trifold-brochure.jpg', '[]', '{"Flat Size": "A4 (297 x 210 mm) or A3", "Fold Type": "Tri-Fold / Z-Fold / Gate-Fold", "Print": "Full Color Both Sides (4/4)"}', '["170 GSM Art Paper", "250 GSM Matt Art Board", "300 GSM Premium Silk"]', '["Matte Lamination", "Gloss Lamination", "Soft Touch + Spot UV"]', 250, 1, 'published'),

(3, 3, 'Custom Rigid Magnetic Gift Box', 'custom-rigid-magnetic-box', 'High-end structural gift box with magnetic flap closure, custom foam inserts, and debossed branding.', '<p>Designed for luxury cosmetics, corporate onboarding hampers, electronics, and VIP press packages. Built with heavy 1200 GSM greyboard wrapped in custom printed art paper or textured leatherette.</p>', 'assets/images/products/magnetic-box.jpg', '[]', '{"Board Thickness": "1.5mm to 3.0mm Greyboard", "Closure": "Dual Hidden Neodymium Magnets", "Interior": "Custom EVA Foam / Velvet Die-Cut Tray"}', '["1200 GSM Heavy Greyboard", "1500 GSM Premium Rigid Board"]', '["Custom Color Foil Stamping", "Debossing", "Soft Touch Matte Lamination"]', 50, 1, 'published'),

(4, 2, 'High-Impact Promotional Flyers', 'high-impact-flyers', 'Vibrant double-sided promotional flyers printed on 170 GSM gloss art paper with fast 24-hour turnaround.', '<p>Drive traffic to store launches, events, menu promotions, and product campaigns with crisp high-volume flyer printing.</p>', 'assets/images/products/flyer-printing.jpg', '[]', '{"Sizes": "A5 (148x210mm), A4, DL (99x210mm)", "Print": "4/4 Double Sided CMYK", "Lead Time": "24 Hours"}', '["130 GSM Gloss", "170 GSM Art Paper", "300 GSM Heavy Board"]', '["Aqueous Coating", "Matte Lamination", "Uncoated Natural"]', 500, 1, 'published'),

(5, 5, 'Executive Corporate Presentation Folder', 'presentation-folders', 'Custom die-cut pocket presentation folder on 350 GSM board with business card slots.', '<p>Keep proposal documents, contracts, and marketing sheets organized inside custom branded pocket presentation folders.</p>', 'assets/images/products/presentation-folder.jpg', '[]', '{"Capacity": "Holds 15-20 A4 sheets", "Pockets": "Single or Double Pocket", "Slots": "Business Card Cutouts Included"}', '["350 GSM Art Card", "400 GSM Kraft Board"]', '["Spot UV on Logo", "Velvet Lamination", "Foil Stamping"]', 100, 0, 'published');

-- ------------------------------------------------------------
-- 6. PORTFOLIO CATEGORIES & PORTFOLIO TABLE
-- ------------------------------------------------------------
CREATE TABLE `portfolio_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `portfolio_categories` (`id`, `name`, `slug`) VALUES
(1, 'Packaging', 'packaging'),
(2, 'Corporate Print', 'corporate-print'),
(3, 'Event & Retail', 'event-retail'),
(4, 'Luxury Stationery', 'luxury-stationery');

CREATE TABLE `portfolio` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NULL,
  `title` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `client_name` VARCHAR(100) NULL,
  `project_date` VARCHAR(50) NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `gallery_json` TEXT NULL,
  `services_used` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `portfolio_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `portfolio` (`id`, `category_id`, `title`, `slug`, `client_name`, `project_date`, `description`, `image`, `services_used`, `is_featured`, `status`) VALUES
(1, 1, 'Aura Skincare Rigid Box & Bottle Sleeves', 'aura-skincare-packaging', 'Aura Botanicals', 'January 2026', 'Designed and produced 10,000 custom soft-touch rigid gift boxes with rose gold foil stamping and custom die-cut velvet insert trays for an organic luxury skincare line.', 'assets/images/portfolio/portfolio-1.jpg', 'Rigid Box Packaging, Foil Stamping, Spot UV', 1, 'published'),

(2, 2, 'Horizon Tech Annual Report & Brand Kit', 'horizon-tech-annual-report', 'Horizon Technologies Ltd', 'December 2025', 'Multi-page offset printed annual report featuring translucent metallic flysheets, silver foil cover titles, and perfect bound spine finishing for 5,000 shareholders.', 'assets/images/portfolio/portfolio-2.jpg', 'Offset Printing, Perfect Binding, Metallic Inks', 1, 'published'),

(3, 4, 'Vanguard Capital Executive Stationery Set', 'vanguard-capital-stationery', 'Vanguard Capital Group', 'February 2026', 'Bespoke 600 GSM triplex edge-painted business cards, watermarked cotton letterheads, and foil embossed presentation folders for executive board members.', 'assets/images/portfolio/portfolio-3.jpg', 'Letterpress, Triplex Business Cards, Edge Painting', 1, 'published'),

(4, 3, 'Summit Global Event Branding & Backdrops', 'summit-global-event-branding', 'Summit Global Conference', 'November 2025', 'Large format tension fabric backdrops, acrylic badges, roll-up standees, and attendee welcome kits delivered within 48 hours for a 2,000-delegate tech conference.', 'assets/images/portfolio/portfolio-4.jpg', 'Large Format Printing, Acrylic Badges, Digital Express', 0, 'published');

-- ------------------------------------------------------------
-- 7. TESTIMONIALS TABLE
-- ------------------------------------------------------------
CREATE TABLE `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `client_name` VARCHAR(100) NOT NULL,
  `company` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100) NOT NULL,
  `avatar` VARCHAR(255) NULL,
  `rating` TINYINT UNSIGNED DEFAULT 5,
  `testimonial` TEXT NOT NULL,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`id`, `client_name`, `company`, `designation`, `rating`, `testimonial`, `sort_order`) VALUES
(1, 'Vikram Malhotra', 'Malhotra Consumer Goods', 'VP Marketing', 5, 'MD Printers executed our product packaging launch with absolute precision. The color fidelity on 50,000 retail folding cartons matched our digital proofs down to the exact Pantone shade. Their turnarounds are unmatched.', 1),
(2, 'Sarah Jenkins', 'Apex Financial Advisors', 'Brand Director', 5, 'The foil business cards and presentation folders we ordered for our wealth management partners are stunning. Clients literally remark on the texture every time we hand them out.', 2),
(3, 'Rajesh Khanna', 'Nova Hotels Group', 'Operations Lead', 5, 'Fast, reliable, and highly communicative. When we needed 3,000 hotel room service menus and event flyers delivered in 24 hours, MD Printers came through flawlessly.', 3);

-- ------------------------------------------------------------
-- 8. BLOG CATEGORIES & POSTS TABLE
-- ------------------------------------------------------------
CREATE TABLE `blog_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blog_categories` (`id`, `name`, `slug`) VALUES
(1, 'Print Knowledge', 'print-knowledge'),
(2, 'Packaging Tips', 'packaging-tips'),
(3, 'Design & Production', 'design-production');

CREATE TABLE `blog_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `content` LONGTEXT NOT NULL,
  `excerpt` TEXT NOT NULL,
  `featured_image` VARCHAR(255) NULL,
  `author` VARCHAR(100) DEFAULT 'MD Printers Editorial Team',
  `is_published` TINYINT(1) DEFAULT 1,
  `published_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `meta_title` VARCHAR(255) NULL,
  `meta_desc` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blog_posts` (`id`, `category_id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author`, `is_published`) VALUES
(1, 1, 'How to Choose the Right Paper GSM for Commercial Printing', 'choose-right-paper-gsm-commercial-printing', '<p>Paper weight measured in GSM (Grams per Square Meter) is one of the single most influential factors defining tactile perception and structural durability in commercial print.</p><h3>GSM Selection Quick Matrix</h3><ul><li><strong>70 - 90 GSM:</strong> Inner letterhead pages, transactional invoices, notepad sheets.</li><li><strong>130 - 170 GSM:</strong> High-volume promotional flyers, brochure inner pages, magazine text pages.</li><li><strong>250 - 300 GSM:</strong> Softcover catalog covers, premium invitation envelopes, heavy flyers.</li><li><strong>350 - 450 GSM:</strong> Executive business cards, presentation folders, product box covers.</li></ul><p>Consult with MD Printers prepress engineers before locking artwork files to select the optimal stock for your application.</p>', 'Learn how GSM paper weight affects tactile brand perception, brochure folding durability, and business card stiffness.', 'assets/images/blog/blog-1.jpg', 'MD Printers Technical Team', 1),

(2, 2, 'RGB vs CMYK: Avoiding Color Shifts Before Going to Press', 'rgb-vs-cmyk-avoiding-color-shifts', '<p>Designing in RGB color mode for digital displays and sending those files directly to an offset press is the #1 cause of muted, unexpected color reproduction.</p><h3>Understanding Color Spaces</h3><p>RGB (Red, Green, Blue) uses additive light suitable for computer monitors, while CMYK (Cyan, Magenta, Yellow, Key/Black) relies on subtractive ink pigments on paper substrate.</p><p>Always convert your design project color mode to CMYK and specify Pantone spot codes for exact brand color matching.</p>', 'Discover why digital screen colors shift when printed on physical press machines and how to set up your prepress files correctly.', 'assets/images/blog/blog-2.jpg', 'Prepress Engineering Team', 1);

-- ------------------------------------------------------------
-- 9. FAQS TABLE
-- ------------------------------------------------------------
CREATE TABLE `faqs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `category` VARCHAR(100) DEFAULT 'General',
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `sort_order`, `is_active`) VALUES
(1, 'What artwork file formats do you accept for printing?', 'We accept high-resolution vector PDF (preferred), Adobe Illustrator (.AI), Adobe Photoshop (.PSD), TIFF, and EPS files. Artwork should be set to 300 DPI resolution in CMYK color mode with 3mm bleed allowance on all sides.', 'Artwork & Prepress', 1, 1),
(2, 'What is the standard turnaround time for printing orders?', 'Standard digital print runs are delivered in 24 to 48 hours. Large volume multi-color offset runs and custom rigid packaging require 4 to 7 business days depending on finishing specs. Express same-day service is available upon request.', 'Turnaround & Shipping', 2, 1),
(3, 'Can I request a physical sample proof before mass production?', 'Yes! We provide digital PDF proofs for all orders. For large offset print runs and custom packaging orders, we can produce hardcopy physical sample proofs or unprinted CAD structural mockups upon approval.', 'Ordering & Proofing', 3, 1),
(4, 'Do you offer custom die-cutting and special finishing options?', 'Absolutely. We offer spot UV, 3D raised UV, metallic foil stamping (gold, silver, rose gold, holographic), matte/gloss/soft-touch lamination, embossing, debossing, edge painting, and custom shape die-cutting.', 'Finishing & Options', 4, 1);

-- ------------------------------------------------------------
-- 10. ENQUIRIES TABLE (Quotes & Contact Submissions CRM)
-- ------------------------------------------------------------
CREATE TABLE `enquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ref_no` VARCHAR(30) NOT NULL UNIQUE,
  `type` ENUM('quote', 'contact') DEFAULT 'quote',
  `customer_name` VARCHAR(120) NOT NULL,
  `company_name` VARCHAR(120) NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `whatsapp` VARCHAR(50) NULL,
  `product_service` VARCHAR(150) NULL,
  `quantity` INT UNSIGNED NULL,
  `size` VARCHAR(100) NULL,
  `material_gsm` VARCHAR(100) NULL,
  `print_type` VARCHAR(100) NULL,
  `finishing` VARCHAR(150) NULL,
  `delivery_date` VARCHAR(50) NULL,
  `message` TEXT NULL,
  `preferred_contact` VARCHAR(50) DEFAULT 'email',
  `sent_to_whatsapp` TINYINT(1) DEFAULT 0,
  `status` ENUM('New', 'Contacted', 'In Progress', 'Quoted', 'Won', 'Lost', 'Closed') DEFAULT 'New',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `enquiries` (`id`, `ref_no`, `type`, `customer_name`, `company_name`, `email`, `phone`, `product_service`, `quantity`, `size`, `material_gsm`, `finishing`, `message`, `status`) VALUES
(1, 'MDQ-2026-000101', 'quote', 'Anand Kumar', 'Vertex Solutions', 'anand@vertexsolutions.in', '+91 98111 22233', 'Gold Foil Business Cards', 500, 'Standard 3.5x2"', '400 GSM Silk', 'Gold Foil + Soft Touch', 'Please quote for 500 cards for 5 executive members with gold foil logo.', 'New'),
(2, 'MDQ-2026-000102', 'contact', 'Priya Sharma', 'Creative Catalyst', 'priya@catalyst.agency', '+91 99887 76655', 'General Enquiry', NULL, NULL, NULL, NULL, 'We are looking for a reliable printing press partner for our client monthly packaging requirements.', 'Contacted');

-- ------------------------------------------------------------
-- 11. ENQUIRY FILES & ENQUIRY NOTES TABLE
-- ------------------------------------------------------------
CREATE TABLE `enquiry_files` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `enquiry_id` INT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(255) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `enquiry_notes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `enquiry_id` INT UNSIGNED NOT NULL,
  `admin_id` INT UNSIGNED NULL,
  `note` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `enquiry_notes` (`enquiry_id`, `admin_id`, `note`) VALUES
(1, 1, 'Sent initial quote estimation via email. Follow up scheduled for tomorrow.');

-- ------------------------------------------------------------
-- 12. MEDIA LIBRARY TABLE
-- ------------------------------------------------------------
CREATE TABLE `media` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 13. HERO BANNERS TABLE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `banners` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `badge` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` TEXT,
  `cta_primary_text` VARCHAR(100) DEFAULT 'Get Quote',
  `cta_primary_link` VARCHAR(255) DEFAULT '/request-quote',
  `cta_secondary_text` VARCHAR(100) DEFAULT 'Call Us',
  `cta_secondary_link` VARCHAR(255) DEFAULT 'tel:8146141450',
  `bg_gradient` VARCHAR(255) DEFAULT 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))',
  `image` VARCHAR(255) DEFAULT 'assets/images/services/offset-printing.jpg',
  `video_url` VARCHAR(255) DEFAULT '',
  `sort_order` INT DEFAULT 0,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


