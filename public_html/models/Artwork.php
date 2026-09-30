<?php
/**
 * MD Printers - B2B Artwork Upload & Versioning Workflow Model
 */
require_once CONFIG_PATH . '/database.php';

class Artwork {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `artwork_files` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `order_item_id` INT NOT NULL,
                `customer_id` INT NOT NULL,
                `original_filename` VARCHAR(255) NOT NULL,
                `stored_filename` VARCHAR(255) NOT NULL,
                `file_path` VARCHAR(255) NOT NULL,
                `mime_type` VARCHAR(100) NOT NULL,
                `file_size` INT NOT NULL DEFAULT 0,
                `version_number` INT NOT NULL DEFAULT 1,
                `status` ENUM('uploaded', 'under_review', 'correction_required', 'approved', 'rejected') NOT NULL DEFAULT 'uploaded',
                `uploaded_by_type` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
                `uploaded_by_id` INT NOT NULL,
                `customer_comment` TEXT DEFAULT NULL,
                `admin_comment` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_order_item` (`order_item_id`),
                INDEX `idx_order` (`order_id`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public static function uploadArtwork(int $orderId, int $orderItemId, int $customerId, array $fileData, string $comment = ''): array {
        self::initTables();
        $db = Database::getInstance();

        if (!$db) return ['success' => false, 'message' => 'Database connection failed.'];

        // Validate upload errors
        if (!isset($fileData['error']) || $fileData['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error code: ' . ($fileData['error'] ?? 'Unknown')];
        }

        // Validate File Size (Max 50MB)
        $maxBytes = 50 * 1024 * 1024;
        if ($fileData['size'] > $maxBytes) {
            return ['success' => false, 'message' => 'File size exceeds maximum allowed 50MB.'];
        }

        // Extension & Mime Validation (PDF, AI, CDR, PSD, TIFF, PNG, JPG)
        $allowedExts = ['pdf', 'ai', 'cdr', 'psd', 'tif', 'tiff', 'png', 'jpg', 'jpeg', 'zip'];
        $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts)) {
            return ['success' => false, 'message' => 'Invalid file extension .' . e($ext) . '. Allowed extensions: PDF, AI, CDR, PSD, TIFF, PNG, JPG, ZIP.'];
        }

        // Calculate Next Version Number
        $vStmt = $db->prepare("SELECT MAX(version_number) FROM artwork_files WHERE order_item_id = :item_id");
        $vStmt->execute(['item_id' => $orderItemId]);
        $maxVer = (int)$vStmt->fetchColumn();
        $newVersion = $maxVer + 1;

        // Generate Randomized Stored Filename
        $randomHash = bin2hex(random_bytes(16));
        $storedFilename = 'art_v' . $newVersion . '_' . time() . '_' . $randomHash . '.' . $ext;

        $targetDir = UPLOADS_PATH . '/artworks/' . date('Y/m');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $storedFilename;
        $relativePath = 'uploads/artworks/' . date('Y/m') . '/' . $storedFilename;

        if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Failed to save uploaded file to press storage server.'];
        }

        try {
            $stmt = $db->prepare("INSERT INTO artwork_files (
                order_id, order_item_id, customer_id, original_filename, stored_filename,
                file_path, mime_type, file_size, version_number, status, uploaded_by_type,
                uploaded_by_id, customer_comment
            ) VALUES (
                :oid, :item_id, :cid, :orig_name, :stored_name,
                :fpath, :mime, :fsize, :ver, 'under_review', 'customer',
                :uid, :comment
            )");

            $stmt->execute([
                'oid' => $orderId,
                'item_id' => $orderItemId,
                'cid' => $customerId,
                'orig_name' => $fileData['name'],
                'stored_name' => $storedFilename,
                'fpath' => $relativePath,
                'mime' => $fileData['type'] ?? 'application/octet-stream',
                'fsize' => $fileData['size'],
                'ver' => $newVersion,
                'uid' => $customerId,
                'comment' => $comment
            ]);

            $artworkId = (int)$db->lastInsertId();

            // Update item artwork status
            $uStmt = $db->prepare("UPDATE order_items SET artwork_status = 'under_review' WHERE id = :item_id");
            $uStmt->execute(['item_id' => $orderItemId]);

            // Update order status if pending artwork
            $oStmt = $db->prepare("UPDATE orders SET status = 'artwork_under_review' WHERE id = :oid");
            $oStmt->execute(['oid' => $orderId]);

            return [
                'success' => true,
                'message' => 'Artwork Version ' . $newVersion . ' uploaded successfully for review!',
                'artwork_id' => $artworkId
            ];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error while saving artwork record.'];
        }
    }

    public static function getVersionsForItem(int $orderItemId): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM artwork_files WHERE order_item_id = :item_id ORDER BY version_number DESC");
            $stmt->execute(['item_id' => $orderItemId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function findById(int $artworkId): ?array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM artwork_files WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $artworkId]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function updateStatus(int $artworkId, string $status, string $adminComment = '', ?int $adminId = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $art = self::findById($artworkId);
            if (!$art) return false;

            $stmt = $db->prepare("UPDATE artwork_files SET status = :status, admin_comment = :comment WHERE id = :id");
            $stmt->execute([
                'status' => $status,
                'comment' => $adminComment,
                'id' => $artworkId
            ]);

            // Update order item status
            $itemStatus = ($status === 'approved') ? 'approved' : (($status === 'correction_required') ? 'correction_required' : 'under_review');
            $iStmt = $db->prepare("UPDATE order_items SET artwork_status = :st, approved_artwork_id = :aid WHERE id = :item_id");
            $iStmt->execute([
                'st' => $itemStatus,
                'aid' => ($status === 'approved') ? $artworkId : $art['order_item_id'],
                'item_id' => $art['order_item_id']
            ]);

            // If approved, update overall order status
            if ($status === 'approved') {
                $oStmt = $db->prepare("UPDATE orders SET status = 'artwork_approved' WHERE id = :oid");
                $oStmt->execute(['oid' => $art['order_id']]);
            } elseif ($status === 'correction_required') {
                $oStmt = $db->prepare("UPDATE orders SET status = 'artwork_correction_required' WHERE id = :oid");
                $oStmt->execute(['oid' => $art['order_id']]);
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
