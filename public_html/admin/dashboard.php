<?php
/**
 * MD Printers - Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin_auth();

$db = getDB();

// Dynamic Dashboard Metrics
$totalEnquiries = $db->fetch("SELECT COUNT(*) as cnt FROM enquiries")['cnt'];
$newEnquiries = $db->fetch("SELECT COUNT(*) as cnt FROM enquiries WHERE status = 'new'")['cnt'];
$convertedLeads = $db->fetch("SELECT COUNT(*) as cnt FROM enquiries WHERE status = 'won'")['cnt'];
$totalProducts = $db->fetch("SELECT COUNT(*) as cnt FROM products")['cnt'];

// Recent Enquiries
$recentEnquiries = $db->fetchAll("SELECT * FROM enquiries ORDER BY id DESC LIMIT 8");

$adminTitle = 'Dashboard Overview';
$adminActivePage = 'dashboard';

include __DIR__ . '/includes/admin-header.php';
?>

<!-- Metric Cards Grid -->
<div class="stats-cards-grid">
    <div class="metric-card">
        <div class="metric-info">
            <h3><?= number_format($totalEnquiries) ?></h3>
            <p>Total Quote Requests</p>
        </div>
        <div class="metric-icon"><i class="fas fa-file-invoice-dollar"></i></div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3><?= number_format($newEnquiries) ?></h3>
            <p>New Unread Enquiries</p>
        </div>
        <div class="metric-icon magenta"><i class="fas fa-sparkles"></i></div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3><?= number_format($convertedLeads) ?></h3>
            <p>Won / Converted Orders</p>
        </div>
        <div class="metric-icon green"><i class="fas fa-check-circle"></i></div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3><?= number_format($totalProducts) ?></h3>
            <p>Active Print Products</p>
        </div>
        <div class="metric-icon yellow"><i class="fas fa-box"></i></div>
    </div>
</div>

<!-- Recent Enquiries Section -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-history" style="color: var(--admin-primary); margin-right: 8px;"></i> Recent Quotation & Contact Requests</h2>
        <a href="<?= ADMIN_URL ?>/enquiries/index.php" class="btn-admin btn-admin-outline">View All Enquiries</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ref No</th>
                    <th>Customer Name</th>
                    <th>Company</th>
                    <th>Product / Service</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Received Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentEnquiries)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--admin-muted);">No enquiries recorded yet.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentEnquiries as $enq): ?>
                    <tr>
                        <td><strong style="color: var(--admin-primary);"><?= e($enq['reference_no']) ?></strong></td>
                        <td><?= e($enq['name']) ?></td>
                        <td><?= e($enq['company'] ?: '—') ?></td>
                        <td><?= e($enq['product_service']) ?></td>
                        <td><?= number_format($enq['quantity']) ?></td>
                        <td><?= get_status_badge($enq['status']) ?></td>
                        <td><?= format_date($enq['created_at']) ?></td>
                        <td>
                            <a href="<?= ADMIN_URL ?>/enquiries/view.php?id=<?= $enq['id'] ?>" class="btn-admin btn-admin-primary" style="padding: 4px 10px; font-size: 0.8rem;">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
