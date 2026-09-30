<?php
/**
 * MD Printers - Customer Dashboard: B2B Orders & Artwork Manager
 */
require_once MODELS_PATH . '/Order.php';
require_once MODELS_PATH . '/Artwork.php';
require_once MODELS_PATH . '/Invoice.php';

require_customer_login();

// Handle Customer PDF GST Invoice Download Request (IDOR Protected)
if (isset($_GET['download_invoice']) && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $customerId = (int)$_SESSION['customer_id'];
    $order = Order::findById($orderId, $customerId);
    if (!$order) {
        die("Invoice not found or unauthorized access.");
    }
    Invoice::streamPdf($order, true);
    exit;
}

require_once INCLUDES_PATH . '/header.php';

$customerId = (int)$_SESSION['customer_id'];
$orders = Order::getForCustomer($customerId);
$activeOrderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selectedOrder = $activeOrderId ? Order::findById($activeOrderId, $customerId) : null;
$selectedInvoice = $selectedOrder ? Invoice::generateForOrder($selectedOrder) : null;
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
                        <a href="<?= SITE_URL ?>/dashboard/orders" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; background: rgba(0,174,239,0.08); color: var(--cmyk-cyan); font-weight: 700; text-decoration: none;">
                            <i class="fas fa-box" style="width: 20px;"></i> My Orders & Artworks
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/dashboard/quotes" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: #64748b; font-weight: 600; text-decoration: none;">
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

                <?php if ($selectedOrder): ?>
                    <!-- Selected Order Details & Artwork Manager View -->
                    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px;">
                            <div>
                                <a href="<?= SITE_URL ?>/dashboard/orders" style="color: var(--cmyk-cyan); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                    <i class="fas fa-arrow-left"></i> Back to Orders List
                                </a>
                                <h2 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0;">Order #<?= e($selectedOrder['order_number']) ?></h2>
                                <p style="color: #64748b; font-size: 0.88rem; margin: 4px 0 0 0;">Placed on <?= date('d M Y, h:i A', strtotime($selectedOrder['placed_at'])) ?></p>
                            </div>
                            <div style="text-align: right;">
                                <?php
                                    $st = $selectedOrder['status'];
                                    $bg = '#64748b';
                                    if (strpos($st, 'approved') !== false || $st === 'delivered') $bg = '#10b981';
                                    elseif (strpos($st, 'production') !== false || strpos($st, 'printing') !== false) $bg = '#00a8e8';
                                    elseif (strpos($st, 'correction') !== false) $bg = '#ef4444';
                                    elseif (strpos($st, 'dispatched') !== false) $bg = '#8b5cf6';
                                ?>
                                <span style="background: <?= $bg ?>; color: #fff; padding: 8px 16px; border-radius: 20px; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">
                                    <?= e(ucwords(str_replace('_', ' ', $st))) ?>
                                </span>
                                <?php if ($selectedInvoice): ?>
                                    <div style="margin-top: 8px;">
                                        <a href="<?= SITE_URL ?>/dashboard/orders?id=<?= $selectedOrder['id'] ?>&print_invoice=1" target="_blank" class="btn btn-sm" style="background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); font-weight: 800; border: 0; padding: 6px 14px; border-radius: 8px;">
                                            <i class="fas fa-file-pdf"></i> View Invoice #<?= e($selectedInvoice['invoice_number']) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Courier Tracking Alert if Dispatched -->
                        <?php if (!empty($selectedOrder['tracking_number'])): ?>
                            <div style="background: rgba(0,174,239,0.06); border: 1px solid rgba(0,174,239,0.2); border-radius: 12px; padding: 18px; margin-bottom: 24px;">
                                <h4 style="font-size: 1rem; font-weight: 900; color: #0f172a; margin-bottom: 6px;">
                                    <i class="fas fa-shipping-fast" style="color: var(--cmyk-cyan);"></i> Courier Tracking Information
                                </h4>
                                <div style="font-size: 0.9rem; color: #334155;">
                                    Transporter: <strong><?= e($selectedOrder['courier_name'] ?: ($selectedOrder['transporter_name'] ?: 'Express Dispatch')) ?></strong> | 
                                    Tracking AWB: <strong><?= e($selectedOrder['tracking_number']) ?></strong>
                                    <?php if (!empty($selectedOrder['tracking_url'])): ?>
                                        <a href="<?= e($selectedOrder['tracking_url']) ?>" target="_blank" style="color: var(--cmyk-cyan); font-weight: 800; margin-left: 10px; text-decoration: underline;">
                                            Track Package <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Order Line Items & Artwork Upload Section -->
                        <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 16px;">Order Items & Artwork Files</h4>

                        <?php foreach ($selectedOrder['items'] as $item): 
                            $artworks = Artwork::getVersionsForItem($item['id']);
                        ?>
                            <div style="border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 20px; background: #ffffff;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                    <div>
                                        <h5 style="font-size: 1.1rem; font-weight: 900; color: #0f172a; margin: 0;"><?= e($item['product_name_snapshot']) ?></h5>
                                        <div style="font-size: 0.88rem; color: #64748b; margin-top: 4px;">
                                            Quantity: <strong><?= e($item['quantity']) ?> units</strong> | Subtotal: <strong>₹<?= number_format($item['total'], 2) ?></strong>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                            $ast = $item['artwork_status'];
                                            $abg = '#64748b';
                                            if ($ast === 'approved') $abg = '#10b981';
                                            elseif ($ast === 'correction_required') $abg = '#ef4444';
                                            elseif ($ast === 'under_review') $abg = '#f59e0b';
                                        ?>
                                        <span style="background: <?= $abg ?>; color: #fff; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: 12px; text-transform: uppercase;">
                                            Artwork: <?= e(str_replace('_', ' ', $ast)) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Artwork Version Upload Form -->
                                <?php if ($item['artwork_status'] !== 'approved'): ?>
                                    <form action="<?= SITE_URL ?>/api/b2b/artwork-upload" method="POST" enctype="multipart/form-data" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                        <input type="hidden" name="order_id" value="<?= $selectedOrder['id'] ?>">
                                        <input type="hidden" name="order_item_id" value="<?= $item['id'] ?>">

                                        <h6 style="font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                                            <i class="fas fa-cloud-upload-alt" style="color: var(--cmyk-cyan);"></i> Upload Artwork File (PDF, AI, CDR, PSD, TIFF, PNG)
                                        </h6>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; align-items: center;">
                                            <input type="file" name="artwork_file" required class="form-control" style="font-size: 0.85rem;">
                                            <input type="text" name="customer_comment" placeholder="Optional comments (e.g. Pantone 185C red used)..." class="form-control" style="font-size: 0.85rem;">
                                            <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 800; white-space: nowrap;">
                                                <i class="fas fa-upload"></i> Upload Version
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>

                                <!-- Uploaded Artwork Versions History -->
                                <?php if (!empty($artworks)): ?>
                                    <h6 style="font-weight: 800; color: #0f172a; margin-bottom: 8px; font-size: 0.88rem;">Uploaded Versions History:</h6>
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        <?php foreach ($artworks as $art): ?>
                                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 0.85rem;">
                                                <div>
                                                    <strong style="color: #0f172a;">Version <?= $art['version_number'] ?>:</strong>
                                                    <span style="color: #475569; margin-left: 6px;"><?= e($art['original_filename']) ?></span>
                                                    <span style="color: #64748b; font-size: 0.78rem; margin-left: 8px;">(<?= round($art['file_size'] / 1024 / 1024, 2) ?> MB)</span>
                                                    
                                                    <?php if (!empty($art['admin_comment'])): ?>
                                                        <div style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; font-weight: 600;">
                                                            <i class="fas fa-comment-dots"></i> Press Review Note: <?= e($art['admin_comment']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="display: flex; gap: 10px; align-items: center;">
                                                    <span style="font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 8px; text-transform: uppercase; background: <?= $art['status'] === 'approved' ? '#10b981' : ($art['status'] === 'correction_required' ? '#ef4444' : '#f59e0b') ?>; color: #fff;">
                                                        <?= e($art['status']) ?>
                                                    </span>
                                                    <a href="<?= SITE_URL ?>/<?= e($art['file_path']) ?>" download target="_blank" class="btn btn-sm" style="background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; padding: 4px 10px; font-size: 0.8rem;">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Status Timeline -->
                        <?php if (!empty($selectedOrder['timeline'])): ?>
                            <h4 style="font-weight: 800; color: #0f172a; margin: 24px 0 16px 0;">Production Timeline History</h4>
                            <div style="border-left: 2px solid var(--cmyk-cyan); padding-left: 20px; margin-left: 10px; display: flex; flex-direction: column; gap: 14px;">
                                <?php foreach ($selectedOrder['timeline'] as $tl): ?>
                                    <div style="position: relative;">
                                        <div style="position: absolute; left: -27px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: var(--cmyk-cyan);"></div>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;">
                                            <?= e(ucwords(str_replace('_', ' ', $tl['new_status']))) ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #64748b;">
                                            <?= date('d M Y, h:i A', strtotime($tl['created_at'])) ?> — <?= e($tl['comment']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

                <!-- Orders History List Table -->
                <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <div>
                            <h3 style="font-size: 1.3rem; font-weight: 900; color: #0f172a; margin: 0;">My B2B Production Orders</h3>
                            <p style="color: #64748b; font-size: 0.88rem; margin: 4px 0 0 0;">Track production progress, upload artwork files, and download GST tax invoices.</p>
                        </div>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div style="text-align: center; padding: 50px 20px; color: #64748b;">
                            <i class="fas fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 14px;"></i>
                            <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 6px;">No Orders Placed Yet</h4>
                            <p style="font-size: 0.9rem; margin-bottom: 20px;">Place your first commercial order from your B2B cart or converted quotation.</p>
                            <a href="<?= SITE_URL ?>/products" class="btn btn-primary">Browse Products</a>
                        </div>
                    <?php else: ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                                        <th style="padding: 14px 18px;">Order No</th>
                                        <th style="padding: 14px 18px;">Placed Date</th>
                                        <th style="padding: 14px 18px;">Total</th>
                                        <th style="padding: 14px 18px;">Status</th>
                                        <th style="padding: 14px 18px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $o): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 16px 18px; font-weight: 900; color: #0f172a;">
                                                #<?= e($o['order_number']) ?>
                                            </td>
                                            <td style="padding: 16px 18px; color: #64748b;">
                                                <?= date('d M Y', strtotime($o['placed_at'])) ?>
                                            </td>
                                            <td style="padding: 16px 18px; font-weight: 900; color: var(--cmyk-cyan);">
                                                ₹<?= number_format($o['total'], 2) ?>
                                            </td>
                                            <td style="padding: 16px 18px;">
                                                <?php
                                                    $st = $o['status'];
                                                    $bg = '#64748b';
                                                    if (strpos($st, 'approved') !== false || $st === 'delivered') $bg = '#10b981';
                                                    elseif (strpos($st, 'production') !== false || strpos($st, 'printing') !== false) $bg = '#00a8e8';
                                                    elseif (strpos($st, 'correction') !== false) $bg = '#ef4444';
                                                    elseif (strpos($st, 'dispatched') !== false) $bg = '#8b5cf6';
                                                ?>
                                                <span style="background: <?= $bg ?>; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                                    <?= e(ucwords(str_replace('_', ' ', $st))) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 16px 18px; text-align: right;">
                                                <a href="<?= SITE_URL ?>/dashboard/orders?id=<?= $o['id'] ?>" class="btn btn-sm" style="background: #f1f5f9; color: #0f172a; font-weight: 700; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px;">
                                                    <i class="fas fa-eye"></i> View Order & Artwork
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
