<?php
/**
 * MD Printers - Admin B2B Customer Management Index View
 */

$statusFilter = $_GET['status'] ?? '';
$search = $_GET['q'] ?? '';

$filters = [];
if (!empty($statusFilter)) $filters['status'] = $statusFilter;
if (!empty($search)) $filters['search'] = $search;

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$totalCustomers = Customer::countAll($filters);
$totalPages = max(1, (int)ceil($totalCustomers / $perPage));
$customers = Customer::getAll($filters, $perPage, ($page - 1) * $perPage);

// Tab Counts
$pendingCount = Customer::countAll(['status' => 'pending']);
$approvedCount = Customer::countAll(['status' => 'approved']);
$rejectedCount = Customer::countAll(['status' => 'rejected']);
$suspendedCount = Customer::countAll(['status' => 'suspended']);

$flash = get_flash();
?>

<div class="admin-content">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0;">B2B Customers & Members</h1>
            <p style="color: #64748b; font-size: 0.9rem; margin: 4px 0 0 0;">Review account registrations, approve trade members, and manage credit lines</p>
        </div>

        <?php if ($pendingCount > 0): ?>
            <div style="background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.35); color: #b45309; padding: 10px 18px; border-radius: 30px; font-weight: 800; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-bell" style="font-size: 1.1rem;"></i> <strong><?= $pendingCount ?></strong> Pending Approval<?= $pendingCount > 1 ? 's' : '' ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($flash): ?>
        <div class="alert-<?= e($flash['type']) ?>" style="margin-bottom: 20px;">
            <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Status Tabs & Search Filter -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="<?= ADMIN_URL ?>?view=customers" class="btn <?= empty($statusFilter) ? 'btn-primary' : 'btn-outline' ?> btn-sm">All (<?= Customer::countAll([]) ?>)</a>
            <a href="<?= ADMIN_URL ?>?view=customers&status=pending" class="btn <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline' ?> btn-sm" style="<?= $pendingCount > 0 ? 'border-color:#eab308; color:#b45309;' : '' ?>">Pending (<?= $pendingCount ?>)</a>
            <a href="<?= ADMIN_URL ?>?view=customers&status=approved" class="btn <?= $statusFilter === 'approved' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Approved (<?= $approvedCount ?>)</a>
            <a href="<?= ADMIN_URL ?>?view=customers&status=rejected" class="btn <?= $statusFilter === 'rejected' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Rejected (<?= $rejectedCount ?>)</a>
            <a href="<?= ADMIN_URL ?>?view=customers&status=suspended" class="btn <?= $statusFilter === 'suspended' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Suspended (<?= $suspendedCount ?>)</a>
        </div>

        <form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; min-width: 280px;">
            <input type="hidden" name="view" value="customers">
            <?php if (!empty($statusFilter)): ?>
                <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
            <?php endif; ?>
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search company, GST, mobile..." class="form-control" style="font-size: 0.88rem;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Customers Table Card -->
    <div class="admin-table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company Name</th>
                    <th>Contact Person</th>
                    <th>Mobile / Email</th>
                    <th>GSTIN</th>
                    <th>Member Tier</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="fas fa-users" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                            No customer accounts found matching criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><strong>#<?= $c['id'] ?></strong></td>
                            <td>
                                <strong style="color: #0f172a; display: block; font-size: 0.95rem;"><?= e($c['company_name']) ?></strong>
                                <span style="font-size: 0.78rem; color: #64748b;"><?= e($c['business_type'] ?: 'Commercial Client') ?></span>
                            </td>
                            <td><?= e($c['contact_person']) ?></td>
                            <td>
                                <div><i class="fas fa-phone-alt" style="font-size: 0.75rem; color: var(--cmyk-cyan);"></i> <?= e($c['mobile']) ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;"><?= e($c['email']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($c['gst_number'])): ?>
                                    <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; border: 1px solid #cbd5e1; color: #0f172a; display: inline-block;">
                                        <?= e($c['gst_number']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 0.8rem;">Unregistered</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-weight: 800; font-size: 0.8rem; color: var(--cmyk-cyan);">
                                    <?= e($c['customer_type'] ?: 'Business') ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $statusBadge = 'badge-Contacted';
                                    if ($c['account_status'] === 'approved') $statusBadge = 'badge-Won';
                                    if ($c['account_status'] === 'pending') $statusBadge = 'badge-Contacted';
                                    if ($c['account_status'] === 'rejected' || $c['account_status'] === 'blocked') $statusBadge = 'badge-Lost';
                                    if ($c['account_status'] === 'suspended') $statusBadge = 'badge-Quoted';
                                ?>
                                <span class="badge-status <?= $statusBadge ?>"><?= e($c['account_status']) ?></span>
                            </td>
                            <td style="font-size: 0.8rem; color: #64748b;">
                                <?= date('d M Y', strtotime($c['created_at'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= ADMIN_URL ?>?view=customers&action=detail&id=<?= $c['id'] ?>" class="btn btn-outline btn-sm" style="font-weight: 800; font-size: 0.8rem;">
                                    Manage <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php 
        $pageBaseUrl = ADMIN_URL . '?view=customers' . (!empty($statusFilter) ? '&status=' . urlencode($statusFilter) : '') . (!empty($search) ? '&q=' . urlencode($search) : '');
        echo render_pagination($page, $totalPages, $pageBaseUrl);
    ?>

</div>
