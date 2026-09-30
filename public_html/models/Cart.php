<?php
/**
 * MD Printers - B2B Cart & Configuration Snapshot Model
 */
require_once CONFIG_PATH . '/database.php';
require_once MODELS_PATH . '/PricingEngine.php';

class Cart {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `cart_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `customer_id` INT DEFAULT NULL,
                `session_id` VARCHAR(191) DEFAULT NULL,
                `product_id` INT NOT NULL,
                `product_name_snapshot` VARCHAR(191) NOT NULL,
                `quantity` INT NOT NULL,
                `configuration_json` TEXT DEFAULT NULL,
                `price_breakdown_json` TEXT DEFAULT NULL,
                `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_cust` (`customer_id`),
                INDEX `idx_sess` (`session_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public static function getSessionId(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return session_id();
    }

    public static function add(int $productId, string $productName, int $quantity, array $config, array $breakdown, ?int $customerId = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $sessId = self::getSessionId();

        try {
            $stmt = $db->prepare("INSERT INTO cart_items 
                (customer_id, session_id, product_id, product_name_snapshot, quantity, configuration_json, price_breakdown_json, unit_price, subtotal)
                VALUES (:cid, :sess, :pid, :pname, :qty, :config, :breakdown, :uprice, :subtotal)");

            return $stmt->execute([
                'cid' => $customerId,
                'sess' => $sessId,
                'pid' => $productId,
                'pname' => $productName,
                'qty' => $quantity,
                'config' => json_encode($config),
                'breakdown' => json_encode($breakdown),
                'uprice' => round($breakdown['final_total'] / max(1, $quantity), 2),
                'subtotal' => $breakdown['final_total']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getItems(?int $customerId = null): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        $sessId = self::getSessionId();

        try {
            if ($customerId) {
                $stmt = $db->prepare("SELECT * FROM cart_items WHERE customer_id = :cid OR session_id = :sess ORDER BY id DESC");
                $stmt->execute(['cid' => $customerId, 'sess' => $sessId]);
            } else {
                $stmt = $db->prepare("SELECT * FROM cart_items WHERE session_id = :sess ORDER BY id DESC");
                $stmt->execute(['sess' => $sessId]);
            }

            $items = $stmt->fetchAll();
            foreach ($items as &$item) {
                $item['configuration'] = json_decode($item['configuration_json'] ?? '{}', true);
                $item['price_breakdown'] = json_decode($item['price_breakdown_json'] ?? '{}', true);
            }

            return $items;
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function remove(int $cartItemId, ?int $customerId = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $sessId = self::getSessionId();

        try {
            if ($customerId) {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE id = :id AND (customer_id = :cid OR session_id = :sess)");
                return $stmt->execute(['id' => $cartItemId, 'cid' => $customerId, 'sess' => $sessId]);
            } else {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE id = :id AND session_id = :sess");
                return $stmt->execute(['id' => $cartItemId, 'sess' => $sessId]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function clear(?int $customerId = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $sessId = self::getSessionId();

        try {
            if ($customerId) {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE customer_id = :cid OR session_id = :sess");
                return $stmt->execute(['cid' => $customerId, 'sess' => $sessId]);
            } else {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE session_id = :sess");
                return $stmt->execute(['sess' => $sessId]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }
}
