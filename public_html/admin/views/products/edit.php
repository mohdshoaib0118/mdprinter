<?php
$product = [
    'id' => 0,
    'name' => '',
    'slug' => '',
    'category_id' => 1,
    'short_desc' => '',
    'full_desc' => '',
    'main_image' => 'assets/images/products/gold-foil-card.jpg',
    'min_quantity' => 100,
    'is_featured' => 0,
    'status' => 'published',
    'meta_title' => '',
    'meta_desc' => ''
];

if ($id > 0) {
    $existing = Product::getBySlug($_GET['slug'] ?? '');
    if (!$existing) {
        $all = Product::getAll([], 100);
        foreach ($all as $item) {
            if ((int)$item['id'] === $id) {
                $existing = $item;
                break;
            }
        }
    }
    if ($existing) $product = array_merge($product, $existing);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['id'] = $id;
    if (empty($_POST['slug'])) $_POST['slug'] = slugify($_POST['name']);

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $up = upload_file($_FILES['image_file'], 'products');
        if (isset($up['filepath'])) {
            $_POST['main_image'] = $up['filepath'];
        }
    }

    if (Product::save($_POST)) {
        set_flash('success', 'Product saved successfully.');
        header('Location: ' . ADMIN_URL . '?view=products');
        exit;
    }
}
$categories = Product::getCategories();
?>

<div style="margin-bottom: 24px;">
    <a href="<?= ADMIN_URL ?>?view=products" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Products List</a>
</div>

<div class="admin-table-card" style="padding: 30px; max-width: 900px;">
    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; border-bottom: 1px solid var(--admin-border); padding-bottom: 12px;">
        <?= $id > 0 ? 'Edit Product' : 'Add New Product' ?>
    </h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" value="<?= e($product['name']) ?>" class="form-control" required placeholder="e.g. Executive Foil Business Cards">
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Min. Order Quantity</label>
                <input type="number" name="min_quantity" value="<?= e($product['min_quantity']) ?>" class="form-control" min="1">
            </div>

            <div class="form-group">
                <label class="form-label">Featured on Homepage?</label>
                <select name="is_featured" class="form-control">
                    <option value="1" <?= $product['is_featured'] ? 'selected' : '' ?>>Yes - Show on Homepage</option>
                    <option value="0" <?= !$product['is_featured'] ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Short Description</label>
            <textarea name="short_desc" rows="2" class="form-control" required><?= e($product['short_desc']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Full Specifications & Description (HTML)</label>
            <textarea name="full_desc" rows="6" class="form-control"><?= e($product['full_desc']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Product Image File</label>
            <input type="file" name="image_file" class="form-control">
            <input type="hidden" name="main_image" value="<?= e($product['main_image']) ?>">
            <small style="color: var(--text-muted);">Current: <?= e($product['main_image']) ?></small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 20px;"><i class="fas fa-save"></i> Save Product</button>
    </form>
</div>
