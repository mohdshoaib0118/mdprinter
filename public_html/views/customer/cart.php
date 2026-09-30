<?php
/**
 * MD Printers - Customer Dashboard: B2B Cart
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Cart.php';

$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
$cartItems = Cart::getItems($customerId);
$grandTotal = 0.00;
?>

<div style="background: #f8fafc; min-height: 80vh; padding: 40px 0;">
    <div class="container">
        <div style="display: flex; gap: 30px; align-items: flex-start;">
            
            <!-- Customer Navigation Sidebar -->
            <?php if (is_customer_logged_in()): ?>
                <div style="width: 280px; flex-shrink: 0; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                    <div style="text-align: center; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; margin-bottom: 20px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--cmyk-cyan), var(--cmyk-magenta)); color: #fff; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                            <?= strtoupper(substr($_SESSION['customer_name'] ?? 'C', 0, 1)) ?>
                        </div>
                        <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;"><?= e($_SESSION['customer_name'] ?? 'Customer') ?></h4>
                        <span style="font-size: 0.8rem; background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); padding: 3px 10px; border-radius: 12px; font-weight: 700; margin-top: 6px; display: inline-block;">
                            Tier: <?= strtoupper(e($_SESSION['customer_tier'] ?? 'REGULAR')) ?>
                        </span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px;">
                        <li>
                            <a href="<?= SITE_URL ?>/dashboard" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #64748b; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-th-large" style="width: 20px;"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/dashboard/quotes" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #64748b; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-file-invoice-dollar" style="width: 20px;"></i> My Quotations
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/dashboard/cart" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; background: rgba(0,174,239,0.08); color: var(--cmyk-cyan); font-weight: 700; text-decoration: none;">
                                <i class="fas fa-shopping-cart" style="width: 20px;"></i> My B2B Cart
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/dashboard/profile" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #64748b; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-user-edit" style="width: 20px;"></i> Company Profile
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/dashboard/addresses" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #64748b; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-map-marker-alt" style="width: 20px;"></i> Address Book
                            </a>
                        </li>
                        <li style="margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                            <a href="<?= SITE_URL ?>/logout" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #ef4444; font-weight: 700; text-decoration: none;">
                                <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Main Cart Content -->
            <div style="flex-grow: 1;">
                <?php display_flash(); ?>

                <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h3 style="font-size: 1.4rem; font-weight: 900; color: #0f172a; margin: 0;">B2B Printing Cart</h3>
                            <p style="color: #64748b; font-size: 0.88rem; margin: 4px 0 0 0;">Review your custom configured product snapshots before requesting formal quote or order placement.</p>
                        </div>
                    </div>

                    <?php if (empty($cartItems)): ?>
                        <div style="text-align: center; padding: 60px 20px; color: #64748b;">
                            <i class="fas fa-shopping-cart" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 8px;">Your B2B Cart is Empty</h4>
                            <p style="font-size: 0.9rem; margin-bottom: 24px;">Configure print job options on any product page to calculate custom B2B pricing.</p>
                            <a href="<?= SITE_URL ?>/products" class="btn btn-primary" style="padding: 12px 28px; font-weight: 800;">Browse Catalogue</a>
                        </div>
                    <?php else: ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                                        <th style="padding: 14px 18px;">Product</th>
                                        <th style="padding: 14px 18px;">Configuration Snapshot</th>
                                        <th style="padding: 14px 18px; text-align: center;">Qty</th>
                                        <th style="padding: 14px 18px; text-align: right;">Subtotal</th>
                                        <th style="padding: 14px 18px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $ci): 
                                        $sub = (float)$ci['subtotal'];
                                        $grandTotal += $sub;
                                    ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 18px; font-weight: 800; color: #0f172a;">
                                                <?= e($ci['product_name_snapshot']) ?>
                                            </td>
                                            <td style="padding: 18px; color: #475569; font-size: 0.88rem;">
                                                <?php
                                                    $cfg = $ci['configuration'];
                                                    if (!empty($cfg) && is_array($cfg)) {
                                                        foreach ($cfg as $k => $v) {
                                                            $valDisplay = is_array($v) ? implode(', ', array_map('strval', $v)) : (string)$v;
                                                            echo "<div><strong>" . e(ucwords(str_replace('_', ' ', (string)$k))) . ":</strong> " . e($valDisplay) . "</div>";
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td style="padding: 18px; text-align: center; font-weight: 800; color: #0f172a;">
                                                <?= e($ci['quantity']) ?>
                                            </td>
                                            <td style="padding: 18px; text-align: right; font-weight: 900; color: var(--cmyk-cyan); font-size: 1.05rem;">
                                                ₹<?= number_format($sub, 2) ?>
                                            </td>
                                            <td style="padding: 18px; text-align: right;">
                                                <form action="<?= SITE_URL ?>/api/b2b/cart-remove" method="POST" style="margin:0;">
                                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                                    <input type="hidden" name="cart_item_id" value="<?= $ci['id'] ?>">
                                                    <button type="submit" class="btn btn-sm" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 0; padding: 6px 12px; font-weight: 700; border-radius: 6px;">
                                                        <i class="fas fa-trash-alt"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Grand Total & Request Quote Trigger -->
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                            <div>
                                <span style="font-size: 0.9rem; color: #64748b; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;">Calculated Cart Total</span>
                                <div style="font-size: 1.8rem; font-weight: 900; color: #0f172a;">
                                    ₹<?= number_format($grandTotal, 2) ?>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #64748b;">(Incl. 18% GST & Tier Savings)</span>
                                </div>
                            </div>
                            <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
                                <a href="<?= SITE_URL ?>/checkout" class="btn btn-primary btn-lg" style="font-weight: 800; padding: 14px 28px; background: #0f172a; border-color: #0f172a; color: #ffffff;">
                                    <i class="fas fa-check-circle"></i> PROCEED TO CHECKOUT / BOOK ORDER
                                </a>
                                <form action="<?= SITE_URL ?>/api/b2b/quote-request" method="POST" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                    <input type="hidden" name="source" value="cart">
                                    <button type="submit" class="btn btn-magenta btn-lg" style="font-weight: 800; padding: 14px 24px;">
                                        <i class="fas fa-file-invoice-dollar"></i> Request Formal B2B Quote
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
