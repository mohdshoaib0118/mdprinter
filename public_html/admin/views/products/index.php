<?php
/**
 * MD Printers Admin - Products Manager View (Modal Popup Form)
 */

// Handle POST Save / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        $productData = [
            'id'           => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'category_id'  => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 1,
            'name'         => $name,
            'slug'         => !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($name),
            'short_desc'   => trim($_POST['short_desc'] ?? ''),
            'full_desc'    => trim($_POST['full_desc'] ?? ''),
            'main_image'   => trim($_POST['main_image'] ?? ''),
            'video_url'    => trim($_POST['video_url'] ?? ''),
            'min_quantity' => (int)($_POST['min_quantity'] ?? 100),
            'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
            'status'       => $_POST['status'] ?? 'published',
            'meta_title'   => trim($_POST['meta_title'] ?? ''),
            'meta_desc'    => trim($_POST['meta_desc'] ?? '')
        ];

        // If updating and no image text supplied, retain existing image
        if (!empty($productData['id']) && empty($productData['main_image'])) {
            $existing = Product::getById($productData['id']);
            if ($existing && !empty($existing['main_image'])) {
                $productData['main_image'] = $existing['main_image'];
            }
        }

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_file($_FILES['image_file'], 'products');
            if (isset($upload['filepath'])) {
                $productData['main_image'] = $upload['filepath'];
            }
        }

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $videoUpload = upload_file($_FILES['video_file'], 'products');
            if (isset($videoUpload['filepath'])) {
                $productData['video_url'] = $videoUpload['filepath'];
            }
        }

        if (Product::save($productData)) {
            set_flash('success', !empty($productData['id']) ? 'Product updated successfully.' : 'New Product added to catalogue!');
        } else {
            set_flash('danger', 'Failed to save product.');
        }
    }
    redirect_to(ADMIN_URL . '?view=products');
}

if ($action === 'delete' && $id > 0) {
    if (verify_csrf($_GET['csrf_token'] ?? '')) {
        Product::delete($id);
        set_flash('success', 'Product deleted successfully.');
    }
    redirect_to(ADMIN_URL . '?view=products');
}

$searchQuery = trim($_GET['search'] ?? '');
$allProducts = Product::getAll(['search' => $searchQuery], 200);

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$totalProducts = count($allProducts);
$totalPages = max(1, (int)ceil($totalProducts / $perPage));
$products = array_slice($allProducts, ($page - 1) * $perPage, $perPage);

$categories = Product::getCategories();
$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            Print Products Catalogue
            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">Total: <?= $totalProducts ?></span>
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Add and edit catalogue items using Modal Popups.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openAddProductModal()" style="font-weight: 800; cursor: pointer;">
        <i class="fas fa-plus"></i> Add New Product
    </button>
</div>

<!-- Search Filter Bar -->
<form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px; background: #ffffff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <input type="hidden" name="view" value="products">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search product name, category, or description..." class="form-control" style="padding-left: 38px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;"><i class="fas fa-filter"></i> Search</button>
    <?php if (!empty($searchQuery)): ?>
        <a href="<?= ADMIN_URL ?>?view=products" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #64748b;">Clear</a>
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
                <th>Product Name & Slug</th>
                <th>Category</th>
                <th>Min Qty</th>
                <th>Featured</th>
                <th>Status</th>
                <th style="text-align: right; width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #64748b;">No products found matching search.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $idx => $p): ?>
                    <?php $srNo = ($page - 1) * $perPage + $idx + 1; ?>
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #94a3b8; font-size: 0.85rem;"><?= $srNo ?></td>
                        <td>
                            <img src="<?= media_url($p['main_image']) ?>" alt="" 
                                 onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" 
                                 style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </td>
                        <td>
                            <strong style="font-size: 0.95rem; color: #0f172a; display: block;"><?= e($p['name']) ?></strong>
                            <small style="color: #64748b;"><?= e($p['slug']) ?></small>
                        </td>
                        <td><span style="font-weight: 700; color: #334155;"><?= e($p['category_name'] ?? 'General') ?></span></td>
                        <td><strong><?= (int)$p['min_quantity'] ?></strong> pcs</td>
                        <td><?= $p['is_featured'] ? '<span style="color: var(--cmyk-gold); font-weight: 800;"><i class="fas fa-star"></i> Featured</span>' : '<span style="color: #94a3b8;">No</span>' ?></td>
                        <td><span class="badge-status badge-<?= e($p['status']) ?>"><?= ucfirst(e($p['status'])) ?></span></td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <button type="button" onclick='openEditProductModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                        class="btn btn-outline btn-sm" style="padding: 6px 12px; border-color: #cbd5e1; color: #334155; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="<?= ADMIN_URL ?>?view=products&action=delete&id=<?= $p['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>" 
                                   onclick="return confirmAdminDelete(event, this.href, 'Are you sure you want to delete this catalogue product?');" 
                                   class="btn btn-sm" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;" title="Delete Product">
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
<?= render_pagination($page, $totalPages, ADMIN_URL . '?view=products' . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '')) ?>

