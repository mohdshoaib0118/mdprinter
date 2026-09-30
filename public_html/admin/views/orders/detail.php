<?php
/**
 * MD Printers Admin - B2B Order Detail & Production Control Panel
 */
require_once MODELS_PATH . '/Order.php';
require_once MODELS_PATH . '/Artwork.php';
require_once MODELS_PATH . '/Invoice.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = Order::findById($orderId);

if (!$order) {
    set_flash('danger', 'Order not found.');
    redirect_to(ADMIN_URL . '?view=orders');
}

$invoice = Invoice::findByOrderId($orderId);

// Process Admin Form Post Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'CSRF security verification failed.');
        redirect_to(ADMIN_URL . '?view=orders&action=detail&id=' . $orderId);
    }

    $act = $_POST['action'] ?? '';

    if ($act === 'update_status') {
        $newStatus = $_POST['status'] ?? 'pending';
        $comment = trim($_POST['status_comment'] ?? '');

        if (Order::updateStatus($orderId, $newStatus, 'admin', (int)($_SESSION['admin_id'] ?? 1), $comment)) {
            set_flash('success', 'Order status updated to: ' . ucwords(str_replace('_', ' ', $newStatus)));
        } else {
            set_flash('danger', 'Failed to update order status.');
        }

    } elseif ($act === 'update_shipping') {
        if (Order::updateShipping($orderId, $_POST)) {
            set_flash('success', 'Logistics & tracking details updated!');
        } else {
            set_flash('danger', 'Failed to update tracking details.');
        }

    } elseif ($act === 'artwork_action') {
        $artworkId = (int)$_POST['artwork_id'];
        $artworkStatus = $_POST['artwork_status']; // approved or correction_required
        $adminComment = trim($_POST['admin_comment'] ?? '');

        if (Artwork::updateStatus($artworkId, $artworkStatus, $adminComment, (int)($_SESSION['admin_id'] ?? 1))) {
            set_flash('success', 'Artwork status updated to ' . strtoupper($artworkStatus) . '!');
        } else {
            set_flash('danger', 'Failed to update artwork status.');
        }

    } elseif ($act === 'generate_invoice') {
        $inv = Invoice::generateForOrder($order);
        if ($inv) {
            set_flash('success', 'B2B GST Invoice #' . $inv['invoice_number'] . ' generated!');
        } else {
            set_flash('danger', 'Failed to generate invoice.');
        }
    }

    redirect_to(ADMIN_URL . '?view=orders&action=detail&id=' . $orderId);
}

$billingAddr = json_decode($order['billing_address_json'] ?? '{}', true);
$shippingAddr = json_decode($order['shipping_address_json'] ?? '{}', true);
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <a href="<?= ADMIN_URL ?>?view=orders" style="color: var(--cmyk-cyan); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
            <i class="fas fa-arrow-left"></i> Back to Orders Directory
        </a>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">Order #<?= e($order['order_number']) ?></h1>
    </div>
    <div style="display: flex; gap: 12px;">
        <?php if ($invoice): ?>
            <a href="<?= SITE_URL ?>/dashboard/orders?id=<?= $order['id'] ?>&print_invoice=1" target="_blank" class="btn" style="background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); font-weight: 800; border: 1px solid var(--cmyk-cyan);">
                <i class="fas fa-print"></i> Print Invoice #<?= e($invoice['invoice_number']) ?>
            </a>
        <?php else: ?>
            <form action="<?= ADMIN_URL ?>?view=orders&action=detail&id=<?= $orderId ?>" method="POST" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                <input type="hidden" name="action" value="generate_invoice">
                <button type="submit" class="btn btn-primary" style="font-weight: 800;">
                    <i class="fas fa-file-invoice"></i> Generate GST Invoice
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php display_flash(); ?>

