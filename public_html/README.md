# MD Printers — Production Website & CMS Documentation

A high-performance, agency-grade commercial printing website and CMS backend built with **Core PHP, MySQL, PDO, HTML5, Vanilla CSS3, and Vanilla JavaScript**.

Designed specifically for **MD Printers** to drive corporate lead generation, showcase print capabilities (Offset, Digital, Rigid Packaging, Stationery), handle multi-step quotation requests with artwork file attachments (`MDQ-2026-XXXXXX`), and allow complete dynamic administration via a secure CMS dashboard.

---

## Technical Stack & Architecture

- **Backend**: Core PHP 8.x (Compatible with PHP 7.4 - 8.3+)
- **Database**: MySQL 5.7 / 8.0+ (PDO Driver with Prepared Statements)
- **Frontend**: HTML5, Vanilla CSS3 (Luxury CMYK Editorial Theme System), Vanilla JavaScript (Zero heavy framework bloat)
- **Security**: 100% Prepared SQL Statements, XSS Output Escaping (`e()`), CSRF Token Verification, MIME & Extension Upload Filtering, `.htaccess` Script Execution Lock in `/uploads/`.
- **Deployment Compatibility**: Localhost XAMPP / WAMP, Apache web server, Shared Hosting, Hostinger, cPanel.

---

## Quick Start / XAMPP Local Setup

### 1. Database Creation
1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open `http://localhost/phpmyadmin`.
3. Create a new database named `md_printers_db` with `utf8mb4_unicode_ci` collation.
4. Click **Import** and upload `database.sql` from the project root directory.

### 2. File Location & Configuration
1. Place the project folder `md-printers` inside `C:\xampp\htdocs\` (or run inside scratch workspace).
2. Check `config/config.php` database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'md_printers_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### 3. Accessing the Website
- **Public Website**: `http://localhost/md-printers/`
- **Admin CMS Dashboard**: `http://localhost/md-printers/admin`

---

## Admin CMS Portal Credentials

- **Admin Login URL**: `/admin` (or `http://localhost/md-printers/admin/login.php`)
- **Default Super Admin Email**: `admin@mdprinters.com`
- **Default Password**: `admin123`

> [!IMPORTANT]
> Change the default admin password in production via phpMyAdmin or the Admin settings panel!

---

## Key Features & Modules

### 1. Public Lead Generation & Quotation Engine (`/request-quote`)
- Interactive multi-step form to calculate and request custom print jobs.
- Direct file upload for vector artwork (PDF, AI, PSD, ZIP, JPG up to 15MB).
- Instant reference tracking code generation (`MDQ-2026-XXXXXX`).
- Real-time AJAX submission without full-page refreshes.

### 2. Lead & Quotation CRM (`/admin?view=enquiries`)
- View all quote and contact form leads.
- Update lead status (*New, Contacted, In Progress, Quoted, Won, Lost, Closed*).
- Download attached artwork files safely.
- Add private internal CRM notes and customer communication logs.

### 3. CMS Content Management
- **Products Catalog**: Full CRUD for products, paper options (GSM), special finishes (Gold foil, Spot UV), min quantities, and featured flags.
- **Services Manager**: Manage press capabilities (Offset, Digital, Packaging).
- **Portfolio Case Studies**: Showcase completed brand packaging runs with category filtering.
- **Technical Blog**: Publish prepress guides on paper weights, color spaces (RGB vs CMYK), and file bleed setups.
- **Testimonials & FAQs**: Manage client reviews and pre-sales Q&As.
- **Media Library**: Centralized asset manager.
- **Site Settings**: Dynamically update phone numbers, WhatsApp, business hours, Google Maps embed, SEO metadata, and homepage hero copy without editing code.

---

## Production Deployment & Hostinger Guide

### Step 1: Upload Files
Upload all files to the `public_html` directory of your Hostinger / cPanel account via FTP or File Manager.

### Step 2: Database Migration
1. Go to **Hostinger Control Panel -> Databases -> MySQL Databases**.
2. Create a new database and database user.
3. Open **phpMyAdmin**, select the new database, and import `database.sql`.

### Step 3: Update Config
Edit `config/config.php` on the server:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456789_mdprinters');
define('DB_USER', 'u123456789_user');
define('DB_PASS', 'YourStrongPassword123!');
```

### Step 4: Upload Directory Permissions
Ensure `uploads/` folder permissions are set to `755` or `775` so image and artwork uploads can be written securely.

### Step 5: SSL & HTTPS Setup
Hostinger provides free SSL certificates. Ensure Apache `mod_rewrite` is enabled. The included `.htaccess` file handles URL rewriting and HTTPS canonicalization.

---

## SEO & Sitemap Configuration

- Dynamic Sitemap: Accessible automatically at `/sitemap.xml`.
- Search Engine Robots: Accessible at `/robots.txt`.
- Schema.org Structured Data: Included on Homepage, Services, Products, and FAQ pages for LocalBusiness, Service, Product, and FAQPage.

---

## Security Verification Checklist

- [x] **PDO Prepared Statements**: Prevents SQL Injection across all controllers and models.
- [x] **XSS Prevention**: All public output rendered through `e()` (`htmlspecialchars`).
- [x] **CSRF Protection**: Form submit tokens validated on API endpoints and admin actions.
- [x] **File Upload Lock**: `.htaccess` inside `/uploads/` denies execution of PHP files.

---

&copy; 2026 MD Printers. All Rights Reserved.
