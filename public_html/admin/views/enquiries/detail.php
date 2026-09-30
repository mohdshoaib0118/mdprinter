<?php
$enquiryId = (int)($_GET['id'] ?? 0);
$enquiry = Enquiry::getById($enquiryId);

if (!$enquiry) {
    echo "<div class='alert alert-danger'>Enquiry not found.</div>";
    return;
}

// Handle Status Update, WhatsApp toggle, or Note addition
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $newStatus = $_POST['status'] ?? 'New';
        if (Enquiry::updateStatus($enquiryId, $newStatus)) {
            $enquiry['status'] = $newStatus;
            $message = 'Enquiry status updated successfully.';
        }
    } elseif (isset($_POST['toggle_whatsapp'])) {
        Enquiry::toggleWhatsApp($enquiryId);
        $enquiry = Enquiry::getById($enquiryId); // Refresh
        $message = 'WhatsApp sent status toggled.';
    } elseif (isset($_POST['add_note'])) {
        $noteText = trim($_POST['note_text'] ?? '');
        if (!empty($noteText)) {
            Enquiry::addNote($enquiryId, $noteText, $_SESSION['admin_id'] ?? 1);
            $enquiry = Enquiry::getById($enquiryId); // Refresh
            $message = 'Internal CRM note added.';
        }
    }
}

$isWaSent = !empty($enquiry['sent_to_whatsapp']);
$waPhoneDigits = preg_replace('/[^0-9]/', '', $enquiry['phone']);
$waMsg = "*PRINTING QUOTE DETAILS [{$enquiry['ref_no']}]*\n\n" .
         "👤 *Customer:* " . $enquiry['customer_name'] . "\n" .
         "📱 *Phone:* " . $enquiry['phone'] . "\n" .
         "✉️ *Email:* " . $enquiry['email'] . "\n" .
         "🖨️ *Product/Service:* " . ($enquiry['product_service'] ?: 'N/A') . "\n" .
         "📦 *Quantity:* " . ($enquiry['quantity'] ?: 'N/A') . "\n" .
         "💬 *Notes:* " . ($enquiry['message'] ?: 'None');
$waDirectUrl = "https://wa.me/" . $waPhoneDigits . "?text=" . urlencode($waMsg);
?>

<div style="margin-bottom: 24px;">
    <a href="<?= ADMIN_URL ?>?view=enquiries" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #334155; font-weight: 700;"><i class="fas fa-arrow-left"></i> Back to All Leads</a>
</div>

