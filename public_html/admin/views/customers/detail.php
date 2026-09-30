<?php
/**
 * MD Printers - Admin Customer Detail & Approval View
 */

$customer = Customer::getById($id);
if (!$customer) {
    set_flash('danger', 'Customer account not found.');
    redirect_to(ADMIN_URL . '?view=customers');
}

$addresses = Customer::getAddresses($id);

// Handle Status Change POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'CSRF verification failed.');
        redirect_to(ADMIN_URL . '?view=customers&action=detail&id=' . $id);
    }

    $actionType = $_POST['action_type'];
    $adminNotes = trim($_POST['admin_notes'] ?? '');
    $customerType = $_POST['customer_type'] ?? $customer['customer_type'];
    $creditLimit = isset($_POST['credit_limit']) ? (float)$_POST['credit_limit'] : (float)$customer['credit_limit'];

    if ($actionType === 'update_status') {
        $newStatus = $_POST['account_status'] ?? $customer['account_status'];
        if (Customer::updateStatus($id, $newStatus, $adminNotes, $customerType, $creditLimit)) {
            set_flash('success', "Customer account updated successfully! Status set to: " . strtoupper($newStatus));
        } else {
            set_flash('danger', 'Failed to update customer status.');
        }
        redirect_to(ADMIN_URL . '?view=customers&action=detail&id=' . $id);
    }
}

$flash = get_flash();
?>

