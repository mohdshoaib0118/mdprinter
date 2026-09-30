<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        foreach ($_POST as $key => $val) {
            if ($key !== 'csrf_token' && $key !== 'save_settings') {
                Setting::set($key, trim($val));
            }
        }
        $message = 'Site settings saved successfully.';
    }
}

$settings = Setting::getAll();
?>

<div style="margin-bottom: 30px;">
    <h2 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin-bottom: 4px;">Website & System Settings</h2>
    <p style="color: #64748b; font-size: 0.92rem; margin: 0;">Manage site branding, SEO tags, contact phone numbers, WhatsApp, and homepage hero text.</p>
</div>

<?php if ($message): ?>
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #047857; padding: 14px 20px; border-radius: 12px; font-weight: 700; margin-bottom: 24px;">
        <i class="fas fa-check-circle"></i> <?= e($message) ?>
    </div>
<?php endif; ?>

<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="admin-table-card" style="padding: 30px; margin-bottom: 30px; background: #ffffff;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--cmyk-cyan); margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <i class="fas fa-globe"></i> General Branding & Homepage Hero
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Brand Name</label>
                <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? 'MD Printers') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Tagline</label>
                <input type="text" name="site_tagline" value="<?= e($settings['site_tagline'] ?? 'Print That Makes Your Brand Impossible to Ignore') ?>" class="form-control">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Homepage Hero Headline</label>
            <input type="text" name="hero_title" value="<?= e($settings['hero_title'] ?? 'Print That Makes Your Brand Impossible to Ignore.') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Homepage Hero Subtitle</label>
            <textarea name="hero_subtitle" rows="2" class="form-control"><?= e($settings['hero_subtitle'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="admin-table-card" style="padding: 30px; margin-bottom: 30px; background: #ffffff;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--cmyk-magenta); margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <i class="fas fa-phone"></i> Contact Details & WhatsApp CTA
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 16px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Contact Phone Hotlines</label>
                <input type="text" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '+91 81461 41450, +91 98786 29214') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">WhatsApp Number (with Country Code)</label>
                <input type="text" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '918146141450') ?>" class="form-control" placeholder="918146141450">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Contact / Quote Email</label>
                <input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? 'info@mdprinters.com') ?>" class="form-control">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Business Facility Address</label>
            <textarea name="business_address" rows="2" class="form-control"><?= e($settings['business_address'] ?? 'Plot No. 360, Phase-1, Industrial Area, Chandigarh') ?></textarea>
        </div>

        <div class="form-group">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Google Maps Embed iFrame URL</label>
            <input type="text" name="google_maps_embed" value="<?= e($settings['google_maps_embed'] ?? '') ?>" class="form-control">
        </div>
    </div>

    <!-- Social Media Channels & Handles Card -->
    <div class="admin-table-card" style="padding: 30px; margin-bottom: 30px; background: #ffffff;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #00aeef; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <i class="fas fa-share-alt"></i> Social Media Links & Handles
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;"><i class="fab fa-facebook" style="color: #1877f2;"></i> Facebook Page URL</label>
                <input type="url" name="facebook_url" value="<?= e($settings['facebook_url'] ?? '') ?>" class="form-control" placeholder="https://facebook.com/yourpage">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;"><i class="fab fa-instagram" style="color: #e4405f;"></i> Instagram Profile URL</label>
                <input type="url" name="instagram_url" value="<?= e($settings['instagram_url'] ?? '') ?>" class="form-control" placeholder="https://instagram.com/yourhandle">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;"><i class="fab fa-linkedin" style="color: #0a66c2;"></i> LinkedIn Page URL</label>
                <input type="url" name="linkedin_url" value="<?= e($settings['linkedin_url'] ?? '') ?>" class="form-control" placeholder="https://linkedin.com/company/yourbrand">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;"><i class="fab fa-twitter" style="color: #1da1f2;"></i> Twitter / X Profile URL</label>
                <input type="url" name="twitter_url" value="<?= e($settings['twitter_url'] ?? '') ?>" class="form-control" placeholder="https://x.com/yourhandle">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;"><i class="fab fa-youtube" style="color: #ff0000;"></i> YouTube Channel URL</label>
                <input type="url" name="youtube_url" value="<?= e($settings['youtube_url'] ?? '') ?>" class="form-control" placeholder="https://youtube.com/@yourchannel">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;"><i class="fab fa-whatsapp" style="color: #25d366;"></i> WhatsApp Direct Chat Link / Phone</label>
                <input type="text" name="whatsapp_url" value="<?= e($settings['whatsapp_url'] ?? '') ?>" class="form-control" placeholder="https://wa.me/918146141450">
            </div>
        </div>
    </div>

    <div class="admin-table-card" style="padding: 30px; margin-bottom: 30px; background: #ffffff;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--cmyk-gold); margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <i class="fas fa-search"></i> SEO Defaults
        </h3>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Default Meta Title</label>
            <input type="text" name="default_meta_title" value="<?= e($settings['default_meta_title'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Default Meta Description</label>
            <textarea name="default_meta_description" rows="2" class="form-control"><?= e($settings['default_meta_description'] ?? '') ?></textarea>
        </div>
    </div>

    <div style="text-align: right;">
        <button type="submit" name="save_settings" class="btn btn-primary btn-lg" style="font-weight: 900; padding: 14px 28px;"><i class="fas fa-save"></i> Save All Settings</button>
    </div>
</form>

