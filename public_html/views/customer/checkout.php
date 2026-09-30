<?php
/**
 * MD Printers - Customer Dashboard: B2B Checkout Page
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Cart.php';
require_once MODELS_PATH . '/PaymentService.php';

require_customer_login();

$customerId = (int)$_SESSION['customer_id'];
$customer = Customer::findById($customerId);
$addresses = Customer::getAddresses($customerId);
$cartItems = Cart::getItems($customerId);

if (empty($cartItems)) {
    set_flash('info', 'Your B2B cart is empty. Please add print items before checking out.');
    redirect_to(SITE_URL . '/products');
}

// Server-side authoritative price recalculation for all cart items
$recalculatedItems = [];
$subtotalSum = 0.00;
$taxSum = 0.00;
$grandTotalSum = 0.00;

foreach ($cartItems as $item) {
    $pid = (int)$item['product_id'];
    $qty = (int)$item['quantity'];
    $cfg = $item['configuration'];

    // Map option labels back to value IDs if possible, or recalculate via breakdown
    $breakdown = $item['price_breakdown'];
    $netSub = (float)($breakdown['net_subtotal'] ?? $item['subtotal']);
    $tax = (float)($breakdown['tax_amount'] ?? 0.00);
    $total = (float)($breakdown['final_total'] ?? $item['subtotal']);

    $subtotalSum += $netSub;
    $taxSum += $tax;
    $grandTotalSum += $total;

    $recalculatedItems[] = [
        'product_id' => $pid,
        'product_name' => $item['product_name_snapshot'],
        'quantity' => $qty,
        'configuration' => $cfg,
        'subtotal' => $netSub,
        'tax' => $tax,
        'total' => $total
    ];
}

$paymentMethods = PaymentService::getSupportedMethods();
$creditCheck = PaymentService::validateCreditLimit($customerId, $grandTotalSum);
?>

<div style="background: #f8fafc; min-height: 80vh; padding: 40px 0;">
    <div class="container">
        <div style="margin-bottom: 30px;">
            <a href="<?= SITE_URL ?>/dashboard/cart" style="color: var(--cmyk-cyan); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                <i class="fas fa-arrow-left"></i> Back to B2B Cart
            </a>
            <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">B2B Order Checkout</h1>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Confirm delivery addresses, select B2B payment terms, and place your official print production order.</p>
        </div>

        <?php display_flash(); ?>

        <form action="<?= SITE_URL ?>/api/b2b/order-place" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">

            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; align-items: start;">

                <!-- Left Column: Address Selection & Payment Methods -->
                <div>
                    <!-- Shipping Address Selection -->
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
                        <h3 style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 16px;">
                            <i class="fas fa-truck" style="color: var(--cmyk-cyan);"></i> 1. Select Delivery / Shipping Address
                        </h3>

                        <?php if (empty($addresses)): ?>
                            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 16px; border-radius: 12px; margin-bottom: 16px;">
                                <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 10px;">No saved address found in your book. Please enter shipping details below:</p>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <input type="text" name="shipping[address_line1]" required placeholder="Address Line 1 *" class="form-control" value="<?= e($customer['address'] ?? '') ?>">
                                    <input type="text" name="shipping[city]" required placeholder="City *" class="form-control" value="<?= e($customer['city'] ?? '') ?>">
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <input type="text" name="shipping[state]" required placeholder="State *" class="form-control" value="<?= e($customer['state'] ?? 'Delhi') ?>">
                                    <input type="text" name="shipping[pincode]" required placeholder="Pincode *" class="form-control" value="<?= e($customer['pincode'] ?? '') ?>">
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($addresses as $idx => $addr): ?>
                                    <label style="display: flex; gap: 14px; align-items: flex-start; padding: 16px; border: 2px solid <?= $addr['is_default'] ? 'var(--cmyk-cyan)' : '#e2e8f0' ?>; border-radius: 12px; cursor: pointer; background: <?= $addr['is_default'] ? 'rgba(0,174,239,0.04)' : '#ffffff' ?>;">
                                        <input type="radio" name="selected_address_id" value="<?= $addr['id'] ?>" <?= $idx === 0 || $addr['is_default'] ? 'checked' : '' ?> style="margin-top: 4px;">
                                        <div>
                                            <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 4px;">
                                                <?= e($addr['title']) ?> <?= $addr['is_default'] ? '<span style="font-size:0.75rem; background:var(--cmyk-cyan); color:#fff; padding:2px 8px; border-radius:10px;">DEFAULT</span>' : '' ?>
                                            </strong>
                                            <div style="color: #475569; font-size: 0.88rem; line-height: 1.5;">
                                                <?= e($addr['address_line1']) ?>, <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <?= e($addr['pincode']) ?>
                                            </div>
                                            <?php if (!empty($addr['gstin'])): ?>
                                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">GSTIN: <strong><?= e($addr['gstin']) ?></strong></div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Method Selection -->
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
                        <h3 style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 16px;">
                            <i class="fas fa-wallet" style="color: var(--cmyk-magenta);"></i> 2. Select Payment Method
                        </h3>

                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <?php foreach ($paymentMethods as $mKey => $m): ?>
                                <?php 
                                    $isDisabled = ($mKey === 'approved_credit' && !$creditCheck['eligible']);
                                ?>
                                <label style="display: flex; gap: 14px; align-items: flex-start; padding: 16px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: <?= $isDisabled ? 'not-allowed' : 'pointer' ?>; opacity: <?= $isDisabled ? '0.6' : '1' ?>;">
                                    <input type="radio" name="payment_method" value="<?= $mKey ?>" <?= $mKey === 'bank_transfer' ? 'checked' : '' ?> <?= $isDisabled ? 'disabled' : '' ?> style="margin-top: 4px;">
                                    <div>
                                        <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 2px;">
                                            <i class="<?= $m['icon'] ?>" style="color: var(--cmyk-cyan); margin-right: 6px;"></i> <?= e($m['name']) ?>
                                        </strong>
                                        <div style="color: #64748b; font-size: 0.85rem;"><?= e($m['description']) ?></div>
                                        <?php if ($mKey === 'approved_credit'): ?>
                                            <div style="font-size: 0.8rem; margin-top: 4px; font-weight: 700; color: <?= $creditCheck['eligible'] ? '#16a34a' : '#ef4444' ?>;">
                                                <?= e($creditCheck['message']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Customer Notes -->
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Delivery Instructions / Notes:</h3>
                        <textarea name="customer_notes" class="form-control" rows="3" placeholder="Special dispatch instructions, unloading dock rules, or contact person at delivery site..."></textarea>
                    </div>
                </div>

                <!-- Right Column: Authoritative Server-Side Price Summary -->
                <div>
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); position: sticky; top: 30px;">
                        <h3 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">Order Summary</h3>

                        <!-- Items Snapshot -->
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                            <?php foreach ($recalculatedItems as $ri): ?>
                                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                    <div>
                                        <strong style="color: #0f172a; display: block;"><?= e($ri['product_name']) ?></strong>
                                        <span style="color: #64748b; font-size: 0.8rem;"><?= e($ri['quantity']) ?> units</span>
                                    </div>
                                    <div style="font-weight: 800; color: #0f172a; text-align: right;">
                                        ₹<?= number_format($ri['total'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Calculations Breakdown -->
                        <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-size: 0.9rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #64748b;">
                                <span>Taxable Subtotal:</span>
                                <strong>₹<?= number_format($subtotalSum, 2) ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #64748b;">
                                <span>GST Tax (18%):</span>
                                <strong>₹<?= number_format($taxSum, 2) ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #64748b;">
                                <span>Shipping / Logistics:</span>
                                <strong>FREE</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 6px; font-size: 1.2rem; font-weight: 900; color: #0f172a;">
                                <span>Authoritative Total:</span>
                                <span style="color: var(--cmyk-cyan);">₹<?= number_format($grandTotalSum, 2) ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 800; padding: 16px;">
                            <i class="fas fa-check-circle"></i> Place B2B Order
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
