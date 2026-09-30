<?php
require_customer_login();
include INCLUDES_PATH . '/header.php';

$customer = current_customer();
$flash = get_flash();

$heroBadge    = 'MEMBER PROFILE';
$heroTitle    = 'Company & Account Settings';
$heroSubtitle = 'Update your company registration details, GSTIN number, and contact preferences.';
$heroIcon     = 'fas fa-building';
$breadcrumb   = ['Customer Portal' => SITE_URL . '/dashboard', 'Company Profile' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container" style="max-width: 860px; margin: 0 auto;">
        
        <?php if ($flash): ?>
            <div class="form-alert alert-<?= e($flash['type']) ?>" style="display: block; padding: 16px; border-radius: 14px; margin-bottom: 24px;">
                <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 36px; box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0;">Edit Company Information</h3>
                <a href="<?= SITE_URL ?>/dashboard" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>

            <form action="<?= SITE_URL ?>/dashboard/profile" method="POST">
                <?= csrf_field() ?>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Company / Business Name *</label>
                        <input type="text" name="company_name" required class="form-control" value="<?= e($customer['company_name']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Person Name *</label>
                        <input type="text" name="contact_person" required class="form-control" value="<?= e($customer['contact_person']) ?>">
                    </div>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Email Address (Read-only)</label>
                        <input type="email" class="form-control" readonly disabled value="<?= e($customer['email']) ?>" style="background: #f1f5f9; cursor: not-allowed;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number (Read-only)</label>
                        <input type="text" class="form-control" readonly disabled value="<?= e($customer['mobile']) ?>" style="background: #f1f5f9; cursor: not-allowed;">
                    </div>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="tel" name="whatsapp" class="form-control" value="<?= e($customer['whatsapp']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">GSTIN Number</label>
                        <input type="text" name="gst_number" class="form-control" style="text-transform: uppercase;" value="<?= e($customer['gst_number']) ?>">
                    </div>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Business Type</label>
                        <input type="text" name="business_type" class="form-control" value="<?= e($customer['business_type']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Company Website</label>
                        <input type="url" name="website" class="form-control" value="<?= e($customer['website']) ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Office Address</label>
                    <textarea name="address" rows="3" class="form-control"><?= e($customer['address']) ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px;">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= e($customer['city']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?= e($customer['state']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="<?= e($customer['pincode']) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 800; padding: 14px;">
                    <i class="fas fa-save"></i> Update Company Profile
                </button>
            </form>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
