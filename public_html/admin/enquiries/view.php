<?php
/**
 * MD Printers - Admin Enquiry Detail & Status Workflow
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$enquiry = $db->fetch("SELECT * FROM enquiries WHERE id = :id LIMIT 1", ['id' => $id]);

if (!$enquiry) {
    header('Location: ' . ADMIN_URL . '/enquiries/index.php');
    exit;
}

$files = $db->fetchAll("SELECT * FROM enquiry_files WHERE enquiry_id = :id", ['id' => $id]);
$notes = $db->fetchAll("SELECT * FROM enquiry_notes WHERE enquiry_id = :id ORDER BY id DESC", ['id' => $id]);

$flash = '';

// Handle Status Update & Note Addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $flash = '<div class="alert-error">CSRF verification failed.</div>';
    } else {
        if (isset($_POST['update_status'])) {
            $newStatus = sanitizeInput($_POST['status'] ?? 'new');
            $db->update('enquiries', ['status' => $newStatus], 'id = :id', ['id' => $id]);
            $enquiry['status'] = $newStatus;
            $flash = '<div style="background: rgba(34, 197, 94, 0.15); border: 1px solid #22C55E; color: #4ADE80; padding: 10px; border-radius: 6px; margin-bottom: 20px;">Enquiry status updated successfully.</div>';
        }

        if (isset($_POST['add_note'])) {
            $noteText = sanitizeInput($_POST['note'] ?? '');
            if (!empty($noteText)) {
                $db->insert('enquiry_notes', [
                    'enquiry_id' => $id,
                    'admin_name' => current_admin_user()['name'],
                    'note' => $noteText
                ]);
                $notes = $db->fetchAll("SELECT * FROM enquiry_notes WHERE enquiry_id = :id ORDER BY id DESC", ['id' => $id]);
                $flash = '<div style="background: rgba(34, 197, 94, 0.15); border: 1px solid #22C55E; color: #4ADE80; padding: 10px; border-radius: 6px; margin-bottom: 20px;">Internal note added.</div>';
            }
        }
    }
}

$adminTitle = 'Enquiry ' . $enquiry['reference_no'];
$adminActivePage = 'enquiries';

include __DIR__ . '/../includes/admin-header.php';
?>

<?= $flash ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <a href="<?= ADMIN_URL ?>/enquiries/index.php" class="btn-admin btn-admin-outline" style="margin-bottom: 10px;">
            <i class="fas fa-arrow-left"></i> Back to Enquiries
        </a>
        <h2 style="margin: 0; font-size: 1.6rem;">Enquiry Ref: <span style="color: var(--admin-primary);"><?= e($enquiry['reference_no']) ?></span></h2>
    </div>
    <div>
        <?= get_status_badge($enquiry['status']) ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 360px; gap: 30px;">
    <!-- Customer Details & Specifications -->
    <div>
        <div class="admin-card">
            <h3 style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; color: var(--admin-primary);">
                <i class="fas fa-user"></i> Customer Information
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.95rem;">
                <div><strong>Full Name:</strong> <?= e($enquiry['name']) ?></div>
                <div><strong>Company:</strong> <?= e($enquiry['company'] ?: 'N/A') ?></div>
                <div><strong>Email:</strong> <a href="mailto:<?= e($enquiry['email']) ?>" style="color: var(--admin-primary);"><?= e($enquiry['email']) ?></a></div>
                <div><strong>Phone:</strong> <a href="tel:<?= e($enquiry['phone']) ?>" style="color: var(--admin-primary);"><?= e($enquiry['phone']) ?></a></div>
                <div><strong>WhatsApp:</strong> <?= e($enquiry['whatsapp'] ?: 'N/A') ?></div>
                <div><strong>Submitted Date:</strong> <?= format_date($enquiry['created_at'], 'M d, Y h:i A') ?></div>
            </div>
        </div>

        <div class="admin-card">
            <h3 style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; color: var(--admin-magenta);">
                <i class="fas fa-sliders-h"></i> Quotation Specifications
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.95rem; margin-bottom: 20px;">
                <div><strong>Product/Service:</strong> <?= e($enquiry['product_service']) ?></div>
                <div><strong>Quantity:</strong> <?= number_format($enquiry['quantity']) ?> pcs</div>
                <div><strong>Material:</strong> <?= e($enquiry['material'] ?: 'Not specified') ?></div>
                <div><strong>Size / Dimensions:</strong> <?= e($enquiry['size'] ?: 'Not specified') ?></div>
                <div><strong>Printing Method:</strong> <?= e($enquiry['printing_type'] ?: 'Not specified') ?></div>
                <div><strong>Finishing:</strong> <?= e($enquiry['finishing'] ?: 'Not specified') ?></div>
            </div>

            <div style="border-top: 1px solid var(--admin-border); padding-top: 16px;">
                <strong>Additional Notes / Instructions:</strong>
                <p style="color: var(--admin-muted); font-size: 0.95rem; line-height: 1.6; margin-top: 8px;">
                    <?= nl2br(e($enquiry['message'] ?: 'No additional notes provided.')) ?>
                </p>
            </div>
        </div>

        <!-- Uploaded Artwork Attachments -->
        <div class="admin-card">
            <h3 style="font-size: 1.1rem; margin-bottom: 16px;">
                <i class="fas fa-paperclip"></i> Uploaded Artwork Files (<?= count($files) ?>)
            </h3>

            <?php if (empty($files)): ?>
            <p style="color: var(--admin-muted); font-size: 0.9rem;">No files uploaded with this enquiry.</p>
            <?php else: ?>
                <div style="display: grid; gap: 10px;">
                    <?php foreach ($files as $f): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--admin-border);">
                        <div>
                            <i class="fas fa-file-alt" style="color: var(--admin-primary); margin-right: 8px;"></i>
                            <strong><?= e($f['original_name']) ?></strong>
                            <span style="font-size: 0.8rem; color: var(--admin-muted); margin-left: 10px;">(<?= round($f['file_size'] / 1024, 1) ?> KB)</span>
                        </div>
                        <a href="<?= SITE_URL ?>/<?= e($f['file_path']) ?>" download target="_blank" class="btn-admin btn-admin-primary" style="padding: 6px 14px;">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar: Status Workflow & Staff Notes -->
    <div>
        <!-- Update Status -->
        <div class="admin-card">
            <h3 style="font-size: 1.1rem; margin-bottom: 16px;">Update Status</h3>
            <form action="" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="update_status" value="1">
                <div class="form-group">
                    <select name="status" class="form-control" style="margin-bottom: 14px;">
                        <option value="new" <?= $enquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="contacted" <?= $enquiry['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                        <option value="in_progress" <?= $enquiry['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="quoted" <?= $enquiry['status'] === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                        <option value="won" <?= $enquiry['status'] === 'won' ? 'selected' : '' ?>>Won (Order Placed)</option>
                        <option value="lost" <?= $enquiry['status'] === 'lost' ? 'selected' : '' ?>>Lost</option>
                        <option value="closed" <?= $enquiry['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Save Status Update
                </button>
            </form>
        </div>

        <!-- Staff Internal Notes -->
        <div class="admin-card">
            <h3 style="font-size: 1.1rem; margin-bottom: 16px;"><i class="fas fa-sticky-note"></i> Internal Staff Notes Log</h3>
            
            <form action="" method="POST" style="margin-bottom: 20px;">
                <?= csrfField() ?>
                <input type="hidden" name="add_note" value="1">
                <div class="form-group">
                    <textarea name="note" class="form-control" rows="3" placeholder="Add staff note or pricing calculation details..." required></textarea>
                </div>
                <button type="submit" class="btn-admin btn-admin-outline" style="width: 100%;">
                    <i class="fas fa-plus"></i> Add Note
                </button>
            </form>

            <div style="display: grid; gap: 12px; max-height: 300px; overflow-y: auto;">
                <?php if (empty($notes)): ?>
                <p style="color: var(--admin-muted); font-size: 0.85rem;">No staff notes logged yet.</p>
                <?php else: ?>
                    <?php foreach ($notes as $n): ?>
                    <div style="background: rgba(255,255,255,0.02); border-left: 3px solid var(--admin-primary); padding: 10px 12px; font-size: 0.85rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px; color: var(--admin-muted);">
                            <strong><?= e($n['admin_name']) ?></strong>
                            <span><?= format_date($n['created_at'], 'M d, h:i A') ?></span>
                        </div>
                        <div style="color: var(--admin-text);"><?= nl2br(e($n['note'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