<?php if (!empty($message)): ?>
    <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #047857; padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-weight: 700;">
        <i class="fas fa-check-circle"></i> <?= e($message) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Lead Details -->
    <div class="admin-table-card" style="padding: 30px; background: #ffffff;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px; flex-wrap: wrap; gap: 10px;">
            <div>
                <span class="badge-status badge-<?= e($enquiry['status']) ?>"><?= e($enquiry['status']) ?></span>
                
                <form action="" method="POST" style="display: inline-block; margin-left: 10px;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="toggle_whatsapp" value="1">
                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;" title="Click to toggle WhatsApp status">
                        <?php if ($isWaSent): ?>
                            <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fab fa-whatsapp" style="font-size: 0.95rem;"></i> Sent on WhatsApp <i class="fas fa-check-circle" style="color: #16a34a;"></i>
                            </span>
                        <?php else: ?>
                            <span style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fab fa-whatsapp"></i> Mark WhatsApp Sent
                            </span>
                        <?php endif; ?>
                    </button>
                </form>

                <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 8px; margin-bottom: 0;">Ref: <?= e($enquiry['ref_no']) ?></h2>
            </div>
            <div style="text-align: right;">
                <a href="<?= $waDirectUrl ?>" target="_blank" class="btn btn-sm" style="background: #25D366; color: #ffffff; font-weight: 800; padding: 8px 16px; border: none; margin-bottom: 6px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i> Open Specs in WhatsApp
                </a>
                <div style="font-size: 0.85rem; color: #64748b;">
                    Submitted: <?= date('d M Y, h:i A', strtotime($enquiry['created_at'])) ?>
                </div>
            </div>
        </div>

        <h3 style="font-size: 1.2rem; color: var(--cmyk-cyan); margin-bottom: 16px;">Customer Information</h3>
        <div class="grid-2" style="margin-bottom: 30px;">
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Name:</span><br>
                <strong><?= e($enquiry['customer_name']) ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Company:</span><br>
                <strong><?= e($enquiry['company_name'] ?: 'Individual Customer') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Email:</span><br>
                <a href="mailto:<?= e($enquiry['email']) ?>" style="color: var(--cmyk-cyan);"><?= e($enquiry['email']) ?></a>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Phone / WhatsApp:</span><br>
                <a href="tel:<?= e($enquiry['phone']) ?>" style="color: var(--cmyk-yellow);"><?= e($enquiry['phone']) ?></a>
            </div>
        </div>

        <h3 style="font-size: 1.2rem; color: var(--cmyk-magenta); margin-bottom: 16px;">Job Specifications</h3>
        <div class="grid-3" style="margin-bottom: 30px; background: rgba(255,255,255,0.02); padding: 20px; border-radius: 8px;">
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Product/Service:</span><br>
                <strong><?= e($enquiry['product_service'] ?: 'N/A') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Quantity:</span><br>
                <strong><?= e($enquiry['quantity'] ? number_format($enquiry['quantity']) : 'N/A') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Size / Dim:</span><br>
                <strong><?= e($enquiry['size'] ?: 'Standard') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Paper GSM:</span><br>
                <strong><?= e($enquiry['material_gsm'] ?: 'N/A') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted); font-size: 0.85rem;">Finishing:</span><br>
                <strong><?= e($enquiry['finishing'] ?: 'N/A') ?></strong>
            </div>
        </div>

        <?php if (!empty($enquiry['message'])): ?>
            <h3 style="font-size: 1.1rem; margin-bottom: 10px;">Customer Message / Instructions:</h3>
            <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--admin-border); padding: 16px; border-radius: 8px; font-size: 0.95rem; color: var(--text-muted); margin-bottom: 30px; line-height: 1.6;">
                <?= e($enquiry['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Artwork Attachments -->
        <h3 style="font-size: 1.2rem; color: var(--cmyk-yellow); margin-bottom: 16px;"><i class="fas fa-paperclip"></i> Uploaded Artwork Files</h3>
        <?php if (empty($enquiry['files'])): ?>
            <p style="color: var(--text-muted); font-size: 0.9rem;">No artwork file attached with this quote request.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($enquiry['files'] as $file): ?>
                    <div style="background: rgba(0, 174, 239, 0.1); border: 1px solid var(--border-highlight); padding: 14px 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="display: block; font-size: 0.95rem; color: var(--text-main);"><i class="fas fa-file-pdf" style="color: var(--cmyk-cyan); margin-right: 8px;"></i> <?= e($file['original_name']) ?></strong>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= format_bytes($file['file_size']) ?> | Uploaded: <?= date('d M Y', strtotime($file['created_at'])) ?></span>
                        </div>
                        <a href="<?= SITE_URL ?>/<?= e($file['filepath']) ?>" target="_blank" download class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Download Artwork File</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar CRM Actions -->
    <div>
        <!-- Update Status Box -->
        <div class="admin-table-card" style="padding: 24px; margin-bottom: 30px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                Lead Status
                <button type="button" class="btn btn-primary btn-sm" onclick="openAdminModal('updateStatusModal')" style="font-size: 0.8rem; padding: 4px 10px;"><i class="fas fa-edit"></i> Change Status</button>
            </h3>
            <div style="font-size: 0.95rem; color: #334155; margin-bottom: 10px;">
                Current Status: <span class="badge-status badge-<?= e($enquiry['status']) ?>"><?= e($enquiry['status']) ?></span>
            </div>
        </div>

        <!-- Internal CRM Notes Log -->
        <div class="admin-table-card" style="padding: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                Internal Notes Timeline
                <button type="button" class="btn btn-outline btn-sm" onclick="openAdminModal('addNoteModal')" style="font-size: 0.8rem; padding: 4px 10px;"><i class="fas fa-plus"></i> Add Note</button>
            </h3>

            <div style="display: flex; flex-direction: column; gap: 14px; max-height: 350px; overflow-y: auto;">
                <?php if (empty($enquiry['notes'])): ?>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">No notes added yet.</p>
                <?php else: ?>
                    <?php foreach ($enquiry['notes'] as $n): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; font-size: 0.85rem;">
                            <p style="margin: 0 0 6px 0; color: #0f172a; font-weight: 600;"><?= e($n['note']) ?></p>
                            <small style="color: #64748b; font-size: 0.75rem;"><?= e($n['admin_name'] ?? 'System') ?> • <?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Update Status Modal Popup -->
<div id="updateStatusModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog" style="max-width: 480px;">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title"><i class="fas fa-tasks"></i> Update Lead Status</h3>
                <button type="button" class="admin-modal-close" data-close-modal="updateStatusModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Select New Status</label>
                        <select name="status" class="form-control">
                            <option value="New" <?= $enquiry['status'] === 'New' ? 'selected' : '' ?>>New</option>
                            <option value="Contacted" <?= $enquiry['status'] === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                            <option value="In Progress" <?= $enquiry['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Quoted" <?= $enquiry['status'] === 'Quoted' ? 'selected' : '' ?>>Quoted</option>
                            <option value="Won" <?= $enquiry['status'] === 'Won' ? 'selected' : '' ?>>Won (Order Placed)</option>
                            <option value="Lost" <?= $enquiry['status'] === 'Lost' ? 'selected' : '' ?>>Lost</option>
                            <option value="Closed" <?= $enquiry['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="button" class="btn btn-outline" data-close-modal="updateStatusModal">Cancel</button>
                        <button type="submit" name="update_status" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Add Note Modal Popup -->
<div id="addNoteModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog" style="max-width: 520px;">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title"><i class="fas fa-edit"></i> Add Internal Note</h3>
                <button type="button" class="admin-modal-close" data-close-modal="addNoteModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Internal CRM Note Details *</label>
                        <textarea name="note_text" rows="4" class="form-control" required placeholder="e.g. Sent formal quote & digital proof via email. Customer requested proof before print."></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="button" class="btn btn-outline" data-close-modal="addNoteModal">Cancel</button>
                        <button type="submit" name="add_note" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-plus-circle"></i> Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

