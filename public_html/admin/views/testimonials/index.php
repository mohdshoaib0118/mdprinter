<?php
/**
 * MD Printers Admin - Testimonials Manager View (Modal Popup Form)
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        if (isset($_POST['save_testimonial'])) {
            Testimonial::save($_POST);
            set_flash('success', !empty($_POST['id']) ? 'Testimonial updated successfully.' : 'New Client Testimonial saved!');
            redirect_to(ADMIN_URL . '?view=testimonials');
        } elseif (isset($_POST['delete_id'])) {
            Testimonial::delete((int)$_POST['delete_id']);
            set_flash('success', 'Testimonial removed.');
            redirect_to(ADMIN_URL . '?view=testimonials');
        }
    }
}

$searchQuery = trim($_GET['search'] ?? '');
$allTestimonials = Testimonial::getAll();
if (!empty($searchQuery)) {
    $allTestimonials = array_filter($allTestimonials, function($t) use ($searchQuery) {
        $haystack = strtolower(($t['client_name'] ?? '') . ' ' . ($t['company'] ?? '') . ' ' . ($t['designation'] ?? '') . ' ' . ($t['testimonial'] ?? ''));
        return strpos($haystack, strtolower($searchQuery)) !== false;
    });
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalTestimonials = count($allTestimonials);
$totalPages = max(1, (int)ceil($totalTestimonials / $perPage));
$testimonials = array_slice($allTestimonials, ($page - 1) * $perPage, $perPage);

$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            Client Testimonials Manager
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalTestimonials ?></span>
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Add, edit, or remove customer reviews in a Modal Popup.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddTestimonialModal()" style="font-weight: 800; cursor: pointer;">
        <i class="fas fa-plus"></i> Add Testimonial
    </button>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="testimonials">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search client name, company, or review text..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=testimonials" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
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
                <th>Client Name & Designation</th>
                <th>Company</th>
                <th>Rating</th>
                <th>Review Quote</th>
                <th style="text-align: right; width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($testimonials)): ?>
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #64748b;">No client testimonials found matching search.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($testimonials as $idx => $t): ?>
                    <?php $srNo = ($page - 1) * $perPage + $idx + 1; ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td>
                            <strong style="font-size: 0.95rem; color: #0f172a; display: block;"><?= e($t['client_name']) ?></strong>
                            <small style="color: #64748b;"><?= e($t['designation'] ?? 'Client') ?></small>
                        </td>
                        <td><span style="font-weight: 700; color: #334155;"><?= e($t['company']) ?></span></td>
                        <td><span style="color: var(--cmyk-gold); font-weight: 800;"><?= e($t['rating']) ?> ★</span></td>
                        <td>
                            <small style="color: #475569; display: block; max-width: 420px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                "<?= e($t['testimonial']) ?>"
                            </small>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <button type="button" onclick='openEditTestimonialModal(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                        class="btn btn-outline btn-sm" style="padding: 6px 12px; border-color: #cbd5e1; color: #334155; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="" method="POST" style="display: inline;" onsubmit="return confirmAdminDelete(event, this, 'Are you sure you want to delete this client testimonial?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="delete_id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="btn btn-sm" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Delete Testimonial">
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
<?= render_pagination($page, $totalPages, ADMIN_URL . '?view=testimonials' . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '')) ?>

<!-- Testimonial Modal Popup -->
<div id="testimonialModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog" style="max-width: 560px;">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="testimonialModalTitle" class="admin-modal-title"><i class="fas fa-comment-dots"></i> Add Client Testimonial</h3>
                <button type="button" class="admin-modal-close" data-close-modal="testimonialModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_testimonial" value="1">
                    <input type="hidden" name="id" id="testimonial_id" value="">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Client Name *</label>
                            <input type="text" name="client_name" id="testimonial_client_name" required placeholder="Vikram Malhotra">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="company" id="testimonial_company" required placeholder="Malhotra Consumer Goods">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Designation / Role</label>
                            <input type="text" name="designation" id="testimonial_designation" placeholder="VP Marketing">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Star Rating (1 - 5)</label>
                            <input type="number" name="rating" id="testimonial_rating" value="5" min="1" max="5">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Client Review Copy *</label>
                        <textarea name="testimonial" id="testimonial_copy" rows="4" required placeholder="MD Printers executed our product packaging launch with absolute precision..."></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" data-close-modal="testimonialModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddTestimonialModal() {
    document.getElementById('testimonialModalTitle').innerHTML = '<i class="fas fa-comment-dots"></i> Add Client Testimonial';
    document.getElementById('testimonial_id').value = '';
    document.getElementById('testimonial_client_name').value = '';
    document.getElementById('testimonial_company').value = '';
    document.getElementById('testimonial_designation').value = '';
    document.getElementById('testimonial_rating').value = '5';
    document.getElementById('testimonial_copy').value = '';
    openAdminModal('testimonialModal');
}

function openEditTestimonialModal(data) {
    document.getElementById('testimonialModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Testimonial';
    document.getElementById('testimonial_id').value = data.id || '';
    document.getElementById('testimonial_client_name').value = data.client_name || '';
    document.getElementById('testimonial_company').value = data.company || '';
    document.getElementById('testimonial_designation').value = data.designation || '';
    document.getElementById('testimonial_rating').value = data.rating || 5;
    document.getElementById('testimonial_copy').value = data.testimonial || '';
    openAdminModal('testimonialModal');
}
</script>
