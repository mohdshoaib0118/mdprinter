<?php
/**
 * MD Printers - Admin Media Library
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$db = getDB();
$artworks = $db->fetchAll("SELECT * FROM enquiry_files ORDER BY id DESC LIMIT 50");

$adminTitle = 'Media Library & Artwork Files';
$adminActivePage = 'media';

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-images" style="color: var(--admin-primary); margin-right: 8px;"></i> Customer Uploaded Artworks & Attachments</h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Original Filename</th>
                    <th>Storage Path</th>
                    <th>File Size</th>
                    <th>Uploaded Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($artworks)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--admin-muted);">No artwork files recorded in database yet.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($artworks as $art): ?>
                    <tr>
                        <td><?= $art['id'] ?></td>
                        <td><strong><?= e($art['original_name']) ?></strong></td>
                        <td><code><?= e($art['file_path']) ?></code></td>
                        <td><?= round($art['file_size'] / 1024, 1) ?> KB</td>
                        <td><?= format_date($art['uploaded_at']) ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/<?= e($art['file_path']) ?>" download target="_blank" class="btn-admin btn-admin-primary" style="padding: 4px 10px; font-size: 0.8rem;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
