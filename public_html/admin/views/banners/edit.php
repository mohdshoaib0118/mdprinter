<?php
/**
 * MD Printers Admin - Create / Edit Hero Banner Slide View
 */

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$banner = [
    'id'                 => 0,
    'badge'              => '🔥 BEST SELLER • EXECUTIVE VISITING CARDS',
    'title'              => '',
    'subtitle'           => '',
    'cta_primary_text'   => 'Get Quote',
    'cta_primary_link'   => SITE_URL . '/request-quote',
    'cta_secondary_text' => 'Call: 81461 41450',
    'cta_secondary_link' => 'tel:8146141450',
    'bg_gradient'        => 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))',
    'image'              => '',
    'video_url'          => '',
    'sort_order'         => 0,
    'status'             => 'published'
];

$error = '';
$isEdit = false;

if ($id > 0) {
    $existing = Banner::getById($id);
    if ($existing) {
        $banner = array_merge($banner, $existing);
        $isEdit = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF verification failed. Please try submitting again.';
    } else {
        $badge = trim($_POST['badge'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');

        if (empty($title) || empty($badge)) {
            $error = 'Title and Top Badge text are required.';
        } else {
            $imagePath = $banner['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = upload_file($_FILES['image'], 'banners');
                if (isset($upload['filepath'])) {
                    $imagePath = $upload['filepath'];
                }
            }

            $videoUrl = trim($_POST['video_url'] ?? '');
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                $videoUpload = upload_file($_FILES['video_file'], 'banners');
                if (isset($videoUpload['filepath'])) {
                    $videoUrl = $videoUpload['filepath'];
                }
            }

            $saveData = [
                'badge'              => $badge,
                'title'              => $title,
                'subtitle'           => $subtitle,
                'cta_primary_text'   => trim($_POST['cta_primary_text'] ?? 'Get Quote'),
                'cta_primary_link'   => trim($_POST['cta_primary_link'] ?? SITE_URL . '/request-quote'),
                'cta_secondary_text' => trim($_POST['cta_secondary_text'] ?? 'Call Us'),
                'cta_secondary_link' => trim($_POST['cta_secondary_link'] ?? 'tel:8146141450'),
                'bg_gradient'        => trim($_POST['bg_gradient'] ?? 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))'),
                'image'              => $imagePath,
                'video_url'          => $videoUrl,
                'sort_order'         => (int)($_POST['sort_order'] ?? 0),
                'status'             => $_POST['status'] ?? 'published'
            ];

            if ($isEdit) {
                $saveData['id'] = $id;
            }

            if (Banner::save($saveData)) {
                set_flash('success', $isEdit ? 'Hero banner slide updated successfully.' : 'New hero banner slide created successfully.');
                redirect_to(ADMIN_URL . '?view=banners');
            } else {
                $error = 'Database error. Could not save banner slide.';
            }
        }
    }
}
?>

<div style="margin-bottom: 24px;">
    <a href="<?= ADMIN_URL ?>?view=banners" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #334155; font-weight: 700;">
        <i class="fas fa-arrow-left"></i> Back to Banners List
    </a>
</div>

<?php if ($error): ?>
    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; padding: 14px 20px; border-radius: 10px; margin-bottom: 24px; font-weight: 700;">
        <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
    </div>
<?php endif; ?>

<div class="admin-table-card" style="padding: 32px; max-width: 900px; background: #ffffff;">
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px;">
        <i class="fas fa-images" style="color: var(--cmyk-cyan);"></i> <?= $isEdit ? 'Edit Hero Banner Slide #' . $id : 'Add New Hero Banner Slide' ?>
    </h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Top Badge Text (Highlights / Offers) *
            </label>
            <input type="text" name="badge" value="<?= e($banner['badge']) ?>" class="form-control" required 
                   placeholder="e.g. 🔥 BEST SELLER • EXECUTIVE VISITING CARDS">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Slide Headline / Title *
            </label>
            <input type="text" name="title" value="<?= e($banner['title']) ?>" class="form-control" required 
                   placeholder="e.g. Executive Gold Foil Business Cards">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Subtitle / Offer Description
            </label>
            <textarea name="subtitle" rows="3" class="form-control" 
                      placeholder="Brief description of the printing product or special offer..."><?= e($banner['subtitle']) ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Primary CTA Button Text
                </label>
                <input type="text" name="cta_primary_text" value="<?= e($banner['cta_primary_text']) ?>" class="form-control" placeholder="Order Visiting Cards">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Primary CTA Link URL
                </label>
                <input type="text" name="cta_primary_link" value="<?= e($banner['cta_primary_link']) ?>" class="form-control" placeholder="<?= SITE_URL ?>/services/visiting-cards">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Secondary CTA Button Text
                </label>
                <input type="text" name="cta_secondary_text" value="<?= e($banner['cta_secondary_text']) ?>" class="form-control" placeholder="Call: 81461 41450">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Secondary CTA Link URL
                </label>
                <input type="text" name="cta_secondary_link" value="<?= e($banner['cta_secondary_link']) ?>" class="form-control" placeholder="tel:8146141450">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Banner Slide Graphic / Poster Image
            </label>
            <?php if (!empty($banner['image'])): ?>
                <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 14px; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <img src="<?= SITE_URL ?>/<?= e($banner['image']) ?>" alt="Preview" style="height: 60px; border-radius: 6px; object-fit: cover;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">Current image: <code><?= e($banner['image']) ?></code></span>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" class="form-control">
            <small style="color: #64748b; margin-top: 4px; display: block;">Recommended dimensions: 1200x800px high quality PNG or JPG.</small>
        </div>

        <div class="form-group" style="margin-bottom: 24px; background: #f0fdf4; border: 1px dashed #86efac; padding: 20px; border-radius: 12px;">
            <label style="display: block; font-size: 0.95rem; font-weight: 800; color: #166534; margin-bottom: 6px;">
                <i class="fas fa-video" style="color: #16a34a;"></i> Optional Slide Video (MP4 / WebM / YouTube Embed URL)
            </label>
            <p style="font-size: 0.84rem; color: #15803d; margin-bottom: 14px;">
                Attach a promotional video or printing reel. If configured, a sleek auto-playing background video or interactive play video frame will be rendered on the hero slider.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #166534; margin-bottom: 4px;">Video Direct Link / External URL</label>
                    <input type="text" name="video_url" value="<?= e($banner['video_url'] ?? '') ?>" class="form-control" placeholder="https://.../promo.mp4 or YouTube link">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #166534; margin-bottom: 4px;">OR Upload MP4 Video File</label>
                    <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" class="form-control">
                </div>
            </div>

            <?php if (!empty($banner['video_url'])): ?>
                <div style="margin-top: 14px; font-size: 0.85rem; color: #166534; font-weight: 700;">
                    <i class="fas fa-check-circle"></i> Current Video Attached: <code><?= e($banner['video_url']) ?></code>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Display Sort Order (Number)
                </label>
                <input type="number" name="sort_order" value="<?= (int)$banner['sort_order'] ?>" class="form-control" min="1">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Status
                </label>
                <select name="status" class="form-control">
                    <option value="published" <?= $banner['status'] === 'published' ? 'selected' : '' ?>>Published (Visible on Home Page)</option>
                    <option value="draft" <?= $banner['status'] === 'draft' ? 'selected' : '' ?>>Draft (Hidden)</option>
                </select>
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <button type="submit" class="btn btn-primary btn-lg" style="font-weight: 900; padding: 12px 28px;">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Update Banner Slide' : 'Save & Publish Slide' ?>
            </button>
        </div>
    </form>
</div>
