<?php
// Handle inline WhatsApp status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_wa_id'])) {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $waEnquiryId = (int)$_POST['toggle_wa_id'];
        Enquiry::toggleWhatsApp($waEnquiryId);
        set_flash('success', 'Lead WhatsApp sent status updated.');
    }
    redirect_to(ADMIN_URL . '?view=enquiries' . (!empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''));
}

$statusFilter = $_GET['status'] ?? '';
$searchQuery  = $_GET['search'] ?? '';

$filters = [];
if ($statusFilter) $filters['status'] = $statusFilter;
if ($searchQuery)  $filters['search'] = $searchQuery;

$allEnquiries = Enquiry::getAll($filters, 200);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalEnquiries = count($allEnquiries);
$totalPages = max(1, (int)ceil($totalEnquiries / $perPage));
$enquiries = array_slice($allEnquiries, ($page - 1) * $perPage, $perPage);
$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h2 style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            Quote & Contact Enquiries CRM 
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalEnquiries ?></span>
        </h2>
        <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Manage lead pipeline, WhatsApp dispatch logs, artwork downloads, and customer notes.</p>
    </div>

    <!-- Status Filters -->
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="<?= ADMIN_URL ?>?view=enquiries" class="btn <?= empty($statusFilter) ? 'btn-primary' : 'btn-outline' ?> btn-sm">All</a>
        <a href="<?= ADMIN_URL ?>?view=enquiries&status=New" class="btn <?= $statusFilter === 'New' ? 'btn-primary' : 'btn-outline' ?> btn-sm">New</a>
        <a href="<?= ADMIN_URL ?>?view=enquiries&status=Contacted" class="btn <?= $statusFilter === 'Contacted' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Contacted</a>
        <a href="<?= ADMIN_URL ?>?view=enquiries&status=Quoted" class="btn <?= $statusFilter === 'Quoted' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Quoted</a>
        <a href="<?= ADMIN_URL ?>?view=enquiries&status=Won" class="btn <?= $statusFilter === 'Won' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Won</a>
    </div>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="enquiries">
    <?php if ($statusFilter): ?><input type="hidden" name="status" value="<?= e($statusFilter) ?>"><?php endif; ?>
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search by customer name, ref no, phone, email, or product..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=enquiries<?= $statusFilter ? '&status=' . urlencode($statusFilter) : '' ?>" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
    <?php endif; ?>
</form>

<?php if ($flash): ?>
    <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #047857; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 700;">
        <i class="fas fa-check-circle"></i> <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="admin-table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">#</th>
                <th>Ref No</th>
                <th>Type</th>
                <th>Customer & Phone</th>
                <th>Product / Service</th>
                <th>Submitted Date</th>
                <th>WhatsApp Status</th>
                <th>Lead Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($enquiries)): ?>
                <tr><td colspan="9" style="text-align: center; color: #64748b; padding: 40px;">No enquiries matched your filter.</td></tr>
            <?php else: ?>
                <?php foreach ($enquiries as $idx => $enq): ?>
                    <?php 
                        $srNo = ($page - 1) * $perPage + $idx + 1;
                        $isWaSent = !empty($enq['sent_to_whatsapp']);
                        $waPhoneDigits = preg_replace('/[^0-9]/', '', $enq['phone']);
                        $waMsg = "Hello " . $enq['customer_name'] . ", regarding your quote request " . $enq['ref_no'] . " (" . ($enq['product_service'] ?? 'Printing Service') . ") at MD Printers...";
                        $waDirectUrl = "https://wa.me/" . $waPhoneDigits . "?text=" . urlencode($waMsg);
                    ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td><strong style="color: var(--cmyk-cyan);"><?= e($enq['ref_no']) ?></strong></td>
                        <td>
                            <span style="font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; background: #f1f5f9; color: #334155; font-weight: 700; text-transform: uppercase;">
                                <?= e($enq['type']) ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color: #0f172a;"><?= e($enq['customer_name']) ?></strong>
                            <?php if ($enq['company_name']): ?>
                                <small style="color: var(--cmyk-magenta); font-weight: 700;">(<?= e($enq['company_name']) ?>)</small>
                            <?php endif; ?>
                            <br><small style="color: #64748b; font-weight: 600;"><?= e($enq['phone']) ?> | <?= e($enq['email']) ?></small>
                        </td>
                        <td><?= e($enq['product_service'] ?? 'General') ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($enq['created_at'])) ?></td>
                        <td>
                            <form action="" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="toggle_wa_id" value="<?= $enq['id'] ?>">
                                <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;" title="Click to toggle WhatsApp status">
                                    <?php if ($isWaSent): ?>
                                        <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 4px 10px; border-radius: 20px; font-weight: 800; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fab fa-whatsapp" style="font-size: 0.9rem;"></i> Sent <i class="fas fa-check-circle" style="color: #16a34a;"></i>
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 20px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fab fa-whatsapp"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </button>
                            </form>
                        </td>
                        <td><span class="badge-status badge-<?= e($enq['status']) ?>"><?= e($enq['status']) ?></span></td>
                        <td style="text-align: right;">
                            <a href="<?= $waDirectUrl ?>" target="_blank" class="btn btn-sm" style="background: #25D366; color: #ffffff; font-weight: 800; padding: 5px 10px; font-size: 0.78rem; border: none; margin-right: 4px;" title="Chat / Resend Lead on WhatsApp">
                                <i class="fab fa-whatsapp"></i> Chat
                            </a>
                            <a href="<?= ADMIN_URL ?>?view=enquiries&action=detail&id=<?= $enq['id'] ?>" class="btn btn-outline btn-sm" style="padding: 5px 10px; font-size: 0.78rem; font-weight: 700; border-color: #cbd5e1; color: #334155;">
                                <i class="fas fa-eye"></i> View CRM
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Admin Pagination Bar -->
<?php 
    $queryParams = array_filter(['view' => 'enquiries', 'status' => $statusFilter, 'search' => $searchQuery]);
    $pageBaseUrl = ADMIN_URL . '?' . http_build_query($queryParams);
    echo render_pagination($page, $totalPages, $pageBaseUrl);
?>
