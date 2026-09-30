<?php
/**
 * MD Printers - Admin Category Management View
 */
require_once MODELS_PATH . '/Category.php';
Category::initTables();

$message = '';
$messageType = 'success';

// Handle POST actions (Create / Edit / Toggle Status / Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type']) && $_POST['action_type'] === 'save_category') {
        $catData = [
            'id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'name' => trim($_POST['name'] ?? ''),
            'slug' => slugify($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'icon' => trim($_POST['icon'] ?? 'fa-print'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active'
        ];

        if (!empty($catData['name'])) {
            if (Category::save($catData)) {
                $message = 'Printing category saved successfully!';
            } else {
                $message = 'Failed to save printing category.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Category name is required.';
            $messageType = 'danger';
        }
    } elseif (isset($_POST['action_type']) && $_POST['action_type'] === 'delete_category') {
        $delId = (int)($_POST['category_id'] ?? 0);
        if ($delId > 0 && Category::delete($delId)) {
            $message = 'Category deleted successfully!';
        }
    }
}

$categories = Category::getAll();
$editCategory = null;
if (isset($_GET['edit_id'])) {
    $editCategory = Category::getById((int)$_GET['edit_id']);
}
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">Printing Categories Management</h1>
        <p style="color: #64748b; margin: 4px 0 0 0;">Manage top-level printing service categories for B2B ordering journey.</p>
    </div>
</div>

<?php if ($message): ?>
    <div style="padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 700; background: <?= $messageType === 'success' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $messageType === 'success' ? '#15803d' : '#991b1b' ?>; border: 1px solid <?= $messageType === 'success' ? '#bbf7d0' : '#fecaca' ?>;">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">

    <!-- Categories List Table -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 20px 0;">Categories List (<?= count($categories) ?>)</h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 12px 16px;">Icon</th>
                        <th style="padding: 12px 16px;">Category Name</th>
                        <th style="padding: 12px 16px;">Subcategories</th>
                        <th style="padding: 12px 16px;">Products</th>
                        <th style="padding: 12px 16px;">Status</th>
                        <th style="padding: 12px 16px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $c): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px;">
                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                        <i class="fas <?= e($c['icon'] ?: 'fa-print') ?>"></i>
                                    </div>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <strong style="color: #0f172a; display: block; font-size: 0.95rem;"><?= e($c['name']) ?></strong>
                                    <span style="font-size: 0.78rem; color: #64748b;">slug: <?= e($c['slug']) ?></span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="background: #f1f5f9; color: #334155; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 10px;">
                                        <?= (int)$c['group_count'] ?> Groups
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="background: #f1f5f9; color: #334155; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 10px;">
                                        <?= (int)$c['product_count'] ?> Products
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="background: <?= $c['status'] === 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $c['status'] === 'active' ? '#15803d' : '#991b1b' ?>; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                        <?= e($c['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    <a href="<?= ADMIN_URL ?>?view=categories&edit_id=<?= $c['id'] ?>" class="btn btn-sm btn-outline" style="font-weight: 700; border-radius: 8px;">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No categories found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create / Edit Form Card -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 20px 0;">
            <?= $editCategory ? 'Edit Category #' . $editCategory['id'] : 'Create New Category' ?>
        </h3>

        <form method="POST" action="<?= ADMIN_URL ?>?view=categories">
            <input type="hidden" name="action_type" value="save_category">
            <?php if ($editCategory): ?>
                <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?>">
            <?php endif; ?>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Category Name *</label>
                <input type="text" name="name" value="<?= e($editCategory['name'] ?? '') ?>" class="form-control" required style="border-radius: 8px; font-weight: 700;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">FontAwesome Icon Class</label>
                <input type="text" name="icon" value="<?= e($editCategory['icon'] ?? 'fa-print') ?>" class="form-control" placeholder="fa-address-card" style="border-radius: 8px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Description</label>
                <textarea name="description" class="form-control" rows="3" style="border-radius: 8px; font-size: 0.88rem;"><?= e($editCategory['description'] ?? '') ?></textarea>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Sort Order</label>
                <input type="number" name="sort_order" value="<?= (int)($editCategory['sort_order'] ?? 0) ?>" class="form-control" style="border-radius: 8px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Status</label>
                <select name="status" class="form-control" style="border-radius: 8px; font-weight: 700;">
                    <option value="active" <?= ($editCategory['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($editCategory['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; font-weight: 800; border-radius: 8px;">
                    <?= $editCategory ? 'Update Category' : 'Save Category' ?>
                </button>
                <?php if ($editCategory): ?>
                    <a href="<?= ADMIN_URL ?>?view=categories" class="btn btn-outline" style="border-radius: 8px; font-weight: 700;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

</div>
