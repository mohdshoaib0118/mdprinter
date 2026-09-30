<?php
/**
 * MD Printers Admin - Portfolio Manager View (Modal Popup Form)
 */

// Handle POST Save / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_portfolio'])) {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $title = trim($_POST['title'] ?? '');
        $portfolioData = [
            'id'            => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'category_id'   => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 1,
            'title'         => $title,
            'slug'          => !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($title),
            'client_name'   => trim($_POST['client_name'] ?? ''),
            'project_date'  => trim($_POST['project_date'] ?? ''),
            'description'   => trim($_POST['description'] ?? ''),
            'image'         => trim($_POST['image'] ?? ''),
            'video_url'     => trim($_POST['video_url'] ?? ''),
            'services_used' => trim($_POST['services_used'] ?? ''),
            'status'        => $_POST['status'] ?? 'published'
        ];

        // If updating and no image text supplied, retain existing image
        if (!empty($portfolioData['id']) && empty($portfolioData['image'])) {
            $existing = Portfolio::getById($portfolioData['id']);
            if ($existing && !empty($existing['image'])) {
                $portfolioData['image'] = $existing['image'];
            }
        }

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_file($_FILES['image_file'], 'portfolio');
            if (isset($upload['filepath'])) {
                $portfolioData['image'] = $upload['filepath'];
            }
        }

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $videoUpload = upload_file($_FILES['video_file'], 'portfolio');
            if (isset($videoUpload['filepath'])) {
                $portfolioData['video_url'] = $videoUpload['filepath'];
            }
        }

        if (Portfolio::save($portfolioData)) {
            set_flash('success', !empty($portfolioData['id']) ? 'Portfolio project updated.' : 'New Portfolio Case Study added!');
        } else {
            set_flash('danger', 'Failed to save portfolio project.');
        }
    }
    redirect_to(ADMIN_URL . '?view=portfolio');
}

if ($action === 'delete' && $id > 0) {
    if (verify_csrf($_GET['csrf_token'] ?? '')) {
        Portfolio::delete($id);
        set_flash('success', 'Portfolio project deleted.');
    }
    redirect_to(ADMIN_URL . '?view=portfolio');
}

$searchQuery = trim($_GET['search'] ?? '');
$allPortfolio = Portfolio::getAll();
if (!empty($searchQuery)) {
    $allPortfolio = array_filter($allPortfolio, function($item) use ($searchQuery) {
        $haystack = strtolower(($item['title'] ?? '') . ' ' . ($item['client_name'] ?? '') . ' ' . ($item['category_name'] ?? '') . ' ' . ($item['description'] ?? ''));
        return strpos($haystack, strtolower($searchQuery)) !== false;
    });
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalPortfolio = count($allPortfolio);
$totalPages = max(1, (int)ceil($totalPortfolio / $perPage));
$portfolio = array_slice($allPortfolio, ($page - 1) * $perPage, $perPage);

$categories = Portfolio::getCategories();
$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            Portfolio Case Studies
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalPortfolio ?></span>
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Manage client case studies and showcased projects in a Modal Popup.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddPortfolioModal()" style="font-weight: 800; cursor: pointer;">
        <i class="fas fa-plus"></i> Add Project
    </button>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="portfolio">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search project title, client name, or category..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=portfolio" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
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
                <th style="width: 70px;">Image</th>
                <th>Project Title & Slug</th>
                <th>Client Name</th>
                <th>Category</th>
                <th>Status</th>
                <th style="text-align: right; width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($portfolio)): ?>
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #64748b;">No portfolio projects found matching search.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($portfolio as $idx => $item): ?>
                    <?php $srNo = ($page - 1) * $perPage + $idx + 1; ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td>
                            <img src="<?= media_url($item['image']) ?>" alt="" 
                                 onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" 
                                 style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </td>
                        <td>
                            <strong style="font-size: 0.95rem; color: #0f172a; display: block;"><?= e($item['title']) ?></strong>
                            <small style="color: #64748b; font-size: 0.8rem;"><?= e($item['slug']) ?></small>
                        </td>
                        <td><span style="font-size: 0.88rem; color: #334155; font-weight: 600;"><?= e($item['client_name']) ?></span></td>
                        <td><span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700;"><?= e($item['category_name'] ?? 'General') ?></span></td>
                        <td>
                            <span class="badge badge-<?= $item['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <button type="button" onclick='openEditPortfolioModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                        class="btn btn-outline btn-sm" style="padding: 6px 12px; border-color: #cbd5e1; color: #334155; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" title="Edit Project">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="<?= ADMIN_URL ?>?view=portfolio&action=delete&id=<?= $item['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>" 
                                   onclick="return confirmAdminDelete(event, this.href, 'Are you sure you want to delete this portfolio project case study?');" 
                                   class="btn btn-sm" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Delete Project">
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
<?= render_pagination($page, $totalPages, ADMIN_URL . '?view=portfolio' . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '')) ?>

