<?php
/**
 * MD Printers - Printable B2B GST Tax Invoice Template
 */
require_once MODELS_PATH . '/Order.php';
require_once MODELS_PATH . '/Invoice.php';

require_customer_login();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customerId = (int)$_SESSION['customer_id'];
$order = Order::findById($orderId, $customerId);

if (!$order) {
    die("Invoice not found or unauthorized access.");
}

$invoice = Invoice::generateForOrder($order);

$seller = json_decode($invoice['seller_snapshot_json'] ?? '{}', true);
$buyer = json_decode($invoice['buyer_snapshot_json'] ?? '{}', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice #<?= e($invoice['invoice_number']) ?> - MD Printers</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #0f172a; margin: 0; padding: 30px; background: #fff; font-size: 14px; }
        .invoice-card { max-width: 800px; margin: 0 auto; border: 1px solid #cbd5e1; padding: 40px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #00a8e8; padding-bottom: 20px; margin-bottom: 24px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; font-weight: bold; }
        .total-box { text-align: right; margin-left: auto; width: 300px; }
        .btn-print { background: #00a8e8; color: #fff; border: 0; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        @media print { .btn-print { display: none; } body { padding: 0; } .invoice-card { border: none; } }
    </style>
</head>
<body>
    <div style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">Print / Download PDF</button>
    </div>

    <div class="invoice-card">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <img src="<?= SITE_URL ?>/assets/images/logo-official.png" alt="MD Printers Official Logo" style="height: 64px; width: 64px; object-fit: contain;">
                <div>
                    <h1 style="margin: 0; color: #00a8e8; font-size: 24px; text-transform: uppercase; font-weight: 900; letter-spacing: 0.5px;">TAX INVOICE</h1>
                    <div style="color: #64748b; font-size: 12px; margin-top: 2px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">ORIGINAL FOR RECIPIENT</div>
                </div>
            </div>
            <div style="text-align: right;">
                <h3 style="margin: 0; color: #0f172a; font-weight: 900; font-size: 1.2rem;"><?= e($seller['name'] ?? 'MD PRINTERS') ?></h3>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Commercial Print & Packaging Press</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">GSTIN: <strong><?= e($seller['gstin'] ?? '07AAAAA0000A1Z5') ?></strong></div>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <h4 style="margin: 0 0 8px 0; color: #64748b; text-transform: uppercase; font-size: 11px;">Billed To (Customer):</h4>
                <strong style="font-size: 16px; color: #0f172a;"><?= e($order['company_name']) ?></strong><br>
                <?= e($buyer['address_line1'] ?? '') ?>, <?= e($buyer['city'] ?? '') ?>, <?= e($buyer['state'] ?? '') ?> - <?= e($buyer['pincode'] ?? '') ?><br>
                Contact: <?= e($order['contact_person']) ?> (<?= e($order['mobile']) ?>)<br>
                GSTIN: <strong><?= e($invoice['gstin'] ?: 'Unregistered B2B') ?></strong>
            </div>
            <div style="text-align: right;">
                <h4 style="margin: 0 0 8px 0; color: #64748b; text-transform: uppercase; font-size: 11px;">Invoice Details:</h4>
                <strong>Invoice No:</strong> #<?= e($invoice['invoice_number']) ?><br>
                <strong>Order No:</strong> #<?= e($order['order_number']) ?><br>
                <strong>Date:</strong> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?><br>
                <strong>Place of Supply:</strong> <?= e($invoice['place_of_supply']) ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Taxable Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $it): ?>
                    <tr>
                        <td>
                            <strong><?= e($it['product_name_snapshot']) ?></strong>
                            <div style="font-size: 11px; color: #64748b;">
                                <?php
                                    $cfg = json_decode($it['configuration_json'] ?? '{}', true);
                                    foreach ($cfg as $k => $v) { echo e($k) . ': ' . e($v) . ' | '; }
                                ?>
                            </div>
                        </td>
                        <td style="text-align: center;"><?= $it['quantity'] ?></td>
                        <td style="text-align: right;">₹<?= number_format($it['unit_price'], 2) ?></td>
                        <td style="text-align: right;">₹<?= number_format($it['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-box">
            <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                <span>Taxable Amount:</span>
                <strong>₹<?= number_format($invoice['taxable_amount'], 2) ?></strong>
            </div>
            <?php if ($invoice['cgst'] > 0): ?>
                <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                    <span>CGST (9%):</span>
                    <strong>₹<?= number_format($invoice['cgst'], 2) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                    <span>SGST (9%):</span>
                    <strong>₹<?= number_format($invoice['sgst'], 2) ?></strong>
                </div>
            <?php else: ?>
                <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                    <span>IGST (18%):</span>
                    <strong>₹<?= number_format($invoice['igst'], 2) ?></strong>
                </div>
            <?php endif; ?>
            <div style="display: flex; justify-content: space-between; border-top: 2px solid #0f172a; padding-top: 8px; margin-top: 6px; font-size: 16px; font-weight: bold; color: #00a8e8;">
                <span>Total Amount:</span>
                <span>₹<?= number_format($invoice['total'], 2) ?></span>
            </div>
        </div>

        <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 11px; color: #64748b; text-align: center;">
            This is a computer-generated tax invoice issued by MD Printers Commercial Press.
        </div>
    </div>
</body>
</html>
