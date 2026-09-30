<?php
/**
 * MD Printers - Admin Product Groups (Subcategories) Management View
 */
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/ProductGroup.php';

Category::initTables();
ProductGroup::initTables();

$message = '';
$messageType = 'success';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type']) && $_POST['action_type'] === 'save_group') {
        $grpData = [
            'id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => slugify($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'min_quantity' => (int)($_POST['min_quantity'] ?? 100),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active'
        ];

        if (!empty($grpData['name']) && $grpData['category_id'] > 0) {
            if (ProductGroup::save($grpData)) {
                $message = 'Product Group / Subcategory saved successfully!';
            } else {
                $message = 'Failed to save Product Group.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Category and Group Name are required.';
            $messageType = 'danger';
        }
    }
}

$categories = Category::getAll();
$filterCatId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$groups = ProductGroup::getAll($filterCatId > 0 ? ['category_id' => $filterCatId] : []);

$editGroup = null;
if (isset($_GET['edit_id'])) {
    $editGroup = ProductGroup::getById((int)$_GET['edit_id']);
}
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">Product Groups / Subcategories</h1>
        <p style="color: #64748b; margin: 4px 0 0 0;">Manage GSM weights, paper options, and product subcategories.</p>
    </div>
</div>

<?php if ($message): ?>
    <div style="padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 700; background: <?= $messageType === 'success' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $messageType === 'success' ? '#15803d' : '#991b1b' ?>; border: 1px solid <?= $messageType === 'success' ? '#bbf7d0' : '#fecaca' ?>;">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">

    <!-- Groups List Table -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;">Subcategories List (<?= count($groups) ?>)</h3>
            
            <!-- Category Filter Dropdown -->
            <select onchange="window.location.href='<?= ADMIN_URL ?>?view=product-groups&category_id=' + this.value" class="form-control" style="width: auto; font-weight: 700; border-radius: 8px; font-size: 0.88rem;">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $filterCatId === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 12px 16px;">Category</th>
                        <th style="padding: 12px 16px;">Subcategory / Group Name</th>
                        <th style="padding: 12px 16px;">Min Qty</th>
                        <th style="padding: 12px 16px;">Products</th>
                        <th style="padding: 12px 16px;">Status</th>
                        <th style="padding: 12px 16px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $g): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; color: #64748b; font-weight: 700;">
                                    <?= e($g['category_name']) ?>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <strong style="color: #0f172a; display: block; font-size: 0.95rem;"><?= e($g['name']) ?></strong>
                                    <span style="font-size: 0.78rem; color: #64748b;">slug: <?= e($g['slug']) ?></span>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 800; color: #0f172a;">
                                    <?= number_format($g['min_quantity']) ?>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="background: #f1f5f9; color: #334155; font-size: 0.8rem; font-weight: 800; padding: 4px 10px; border-radius: 10px;">
                                        <?= (int)$g['product_count'] ?> Products
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="background: <?= $g['status'] === 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $g['status'] === 'active' ? '#15803d' : '#991b1b' ?>; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                        <?= e($g['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    <a href="<?= ADMIN_URL ?>?view=product-groups&edit_id=<?= $g['id'] ?>" class="btn btn-sm btn-outline" style="font-weight: 700; border-radius: 8px;">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">No product groups found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create / Edit Form Card -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 20px 0;">
            <?= $editGroup ? 'Edit Group #' . $editGroup['id'] : 'Create Subcategory / Group' ?>
        </h3>

        <form method="POST" action="<?= ADMIN_URL ?>?view=product-groups">
            <input type="hidden" name="action_type" value="save_group">
            <?php if ($editGroup): ?>
                <input type="hidden" name="group_id" value="<?= $editGroup['id'] ?>">
            <?php endif; ?>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Parent Category *</label>
                <select name="category_id" class="form-control" required style="border-radius: 8px; font-weight: 700;">
                    <option value="">Select Parent Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($editGroup['category_id'] ?? $filterCatId) == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Group / GSM Name *</label>
                <input type="text" name="name" value="<?= e($editGroup['name'] ?? '') ?>" placeholder="e.g. 500 GSM Cards" class="form-control" required style="border-radius: 8px; font-weight: 700;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Minimum Order Quantity</label>
                <input type="number" name="min_quantity" value="<?= (int)($editGroup['min_quantity'] ?? 100) ?>" class="form-control" style="border-radius: 8px; font-weight: 700;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Description</label>
                <textarea name="description" class="form-control" rows="3" style="border-radius: 8px; font-size: 0.88rem;"><?= e($editGroup['description'] ?? '') ?></textarea>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Sort Order</label>
                <input type="number" name="sort_order" value="<?= (int)($editGroup['sort_order'] ?? 0) ?>" class="form-control" style="border-radius: 8px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 0.88rem;">Status</label>
                <select name="status" class="form-control" style="border-radius: 8px; font-weight: 700;">
                    <option value="active" <?= ($editGroup['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($editGroup['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; font-weight: 800; border-radius: 8px;">
                    <?= $editGroup ? 'Update Group' : 'Save Group' ?>
                </button>
                <?php if ($editGroup): ?>
                    <a href="<?= ADMIN_URL ?>?view=product-groups" class="btn btn-outline" style="border-radius: 8px; font-weight: 700;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

</div>
