<?php
/**
 * MD Printers Admin - Media Library Manager View
 */

// Self-healing database table check
$db = Database::getInstance();
if ($db) {
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS `media` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `filename` VARCHAR(255) NOT NULL,
            `original_name` VARCHAR(255) NOT NULL,
            `filepath` VARCHAR(255) NOT NULL,
            `file_type` VARCHAR(50) NOT NULL,
            `file_size` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (\Throwable $ex) {}
}

// Handle POST Upload / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $res = upload_file($_FILES['media_file'], 'media');
            if (isset($res['filepath'])) {
                if ($db) {
                    $stmt = $db->prepare("INSERT INTO media (filename, original_name, filepath, file_type, file_size) VALUES (:fn, :on, :fp, :ft, :fs)");
                    $stmt->execute([
                        'fn' => $res['filename'],
                        'on' => $res['original_name'],
                        'fp' => $res['filepath'],
                        'ft' => $res['extension'],
                        'fs' => $res['file_size']
                    ]);
                }
                set_flash('success', 'File uploaded to central media library successfully!');
            } else {
                set_flash('danger', 'Upload error: ' . ($res['error'] ?? 'Failed to save file.'));
            }
            redirect_to(ADMIN_URL . '?view=media');
        } elseif (isset($_POST['delete_media_id'])) {
            $deleteId = (int)$_POST['delete_media_id'];
            if ($db) {
                $stmt = $db->prepare("SELECT filepath FROM media WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $deleteId]);
                $item = $stmt->fetch();
                if ($item) {
                    $fileOnDisk = ROOT_PATH . '/' . ltrim(str_replace('\\', '/', $item['filepath']), '/');
                    if (file_exists($fileOnDisk)) {
                        @unlink($fileOnDisk);
                    }
                    $delStmt = $db->prepare("DELETE FROM media WHERE id = :id");
                    $delStmt->execute(['id' => $deleteId]);
                    set_flash('success', 'Media asset deleted successfully.');
                }
            }
            redirect_to(ADMIN_URL . '?view=media');
        }
    }
}

$mediaItems = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM media ORDER BY id DESC");
        $mediaItems = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

$flash = get_flash();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0;">Central Media Library</h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Upload product photos, PDFs, artwork templates, and media assets.</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert-<?= e($flash['type']) ?>" style="margin-bottom: 24px;">
        <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Instructional Explanation Banner -->
<div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 18px 22px; margin-bottom: 28px; display: flex; align-items: flex-start; gap: 16px;">
    <div style="background: #0284c7; color: #fff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; shrink: 0; margin-top: 2px;">
        <i class="fas fa-info"></i>
    </div>
    <div>
        <strong style="font-size: 0.98rem; color: #0369a1; display: block; margin-bottom: 4px;">How Media Assets Work Across Your Website:</strong>
        <p style="font-size: 0.88rem; color: #334155; line-height: 1.5; margin: 0;">
            Assets uploaded here are stored in your central server directory (<code>uploads/media/</code>). 
            To use any uploaded image in <strong>Hero Banners, Catalogue Products, Print Services, Portfolio Projects, or Blog Articles</strong>:
            simply click <strong><i class="fas fa-copy"></i> Copy Path</strong> below any image, and paste it into the <em>Image Path</em> field in that section's Add/Edit form!
        </p>
    </div>
</div>

<!-- Upload Box -->
<div class="admin-table-card" style="padding: 24px; margin-bottom: 32px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 16px 0;"><i class="fas fa-cloud-upload-alt" style="color: var(--cmyk-cyan);"></i> Upload New Media Asset</h3>
    <form action="" method="POST" enctype="multipart/form-data" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
        <?= csrf_field() ?>
        <input type="file" name="media_file" required style="padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px; flex: 1; min-width: 260px; background: #f8fafc;">
        <button type="submit" class="btn btn-primary" style="font-weight: 800; padding: 11px 24px;">
            <i class="fas fa-upload"></i> Upload Asset
        </button>
    </form>
</div>

<!-- Media Items Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
    <?php if (empty($mediaItems)): ?>
        <div style="grid-column: 1 / -1; padding: 60px; text-align: center; color: #64748b; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
            <i class="fas fa-folder-open" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
            No assets uploaded to the media library yet. Choose a file above to upload.
        </div>
    <?php else: ?>
        <?php foreach ($mediaItems as $item): ?>
            <div class="admin-table-card" style="padding: 14px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column;">
                <div style="height: 150px; background: #f8fafc; border-radius: 8px; overflow: hidden; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid #f1f5f9;">
                    <?php if (in_array(strtolower($item['file_type']), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])): ?>
                        <img src="<?= media_url($item['filepath']) ?>" alt="<?= e($item['original_name']) ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;" 
                             onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';">
                    <?php else: ?>
                        <div style="text-align: center; color: var(--cmyk-cyan);">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; margin-bottom: 6px;"></i>
                            <span style="display: block; font-size: 0.75rem; text-transform: uppercase; font-weight: 800;"><?= e($item['file_type']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <strong style="font-size: 0.88rem; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px;" title="<?= e($item['original_name']) ?>">
                    <?= e($item['original_name']) ?>
                </strong>
                <span style="color: #64748b; font-size: 0.78rem; margin-bottom: 10px; display: block;">
                    <?= format_bytes($item['file_size']) ?> • <?= date('d M Y', strtotime($item['created_at'])) ?>
                </span>

                <div style="margin-top: auto; display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; gap: 6px;">
                        <button type="button" onclick="copyTextToClipboard('<?= e($item['filepath']) ?>', 'Relative Path Copied!')" 
                                class="btn btn-outline btn-sm" style="flex: 1; padding: 6px; font-size: 0.75rem; font-weight: 700; border-color: #cbd5e1; color: #334155; text-align: center;" title="Copy path for Forms">
                            <i class="fas fa-copy"></i> Copy Path
                        </button>
                        <button type="button" onclick="copyTextToClipboard('<?= SITE_URL ?>/<?= e($item['filepath']) ?>', 'Full URL Copied!')" 
                                class="btn btn-outline btn-sm" style="flex: 1; padding: 6px; font-size: 0.75rem; font-weight: 700; border-color: #cbd5e1; color: var(--cmyk-cyan); text-align: center;" title="Copy Full URL">
                            <i class="fas fa-link"></i> Full URL
                        </button>
                    </div>

                    <form action="" method="POST" onsubmit="return confirmAdminDelete(event, this, 'Are you sure you want to delete this media asset permanently?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_media_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-sm" style="width: 100%; padding: 6px; background: rgba(239, 68, 68, 0.08); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800; font-size: 0.78rem;" title="Delete File">
                            <i class="fas fa-trash-alt"></i> Delete Asset
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function copyTextToClipboard(text, msg) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showToastMessage(msg || 'Copied to clipboard!');
        });
    } else {
        var tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        showToastMessage(msg || 'Copied to clipboard!');
    }
}

function showToastMessage(message) {
    var toast = document.createElement('div');
    toast.style.cssText = 'position: fixed; bottom: 30px; right: 30px; background: #0f172a; color: #fff; padding: 12px 22px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; display: flex; align-items: center; gap: 10px; animation: fadeInToast 0.3s ease;';
    toast.innerHTML = '<i class="fas fa-check-circle" style="color: #22c55e;"></i> ' + message;
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(function() { document.body.removeChild(toast); }, 300);
    }, 2000);
}
</script>
