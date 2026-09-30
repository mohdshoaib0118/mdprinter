<?php
/**
 * MD Printers - Admin Settings & Homepage CMS Control Manager
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $flash = '<div class="alert-error">CSRF Token Verification Failed.</div>';
    } else {
        $settingsToUpdate = [
            'site_name', 'site_tagline', 'phone_primary', 'phone_secondary', 'email_primary', 'email_quotes',
            'whatsapp_number', 'address_street', 'address_city', 'business_hours',
            'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url',
            'meta_default_title', 'meta_default_description', 'meta_default_keywords',
            'google_analytics_id', 'google_tag_manager_id', 'google_maps_iframe',
            'hero_badge', 'hero_title', 'hero_subtitle', 'stat_years', 'stat_projects', 'stat_clients', 'stat_turnaround'
        ];

        foreach ($settingsToUpdate as $key) {
            if (isset($_POST[$key])) {
                $val = $_POST[$key];
                if ($key !== 'google_maps_iframe') {
                    $val = sanitizeInput($val);
                }
                
                // Check if key exists
                $exists = $db->fetch("SELECT setting_key FROM site_settings WHERE setting_key = :k", ['k' => $key]);
                if ($exists) {
                    $db->update('site_settings', ['setting_value' => $val], 'setting_key = :k', ['k' => $key]);
                } else {
                    $db->insert('site_settings', ['setting_key' => $key, 'setting_value' => $val, 'setting_group' => 'general']);
                }
            }
        }

        $flash = '<div style="background: rgba(34, 197, 94, 0.15); border: 1px solid #22C55E; color: #4ADE80; padding: 14px; border-radius: 8px; margin-bottom: 24px;"><i class="fas fa-check-circle"></i> Website Settings & Homepage CMS successfully saved!</div>';
    }
}

$adminTitle = 'Site Settings & CMS Manager';
$adminActivePage = 'settings';

include __DIR__ . '/../includes/admin-header.php';
?>

<?= $flash ?>

<div class="admin-card" style="max-width: 900px;">
    <div class="admin-card-header">
        <h2><i class="fas fa-sliders-h" style="color: var(--admin-primary); margin-right: 8px;"></i> Website Settings & CMS Control</h2>
    </div>

    <form action="" method="POST">
        <?= csrfField() ?>

        <h3 style="color: var(--admin-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; margin-bottom: 20px;">
            1. Business Contact & Address
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Company / Brand Name</label>
                <input type="text" name="site_name" class="form-control" value="<?= e(get_setting('site_name')) ?>">
            </div>
            <div class="form-group">
                <label>Primary Phone</label>
                <input type="text" name="phone_primary" class="form-control" value="<?= e(get_setting('phone_primary')) ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Primary Email</label>
                <input type="email" name="email_primary" class="form-control" value="<?= e(get_setting('email_primary')) ?>">
            </div>
            <div class="form-group">
                <label>Quote Notification Email</label>
                <input type="email" name="email_quotes" class="form-control" value="<?= e(get_setting('email_quotes')) ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>WhatsApp Contact Number (digits only)</label>
                <input type="text" name="whatsapp_number" class="form-control" value="<?= e(get_setting('whatsapp_number')) ?>">
            </div>
            <div class="form-group">
                <label>Business Operating Hours</label>
                <input type="text" name="business_hours" class="form-control" value="<?= e(get_setting('business_hours')) ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="address_street" class="form-control" value="<?= e(get_setting('address_street')) ?>">
            </div>
            <div class="form-group">
                <label>City, State Zip</label>
                <input type="text" name="address_city" class="form-control" value="<?= e(get_setting('address_city')) ?>">
            </div>
        </div>

        <h3 style="color: var(--admin-magenta); border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; margin: 30px 0 20px 0;">
            2. Homepage CMS Hero & Statistics
        </h3>

        <div class="form-group">
            <label>Hero Badge Pill Text</label>
            <input type="text" name="hero_badge" class="form-control" value="<?= e(get_setting('hero_badge')) ?>">
        </div>

        <div class="form-group">
            <label>Hero Headline (Main H1 Title)</label>
            <input type="text" name="hero_title" class="form-control" value="<?= e(get_setting('hero_title')) ?>">
        </div>

        <div class="form-group">
            <label>Hero Subtitle / Description Paragraph</label>
            <textarea name="hero_subtitle" class="form-control" rows="3"><?= e(get_setting('hero_subtitle')) ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div class="form-group">
                <label>Years Stat</label>
                <input type="text" name="stat_years" class="form-control" value="<?= e(get_setting('stat_years')) ?>">
            </div>
            <div class="form-group">
                <label>Projects Stat</label>
                <input type="text" name="stat_projects" class="form-control" value="<?= e(get_setting('stat_projects')) ?>">
            </div>
            <div class="form-group">
                <label>Clients Stat</label>
                <input type="text" name="stat_clients" class="form-control" value="<?= e(get_setting('stat_clients')) ?>">
            </div>
            <div class="form-group">
                <label>Turnaround Stat</label>
                <input type="text" name="stat_turnaround" class="form-control" value="<?= e(get_setting('stat_turnaround')) ?>">
            </div>
        </div>

        <h3 style="color: #FFD600; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; margin: 30px 0 20px 0;">
            3. Global SEO & Analytics
        </h3>

        <div class="form-group">
            <label>Default Meta Title</label>
            <input type="text" name="meta_default_title" class="form-control" value="<?= e(get_setting('meta_default_title')) ?>">
        </div>

        <div class="form-group">
            <label>Default Meta Description</label>
            <textarea name="meta_default_description" class="form-control" rows="2"><?= e(get_setting('meta_default_description')) ?></textarea>
        </div>

        <div class="form-group">
            <label>Google Maps Embed Iframe Code</label>
            <textarea name="google_maps_iframe" class="form-control" rows="3"><?= e(get_setting('google_maps_iframe')) ?></textarea>
        </div>

        <button type="submit" class="btn-admin btn-admin-primary" style="padding: 14px 28px; font-size: 1rem; margin-top: 10px;">
            <i class="fas fa-save"></i> Save All Settings
        </button>
    </form>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
