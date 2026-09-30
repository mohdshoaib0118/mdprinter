<?php
/**
 * MD Printers Admin - Hero Banners Manager View (Modal Popup Form)
 */

// Handle POST Save / Update / Toggle Status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        if (isset($_POST['save_banner'])) {
            $bannerData = [
                'id'                 => !empty($_POST['id']) ? (int)$_POST['id'] : null,
                'badge'              => trim($_POST['badge'] ?? ''),
                'title'              => trim($_POST['title'] ?? ''),
                'subtitle'           => trim($_POST['subtitle'] ?? ''),
                'cta_primary_text'   => trim($_POST['cta_primary_text'] ?? 'Get Quote'),
                'cta_primary_link'   => trim($_POST['cta_primary_link'] ?? SITE_URL . '/request-quote'),
                'cta_secondary_text' => trim($_POST['cta_secondary_text'] ?? ''),
                'cta_secondary_link' => trim($_POST['cta_secondary_link'] ?? ''),
                'bg_gradient'        => trim($_POST['bg_gradient'] ?? 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))'),
                'image'              => trim($_POST['image'] ?? ''),
                'video_url'          => trim($_POST['video_url'] ?? ''),
                'sort_order'         => (int)($_POST['sort_order'] ?? 0),
                'status'             => $_POST['status'] ?? 'published'
            ];

            // If updating and no image text supplied, retain existing image
            if (!empty($bannerData['id']) && empty($bannerData['image'])) {
                $existing = Banner::getById($bannerData['id']);
                if ($existing && !empty($existing['image'])) {
                    $bannerData['image'] = $existing['image'];
                }
            }

            // Handle image upload if provided
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $upload = upload_file($_FILES['image_file'], 'banners');
                if (isset($upload['filepath'])) {
                    $bannerData['image'] = $upload['filepath'];
                } elseif (isset($upload['error'])) {
                    set_flash('danger', 'Image upload failed: ' . $upload['error']);
                    redirect_to(ADMIN_URL . '?view=banners');
                }
            }

            // Handle video file upload if provided
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                $videoUpload = upload_file($_FILES['video_file'], 'banners');
                if (isset($videoUpload['filepath'])) {
                    $bannerData['video_url'] = $videoUpload['filepath'];
                } elseif (isset($videoUpload['error'])) {
                    set_flash('danger', 'Video upload failed: ' . $videoUpload['error']);
                    redirect_to(ADMIN_URL . '?view=banners');
                }
            }

            if (Banner::save($bannerData)) {
                set_flash('success', !empty($bannerData['id']) ? 'Banner updated successfully.' : 'New Hero Banner slide created!');
            } else {
                set_flash('danger', 'Failed to save banner slide.');
            }
            redirect_to(ADMIN_URL . '?view=banners');
        } elseif (isset($_POST['toggle_status_id'])) {
            $bannerId = (int)$_POST['toggle_status_id'];
            $currentBanner = Banner::getById($bannerId);
            if ($currentBanner) {
                $currentBanner['status'] = ($currentBanner['status'] === 'published') ? 'draft' : 'published';
                Banner::save($currentBanner);
                set_flash('success', 'Banner status toggled to ' . ucfirst($currentBanner['status']) . '.');
            }
            redirect_to(ADMIN_URL . '?view=banners');
        }
    }
}

