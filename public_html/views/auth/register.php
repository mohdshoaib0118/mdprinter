<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'B2B MEMBER REGISTRATION';
$heroTitle    = 'Register B2B Printing Account';
$heroSubtitle = 'Join MD Printers member network for exclusive trade pricing, credit lines, artwork proofing, and express production.';
$heroIcon     = 'fas fa-user-plus';
$breadcrumb   = ['Register' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';

$flash = get_flash();
?>

<section style="padding: 70px 0; background: #ffffff;">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 24px; padding: 40px; box-shadow: var(--shadow-xl);">
            
            <div style="text-align: center; margin-bottom: 32px;">
                <span style="display: inline-flex; align-items: center; gap: 8px; background: rgba(0, 168, 232, 0.08); border: 1px solid rgba(0, 168, 232, 0.2); padding: 6px 16px; border-radius: 20px; color: var(--cmyk-cyan); font-weight: 800; font-size: 0.8rem; text-transform: uppercase;">
                    <i class="fas fa-building"></i> Commercial Printers & Corporate Membership
                </span>
                <h2 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin-top: 10px; margin-bottom: 6px;">Create Your B2B Account</h2>
                <p style="color: #64748b; font-size: 0.95rem;">Accounts require admin verification. Once approved, you can access trade rates & instant orders.</p>
            </div>

            <?php if ($flash): ?>
                <div class="form-alert alert-<?= e($flash['type']) ?>" style="display: block; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
                    <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= SITE_URL ?>/register" method="POST">
                <?= csrf_field() ?>

                <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--cmyk-cyan); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px;">
                    <i class="fas fa-briefcase"></i> Company & Contact Credentials
                </h4>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Business / Company Name *</label>
                        <input type="text" name="company_name" required placeholder="e.g. Apex Print & Media Pvt Ltd" class="form-control" value="<?= e($_POST['company_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Person Name *</label>
                        <input type="text" name="contact_person" required placeholder="e.g. Rahul Sharma" class="form-control" value="<?= e($_POST['contact_person'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Mobile Number *</label>
                        <input type="tel" name="mobile" required placeholder="+91 98765 43210" class="form-control" value="<?= e($_POST['mobile'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">WhatsApp Number (Optional)</label>
                        <input type="tel" name="whatsapp" placeholder="+91 98765 43210" class="form-control" value="<?= e($_POST['whatsapp'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" required placeholder="rahul@company.com" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Account Password *</label>
                        <input type="password" name="password" required placeholder="Min 6 characters" class="form-control">
                    </div>
                </div>

                <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--cmyk-magenta); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px;">
                    <i class="fas fa-file-invoice"></i> Business Details & GST
                </h4>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">GSTIN Number (Optional)</label>
                        <input type="text" name="gst_number" placeholder="22AAAAA0000A1Z5" class="form-control" style="text-transform: uppercase;" value="<?= e($_POST['gst_number'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Business Type</label>
                        <select name="business_type" class="form-control">
                            <option value="Print Reseller / Broker">Print Reseller / Broker</option>
                            <option value="Advertising / Creative Agency">Advertising / Creative Agency</option>
                            <option value="Corporate Brand / Enterprise">Corporate Brand / Enterprise</option>
                            <option value="Retail Brand / E-commerce">Retail Brand / E-commerce</option>
                            <option value="Packaging Client">Packaging Client</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Registered Office Address</label>
                    <textarea name="address" rows="2" placeholder="Building, Street, Area" class="form-control"><?= e($_POST['address'] ?? '') ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" placeholder="e.g. New Delhi" class="form-control" value="<?= e($_POST['city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" placeholder="e.g. Delhi" class="form-control" value="<?= e($_POST['state'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" placeholder="110020" class="form-control" value="<?= e($_POST['pincode'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label class="form-label">Company Website (Optional)</label>
                    <input type="url" name="website" placeholder="https://www.company.com" class="form-control" value="<?= e($_POST['website'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 900; font-size: 1.05rem; padding: 16px;">
                    <i class="fas fa-check-circle"></i> Submit B2B Account Registration
                </button>

                <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: #64748b;">
                    Already have an approved account? <a href="<?= SITE_URL ?>/login" style="color: var(--cmyk-cyan); font-weight: 800;">Log In to Member Portal <i class="fas fa-arrow-right"></i></a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