<div class="admin-content">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <a href="<?= ADMIN_URL ?>?view=customers" style="color: #64748b; text-decoration: none;"><i class="fas fa-arrow-left"></i> Back to Customers</a>
                <span style="color: #cbd5e1;">|</span>
                <span style="font-weight: 800; font-size: 0.85rem; color: var(--cmyk-cyan);">CUSTOMER ID #<?= $customer['id'] ?></span>
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;"><?= e($customer['company_name']) ?></h1>
        </div>

        <div>
            <?php
                $statusBadge = 'badge-Contacted';
                if ($customer['account_status'] === 'approved') $statusBadge = 'badge-Won';
                if ($customer['account_status'] === 'rejected' || $customer['account_status'] === 'blocked') $statusBadge = 'badge-Lost';
                if ($customer['account_status'] === 'suspended') $statusBadge = 'badge-Quoted';
            ?>
            <span class="badge-status <?= $statusBadge ?>" style="font-size: 0.9rem; padding: 8px 16px;">
                STATUS: <?= e(strtoupper($customer['account_status'])) ?>
            </span>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert-<?= e($flash['type']) ?>" style="margin-bottom: 24px;">
            <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Main Detail Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        
        <div>
            <!-- Company Profile Card -->
            <div class="admin-card" style="padding: 28px; margin-bottom: 30px;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fas fa-building" style="color: var(--cmyk-cyan);"></i> Company & Account Details
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 0.92rem; margin-bottom: 20px;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">COMPANY NAME</span>
                        <strong style="color: #0f172a; font-size: 1.05rem;"><?= e($customer['company_name']) ?></strong>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">CONTACT PERSON</span>
                        <strong style="color: #0f172a; font-size: 1.05rem;"><?= e($customer['contact_person']) ?></strong>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 0.92rem; margin-bottom: 20px;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">MOBILE NUMBER</span>
                        <a href="tel:<?= e($customer['mobile']) ?>" style="color: var(--cmyk-cyan); font-weight: 800;"><i class="fas fa-phone-alt"></i> <?= e($customer['mobile']) ?></a>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">WHATSAPP NUMBER</span>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $customer['whatsapp'] ?: $customer['mobile']) ?>" target="_blank" style="color: #25D366; font-weight: 800;"><i class="fab fa-whatsapp"></i> <?= e($customer['whatsapp'] ?: $customer['mobile']) ?></a>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 0.92rem; margin-bottom: 20px;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">EMAIL ADDRESS</span>
                        <a href="mailto:<?= e($customer['email']) ?>" style="color: #0f172a; font-weight: 700;"><?= e($customer['email']) ?></a>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">GSTIN NUMBER</span>
                        <?php if (!empty($customer['gst_number'])): ?>
                            <strong style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.88rem;"><?= e($customer['gst_number']) ?></strong>
                        <?php else: ?>
                            <span style="color: #94a3b8;">Not Provided</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 0.92rem; margin-bottom: 20px;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">BUSINESS TYPE</span>
                        <strong style="color: #0f172a;"><?= e($customer['business_type'] ?: 'N/A') ?></strong>
                    </div>

                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">WEBSITE</span>
                        <?php if (!empty($customer['website'])): ?>
                            <a href="<?= e($customer['website']) ?>" target="_blank" style="color: var(--cmyk-cyan); font-weight: 700;"><?= e($customer['website']) ?> <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i></a>
                        <?php else: ?>
                            <span style="color: #94a3b8;">N/A</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($customer['address'])): ?>
                    <div style="margin-bottom: 20px;">
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700;">REGISTERED OFFICE ADDRESS</span>
                        <p style="color: #334155; margin: 4px 0 0 0; line-height: 1.6;">
                            <?= e($customer['address']) ?><br>
                            <?= e($customer['city']) ?>, <?= e($customer['state']) ?> - <?= e($customer['pincode']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($customer['interested_products'])): ?>
                    <div style="margin-bottom: 20px;">
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">INTERESTED PRODUCTS</span>
                        <span style="color: var(--cmyk-cyan); font-weight: 700;"><?= e($customer['interested_products']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($customer['notes'])): ?>
                    <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px dashed #cbd5e1;">
                        <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">CUSTOMER NOTES UPON REGISTRATION</span>
                        <p style="color: #334155; margin: 0; font-size: 0.9rem; line-height: 1.5;"><?= e($customer['notes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Saved Addresses -->
            <div class="admin-card" style="padding: 28px;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--cmyk-magenta);"></i> Saved Delivery Locations (<?= count($addresses) ?>)
                </h3>

                <?php if (empty($addresses)): ?>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">No shipping addresses registered yet.</p>
                <?php else: ?>
                    <div class="grid-2">
                        <?php foreach ($addresses as $a): ?>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; font-size: 0.88rem;">
                                <strong style="color: #0f172a; display: block; margin-bottom: 4px;"><?= e($a['title']) ?> (<?= e(strtoupper($a['address_type'])) ?>)</strong>
                                <p style="color: #475569; margin: 0; line-height: 1.5;">
                                    <?= e($a['address_line1']) ?><br>
                                    <?= e($a['city']) ?>, <?= e($a['state']) ?> - <?= e($a['pincode']) ?><br>
                                    Phone: <?= e($a['phone']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right Action Sidebar -->
        <div>
            <!-- Admin Status Approval Control Card -->
            <div class="admin-card" style="padding: 28px; margin-bottom: 30px;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fas fa-user-check" style="color: var(--cmyk-cyan);"></i> Admin Approval Controls
                </h3>

                <form action="<?= ADMIN_URL ?>?view=customers&action=detail&id=<?= $id ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action_type" value="update_status">

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Account Approval Status</label>
                        <select name="account_status" class="form-control" style="font-weight: 800;">
                            <option value="pending" <?= $customer['account_status'] === 'pending' ? 'selected' : '' ?>>⏳ Pending Approval</option>
                            <option value="approved" <?= $customer['account_status'] === 'approved' ? 'selected' : '' ?>>✅ Approved (Active Member)</option>
                            <option value="rejected" <?= $customer['account_status'] === 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                            <option value="suspended" <?= $customer['account_status'] === 'suspended' ? 'selected' : '' ?>>⚠️ Suspended</option>
                            <option value="blocked" <?= $customer['account_status'] === 'blocked' ? 'selected' : '' ?>>🚫 Blocked</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">B2B Customer Membership Tier</label>
                        <select name="customer_type" class="form-control" style="font-weight: 800;">
                            <option value="Retail" <?= $customer['customer_type'] === 'Retail' ? 'selected' : '' ?>>Retail Client</option>
                            <option value="Business" <?= $customer['customer_type'] === 'Business' ? 'selected' : '' ?>>Business / Enterprise</option>
                            <option value="Printer" <?= $customer['customer_type'] === 'Printer' ? 'selected' : '' ?>>Printer / Press Partner</option>
                            <option value="Dealer" <?= $customer['customer_type'] === 'Dealer' ? 'selected' : '' ?>>Dealer</option>
                            <option value="Distributor" <?= $customer['customer_type'] === 'Distributor' ? 'selected' : '' ?>>Distributor</option>
                            <option value="Premium Member" <?= $customer['customer_type'] === 'Premium Member' ? 'selected' : '' ?>>Premium VIP Member</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Approved B2B Credit Limit (₹)</label>
                        <input type="number" step="1000" name="credit_limit" value="<?= (float)$customer['credit_limit'] ?>" class="form-control" placeholder="e.g. 50000">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Internal Admin Remarks / Notes</label>
                        <textarea name="admin_notes" rows="3" placeholder="Reason for approval, pricing tier, terms..." class="form-control"><?= e($customer['admin_notes']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 800; padding: 12px;">
                        <i class="fas fa-save"></i> Save Status & Tier Changes
                    </button>
                </form>
            </div>

            <!-- Quick Info -->
            <div class="admin-card" style="padding: 24px; font-size: 0.88rem; color: #64748b;">
                <div style="margin-bottom: 10px;">
                    <strong>Registration Date:</strong><br>
                    <?= date('d M Y, h:i A', strtotime($customer['created_at'])) ?>
                </div>
                <div>
                    <strong>Last Login Timestamp:</strong><br>
                    <?= !empty($customer['last_login']) ? date('d M Y, h:i A', strtotime($customer['last_login'])) : 'Never logged in' ?>
                </div>
            </div>
        </div>

    </div>

</div>
