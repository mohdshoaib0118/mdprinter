<?php
/**
 * MD Printers - Admin Services Management CRUD
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$services = $db->fetchAll("SELECT s.*, c.name as category_name FROM services s JOIN service_categories c ON s.category_id = c.id ORDER BY s.id ASC");

$adminTitle = 'Print Services Management';
$adminActivePage = 'services';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-cogs" style="color: var(--admin-primary); margin-right: 8px;"></i> Services List</h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Icon</th>
                    <th>Service Name</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><i class="fas <?= e($s['icon']) ?>" style="color: var(--admin-primary); font-size: 1.2rem;"></i></td>
                    <td><strong><?= e($s['name']) ?></strong></td>
                    <td><?= e($s['category_name']) ?></td>
                    <td><?= $s['is_featured'] ? '<span class="badge badge-won">Yes</span>' : '<span class="badge badge-closed">No</span>' ?></td>
                    <td><?= $s['is_active'] ? '<span class="badge badge-won">Active</span>' : '<span class="badge badge-lost">Inactive</span>' ?></td>
                    <td>
                        <a href="<?= SITE_URL ?>/services/<?= e($s['slug']) ?>" target="_blank" class="btn-admin btn-admin-outline" style="padding: 4px 8px; font-size: 0.8rem;">
                            <i class="fas fa-eye"></i> View Live
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
