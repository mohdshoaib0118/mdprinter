<?php
/**
 * MD Printers Admin - B2B Payment Transactions & Credit Ledger Management
 */
require_once MODELS_PATH . '/PaymentService.php';
require_once MODELS_PATH . '/Order.php';
require_once MODELS_PATH . '/Customer.php';

// Handle Manual Bank Transfer Verification POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_payment') {
    if (!verify_csrf()) {
        set_flash('error', 'Security token invalid. Please try again.');
        redirect(ADMIN_URL . '?view=payments');
    }

    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.00;
    $paymentReference = trim($_POST['payment_reference'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'bank_transfer');
    $notes = trim($_POST['notes'] ?? '');
    $adminId = (int)($_SESSION['admin_user_id'] ?? 1);

    if ($orderId <= 0 || $amount <= 0 || empty($paymentReference)) {
        set_flash('error', 'Please provide a valid Order ID, Amount, and Payment Reference Number.');
    } else {
        $result = PaymentService::recordPaymentVerification($orderId, $amount, $paymentReference, $paymentMethod, $notes, $adminId);
        if ($result['success']) {
            set_flash('success', $result['message']);
        } else {
            set_flash('error', $result['message']);
        }
    }
    redirect(ADMIN_URL . '?view=payments');
}

$statusFilter = $_GET['status'] ?? '';
$payments = PaymentService::getAllPayments(['status' => $statusFilter]);
$gatewayEnabled = PaymentService::isGatewayEnabled();
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">B2B Payments & Credit Ledger</h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Record bank transfer verifications, manage B2B account credit lines, and review payment transactions.</p>
    </div>
</div>

<?php display_flash(); ?>

<!-- Gateway Configuration State Banner -->
<div style="background: <?= $gatewayEnabled ? '#f0fdf4' : '#fffbeb' ?>; border: 1px solid <?= $gatewayEnabled ? '#bbf7d0' : '#fef08a' ?>; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <i class="fas <?= $gatewayEnabled ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>" style="font-size: 1.5rem; color: <?= $gatewayEnabled ? '#16a34a' : '#d97706' ?>;"></i>
        <div>
            <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">
                Online Payment Gateway Status: <?= $gatewayEnabled ? '<span style="color: #16a34a;">ACTIVE</span>' : '<span style="color: #d97706;">CONFIGURATION PENDING / DISABLED</span>' ?>
            </h4>
            <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #64748b;">
                <?= $gatewayEnabled ? 'Razorpay API keys loaded and active.' : 'No live online gateway API credentials configured. B2B operations continue seamlessly using Bank Transfer (NEFT/RTGS) and Approved B2B Credit.' ?>
            </p>
        </div>
    </div>
    <span style="font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; background: <?= $gatewayEnabled ? '#dcfce7' : '#fef3c7' ?>; color: <?= $gatewayEnabled ? '#15803d' : '#92400e' ?>;">
        PAYMENT_GATEWAY_ENABLED = <?= $gatewayEnabled ? 'TRUE' : 'FALSE' ?>
    </span>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px;">

    <!-- Bank Transfer Verification Form -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); height: fit-content;">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px;">
            <i class="fas fa-money-check-alt" style="color: var(--cmyk-cyan);"></i> Verify Bank Transfer / NEFT
        </h3>

        <form action="<?= ADMIN_URL ?>?view=payments" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="verify_payment">

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Order ID / Order Number *</label>
                <input type="number" name="order_id" required class="form-control" placeholder="e.g. 15" style="font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Verified Amount (₹) *</label>
                <input type="number" step="0.01" name="amount" required class="form-control" placeholder="e.g. 12500.00" style="font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Bank Reference / UTR / Transaction No *</label>
                <input type="text" name="payment_reference" required class="form-control" placeholder="e.g. UTR-987654321" style="font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Payment Channel</label>
                <select name="payment_method" class="form-control" style="font-size: 0.9rem;">
                    <option value="bank_transfer">Bank Transfer / NEFT / RTGS</option>
                    <option value="imps">IMPS / UPI Transfer</option>
                    <option value="cheque">Cheque Deposit</option>
                    <option value="approved_credit">B2B Credit Facility Adjustment</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label style="font-weight: 700; font-size: 0.85rem;">Verification Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Verified in HDFC press ledger account..." style="font-size: 0.85rem;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: 800;">
                <i class="fas fa-check-circle"></i> Confirm & Record Payment
            </button>
        </form>
    </div>

    <!-- Payments Ledger Table -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">
                Payment Transactions History
            </h3>
            <div>
                <a href="<?= ADMIN_URL ?>?view=payments" class="btn btn-sm <?= empty($statusFilter) ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
                <a href="<?= ADMIN_URL ?>?view=payments&status=paid" class="btn btn-sm <?= $statusFilter === 'paid' ? 'btn-primary' : 'btn-outline-secondary' ?>">Paid</a>
                <a href="<?= ADMIN_URL ?>?view=payments&status=pending" class="btn btn-sm <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline-secondary' ?>">Pending</a>
            </div>
        </div>

        <?php if (empty($payments)): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fas fa-receipt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                <h4 style="font-weight: 800; color: #0f172a;">No Payment Transactions Found</h4>
                <p style="font-size: 0.88rem;">No transactions match the selected filter criteria.</p>
            </div>
        <?php else: ?>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                            <th style="padding: 12px 14px;">Reference</th>
                            <th style="padding: 12px 14px;">Order #</th>
                            <th style="padding: 12px 14px;">Customer / Company</th>
                            <th style="padding: 12px 14px;">Amount</th>
                            <th style="padding: 12px 14px;">Channel</th>
                            <th style="padding: 12px 14px;">Status</th>
                            <th style="padding: 12px 14px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px; font-weight: 800; color: #0f172a;">
                                    <?= e($p['payment_reference']) ?>
                                </td>
                                <td style="padding: 14px; font-weight: 800;">
                                    <a href="<?= ADMIN_URL ?>?view=orders&action=detail&id=<?= $p['order_id'] ?>" style="color: #00a8e8; text-decoration: none;">
                                        #<?= e($p['order_number'] ?: ('ID:' . $p['order_id'])) ?>
                                    </a>
                                </td>
                                <td style="padding: 14px;">
                                    <strong><?= e($p['company_name'] ?: 'B2B Client') ?></strong><br>
                                    <span style="font-size: 0.8rem; color: #64748b;"><?= e($p['contact_person']) ?></span>
                                </td>
                                <td style="padding: 14px; font-weight: 900; color: #0f172a;">
                                    ₹<?= number_format($p['amount'], 2) ?>
                                </td>
                                <td style="padding: 14px;">
                                    <span style="text-transform: uppercase; font-size: 0.75rem; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: #f1f5f9;">
                                        <?= e($p['payment_method']) ?>
                                    </span>
                                </td>
                                <td style="padding: 14px;">
                                    <span style="font-size: 0.75rem; font-weight: 900; padding: 3px 8px; border-radius: 12px; background: <?= $p['status'] === 'paid' ? '#dcfce7' : '#fef3c7' ?>; color: <?= $p['status'] === 'paid' ? '#15803d' : '#92400e' ?>;">
                                        <?= strtoupper($p['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 14px; font-size: 0.8rem; color: #64748b;">
                                    <?= date('d M Y H:i', strtotime($p['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
