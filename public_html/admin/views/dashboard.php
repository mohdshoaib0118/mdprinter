<?php
$enquiries = Enquiry::getAll([], 10);
$totalEnquiries = count($enquiries);
$totalProducts = Product::countAll();
$totalServices = count(Service::getAll());
$totalPosts = count(Blog::getAll());
?>

<!-- Analytics Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 36px;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">Total Quote Leads</span>
            <h3 style="font-size: 2.2rem; font-weight: 900; margin: 4px 0 0 0; color: var(--cmyk-cyan);"><?= $totalEnquiries ?></h3>
        </div>
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(0, 133, 178, 0.1); color: var(--cmyk-cyan); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-file-invoice"></i></div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">Active Products</span>
            <h3 style="font-size: 2.2rem; font-weight: 900; margin: 4px 0 0 0; color: var(--cmyk-magenta);"><?= $totalProducts ?></h3>
        </div>
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(238, 0, 131, 0.1); color: var(--cmyk-magenta); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-boxes"></i></div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">Press Capabilities</span>
            <h3 style="font-size: 2.2rem; font-weight: 900; margin: 4px 0 0 0; color: #b45309;"><?= $totalServices ?></h3>
        </div>
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(234, 179, 8, 0.1); color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-industry"></i></div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 800; text-transform: uppercase;">Published Articles</span>
            <h3 style="font-size: 2.2rem; font-weight: 900; margin: 4px 0 0 0; color: #047857;"><?= $totalPosts ?></h3>
        </div>
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(16, 185, 129, 0.1); color: #047857; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-newspaper"></i></div>
    </div>
</div>

<!-- Recent Quotation Submissions Table -->
<div class="admin-table-card">
    <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
        <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: #0f172a;"><i class="fas fa-clock" style="color: var(--cmyk-cyan); margin-right: 8px;"></i> Recent Quotation Requests</h3>
        <a href="<?= ADMIN_URL ?>?view=enquiries" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #334155; font-weight: 700;">Manage All Leads</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Ref No</th>
                <th>Customer Name</th>
                <th>Product / Service</th>
                <th>Quantity</th>
                <th>Received Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($enquiries)): ?>
                <tr><td colspan="7" style="text-align: center; color: #64748b; padding: 30px;">No quotation requests received yet.</td></tr>
            <?php else: ?>
                <?php foreach ($enquiries as $enq): ?>
                    <tr>
                        <td><strong style="color: var(--cmyk-cyan); font-weight: 800;"><?= e($enq['ref_no']) ?></strong></td>
                        <td>
                            <strong style="color: #0f172a; font-size: 0.92rem;"><?= e($enq['customer_name']) ?></strong><br>
                            <span style="color: #64748b; font-size: 0.8rem;"><?= e($enq['email']) ?></span>
                        </td>
                        <td><span style="font-weight: 700; color: #334155;"><?= e($enq['product_service'] ?? 'General Quote') ?></span></td>
                        <td><strong><?= e($enq['quantity'] ? number_format($enq['quantity']) : '-') ?></strong></td>
                        <td style="font-size: 0.85rem; color: #64748b;"><?= date('d M Y, h:i A', strtotime($enq['created_at'])) ?></td>
                        <td><span class="badge-status badge-<?= e($enq['status']) ?>"><?= e($enq['status']) ?></span></td>
                        <td>
                            <a href="<?= ADMIN_URL ?>?view=enquiries&action=detail&id=<?= $enq['id'] ?>" class="btn btn-primary btn-sm" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-eye"></i> View Lead</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

