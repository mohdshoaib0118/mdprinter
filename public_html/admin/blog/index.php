<?php
/**
 * MD Printers - Admin Blog Management
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$posts = $db->fetchAll("SELECT b.*, c.name as category_name FROM blog_posts b JOIN blog_categories c ON b.category_id = c.id ORDER BY b.id DESC");

$adminTitle = 'Blog Articles Management';
$adminActivePage = 'blog';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-newspaper" style="color: var(--admin-primary); margin-right: 8px;"></i> Blog Articles</h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Article Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><strong><?= e($b['title']) ?></strong></td>
                    <td><?= e($b['category_name']) ?></td>
                    <td><?= e($b['author']) ?></td>
                    <td><?= number_format($b['views']) ?></td>
                    <td><?= $b['is_published'] ? '<span class="badge badge-won">Published</span>' : '<span class="badge badge-closed">Draft</span>' ?></td>
                    <td>
                        <a href="<?= SITE_URL ?>/blog/<?= e($b['slug']) ?>" target="_blank" class="btn-admin btn-admin-outline" style="padding: 4px 8px; font-size: 0.8rem;">
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