$searchQuery = trim($_GET['search'] ?? '');
$allBanners = Banner::getAll();
if (!empty($searchQuery)) {
    $allBanners = array_filter($allBanners, function($b) use ($searchQuery) {
        $haystack = strtolower(($b['title'] ?? '') . ' ' . ($b['badge'] ?? '') . ' ' . ($b['subtitle'] ?? ''));
        return strpos($haystack, strtolower($searchQuery)) !== false;
    });
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalBanners = count($allBanners);
$totalPages = max(1, (int)ceil($totalBanners / $perPage));
$banners = array_slice($allBanners, ($page - 1) * $perPage, $perPage);

$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            Hero Banner Sliders
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalBanners ?></span>
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Manage hero slider images, video URLs, badges, and CTAs in a Popup Modal.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddBannerModal()" style="font-weight: 800; cursor: pointer;">
        <i class="fas fa-plus"></i> Add New Slide
    </button>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="banners">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search banner title, badge, or subtitle..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=banners" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
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
                <th style="width: 100px;">Preview</th>
                <th>Badge & Slide Title</th>
                <th>Call To Action</th>
                <th style="width: 80px; text-align: center;">Order</th>
                <th style="width: 110px;">Status</th>
                <th style="text-align: right; width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($banners)): ?>
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #64748b;">
                        <i class="fas fa-images" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                        No banner slides found. Click <strong>"Add New Slide"</strong> to open the modal popup form.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($banners as $idx => $b): ?>
                    <?php $srNo = ($page - 1) * $perPage + $idx + 1; ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td>
                            <div style="position: relative; width: 80px; height: 52px;">
                                <img src="<?= media_url($b['image']) ?>" alt="<?= e($b['title']) ?>" 
                                     style="width: 80px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc;"
                                     onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';">
                                <?php if (!empty($b['video_url'])): ?>
                                    <span style="position: absolute; bottom: 4px; right: 4px; background: rgba(0,0,0,0.75); color: #22c55e; font-size: 0.65rem; font-weight: 800; padding: 2px 5px; border-radius: 4px;" title="Includes Video">
                                        <i class="fas fa-play"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.75rem; color: var(--cmyk-cyan); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">
                                <?= e($b['badge']) ?>
                            </div>
                            <strong style="font-size: 0.95rem; color: #0f172a; display: block; margin-bottom: 2px;"><?= e($b['title']) ?></strong>
                            <?php if (!empty($b['subtitle'])): ?>
                                <small style="color: #64748b; font-size: 0.82rem; display: block; max-width: 450px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= e($b['subtitle']) ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 2px;">
                                <i class="fas fa-link" style="color: var(--cmyk-magenta); font-size: 0.75rem;"></i> <?= e($b['cta_primary_text']) ?>
                            </div>
                            <?php if (!empty($b['cta_secondary_text'])): ?>
                                <div style="font-size: 0.78rem; color: #64748b;">
                                    <i class="fas fa-link" style="font-size: 0.7rem;"></i> <?= e($b['cta_secondary_text']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-weight: 700; color: #475569;">
                            <?= (int)$b['sort_order'] ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $b['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= ucfirst($b['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <button type="button" onclick='openEditBannerModal(<?= json_encode($b) ?>)' class="btn btn-sm btn-outline" style="padding: 6px 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Edit Banner">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="<?= ADMIN_URL ?>?view=banners&action=delete&id=<?= $b['id'] ?>&csrf_token=<?= csrf_token() ?>" 
                                   onclick="return confirmAdminDelete(event, this.href, 'Are you sure you want to delete this hero banner slide?');" 
                                   class="btn btn-sm" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Delete Banner">
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
<?= render_pagination($page, $totalPages, ADMIN_URL . '?view=banners' . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '')) ?>

<!-- Banner Add/Edit Modal Popup -->
<div id="bannerModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="bannerModalTitle" class="admin-modal-title"><i class="fas fa-images"></i> Add New Hero Banner Slide</h3>
                <button type="button" class="admin-modal-close" data-close-modal="bannerModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_banner" value="1">
                    <input type="hidden" name="id" id="banner_id" value="">

                    <div class="form-group">
                        <label class="form-label">Badge Text *</label>
                        <input type="text" name="badge" id="banner_badge" required placeholder="🔥 BEST SELLER • EXECUTIVE VISITING CARDS">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Banner Title *</label>
                        <input type="text" name="title" id="banner_title" required placeholder="Executive Gold Foil Business Cards">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subtitle Description *</label>
                        <textarea name="subtitle" id="banner_subtitle" rows="3" required placeholder="Premium 400 GSM Silk Artboard coated with Velvet Soft-Touch..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Primary CTA Label</label>
                            <input type="text" name="cta_primary_text" id="banner_cta_primary_text" value="Get Quote">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Primary CTA Link</label>
                            <input type="text" name="cta_primary_link" id="banner_cta_primary_link" value="<?= SITE_URL ?>/request-quote">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Secondary CTA Label</label>
                            <input type="text" name="cta_secondary_text" id="banner_cta_secondary_text" value="Call Us">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Secondary CTA Link</label>
                            <input type="text" name="cta_secondary_link" id="banner_cta_secondary_link" value="tel:8146141450">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Background Media Image Path</label>
                        <input type="text" name="image" id="banner_image" value="" placeholder="Upload file below or enter image path">
                        <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Or upload new image below:</small>
                        <input type="file" name="image_file" accept="image/*" style="margin-top: 6px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Video Media URL or Path (Optional MP4 / WebM / YouTube / Vimeo)</label>
                        <input type="text" name="video_url" id="banner_video_url" placeholder="https://example.com/video.mp4 or YouTube link">
                        <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Or upload MP4/WebM video file below:</small>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" style="margin-top: 6px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="sort_order" id="banner_sort_order" value="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" id="banner_status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" data-close-modal="bannerModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save Banner Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddBannerModal() {
    document.getElementById('bannerModalTitle').innerHTML = '<i class="fas fa-images"></i> Add New Hero Banner Slide';
    document.getElementById('banner_id').value = '';
    document.getElementById('banner_badge').value = '';
    document.getElementById('banner_title').value = '';
    document.getElementById('banner_subtitle').value = '';
    document.getElementById('banner_cta_primary_text').value = 'Get Quote';
    document.getElementById('banner_cta_primary_link').value = '<?= SITE_URL ?>/request-quote';
    document.getElementById('banner_cta_secondary_text').value = 'Call Us';
    document.getElementById('banner_cta_secondary_link').value = 'tel:8146141450';
    document.getElementById('banner_image').value = '';
    document.getElementById('banner_video_url').value = '';
    document.getElementById('banner_sort_order').value = '0';
    document.getElementById('banner_status').value = 'published';
    openAdminModal('bannerModal');
}

function openEditBannerModal(data) {
    document.getElementById('bannerModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Hero Banner Slide';
    document.getElementById('banner_id').value = data.id || '';
    document.getElementById('banner_badge').value = data.badge || '';
    document.getElementById('banner_title').value = data.title || '';
    document.getElementById('banner_subtitle').value = data.subtitle || '';
    document.getElementById('banner_cta_primary_text').value = data.cta_primary_text || 'Get Quote';
    document.getElementById('banner_cta_primary_link').value = data.cta_primary_link || '';
    document.getElementById('banner_cta_secondary_text').value = data.cta_secondary_text || '';
    document.getElementById('banner_cta_secondary_link').value = data.cta_secondary_link || '';
    document.getElementById('banner_image').value = data.image || '';
    document.getElementById('banner_video_url').value = data.video_url || '';
    document.getElementById('banner_sort_order').value = (data.sort_order !== undefined && data.sort_order !== null) ? data.sort_order : 0;
    document.getElementById('banner_status').value = data.status || 'published';
    openAdminModal('bannerModal');
}
</script>