<div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; align-items: start;">

    <!-- Left Column: Customer Details, Items & Artwork Review -->
    <div>
        <!-- Customer Info Card -->
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Customer & Shipping Address Profile</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.9rem;">
                <div>
                    <span style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Company</span>
                    <div style="font-weight: 900; color: #0f172a; font-size: 1.05rem;"><?= e($order['company_name'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Contact Person</span>
                    <div style="font-weight: 700; color: #0f172a;"><?= e($order['contact_person'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Shipping Address</span>
                    <div style="font-weight: 600; color: #0f172a;">
                        <?= e($shippingAddr['address_line1'] ?? '') ?>, <?= e($shippingAddr['city'] ?? '') ?>, <?= e($shippingAddr['state'] ?? '') ?> - <?= e($shippingAddr['pincode'] ?? '') ?>
                    </div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Client GSTIN</span>
                    <div style="font-weight: 700; color: #0f172a;"><?= e($order['gstin'] ?: ($shippingAddr['gstin'] ?? 'Not Provided')) ?></div>
                </div>
            </div>
        </div>

        <!-- Order Items & Artwork Version Review Panel -->
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Order Items & Artwork Review Panel</h4>

            <?php foreach ($order['items'] as $item): 
                $artworks = Artwork::getVersionsForItem($item['id']);
            ?>
                <div style="border: 1px solid #cbd5e1; border-radius: 14px; padding: 18px; margin-bottom: 20px; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        <div>
                            <h5 style="font-size: 1.05rem; font-weight: 900; color: #0f172a; margin: 0;"><?= e($item['product_name_snapshot']) ?></h5>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">
                                Qty: <strong><?= e($item['quantity']) ?> units</strong> | Unit Price: <strong>₹<?= number_format($item['unit_price'], 2) ?></strong> | Total: <strong style="color: var(--cmyk-cyan);">₹<?= number_format($item['total'], 2) ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Config Specs -->
                    <div style="font-size: 0.85rem; color: #475569; background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 14px;">
                        <?php
                            $cfg = json_decode($item['configuration_json'] ?? '{}', true);
                            if (is_array($cfg)) {
                                foreach ($cfg as $k => $v) {
                                    $vStr = is_array($v) ? implode(', ', array_filter($v, 'is_scalar')) : (string)($v ?? '');
                                    echo "<span style='margin-right:12px;'><strong>" . e(ucwords(str_replace('_', ' ', (string)$k))) . ":</strong> " . e($vStr) . "</span>";
                                }
                            }
                        ?>
                    </div>

                    <!-- Artwork Version List & Admin Review Actions -->
                    <h6 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-bottom: 10px;">Submitted Artwork Versions:</h6>
                    <?php if (empty($artworks)): ?>
                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 700; padding: 8px; background: rgba(239,68,68,0.06); border-radius: 8px;">
                            <i class="fas fa-exclamation-triangle"></i> Customer has not uploaded artwork for this item yet.
                        </div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <?php foreach ($artworks as $art): ?>
                                <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; background: #f8fafc;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <div>
                                            <strong style="color: #0f172a; font-size: 0.9rem;">Version <?= $art['version_number'] ?>:</strong>
                                            <span style="color: #334155; margin-left: 6px; font-weight: 700;"><?= e($art['original_filename']) ?></span>
                                            <span style="color: #64748b; font-size: 0.78rem; margin-left: 8px;">(<?= round($art['file_size']/1024/1024, 2) ?> MB)</span>
                                        </div>
                                        <div style="display: flex; gap: 8px; align-items: center;">
                                            <span style="font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 8px; text-transform: uppercase; background: <?= $art['status'] === 'approved' ? '#10b981' : ($art['status'] === 'correction_required' ? '#ef4444' : '#f59e0b') ?>; color: #fff;">
                                                <?= e($art['status']) ?>
                                            </span>
                                            <a href="<?= SITE_URL ?>/<?= e($art['file_path']) ?>" download target="_blank" class="btn btn-sm" style="background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; padding: 4px 10px; font-size: 0.8rem;">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>

                                    <?php if (!empty($art['customer_comment'])): ?>
                                        <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 8px;">
                                            Customer Note: <em>"<?= e($art['customer_comment']) ?>"</em>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Admin Approval / Correction Trigger Form -->
                                    <form action="<?= ADMIN_URL ?>?view=orders&action=detail&id=<?= $orderId ?>" method="POST" style="display: grid; grid-template-columns: 1fr auto auto; gap: 8px; align-items: center; border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 6px;">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                        <input type="hidden" name="action" value="artwork_action">
                                        <input type="hidden" name="artwork_id" value="<?= $art['id'] ?>">

                                        <input type="text" name="admin_comment" value="<?= e($art['admin_comment'] ?? '') ?>" placeholder="Review comment for client (e.g. Bleed margin looks fine)..." class="form-control" style="font-size: 0.82rem;">

                                        <button type="submit" name="artwork_status" value="approved" class="btn btn-sm" style="background: #10b981; color: #fff; font-weight: 800; border: 0; padding: 6px 12px; font-size: 0.8rem;">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button type="submit" name="artwork_status" value="correction_required" class="btn btn-sm" style="background: #ef4444; color: #fff; font-weight: 800; border: 0; padding: 6px 12px; font-size: 0.8rem;">
                                            <i class="fas fa-exclamation-circle"></i> Request Revision
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Column: Status & Logistics Controls -->
    <div>
        <!-- Production Status Control Card -->
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Production Status Controller</h4>

            <form action="<?= ADMIN_URL ?>?view=orders&action=detail&id=<?= $orderId ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                <input type="hidden" name="action" value="update_status">

                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Production Stage</label>
                    <select name="status" class="form-control" style="font-weight: 700;">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                        <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Order Confirmed</option>
                        <option value="artwork_pending" <?= $order['status'] === 'artwork_pending' ? 'selected' : '' ?>>Artwork Pending Upload</option>
                        <option value="artwork_under_review" <?= $order['status'] === 'artwork_under_review' ? 'selected' : '' ?>>Artwork Under Review</option>
                        <option value="artwork_correction_required" <?= $order['status'] === 'artwork_correction_required' ? 'selected' : '' ?>>Artwork Revision Requested</option>
                        <option value="artwork_approved" <?= $order['status'] === 'artwork_approved' ? 'selected' : '' ?>>Artwork Approved</option>
                        <option value="production_queued" <?= $order['status'] === 'production_queued' ? 'selected' : '' ?>>Queued in Press</option>
                        <option value="production_started" <?= $order['status'] === 'production_started' ? 'selected' : '' ?>>Offset / Digital Printing Started</option>
                        <option value="finishing" <?= $order['status'] === 'finishing' ? 'selected' : '' ?>>Post-Press Finishing (Foil/Lamination)</option>
                        <option value="quality_check" <?= $order['status'] === 'quality_check' ? 'selected' : '' ?>>Quality Check (QC)</option>
                        <option value="ready_to_dispatch" <?= $order['status'] === 'ready_to_dispatch' ? 'selected' : '' ?>>Ready to Dispatch</option>
                        <option value="dispatched" <?= $order['status'] === 'dispatched' ? 'selected' : '' ?>>Dispatched / In Transit</option>
                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="on_hold" <?= $order['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">Timeline Log Note</label>
                    <input type="text" name="status_comment" placeholder="Optional timeline note (e.g. Plates mounted on Heidelberg offset press)..." class="form-control" style="font-size: 0.85rem;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 800; padding: 12px;">
                    <i class="fas fa-sync-alt"></i> Update Stage
                </button>
            </form>
        </div>

        <!-- Shipping & Logistics Control Card -->
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Logistics & Courier Tracking</h4>

            <form action="<?= ADMIN_URL ?>?view=orders&action=detail&id=<?= $orderId ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                <input type="hidden" name="action" value="update_shipping">

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #64748b;">Courier / Transporter Name</label>
                    <input type="text" name="courier_name" value="<?= e($order['courier_name'] ?? '') ?>" placeholder="Bluedart / Delhivery / Safexpress" class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #64748b;">Tracking AWB Number</label>
                    <input type="text" name="tracking_number" value="<?= e($order['tracking_number'] ?? '') ?>" placeholder="AWB12345678" class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #64748b;">Tracking Website URL</label>
                    <input type="url" name="tracking_url" value="<?= e($order['tracking_url'] ?? '') ?>" placeholder="https://..." class="form-control">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 800; padding: 12px;">
                    <i class="fas fa-truck-loading"></i> Save Tracking Details
                </button>
            </form>
        </div>
    </div>

</div>
