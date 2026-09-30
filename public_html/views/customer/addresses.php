<?php
require_customer_login();
include INCLUDES_PATH . '/header.php';

$customer = current_customer();
$addresses = Customer::getAddresses((int)$customer['id']);
$flash = get_flash();

$heroBadge    = 'ADDRESS BOOK';
$heroTitle    = 'Shipping & Billing Addresses';
$heroSubtitle = 'Manage multiple delivery locations, warehouse destinations, and billing addresses for fast checkout.';
$heroIcon     = 'fas fa-map-marker-alt';
$breadcrumb   = ['Customer Portal' => SITE_URL . '/dashboard', 'Address Book' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        
        <?php if ($flash): ?>
            <div class="form-alert alert-<?= e($flash['type']) ?>" style="display: block; padding: 16px; border-radius: 14px; margin-bottom: 24px;">
                <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0;">Saved Addresses (<?= count($addresses) ?>)</h3>
                <p style="color: #64748b; font-size: 0.88rem; margin: 2px 0 0 0;">Add shipping locations for doorstep dispatch</p>
            </div>

            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addAddressForm').style.display='block'; window.scrollTo({top: document.getElementById('addAddressForm').offsetTop - 100, behavior:'smooth'});">
                <i class="fas fa-plus"></i> Add New Address
            </button>
        </div>

        <!-- Address Grid Cards -->
        <div class="grid-3" style="margin-bottom: 40px;">
            <?php foreach ($addresses as $addr): ?>
                <div style="background: #ffffff; border: 1.5px solid <?= $addr['is_default'] ? 'var(--cmyk-cyan)' : '#e2e8f0' ?>; border-radius: 18px; padding: 24px; box-shadow: var(--shadow-sm); position: relative;">
                    <?php if ($addr['is_default']): ?>
                        <span style="position: absolute; top: 16px; right: 16px; background: rgba(0, 168, 232, 0.12); color: var(--cmyk-cyan); padding: 4px 10px; border-radius: 12px; font-weight: 800; font-size: 0.75rem; uppercase;">
                            DEFAULT
                        </span>
                    <?php endif; ?>

                    <span style="display: inline-block; font-size: 0.78rem; font-weight: 800; color: var(--cmyk-magenta); text-transform: uppercase; margin-bottom: 6px;">
                        <?= e($addr['address_type']) ?>
                    </span>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;"><?= e($addr['title']) ?></h4>
                    <p style="color: #475569; font-size: 0.9rem; line-height: 1.6; margin-bottom: 16px;">
                        <strong><?= e($addr['contact_person'] ?: $addr['company_name']) ?></strong><br>
                        <?= e($addr['address_line1']) ?><br>
                        <?php if ($addr['address_line2']): ?><?= e($addr['address_line2']) ?><br><?php endif; ?>
                        <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <strong><?= e($addr['pincode']) ?></strong><br>
                        <i class="fas fa-phone-alt" style="color: #94a3b8; margin-right: 4px;"></i> <?= e($addr['phone']) ?>
                    </p>

                    <form action="<?= SITE_URL ?>/dashboard/addresses" method="POST" style="margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="address_id" value="<?= e($addr['id']) ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this address?');" class="btn btn-outline btn-sm" style="color: #ef4444; border-color: #fca5a5; width: 100%;">
                            <i class="fas fa-trash-alt"></i> Remove Address
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add Address Form -->
        <div id="addAddressForm" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 20px; padding: 36px; box-shadow: var(--shadow-lg); <?= empty($addresses) ? '' : 'display: none;' ?>">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                <i class="fas fa-plus-circle" style="color: var(--cmyk-cyan);"></i> Add New Shipping Address
            </h3>

            <form action="<?= SITE_URL ?>/dashboard/addresses" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Address Title / Label *</label>
                        <input type="text" name="title" required placeholder="e.g. Main Factory / Central Warehouse" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address Type</label>
                        <select name="address_type" class="form-control">
                            <option value="shipping">Shipping Address</option>
                            <option value="billing">Billing Address</option>
                            <option value="office">Office Address</option>
                            <option value="warehouse">Warehouse Address</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Receiver Contact Person</label>
                        <input type="text" name="contact_person" placeholder="John Doe" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Receiver Phone *</label>
                        <input type="tel" name="phone" required placeholder="+91 98765 43210" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Street Address / Line 1 *</label>
                    <textarea name="address_line1" required rows="2" placeholder="Building No., Street, Industrial Area" class="form-control"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label class="form-label">City *</label>
                        <input type="text" name="city" required placeholder="e.g. New Delhi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State *</label>
                        <input type="text" name="state" required placeholder="e.g. Delhi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pincode *</label>
                        <input type="text" name="pincode" required placeholder="110020" class="form-control">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 700; color: #334155; cursor: pointer;">
                        <input type="checkbox" name="is_default" value="1" checked style="width: 18px; height: 18px;"> Set as Default Address
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 800; padding: 14px;">
                    <i class="fas fa-save"></i> Save Address to Book
                </button>
            </form>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
