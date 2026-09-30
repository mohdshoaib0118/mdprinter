<?php
/**
 * MD Printers - Admin FAQs Management
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY sort_order ASC");

$adminTitle = 'FAQs Management';
$adminActivePage = 'faqs';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-question-circle" style="color: var(--admin-primary); margin-right: 8px;"></i> Frequently Asked Questions</h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faqs as $f): ?>
                <tr>
                    <td><?= $f['sort_order'] ?></td>
                    <td><strong><?= e($f['question']) ?></strong></td>
                    <td style="max-width: 450px;"><?= e(substr($f['answer'], 0, 100)) ?>...</td>
                    <td><?= $f['is_active'] ? '<span class="badge badge-won">Active</span>' : '<span class="badge badge-closed">Hidden</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
