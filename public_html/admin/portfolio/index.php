<?php
/**
 * MD Printers - Admin Portfolio Management
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$portfolio = $db->fetchAll("SELECT p.*, c.name as category_name FROM portfolio p JOIN portfolio_categories c ON p.category_id = c.id ORDER BY p.id DESC");

$adminTitle = 'Portfolio Case Studies';
$adminActivePage = 'portfolio';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-camera" style="color: var(--admin-primary); margin-right: 8px;"></i> Portfolio Showcase Items</h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Project Title</th>
                    <th>Client Name</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($portfolio as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><strong><?= e($p['title']) ?></strong></td>
                    <td><?= e($p['client_name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= $p['is_featured'] ? '<span class="badge badge-won">Yes</span>' : '<span class="badge badge-closed">No</span>' ?></td>
                    <td>
                        <a href="<?= SITE_URL ?>/portfolio/<?= e($p['slug']) ?>" target="_blank" class="btn-admin btn-admin-outline" style="padding: 4px 8px; font-size: 0.8rem;">
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
