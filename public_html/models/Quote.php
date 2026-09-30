<?php
/**
 * MD Printers - Formal B2B Quotation Model
 */
require_once CONFIG_PATH . '/database.php';

class Quote {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `quotes` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `quote_number` VARCHAR(50) NOT NULL UNIQUE,
                `customer_id` INT NOT NULL,
                `valid_until` DATETIME DEFAULT NULL,
                `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `shipping` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `status` ENUM('requested', 'preparing', 'sent', 'viewed', 'accepted', 'rejected', 'expired', 'converted') NOT NULL DEFAULT 'requested',
                `notes` TEXT DEFAULT NULL,
                `admin_notes` TEXT DEFAULT NULL,
                `converted_to_order_id` INT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_customer` (`customer_id`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            $db->exec("CREATE TABLE IF NOT EXISTS `quote_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `quote_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `product_name_snapshot` VARCHAR(191) NOT NULL,
                `quantity` INT NOT NULL,
                `configuration_json` TEXT DEFAULT NULL,
                `price_breakdown_json` TEXT DEFAULT NULL,
                `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`quote_id`) REFERENCES `quotes`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public static function generateQuoteNumber(): string {
        return 'QT-' . date('Ymd') . '-' . rand(1000, 9999);
    }

    public static function create(array $data): ?int {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        try {
            $quoteNumber = self::generateQuoteNumber();
            $stmt = $db->prepare("INSERT INTO quotes 
                (quote_number, customer_id, valid_until, subtotal, discount, tax, shipping, total, status, notes) 
                VALUES (:qnum, :cid, :valid, :subtotal, :disc, :tax, :ship, :total, :status, :notes)");

            $validUntil = date('Y-m-d H:i:s', strtotime('+14 days'));

            $stmt->execute([
                'qnum' => $quoteNumber,
                'cid' => $data['customer_id'],
                'valid' => $data['valid_until'] ?? $validUntil,
                'subtotal' => $data['subtotal'] ?? 0.00,
                'disc' => $data['discount'] ?? 0.00,
                'tax' => $data['tax'] ?? 0.00,
                'ship' => $data['shipping'] ?? 0.00,
                'total' => $data['total'] ?? 0.00,
                'status' => $data['status'] ?? 'requested',
                'notes' => $data['notes'] ?? ''
            ]);

            $quoteId = (int)$db->lastInsertId();

            if (!empty($data['items']) && is_array($data['items'])) {
                $itemStmt = $db->prepare("INSERT INTO quote_items 
                    (quote_id, product_id, product_name_snapshot, quantity, configuration_json, price_breakdown_json, unit_price, subtotal)
                    VALUES (:qid, :pid, :pname, :qty, :config, :breakdown, :uprice, :subtotal)");

                foreach ($data['items'] as $item) {
                    $itemStmt->execute([
                        'qid' => $quoteId,
                        'pid' => $item['product_id'],
                        'pname' => $item['product_name_snapshot'] ?? 'Custom Print Job',
                        'qty' => $item['quantity'],
                        'config' => is_array($item['configuration_json']) ? json_encode($item['configuration_json']) : ($item['configuration_json'] ?? '{}'),
                        'breakdown' => is_array($item['price_breakdown_json']) ? json_encode($item['price_breakdown_json']) : ($item['price_breakdown_json'] ?? '{}'),
                        'uprice' => $item['unit_price'] ?? 0.00,
                        'subtotal' => $item['subtotal'] ?? 0.00
                    ]);
                }
            }

            return $quoteId;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function getForCustomer(int $customerId): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM quotes WHERE customer_id = :cid ORDER BY id DESC");
            $stmt->execute(['cid' => $customerId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function findById(int $quoteId, ?int $customerId = null): ?array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        try {
            $sql = "SELECT q.*, c.company_name, c.contact_person, c.email, c.mobile, c.gst_number as gstin 
                    FROM quotes q 
                    LEFT JOIN customers c ON q.customer_id = c.id 
                    WHERE q.id = :id";
            $params = ['id' => $quoteId];

            if ($customerId !== null) {
                $sql .= " AND q.customer_id = :cid";
                $params['cid'] = $customerId;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $quote = $stmt->fetch();
            if (!$quote) return null;

            // Load Items
            $itemStmt = $db->prepare("SELECT * FROM quote_items WHERE quote_id = :qid");
            $itemStmt->execute(['qid' => $quoteId]);
            $quote['items'] = $itemStmt->fetchAll();

            return $quote;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function updateStatus(int $quoteId, string $status, ?int $customerId = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $sql = "UPDATE quotes SET status = :status WHERE id = :id";
            $params = ['status' => $status, 'id' => $quoteId];

            if ($customerId !== null) {
                $sql .= " AND customer_id = :cid";
                $params['cid'] = $customerId;
            }

            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getAll(array $filters = []): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $sql = "SELECT q.*, c.company_name, c.contact_person, c.email, c.mobile 
                    FROM quotes q 
                    LEFT JOIN customers c ON q.customer_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND q.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['customer_id'])) {
                $sql .= " AND q.customer_id = :cid";
                $params['cid'] = $filters['customer_id'];
            }

            $sql .= " ORDER BY q.id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function adminUpdate(int $quoteId, array $data): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE quotes SET 
                subtotal = :subtotal,
                discount = :discount,
                tax = :tax,
                shipping = :shipping,
                total = :total,
                status = :status,
                valid_until = :valid_until,
                admin_notes = :admin_notes
                WHERE id = :id");

            return $stmt->execute([
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'],
                'tax' => $data['tax'],
                'shipping' => $data['shipping'],
                'total' => $data['total'],
                'status' => $data['status'],
                'valid_until' => $data['valid_until'],
                'admin_notes' => $data['admin_notes'] ?? '',
                'id' => $quoteId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
