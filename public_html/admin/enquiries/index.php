<?php
/**
 * MD Printers - Admin Enquiry Management Hub
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();

$statusFilter = $_GET['status'] ?? 'all';
$searchQuery = sanitizeInput($_GET['q'] ?? '');

$where = "1=1";
$params = [];

if ($statusFilter !== 'all' && !empty($statusFilter)) {
    $where .= " AND status = :status";
    $params['status'] = $statusFilter;
}

if (!empty($searchQuery)) {
    $where .= " AND (reference_no LIKE :q OR name LIKE :q OR company LIKE :q OR email LIKE :q OR product_service LIKE :q)";
    $params['q'] = '%' . $searchQuery . '%';
}

$enquiries = $db->fetchAll("SELECT * FROM enquiries WHERE $where ORDER BY id DESC", $params);

$adminTitle = 'Quotation & Lead Management Hub';
$adminActivePage = 'enquiries';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header" style="flex-wrap: wrap; gap: 16px;">
        <h2><i class="fas fa-file-invoice-dollar" style="color: var(--admin-primary); margin-right: 8px;"></i> Enquiry Records</h2>
        
        <form action="<?= ADMIN_URL ?>/enquiries/index.php" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <select name="status" class="form-control" style="width: auto;" onchange="this.form.submit()">
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New</option>
                <option value="contacted" <?= $statusFilter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="quoted" <?= $statusFilter === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                <option value="won" <?= $statusFilter === 'won' ? 'selected' : '' ?>>Won</option>
                <option value="lost" <?= $statusFilter === 'lost' ? 'selected' : '' ?>>Lost</option>
                <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="Search Ref, Name, Company..." class="form-control" style="width: 220px;">
            <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-search"></i> Search</button>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ref No</th>
                    <th>Type</th>
                    <th>Customer Name</th>
                    <th>Company</th>
                    <th>Product / Service</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--admin-muted);">No enquiries matching criteria.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $enq): ?>
                    <tr>
                        <td><strong style="color: var(--admin-primary);"><?= e($enq['reference_no']) ?></strong></td>
                        <td><span style="font-size: 0.75rem; text-transform: uppercase; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px;"><?= e($enq['type']) ?></span></td>
                        <td><?= e($enq['name']) ?></td>
                        <td><?= e($enq['company'] ?: '—') ?></td>
                        <td><?= e($enq['product_service']) ?></td>
                        <td><?= number_format($enq['quantity']) ?></td>
                        <td><?= get_status_badge($enq['status']) ?></td>
                        <td><?= format_date($enq['created_at']) ?></td>
                        <td>
                            <a href="<?= ADMIN_URL ?>/enquiries/view.php?id=<?= $enq['id'] ?>" class="btn-admin btn-admin-primary" style="padding: 4px 10px; font-size: 0.8rem;">
                                <i class="fas fa-eye"></i> Manage
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
