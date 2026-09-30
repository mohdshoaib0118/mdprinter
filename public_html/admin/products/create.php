<?php
/**
 * MD Printers - Add New Product Form
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$categories = $db->fetchAll("SELECT * FROM product_categories ORDER BY name ASC");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Try again.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $short_desc = sanitizeInput($_POST['short_desc'] ?? '');
        $full_desc = sanitizeInput($_POST['full_desc'] ?? '');
        $materials = sanitizeInput($_POST['materials'] ?? '');
        $sizes = sanitizeInput($_POST['sizes'] ?? '');
        $finishes = sanitizeInput($_POST['finishes'] ?? '');
        $min_quantity = (int)($_POST['min_quantity'] ?? 100);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $slug = slugify($name);

        $main_image = 'assets/images/products/business-cards.jpg';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = handle_file_upload($_FILES['image'], 'products');
            if ($up['success']) {
                $main_image = $up['file_path'];
            }
        }

        if (empty($name) || empty($category_id)) {
            $error = 'Product name and category are required.';
        } else {
            $db->insert('products', [
                'category_id' => $category_id,
                'name' => $name,
                'slug' => $slug,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'materials' => $materials,
                'sizes' => $sizes,
                'finishes' => $finishes,
                'min_quantity' => $min_quantity,
                'main_image' => $main_image,
                'is_featured' => $is_featured,
                'is_active' => $is_active
            ]);

            header('Location: ' . ADMIN_URL . '/products/index.php');
            exit;
        }
    }
}

$adminTitle = 'Add New Product';
$adminActivePage = 'products';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card" style="max-width: 800px;">
    <div class="admin-card-header">
        <h2>Add New Product</h2>
        <a href="<?= ADMIN_URL ?>/products/index.php" class="btn-admin btn-admin-outline"><i class="fas fa-arrow-left"></i> Cancel</a>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert-error" style="margin-bottom: 20px;"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Luxury Velvet Business Cards">
        </div>

        <div class="form-group">
            <label>Category *</label>
            <select name="category_id" class="form-control" required>
                <option value="">Select Category...</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Short Summary</label>
            <input type="text" name="short_desc" class="form-control" placeholder="Brief 1-sentence product summary">
        </div>

        <div class="form-group">
            <label>Full Product Description & Specifications</label>
            <textarea name="full_desc" class="form-control" rows="5" placeholder="Enter detailed specs..."></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Materials / Paper Stocks</label>
                <input type="text" name="materials" class="form-control" placeholder="e.g. 400 GSM Art Board, Kraft">
            </div>
            <div class="form-group">
                <label>Available Sizes</label>
                <input type="text" name="sizes" class="form-control" placeholder="e.g. 3.5 x 2.0 in, Custom Sizes">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Finishing Options</label>
                <input type="text" name="finishes" class="form-control" placeholder="e.g. Gold Foil, Velvet Matte, Spot UV">
            </div>
            <div class="form-group">
                <label>Minimum Quantity</label>
                <input type="number" name="min_quantity" class="form-control" value="100" min="1">
            </div>
        </div>

        <div class="form-group">
            <label>Product Main Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 24px;">
            <label><input type="checkbox" name="is_featured" value="1" checked> Featured on Homepage</label>
            <label><input type="checkbox" name="is_active" value="1" checked> Active / Published</label>
        </div>

        <button type="submit" class="btn-admin btn-admin-primary" style="padding: 12px 24px;">
            <i class="fas fa-save"></i> Save Product
        </button>
    </form>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
