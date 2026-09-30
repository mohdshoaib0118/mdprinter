<?php
/**
 * MD Printers Admin - Services Manager View (Modal Popup Form)
 */

// Handle POST Save / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        $serviceData = [
            'id'          => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 1,
            'name'        => $name,
            'slug'        => !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($name),
            'icon'        => trim($_POST['icon'] ?? 'fas fa-print'),
            'short_desc'  => trim($_POST['short_desc'] ?? ''),
            'full_desc'   => trim($_POST['full_desc'] ?? ''),
            'main_image'  => trim($_POST['main_image'] ?? ''),
            'video_url'   => trim($_POST['video_url'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'status'      => $_POST['status'] ?? 'published',
            'meta_title'  => trim($_POST['meta_title'] ?? ''),
            'meta_desc'   => trim($_POST['meta_desc'] ?? '')
        ];

        // If updating and no image text supplied, retain existing image
        if (!empty($serviceData['id']) && empty($serviceData['main_image'])) {
            $existing = Service::getById($serviceData['id']);
            if ($existing && !empty($existing['main_image'])) {
                $serviceData['main_image'] = $existing['main_image'];
            }
        }

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_file($_FILES['image_file'], 'services');
            if (isset($upload['filepath'])) {
                $serviceData['main_image'] = $upload['filepath'];
            }
        }

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $videoUpload = upload_file($_FILES['video_file'], 'services');
            if (isset($videoUpload['filepath'])) {
                $serviceData['video_url'] = $videoUpload['filepath'];
            }
        }

        if (Service::save($serviceData)) {
            set_flash('success', !empty($serviceData['id']) ? 'Service updated successfully.' : 'New Service capability added!');
        } else {
            set_flash('danger', 'Failed to save service.');
        }
    }
    redirect_to(ADMIN_URL . '?view=services');
}

if ($action === 'delete' && $id > 0) {
    if (verify_csrf($_GET['csrf_token'] ?? '')) {
        Service::delete($id);
        set_flash('success', 'Service deleted successfully.');
    }
    redirect_to(ADMIN_URL . '?view=services');
}

$searchQuery = trim($_GET['search'] ?? '');
$allServices = Service::getAll();
if (!empty($searchQuery)) {
    $allServices = array_filter($allServices, function($s) use ($searchQuery) {
        $haystack = strtolower(($s['name'] ?? '') . ' ' . ($s['short_desc'] ?? '') . ' ' . ($s['slug'] ?? ''));
        return strpos($haystack, strtolower($searchQuery)) !== false;
    });
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalServices = count($allServices);
$totalPages = max(1, (int)ceil($totalServices / $perPage));
$services = array_slice($allServices, ($page - 1) * $perPage, $perPage);

$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            Commercial Services
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalServices ?></span>
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Manage press capabilities and offerings in a Modal Popup.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddServiceModal()" style="font-weight: 800; cursor: pointer;">
        <i class="fas fa-plus"></i> Add Service
    </button>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="services">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search service name or overview..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=services" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
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
                <th style="width: 60px;">Icon</th>
                <th>Service Name & Slug</th>
                <th>Short Overview</th>
                <th>Featured</th>
                <th>Status</th>
                <th style="text-align: right; width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #64748b;">No services found matching search.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $idx => $s): ?>
                    <?php $srNo = ($page - 1) * $perPage + $idx + 1; ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td style="font-size: 1.4rem; color: var(--cmyk-cyan); text-align: center;"><i class="<?= e($s['icon']) ?>"></i></td>
                        <td>
                            <strong style="font-size: 0.95rem; color: #0f172a; display: block;"><?= e($s['name']) ?></strong>
                            <small style="color: #64748b; font-size: 0.8rem;"><?= e($s['slug']) ?></small>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: #475569; max-width: 350px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($s['short_desc']) ?>
                            </div>
                        </td>
                        <td><?= $s['is_featured'] ? '<span class="badge badge-info">Featured</span>' : '<span class="badge" style="background:#f1f5f9; color:#64748b;">Standard</span>' ?></td>
                        <td>
                            <span class="badge badge-<?= $s['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= ucfirst($s['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <button type="button" onclick='openEditServiceModal(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                        class="btn btn-outline btn-sm" style="padding: 6px 12px; border-color: #cbd5e1; color: #334155; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="<?= ADMIN_URL ?>?view=services&action=delete&id=<?= $s['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>" 
                                   onclick="return confirmAdminDelete(event, this.href, 'Are you sure you want to delete this commercial service capability?');" 
                                   class="btn btn-sm" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Delete Service">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Admin Pagination Bar -->
