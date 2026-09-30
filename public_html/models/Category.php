<?php
/**
 * MD Printers - B2B Printing Category Model
 */
require_once CONFIG_PATH . '/database.php';

class Category {

    public static function initTables(): void {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `product_categories` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `slug` VARCHAR(150) NOT NULL UNIQUE,
                `description` TEXT NULL,
                `main_image` VARCHAR(255) NULL,
                `icon` VARCHAR(100) DEFAULT 'fa-print',
                `sort_order` INT NOT NULL DEFAULT 0,
                `status` ENUM('active', 'inactive') DEFAULT 'active',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (`slug`),
                INDEX (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Ensure missing columns exist
            $colsToEnsure = [
                'description' => "TEXT NULL",
                'main_image'  => "VARCHAR(255) NULL",
                'icon'        => "VARCHAR(100) DEFAULT 'fa-print'",
                'sort_order'  => "INT NOT NULL DEFAULT 0",
                'status'      => "VARCHAR(50) DEFAULT 'active'"
            ];

            foreach ($colsToEnsure as $col => $def) {
                try {
                    $db->exec("ALTER TABLE `product_categories` ADD COLUMN `{$col}` {$def}");
                } catch (\Throwable $ex) {}
            }

            self::seedDefaultsIfEmpty($db);
        } catch (\Throwable $e) {}
    }

    public static function seedDefaultsIfEmpty($db): void {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM `product_categories`");
            if ((int)$stmt->fetchColumn() > 0) return;

            $defaults = [
                ['name' => 'Visiting Cards', 'slug' => 'visiting-cards', 'description' => 'Premium business cards, 500 GSM velvet, gold foil, NT PVC & spot UV cards.', 'icon' => 'fa-address-card', 'sort_order' => 1],
                ['name' => 'Pamphlets & Flyers', 'slug' => 'brochures-flyers', 'description' => 'High impact marketing flyers, corporate bi-fold & tri-fold brochures.', 'icon' => 'fa-file-alt', 'sort_order' => 2],
                ['name' => 'Packaging & Boxes', 'slug' => 'packaging-boxes', 'description' => 'Custom rigid magnetic boxes, product folding cartons and gift sleeves.', 'icon' => 'fa-box-open', 'sort_order' => 3],
                ['name' => 'Posters & Banners', 'slug' => 'posters-banners', 'description' => 'Large format HD vinyl banners, standees, rigid sunboard posters.', 'icon' => 'fa-scroll', 'sort_order' => 4],
                ['name' => 'Corporate Stationery', 'slug' => 'stationery', 'description' => 'Executive letterheads, document folders, notepad pads & certificates.', 'icon' => 'fa-envelope-open-text', 'sort_order' => 5],
                ['name' => 'Files & Folders', 'slug' => 'files-folders', 'description' => 'Pocket presentation folders, L-type document files & ring binders.', 'icon' => 'fa-folder-open', 'sort_order' => 6],
                ['name' => 'Envelopes & ATM Pouches', 'slug' => 'envelopes-pouches', 'description' => 'Custom branded office envelopes, window envelopes & bank ATM pouches.', 'icon' => 'fa-envelope', 'sort_order' => 7],
                ['name' => 'Bill Books & Registers', 'slug' => 'bill-books', 'description' => 'Duplicate & triplicate NCR carbonless tax invoices and receipt bill books.', 'icon' => 'fa-book', 'sort_order' => 8],
                ['name' => 'Stickers & Labels', 'slug' => 'stickers-labels', 'description' => 'Die-cut product labels, vinyl stickers, barcode roll labels & badges.', 'icon' => 'fa-tags', 'sort_order' => 9],
                ['name' => 'Promotional Pens & Gifts', 'slug' => 'promo-gifts', 'description' => 'Engraved metallic pens, corporate hampers, keychains & branding gifts.', 'icon' => 'fa-pen-fancy', 'sort_order' => 10]
            ];

            $stmt = $db->prepare("INSERT INTO `product_categories` (`name`, `slug`, `description`, `icon`, `sort_order`, `status`) VALUES (:name, :slug, :description, :icon, :sort_order, 'active')");
            foreach ($defaults as $cat) {
                $stmt->execute($cat);
            }
        } catch (\Throwable $e) {}
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return [];

        try {
            $sql = "SELECT c.*, 
                           (SELECT COUNT(*) FROM product_groups g WHERE g.category_id = c.id AND g.status = 'active') as group_count,
                           (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = 'published') as product_count
                    FROM product_categories c 
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND c.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (c.name LIKE :search OR c.description LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY c.sort_order ASC, c.name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT c.*, 
                                   (SELECT COUNT(*) FROM product_groups g WHERE g.category_id = c.id AND g.status = 'active') as group_count,
                                   (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = 'published') as product_count
                                  FROM product_categories c 
                                  WHERE c.slug = :slug LIMIT 1");
            $stmt->execute(['slug' => $slug]);
            $res = $stmt->fetch();
            return $res ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT c.* FROM product_categories c WHERE c.id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $res = $stmt->fetch();
            return $res ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function save(array $data): bool {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return false;

        try {
            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE product_categories SET 
                    name = :name, slug = :slug, description = :description, main_image = :main_image,
                    icon = :icon, sort_order = :sort_order, status = :status
                    WHERE id = :id");
                return $stmt->execute([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? '',
                    'main_image' => $data['main_image'] ?? null,
                    'icon' => $data['icon'] ?? 'fa-print',
                    'sort_order' => $data['sort_order'] ?? 0,
                    'status' => $data['status'] ?? 'active',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO product_categories 
                    (name, slug, description, main_image, icon, sort_order, status) 
                    VALUES (:name, :slug, :description, :main_image, :icon, :sort_order, :status)");
                return $stmt->execute([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? '',
                    'main_image' => $data['main_image'] ?? null,
                    'icon' => $data['icon'] ?? 'fa-print',
                    'sort_order' => $data['sort_order'] ?? 0,
                    'status' => $data['status'] ?? 'active'
                ]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function delete(int $id): bool {
        $db = Database::getInstance();
        if (!$db) return false;
        try {
            $stmt = $db->prepare("DELETE FROM product_categories WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
