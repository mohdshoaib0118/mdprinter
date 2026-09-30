<?php
/**
 * MD Printers - Admin Products Management CRUD
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$products = $db->fetchAll("SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id ORDER BY p.id DESC");

$adminTitle = 'Product Catalogue CRUD';
$adminActivePage = 'products';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-box" style="color: var(--admin-primary); margin-right: 8px;"></i> Products List</h2>
        <a href="<?= ADMIN_URL ?>/products/create.php" class="btn-admin btn-admin-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Min Quantity</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><strong><?= e($p['name']) ?></strong></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= number_format($p['min_quantity']) ?> pcs</td>
                    <td><?= $p['is_featured'] ? '<span class="badge badge-won">Yes</span>' : '<span class="badge badge-closed">No</span>' ?></td>
                    <td><?= $p['is_active'] ? '<span class="badge badge-won">Active</span>' : '<span class="badge badge-lost">Inactive</span>' ?></td>
                    <td>
                        <a href="<?= ADMIN_URL ?>/products/edit.php?id=<?= $p['id'] ?>" class="btn-admin btn-admin-outline" style="padding: 4px 8px; font-size: 0.8rem;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?= ADMIN_URL ?>/products/delete.php?id=<?= $p['id'] ?>" class="btn-admin btn-admin-danger" onclick="return confirm('Are you sure you want to delete this product?');" style="padding: 4px 8px; font-size: 0.8rem;">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
