<?php
/**
 * MD Printers Admin - Quotations Directory
 */
require_once MODELS_PATH . '/Quote.php';

$filterStatus = $_GET['status'] ?? '';
$quotes = Quote::getAll($filterStatus ? ['status' => $filterStatus] : []);
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">Formal B2B Quotations</h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Review, pricing adjust, and manage customer formal printing quotations.</p>
    </div>
</div>

<?php display_flash(); ?>

<div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
    <!-- Status Filter Tabs -->
    <div style="display: flex; gap: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 24px; flex-wrap: wrap;">
        <?php
            $tabs = [
                '' => 'All Quotes',
                'requested' => 'Requested',
                'preparing' => 'Preparing',
                'sent' => 'Sent to Customer',
                'accepted' => 'Accepted',
                'rejected' => 'Rejected',
                'converted' => 'Converted to Order'
            ];
            foreach ($tabs as $key => $label):
                $active = ($filterStatus === $key) ? 'background: var(--cmyk-cyan); color: #fff;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;';
        ?>
            <a href="<?= ADMIN_URL ?>?view=quotations<?= $key ? '&status=' . $key : '' ?>" 
               style="padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; <?= $active ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($quotes)): ?>
        <div style="text-align: center; padding: 50px 20px; color: #64748b;">
            <i class="fas fa-file-invoice" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 14px;"></i>
            <h4 style="font-weight: 800; color: #0f172a;">No Quotations Found</h4>
            <p style="font-size: 0.9rem;">There are no formal quotes matching the selected status filter.</p>
        </div>
    <?php else: ?>
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.92rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                        <th style="padding: 14px 18px;">Quote No</th>
                        <th style="padding: 14px 18px;">Customer / Company</th>
                        <th style="padding: 14px 18px;">Date</th>
                        <th style="padding: 14px 18px;">Total</th>
                        <th style="padding: 14px 18px;">Status</th>
                        <th style="padding: 14px 18px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $q): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 16px 18px; font-weight: 900; color: #0f172a;">
                                #<?= e($q['quote_number']) ?>
                            </td>
                            <td style="padding: 16px 18px;">
                                <div style="font-weight: 800; color: #0f172a;"><?= e($q['company_name'] ?? 'B2B Client') ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;"><?= e($q['contact_person'] ?? '') ?> (<?= e($q['mobile'] ?? '') ?>)</div>
                            </td>
                            <td style="padding: 16px 18px; color: #64748b;">
                                <?= date('d M Y, h:i A', strtotime($q['created_at'])) ?>
                            </td>
                            <td style="padding: 16px 18px; font-weight: 900; color: var(--cmyk-cyan);">
                                ₹<?= number_format($q['total'], 2) ?>
                            </td>
                            <td style="padding: 16px 18px;">
                                <?php
                                    $st = $q['status'];
                                    $bg = '#64748b';
                                    if ($st === 'sent') $bg = '#3b82f6';
                                    elseif ($st === 'accepted') $bg = '#10b981';
                                    elseif ($st === 'rejected') $bg = '#ef4444';
                                    elseif ($st === 'preparing') $bg = '#f59e0b';
                                    elseif ($st === 'converted') $bg = '#8b5cf6';
                                ?>
                                <span style="background: <?= $bg ?>; color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                    <?= e($st) ?>
                                </span>
                            </td>
                            <td style="padding: 16px 18px; text-align: right;">
                                <a href="<?= ADMIN_URL ?>?view=quotations&action=detail&id=<?= $q['id'] ?>" class="btn btn-sm" style="background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); font-weight: 800; border: 0; padding: 6px 14px; border-radius: 8px;">
                                    <i class="fas fa-edit"></i> Manage / Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
