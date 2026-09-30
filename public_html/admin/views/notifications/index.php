<?php
/**
 * MD Printers Admin - Notification Logs & Channel Integration Status
 */
require_once MODELS_PATH . '/NotificationService.php';

$channelFilter = $_GET['channel'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$filters = [];
if (!empty($channelFilter)) $filters['channel'] = $channelFilter;
if (!empty($statusFilter)) $filters['status'] = $statusFilter;

$logs = NotificationService::getLogs($filters);
$smtpConfigured = NotificationService::isSmtpConfigured();
$waConfigured = NotificationService::isWhatsAppConfigured();
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">Automated Notifications Log</h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Monitor automated email receipts and WhatsApp business alerts triggered across the order lifecycle.</p>
    </div>
</div>

<!-- Integration Status Overview Cards -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
    <!-- SMTP Email Status -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #e0f2fe; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-envelope" style="font-size: 1.4rem; color: #0284c7;"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">SMTP Email Service</h4>
                <p style="margin: 3px 0 0 0; font-size: 0.83rem; color: #64748b;">Branded order confirmations, invoice delivery & artwork status alerts.</p>
            </div>
        </div>
        <span style="font-size: 0.8rem; font-weight: 900; padding: 4px 12px; border-radius: 20px; background: <?= $smtpConfigured ? '#dcfce7' : '#fef3c7' ?>; color: <?= $smtpConfigured ? '#15803d' : '#92400e' ?>;">
            <?= $smtpConfigured ? 'CONFIGURED' : 'PENDING CONFIG' ?>
        </span>
    </div>

    <!-- WhatsApp Business Status -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #dcfce7; display: flex; align-items: center; justify-content: center;">
                <i class="fab fa-whatsapp" style="font-size: 1.6rem; color: #16a34a;"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">WhatsApp Business API</h4>
                <p style="margin: 3px 0 0 0; font-size: 0.83rem; color: #64748b;">Instant mobile tracking links and dispatch status notifications.</p>
            </div>
        </div>
        <span style="font-size: 0.8rem; font-weight: 900; padding: 4px 12px; border-radius: 20px; background: <?= $waConfigured ? '#dcfce7' : '#fef3c7' ?>; color: <?= $waConfigured ? '#15803d' : '#92400e' ?>;">
            <?= $waConfigured ? 'CONFIGURED' : 'PENDING CONFIG' ?>
        </span>
    </div>
</div>

<!-- Logs Directory -->
<div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">Notification Delivery Audit Trail</h3>
        <div>
            <a href="<?= ADMIN_URL ?>?view=notifications" class="btn btn-sm <?= empty($channelFilter) ? 'btn-primary' : 'btn-outline-secondary' ?>">All Logs</a>
            <a href="<?= ADMIN_URL ?>?view=notifications&channel=email" class="btn btn-sm <?= $channelFilter === 'email' ? 'btn-primary' : 'btn-outline-secondary' ?>">Email</a>
            <a href="<?= ADMIN_URL ?>?view=notifications&channel=whatsapp" class="btn btn-sm <?= $channelFilter === 'whatsapp' ? 'btn-primary' : 'btn-outline-secondary' ?>">WhatsApp</a>
        </div>
    </div>

    <?php if (empty($logs)): ?>
        <div style="text-align: center; padding: 45px 20px; color: #64748b;">
            <i class="fas fa-bell-slash" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 12px;"></i>
            <h4 style="font-weight: 800; color: #0f172a;">No Notification Logs</h4>
            <p style="font-size: 0.88rem;">No notification logs recorded yet.</p>
        </div>
    <?php else: ?>
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                        <th style="padding: 12px 14px;">Channel</th>
                        <th style="padding: 12px 14px;">Recipient</th>
                        <th style="padding: 12px 14px;">Event Type</th>
                        <th style="padding: 12px 14px;">Subject / Message Snippet</th>
                        <th style="padding: 12px 14px;">Status</th>
                        <th style="padding: 12px 14px;">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $l): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px;">
                                <span style="font-size: 0.75rem; font-weight: 900; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; background: <?= $l['channel'] === 'email' ? '#e0f2fe' : '#dcfce7' ?>; color: <?= $l['channel'] === 'email' ? '#0369a1' : '#15803d' ?>;">
                                    <i class="<?= $l['channel'] === 'email' ? 'fas fa-envelope' : 'fab fa-whatsapp' ?>"></i> <?= e($l['channel']) ?>
                                </span>
                            </td>
                            <td style="padding: 14px; font-weight: 700; color: #0f172a;">
                                <?= e($l['recipient']) ?>
                                <?php if (!empty($l['company_name'])): ?>
                                    <br><span style="font-size: 0.78rem; font-weight: normal; color: #64748b;"><?= e($l['company_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px;">
                                <code style="font-size: 0.8rem; color: #0f172a; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;"><?= e($l['type']) ?></code>
                            </td>
                            <td style="padding: 14px; max-width: 320px;">
                                <strong><?= e($l['subject'] ?: 'No Subject') ?></strong><br>
                                <span style="font-size: 0.8rem; color: #64748b; font-style: italic;">
                                    <?= e(substr($l['message'] ?? '', 0, 80)) ?><?= strlen($l['message'] ?? '') > 80 ? '...' : '' ?>
                                </span>
                            </td>
                            <td style="padding: 14px;">
                                <?php
                                    $stBg = '#f1f5f9'; $stClr = '#475569';
                                    if ($l['status'] === 'sent' || $l['status'] === 'delivered') { $stBg = '#dcfce7'; $stClr = '#15803d'; }
                                    elseif ($l['status'] === 'pending_configuration' || $l['status'] === 'disabled') { $stBg = '#fef3c7'; $stClr = '#92400e'; }
                                    elseif ($l['status'] === 'failed') { $stBg = '#fee2e2'; $stClr = '#991b1b'; }
                                ?>
                                <span style="font-size: 0.75rem; font-weight: 900; padding: 3px 8px; border-radius: 12px; background: <?= $stBg ?>; color: <?= $stClr ?>;">
                                    <?= strtoupper(str_replace('_', ' ', $l['status'])) ?>
                                </span>
                                <?php if (!empty($l['error_message'])): ?>
                                    <div style="font-size: 0.72rem; color: #ef4444; margin-top: 2px;" title="<?= e($l['error_message']) ?>">
                                        <?= e(substr($l['error_message'], 0, 40)) ?>...
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px; font-size: 0.8rem; color: #64748b;">
                                <?= date('d M Y H:i', strtotime($l['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
