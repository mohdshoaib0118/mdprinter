<?php
/**
 * MD Printers - Customer Dashboard: Formal Quotations
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Quote.php';

$customerId = (int)$_SESSION['customer_id'];
$quotes = Quote::getForCustomer($customerId);
$activeQuoteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selectedQuote = $activeQuoteId ? Quote::findById($activeQuoteId, $customerId) : null;
?>

<div style="background: #f8fafc; min-height: 80vh; padding: 40px 0;">
    <div class="container">
        <div style="display: flex; gap: 30px; align-items: flex-start;">
            
            <!-- Customer Navigation Sidebar -->
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
                        <a href="<?= SITE_URL ?>/dashboard/quotes" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; background: rgba(0,174,239,0.08); color: var(--cmyk-cyan); font-weight: 700; text-decoration: none;">
                            <i class="fas fa-file-invoice-dollar" style="width: 20px;"></i> My Quotations
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/dashboard/cart" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #64748b; font-weight: 600; text-decoration: none;">
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
                        <a href="<?= SITE_URL ?>/order" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 14px; border-radius: 12px; background: var(--cmyk-cyan); color: #ffffff; font-weight: 800; font-size: 0.85rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0, 168, 232, 0.25);">
                            <i class="fas fa-shopping-cart"></i> START NEW ORDER
                        </a>
                    </li>
                    <li style="margin-top: 6px;">
                        <a href="<?= SITE_URL ?>/request-quote" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 14px; border-radius: 12px; background: rgba(236,0,140,0.1); color: var(--cmyk-magenta); border: 1.5px solid rgba(236,0,140,0.3); font-weight: 800; font-size: 0.85rem; text-decoration: none;">
                            <i class="fas fa-edit"></i> REQUEST A QUOTE
                        </a>
                    </li>
                    <li style="margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                        <a href="<?= SITE_URL ?>/logout" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #ef4444; font-weight: 700; text-decoration: none;">
                            <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content Area -->
            <div style="flex-grow: 1;">
                <?php display_flash(); ?>

                <?php if ($selectedQuote): ?>
                    <!-- Quote Detail View -->
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px;">
                            <div>
                                <a href="<?= SITE_URL ?>/dashboard/quotes" style="color: var(--cmyk-cyan); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                    <i class="fas fa-arrow-left"></i> Back to All Quotations
                                </a>
                                <h2 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0;">Quotation #<?= e($selectedQuote['quote_number']) ?></h2>
                                <p style="color: #64748b; font-size: 0.88rem; margin: 4px 0 0 0;">
                                    Requested on <?= date('d M Y, h:i A', strtotime($selectedQuote['created_at'])) ?>
                                </p>
                            </div>
                            <div>
                                <?php
                                    $st = $selectedQuote['status'];
                                    $bg = '#64748b';
                                    if ($st === 'sent') $bg = '#3b82f6';
                                    elseif ($st === 'accepted') $bg = '#10b981';
                                    elseif ($st === 'rejected') $bg = '#ef4444';
                                    elseif ($st === 'preparing') $bg = '#f59e0b';
                                ?>
                                <span style="background: <?= $bg ?>; color: #fff; padding: 8px 16px; border-radius: 20px; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">
                                    Status: <?= e($st) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Quote Line Items -->
                        <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 16px;">Configured Print Items</h4>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569;">
                                        <th style="padding: 14px 18px;">Product</th>
                                        <th style="padding: 14px 18px;">Configuration Specs</th>
                                        <th style="padding: 14px 18px; text-align: center;">Qty</th>
                                        <th style="padding: 14px 18px; text-align: right;">Unit Price</th>
                                        <th style="padding: 14px 18px; text-align: right;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($selectedQuote['items'] as $it): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 16px 18px; font-weight: 800; color: #0f172a;">
                                                <?= e($it['product_name_snapshot']) ?>
                                            </td>
                                            <td style="padding: 16px 18px; color: #475569; font-size: 0.88rem;">
                                                <?php
                                                    $cfg = json_decode($it['configuration_json'] ?? '{}', true);
                                                    if (!empty($cfg)) {
                                                        foreach ($cfg as $k => $v) {
                                                            echo "<div><strong>" . e(ucwords(str_replace('_', ' ', $k))) . ":</strong> " . e($v) . "</div>";
                                                        }
                                                    } else {
                                                        echo "Standard Specification";
                                                    }
                                                ?>
                                            </td>
                                            <td style="padding: 16px 18px; text-align: center; font-weight: 700; color: #0f172a;">
                                                <?= e($it['quantity']) ?>
                                            </td>
                                            <td style="padding: 16px 18px; text-align: right; color: #475569;">
                                                ₹<?= number_format($it['unit_price'], 2) ?>
                                            </td>
                                            <td style="padding: 16px 18px; text-align: right; font-weight: 800; color: #0f172a;">
                                                ₹<?= number_format($it['subtotal'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Price Totals Breakdown -->
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
                            <div style="width: 320px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
                                <div style="display: flex; justify-content: space-between; padding-bottom: 8px; color: #64748b;">
                                    <span>Gross Subtotal:</span>
                                    <strong>₹<?= number_format($selectedQuote['subtotal'], 2) ?></strong>
                                </div>
                                <?php if ($selectedQuote['discount'] > 0): ?>
                                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; color: #16a34a;">
                                        <span>Tier Discount:</span>
                                        <strong>-₹<?= number_format($selectedQuote['discount'], 2) ?></strong>
                                    </div>
                                <?php endif; ?>
                                <div style="display: flex; justify-content: space-between; padding-bottom: 8px; color: #64748b;">
                                    <span>GST (18%):</span>
                                    <strong>₹<?= number_format($selectedQuote['tax'], 2) ?></strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-bottom: 8px; color: #64748b;">
                                    <span>Shipping / Freight:</span>
                                    <strong>₹<?= number_format($selectedQuote['shipping'], 2) ?></strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 6px; font-size: 1.15rem; font-weight: 900; color: #0f172a;">
                                    <span>Final Total:</span>
                                    <span style="color: var(--cmyk-cyan);">₹<?= number_format($selectedQuote['total'], 2) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions for Customer -->
                        <?php if (in_array($selectedQuote['status'], ['sent', 'requested', 'preparing'])): ?>
                            <div style="display: flex; gap: 16px; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 24px;">
                                <form action="<?= SITE_URL ?>/api/b2b/quote-reject" method="POST" onsubmit="return confirm('Are you sure you want to reject this quotation?');">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                    <input type="hidden" name="quote_id" value="<?= $selectedQuote['id'] ?>">
                                    <button type="submit" class="btn" style="background: #f1f5f9; color: #ef4444; font-weight: 800; border: 1px solid #cbd5e1; padding: 12px 24px;">
                                        <i class="fas fa-times-circle"></i> Reject Quote
                                    </button>
                                </form>

                                <form action="<?= SITE_URL ?>/api/b2b/quote-accept" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                    <input type="hidden" name="quote_id" value="<?= $selectedQuote['id'] ?>">
                                    <button type="submit" class="btn btn-primary" style="font-weight: 800; padding: 12px 28px;">
                                        <i class="fas fa-check-circle"></i> Accept Quotation
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <!-- Quotes List -->
                <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h3 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin: 0;">Formal B2B Quotations</h3>
                            <p style="color: #64748b; font-size: 0.88rem; margin: 4px 0 0 0;">View, review, and accept official price quotes generated for your business.</p>
                        </div>
                    </div>

                    <?php if (empty($quotes)): ?>
                        <div style="text-align: center; padding: 50px 20px; color: #64748b;">
                            <i class="fas fa-file-invoice" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 8px;">No Formal Quotes Found</h4>
                            <p style="font-size: 0.9rem; margin-bottom: 20px;">Configure any product from our catalogue to request a custom B2B quote.</p>
                            <a href="<?= SITE_URL ?>/products" class="btn btn-primary">Browse Catalogue</a>
                        </div>
                    <?php else: ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                                        <th style="padding: 14px 18px;">Quote Ref</th>
                                        <th style="padding: 14px 18px;">Date</th>
                                        <th style="padding: 14px 18px;">Valid Until</th>
                                        <th style="padding: 14px 18px;">Total Amount</th>
                                        <th style="padding: 14px 18px;">Status</th>
                                        <th style="padding: 14px 18px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quotes as $q): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 16px 18px; font-weight: 800; color: #0f172a;">
                                                #<?= e($q['quote_number']) ?>
                                            </td>
                                            <td style="padding: 16px 18px; color: #64748b;">
                                                <?= date('d M Y', strtotime($q['created_at'])) ?>
                                            </td>
                                            <td style="padding: 16px 18px; color: #64748b;">
                                                <?= $q['valid_until'] ? date('d M Y', strtotime($q['valid_until'])) : '14 Days' ?>
                                            </td>
                                            <td style="padding: 16px 18px; font-weight: 800; color: var(--cmyk-cyan);">
                                                ₹<?= number_format($q['total'], 2) ?>
                                            </td>
                                            <td style="padding: 16px 18px;">
                                                <?php
                                                    $st = $q['status'];
                                                    $badgeBg = '#64748b';
                                                    if ($st === 'sent') $badgeBg = '#3b82f6';
                                                    elseif ($st === 'accepted') $badgeBg = '#10b981';
                                                    elseif ($st === 'rejected') $badgeBg = '#ef4444';
                                                    elseif ($st === 'preparing') $badgeBg = '#f59e0b';
                                                ?>
                                                <span style="background: <?= $badgeBg ?>; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                                    <?= e($st) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 16px 18px; text-align: right;">
                                                <a href="<?= SITE_URL ?>/dashboard/quotes?id=<?= $q['id'] ?>" class="btn btn-sm" style="background: #f1f5f9; color: #0f172a; font-weight: 700; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px;">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/footer.php'; ?>