<?= render_pagination($page, $totalPages, ADMIN_URL . '?view=services' . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '')) ?>

<!-- Service Modal Popup -->
<div id="serviceModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="serviceModalTitle" class="admin-modal-title"><i class="fas fa-concierge-bell"></i> Add New Commercial Service</h3>
                <button type="button" class="admin-modal-close" data-close-modal="serviceModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_service" value="1">
                    <input type="hidden" name="id" id="service_id" value="">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Service Name *</label>
                            <input type="text" name="name" id="service_name" required placeholder="Commercial Offset Printing">
                        </div>
                        <div class="form-group">
                            <label class="form-label">FontAwesome Icon Class</label>
                            <input type="text" name="icon" id="service_icon" value="fas fa-print" placeholder="fas fa-print">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SEO Slug (Auto-generated if empty)</label>
                        <input type="text" name="slug" id="service_slug" placeholder="printing">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Short Summary *</label>
                        <textarea name="short_desc" id="service_short_desc" rows="2" required placeholder="High-volume commercial offset & digital printing..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Full Service Description</label>
                        <textarea name="full_desc" id="service_full_desc" rows="4" placeholder="Comprehensive service capability description..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Main Feature Image Path</label>
                        <input type="text" name="main_image" id="service_main_image" value="" placeholder="Upload file below or enter path">
                        <input type="file" name="image_file" accept="image/*" style="margin-top: 6px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Video Media URL or Path (Optional MP4 / WebM / YouTube / Vimeo)</label>
                        <input type="text" name="video_url" id="service_video_url" placeholder="https://example.com/video.mp4 or YouTube link">
                        <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Or upload MP4/WebM service demonstration video below:</small>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" style="margin-top: 6px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" id="service_status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 28px;">
                            <label style="font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" name="is_featured" id="service_is_featured" value="1" style="width: 18px; height: 18px;">
                                Feature on Public Nav
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" data-close-modal="serviceModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddServiceModal() {
    document.getElementById('serviceModalTitle').innerHTML = '<i class="fas fa-concierge-bell"></i> Add New Service';
    document.getElementById('service_id').value = '';
    document.getElementById('service_name').value = '';
    document.getElementById('service_icon').value = 'fas fa-print';
    document.getElementById('service_slug').value = '';
    document.getElementById('service_short_desc').value = '';
    document.getElementById('service_full_desc').value = '';
    document.getElementById('service_main_image').value = '';
    document.getElementById('service_video_url').value = '';
    document.getElementById('service_status').value = 'published';
    document.getElementById('service_is_featured').checked = true;
    openAdminModal('serviceModal');
}

function openEditServiceModal(data) {
    document.getElementById('serviceModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Service';
    document.getElementById('service_id').value = data.id || '';
    document.getElementById('service_name').value = data.name || '';
    document.getElementById('service_icon').value = data.icon || 'fas fa-print';
    document.getElementById('service_slug').value = data.slug || '';
    document.getElementById('service_short_desc').value = data.short_desc || '';
    document.getElementById('service_full_desc').value = data.full_desc || '';
    document.getElementById('service_main_image').value = data.main_image || '';
    document.getElementById('service_video_url').value = data.video_url || '';
    document.getElementById('service_status').value = data.status || 'published';
    document.getElementById('service_is_featured').checked = (data.is_featured == 1);
    openAdminModal('serviceModal');
}
</script>
