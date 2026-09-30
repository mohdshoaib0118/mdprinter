<?php
/**
 * MD Printers Admin - FAQs Manager View (Modal Popup Form)
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        if (isset($_POST['save_faq'])) {
            Faq::save($_POST);
            set_flash('success', !empty($_POST['id']) ? 'FAQ item updated successfully.' : 'New FAQ item added!');
            redirect_to(ADMIN_URL . '?view=faqs');
        } elseif (isset($_POST['delete_id'])) {
            Faq::delete((int)$_POST['delete_id']);
            set_flash('success', 'FAQ item removed.');
            redirect_to(ADMIN_URL . '?view=faqs');
        }
    }
}

$searchQuery = trim($_GET['search'] ?? '');
$allFaqs = Faq::getAll();
if (!empty($searchQuery)) {
    $allFaqs = array_filter($allFaqs, function($f) use ($searchQuery) {
        $haystack = strtolower(($f['question'] ?? '') . ' ' . ($f['answer'] ?? '') . ' ' . ($f['category'] ?? ''));
        return strpos($haystack, strtolower($searchQuery)) !== false;
    });
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalFaqs = count($allFaqs);
$totalPages = max(1, (int)ceil($totalFaqs / $perPage));
$faqs = array_slice($allFaqs, ($page - 1) * $perPage, $perPage);

$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            FAQ Manager
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalFaqs ?></span>
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Add and update pre-sales technical questions in a Modal Popup.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddFaqModal()" style="font-weight: 800; cursor: pointer;">
        <i class="fas fa-plus"></i> Add New FAQ
    </button>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="faqs">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search FAQ question, answer, or category..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=faqs" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
    <?php endif; ?>
</form>

<?php if ($flash): ?>
    <div class="alert-<?= e($flash['type']) ?>" style="margin-bottom: 24px;">
        <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="admin-table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">#</th>
                <th>Category</th>
                <th>Question</th>
                <th>Answer Detail</th>
                <th style="text-align: right; width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($faqs)): ?>
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #64748b;">No FAQ items found matching search.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($faqs as $idx => $f): ?>
                    <?php $srNo = ($page - 1) * $perPage + $idx + 1; ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td><span style="font-weight: 800; color: var(--cmyk-cyan);"><?= e($f['category']) ?></span></td>
                        <td><strong style="font-size: 0.95rem; color: #0f172a;"><?= e($f['question']) ?></strong></td>
                        <td>
                            <small style="color: #475569; display: block; max-width: 450px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($f['answer']) ?>
                            </small>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <button type="button" onclick='openEditFaqModal(<?= json_encode($f, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                        class="btn btn-outline btn-sm" style="padding: 6px 12px; border-color: #cbd5e1; color: #334155; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="" method="POST" style="display: inline;" onsubmit="return confirmAdminDelete(event, this, 'Are you sure you want to delete this FAQ question and answer?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="delete_id" value="<?= $f['id'] ?>">
                                    <button type="submit" class="btn btn-sm" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Delete FAQ">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Admin Pagination Bar -->
<?= render_pagination($page, $totalPages, ADMIN_URL . '?view=faqs' . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '')) ?>

<!-- FAQ Modal Popup -->
<div id="faqModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog" style="max-width: 560px;">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="faqModalTitle" class="admin-modal-title"><i class="fas fa-question-circle"></i> Add New FAQ</h3>
                <button type="button" class="admin-modal-close" data-close-modal="faqModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_faq" value="1">
                    <input type="hidden" name="id" id="faq_id" value="">

                    <div class="form-group">
                        <label class="form-label">Category Group</label>
                        <input type="text" name="category" id="faq_category" value="Artwork & Prepress" placeholder="Artwork & Prepress">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Question *</label>
                        <input type="text" name="question" id="faq_question" required placeholder="What artwork file formats do you accept?">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Detailed Answer *</label>
                        <textarea name="answer" id="faq_answer" rows="4" required placeholder="We accept vector PDF, AI, PSD, and high-res TIFF..."></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" data-close-modal="faqModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save FAQ Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddFaqModal() {
    document.getElementById('faqModalTitle').innerHTML = '<i class="fas fa-question-circle"></i> Add New FAQ';
    document.getElementById('faq_id').value = '';
    document.getElementById('faq_category').value = 'Artwork & Prepress';
    document.getElementById('faq_question').value = '';
    document.getElementById('faq_answer').value = '';
    openAdminModal('faqModal');
}

function openEditFaqModal(data) {
    document.getElementById('faqModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit FAQ';
    document.getElementById('faq_id').value = data.id || '';
    document.getElementById('faq_category').value = data.category || 'Artwork & Prepress';
    document.getElementById('faq_question').value = data.question || '';
    document.getElementById('faq_answer').value = data.answer || '';
    openAdminModal('faqModal');
}
</script>
