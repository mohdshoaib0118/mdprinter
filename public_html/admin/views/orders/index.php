<?php
/**
 * MD Printers Admin - B2B Orders Directory
 */
require_once MODELS_PATH . '/Order.php';

$filterStatus = $_GET['status'] ?? '';
$filterPayStatus = $_GET['pay_status'] ?? '';
$search = $_GET['search'] ?? '';

$filters = [];
if (!empty($filterStatus)) $filters['status'] = $filterStatus;
if (!empty($filterPayStatus)) $filters['payment_status'] = $filterPayStatus;
if (!empty($search)) $filters['search'] = $search;

$orders = Order::getAll($filters);
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">B2B Production Orders</h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Manage client print production workflow, review versioned artworks, update shipping status, and generate GST invoices.</p>
    </div>
</div>

<?php display_flash(); ?>

<div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">

    <!-- Search & Filter Controls -->
    <div style="display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap;">
        <form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; flex: 1;">
            <input type="hidden" name="view" value="orders">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search by Order #, Company, or Client Name..." class="form-control" style="font-size: 0.9rem;">
            <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 800; white-space: nowrap;">
                <i class="fas fa-search"></i> Search Orders
            </button>
        </form>

        <form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px;">
            <input type="hidden" name="view" value="orders">
            <select name="status" class="form-control" style="font-size: 0.88rem; font-weight: 700;" onchange="this.form.submit()">
                <option value="">All Production Statuses</option>
                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                <option value="confirmed" <?= $filterStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="artwork_under_review" <?= $filterStatus === 'artwork_under_review' ? 'selected' : '' ?>>Artwork Under Review</option>
                <option value="artwork_approved" <?= $filterStatus === 'artwork_approved' ? 'selected' : '' ?>>Artwork Approved</option>
                <option value="production_started" <?= $filterStatus === 'production_started' ? 'selected' : '' ?>>In Production / Printing</option>
                <option value="dispatched" <?= $filterStatus === 'dispatched' ? 'selected' : '' ?>>Dispatched</option>
                <option value="delivered" <?= $filterStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            </select>
        </form>
    </div>

    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 50px 20px; color: #64748b;">
            <i class="fas fa-box" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 14px;"></i>
            <h4 style="font-weight: 800; color: #0f172a;">No Orders Found</h4>
            <p style="font-size: 0.9rem;">No commercial print orders match the specified criteria.</p>
        </div>
    <?php else: ?>
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                        <th style="padding: 14px 18px;">Order No</th>
                        <th style="padding: 14px 18px;">Customer / Company</th>
                        <th style="padding: 14px 18px;">Date Placed</th>
                        <th style="padding: 14px 18px;">Total Amount</th>
                        <th style="padding: 14px 18px;">Payment</th>
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
                            <td style="padding: 16px 18px;">
                                <div style="font-weight: 800; color: #0f172a;"><?= e($o['company_name'] ?? 'B2B Client') ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;"><?= e($o['contact_person'] ?? '') ?> (<?= e($o['mobile'] ?? '') ?>)</div>
                            </td>
                            <td style="padding: 16px 18px; color: #64748b;">
                                <?= date('d M Y, h:i A', strtotime($o['placed_at'])) ?>
                            </td>
                            <td style="padding: 16px 18px; font-weight: 900; color: var(--cmyk-cyan);">
                                ₹<?= number_format($o['total'], 2) ?>
                            </td>
                            <td style="padding: 16px 18px;">
                                <span style="font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 10px; text-transform: uppercase; background: <?= $o['payment_status'] === 'paid' ? '#10b981' : '#ef4444' ?>; color: #fff;">
                                    <?= e($o['payment_status']) ?>
                                </span>
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
                                <a href="<?= ADMIN_URL ?>?view=orders&action=detail&id=<?= $o['id'] ?>" class="btn btn-sm" style="background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); font-weight: 800; border: 0; padding: 6px 14px; border-radius: 8px;">
                                    <i class="fas fa-edit"></i> Manage Order
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
