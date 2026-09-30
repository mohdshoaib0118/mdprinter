<?php
/**
 * MD Printers Admin - Quotation Detail & Edit Panel
 */
require_once MODELS_PATH . '/Quote.php';

$quoteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quote = Quote::findById($quoteId);

if (!$quote) {
    set_flash('danger', 'Quotation not found.');
    redirect_to(ADMIN_URL . '?view=quotations');
}

// Process Admin Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quote'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'CSRF security check failed.');
        redirect_to(ADMIN_URL . '?view=quotations&action=detail&id=' . $quoteId);
    }

    $updateData = [
        'subtotal' => (float)$_POST['subtotal'],
        'discount' => (float)$_POST['discount'],
        'tax' => (float)$_POST['tax'],
        'shipping' => (float)$_POST['shipping'],
        'total' => (float)$_POST['total'],
        'status' => $_POST['status'],
        'valid_until' => $_POST['valid_until'],
        'admin_notes' => $_POST['admin_notes'] ?? ''
    ];

    if (Quote::adminUpdate($quoteId, $updateData)) {
        set_flash('success', 'Quotation #' . $quote['quote_number'] . ' updated successfully!');
        redirect_to(ADMIN_URL . '?view=quotations&action=detail&id=' . $quoteId);
    } else {
        set_flash('danger', 'Failed to update quotation.');
    }
}
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <a href="<?= ADMIN_URL ?>?view=quotations" style="color: var(--cmyk-cyan); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
            <i class="fas fa-arrow-left"></i> Back to Quotations Directory
        </a>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">Quotation #<?= e($quote['quote_number']) ?></h1>
    </div>
</div>

<?php display_flash(); ?>

<div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; align-items: start;">

    <!-- Left Column: Customer & Line Items Info -->
    <div>
        <!-- Customer Info Card -->
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">B2B Customer Profile</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.9rem;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Company Name</span>
                    <div style="font-weight: 800; color: #0f172a; font-size: 1.05rem;"><?= e($quote['company_name'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Contact Person</span>
                    <div style="font-weight: 700; color: #0f172a;"><?= e($quote['contact_person'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Email Address</span>
                    <div style="font-weight: 600; color: #0f172a;"><?= e($quote['email'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Mobile / WhatsApp</span>
                    <div style="font-weight: 600; color: #0f172a;"><?= e($quote['mobile'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">GSTIN</span>
                    <div style="font-weight: 600; color: #0f172a;"><?= e($quote['gstin'] ?? 'Not Specified') ?></div>
                </div>
            </div>
        </div>

        <!-- Line Items Card -->
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Configured Print Specifications</h4>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                            <th style="padding: 12px 16px;">Item</th>
                            <th style="padding: 12px 16px;">Selected Options</th>
                            <th style="padding: 12px 16px; text-align: center;">Qty</th>
                            <th style="padding: 12px 16px; text-align: right;">Unit Price</th>
                            <th style="padding: 12px 16px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quote['items'] as $it): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; font-weight: 800; color: #0f172a;">
                                    <?= e($it['product_name_snapshot']) ?>
                                </td>
                                <td style="padding: 14px 16px; color: #475569; font-size: 0.85rem;">
                                    <?php
                                        $cfg = json_decode($it['configuration_json'] ?? '{}', true);
                                        if (!empty($cfg) && is_array($cfg)) {
                                            foreach ($cfg as $k => $v) {
                                                $vStr = is_array($v) ? implode(', ', array_filter($v, 'is_scalar')) : (string)($v ?? '');
                                                echo "<div><strong>" . e(ucwords(str_replace('_', ' ', (string)$k))) . ":</strong> " . e($vStr) . "</div>";
                                            }
                                        }
                                    ?>
                                </td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: #0f172a;">
                                    <?= e($it['quantity']) ?>
                                </td>
                                <td style="padding: 14px 16px; text-align: right; color: #475569;">
                                    ₹<?= number_format($it['unit_price'], 2) ?>
                                </td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 800; color: #0f172a;">
                                    ₹<?= number_format($it['subtotal'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Admin Overrides Form -->
    <div>
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">Admin Pricing & Status Control</h4>

            <form action="<?= ADMIN_URL ?>?view=quotations&action=detail&id=<?= $quoteId ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                <input type="hidden" name="update_quote" value="1">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Quote Status</label>
                    <select name="status" class="form-control" style="font-weight: 700;">
                        <option value="requested" <?= $quote['status'] === 'requested' ? 'selected' : '' ?>>Requested (Pending Review)</option>
                        <option value="preparing" <?= $quote['status'] === 'preparing' ? 'selected' : '' ?>>Preparing Formal Offer</option>
                        <option value="sent" <?= $quote['status'] === 'sent' ? 'selected' : '' ?>>Sent to Customer</option>
                        <option value="accepted" <?= $quote['status'] === 'accepted' ? 'selected' : '' ?>>Accepted by Client</option>
                        <option value="rejected" <?= $quote['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="expired" <?= $quote['status'] === 'expired' ? 'selected' : '' ?>>Expired</option>
                        <option value="converted" <?= $quote['status'] === 'converted' ? 'selected' : '' ?>>Converted to Order</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Valid Until Date</label>
                    <input type="date" name="valid_until" value="<?= date('Y-m-d', strtotime($quote['valid_until'] ?? '+14 days')) ?>" class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Subtotal (₹)</label>
                    <input type="number" step="0.01" name="subtotal" id="admin_subtotal" value="<?= $quote['subtotal'] ?>" class="form-control" oninput="recalcAdminTotal()">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Discount (₹)</label>
                    <input type="number" step="0.01" name="discount" id="admin_discount" value="<?= $quote['discount'] ?>" class="form-control" oninput="recalcAdminTotal()">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">GST Tax (₹)</label>
                    <input type="number" step="0.01" name="tax" id="admin_tax" value="<?= $quote['tax'] ?>" class="form-control" oninput="recalcAdminTotal()">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Shipping / Freight (₹)</label>
                    <input type="number" step="0.01" name="shipping" id="admin_shipping" value="<?= $quote['shipping'] ?>" class="form-control" oninput="recalcAdminTotal()">
                </div>

                <div class="form-group" style="margin-bottom: 20px; background: #f8fafc; padding: 16px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <label style="font-weight: 900; color: #0f172a; font-size: 0.95rem;">Final Calculated Total (₹)</label>
                    <input type="number" step="0.01" name="total" id="admin_total" value="<?= $quote['total'] ?>" class="form-control" style="font-size: 1.2rem; font-weight: 900; color: var(--cmyk-cyan);" readonly>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Admin Internal Notes</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Notes regarding press setup, custom die charges, or customer negotiation..."><?= e($quote['admin_notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 800; padding: 14px;">
                    <i class="fas fa-save"></i> Save Quotation Changes
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function recalcAdminTotal() {
    var sub = parseFloat(document.getElementById('admin_subtotal').value) || 0;
    var disc = parseFloat(document.getElementById('admin_discount').value) || 0;
    var tax = parseFloat(document.getElementById('admin_tax').value) || 0;
    var ship = parseFloat(document.getElementById('admin_shipping').value) || 0;
    
    var tot = (sub - disc) + tax + ship;
    document.getElementById('admin_total').value = tot.toFixed(2);
}
</script>