<!-- Product Modal Popup -->
<div id="productModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="productModalTitle" class="admin-modal-title"><i class="fas fa-box"></i> Add New Product</h3>
                <button type="button" class="admin-modal-close" data-close-modal="productModal">&times;</button>
            </div>
            <div class="admin-modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_product" value="1">
                    <input type="hidden" name="id" id="product_id" value="">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" id="product_name" required placeholder="Executive Gold Foil Cards">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="product_category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SEO Slug (Auto-generated if empty)</label>
                        <input type="text" name="slug" id="product_slug" placeholder="gold-foil-cards">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Short Summary *</label>
                        <textarea name="short_desc" id="product_short_desc" rows="2" required placeholder="Luxury 400 GSM velvet touch business cards..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Full Product Description</label>
                        <textarea name="full_desc" id="product_full_desc" rows="4" placeholder="Detailed product specification paragraph..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Main Image Path</label>
                            <input type="text" name="main_image" id="product_main_image" value="" placeholder="Upload file below or enter path">
                            <input type="file" name="image_file" accept="image/*" style="margin-top: 6px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Minimum Order Qty</label>
                            <input type="number" name="min_quantity" id="product_min_quantity" value="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Video Media URL or Path (Optional MP4 / WebM / YouTube / Vimeo)</label>
                        <input type="text" name="video_url" id="product_video_url" placeholder="https://example.com/video.mp4 or YouTube link">
                        <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Or upload MP4/WebM product reel video below:</small>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" style="margin-top: 6px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" id="product_status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 28px;">
                            <label style="font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" name="is_featured" id="product_is_featured" value="1" style="width: 18px; height: 18px;">
                                Feature on Homepage
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="button" class="btn btn-outline" data-close-modal="productModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 800;"><i class="fas fa-save"></i> Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddProductModal() {
    document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-box"></i> Add New Product';
    document.getElementById('product_id').value = '';
    document.getElementById('product_name').value = '';
    document.getElementById('product_slug').value = '';
    document.getElementById('product_short_desc').value = '';
    document.getElementById('product_full_desc').value = '';
    document.getElementById('product_main_image').value = '';
    document.getElementById('product_video_url').value = '';
    document.getElementById('product_min_quantity').value = '100';
    document.getElementById('product_status').value = 'published';
    document.getElementById('product_is_featured').checked = false;
    openAdminModal('productModal');
}

function openEditProductModal(data) {
    document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Product';
    document.getElementById('product_id').value = data.id || '';
    document.getElementById('product_name').value = data.name || '';
    document.getElementById('product_slug').value = data.slug || '';
    document.getElementById('product_category_id').value = data.category_id || 1;
    document.getElementById('product_short_desc').value = data.short_desc || '';
    document.getElementById('product_full_desc').value = data.full_desc || '';
    document.getElementById('product_main_image').value = data.main_image || '';
    document.getElementById('product_video_url').value = data.video_url || '';
    document.getElementById('product_min_quantity').value = data.min_quantity || 100;
    document.getElementById('product_status').value = data.status || 'published';
    document.getElementById('product_is_featured').checked = (data.is_featured == 1);
    openAdminModal('productModal');
}
</script>