<!-- Portfolio Modal Popup -->
<div id="portfolioModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="portfolioModalTitle" class="admin-modal-title"><i class="fas fa-briefcase"></i> Add Portfolio Project</h3>
                <button type="button" class="admin-modal-close" data-close-modal="portfolioModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_portfolio" value="1">
                    <input type="hidden" name="id" id="portfolio_id" value="">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Project Title *</label>
                            <input type="text" name="title" id="portfolio_title" required placeholder="Aura Skincare Rigid Box">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="portfolio_category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SEO Slug (Auto-generated if empty)</label>
                        <input type="text" name="slug" id="portfolio_slug" placeholder="aura-skincare-packaging">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Client Name</label>
                            <input type="text" name="client_name" id="portfolio_client_name" placeholder="Aura Botanicals">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Project Date / Delivery Year</label>
                            <input type="text" name="project_date" id="portfolio_project_date" placeholder="January 2026">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Project Summary / Description *</label>
                        <textarea name="description" id="portfolio_description" rows="3" required placeholder="Designed and produced 10,000 custom soft-touch rigid gift boxes..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Services Used (Comma separated)</label>
                        <input type="text" name="services_used" id="portfolio_services_used" placeholder="Rigid Box Packaging, Foil Stamping, Spot UV">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Main Project Image Path</label>
                            <input type="text" name="image" id="portfolio_image" value="" placeholder="Upload file below or enter path">
                            <input type="file" name="image_file" accept="image/*" style="margin-top: 6px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" id="portfolio_status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Video Media URL or Path (Optional MP4 / WebM / YouTube / Vimeo)</label>
                        <input type="text" name="video_url" id="portfolio_video_url" placeholder="https://example.com/video.mp4 or YouTube link">
                        <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Or upload MP4/WebM project showcase video below:</small>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" style="margin-top: 6px;">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" data-close-modal="portfolioModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddPortfolioModal() {
    document.getElementById('portfolioModalTitle').innerHTML = '<i class="fas fa-briefcase"></i> Add Portfolio Project';
    document.getElementById('portfolio_id').value = '';
    document.getElementById('portfolio_title').value = '';
    document.getElementById('portfolio_slug').value = '';
    document.getElementById('portfolio_client_name').value = '';
    document.getElementById('portfolio_project_date').value = '2026';
    document.getElementById('portfolio_description').value = '';
    document.getElementById('portfolio_services_used').value = '';
    document.getElementById('portfolio_image').value = '';
    document.getElementById('portfolio_video_url').value = '';
    document.getElementById('portfolio_status').value = 'published';
    openAdminModal('portfolioModal');
}

function openEditPortfolioModal(data) {
    document.getElementById('portfolioModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Portfolio Project';
    document.getElementById('portfolio_id').value = data.id || '';
    document.getElementById('portfolio_title').value = data.title || '';
    document.getElementById('portfolio_slug').value = data.slug || '';
    document.getElementById('portfolio_category_id').value = data.category_id || 1;
    document.getElementById('portfolio_client_name').value = data.client_name || '';
    document.getElementById('portfolio_project_date').value = data.project_date || '';
    document.getElementById('portfolio_description').value = data.description || '';
    document.getElementById('portfolio_services_used').value = data.services_used || '';
    document.getElementById('portfolio_image').value = data.image || '';
    document.getElementById('portfolio_video_url').value = data.video_url || '';
    document.getElementById('portfolio_status').value = data.status || 'published';
    openAdminModal('portfolioModal');
}
</script>
