<?php
/**
 * MD Printers - Admin Testimonials Management
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$testimonials = $db->fetchAll("SELECT * FROM testimonials ORDER BY id DESC");

$adminTitle = 'Testimonials Management';
$adminActivePage = 'testimonials';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-star" style="color: var(--admin-primary); margin-right: 8px;"></i> Client Testimonials</h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client Name</th>
                    <th>Company</th>
                    <th>Rating</th>
                    <th>Testimonial Quote</th>
                    <th>Featured</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testimonials as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><strong><?= e($t['client_name']) ?></strong></td>
                    <td><?= e($t['company']) ?></td>
                    <td><span style="color: #FFD600;"><?= str_repeat('★', $t['rating']) ?></span></td>
                    <td style="max-width: 400px;"><?= e(substr($t['testimonial'], 0, 90)) ?>...</td>
                    <td><?= $t['is_featured'] ? '<span class="badge badge-won">Yes</span>' : '<span class="badge badge-closed">No</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
