<?php
/**
 * MD Printers - B2B Product Group / Subcategory Model
 */
require_once CONFIG_PATH . '/database.php';

class ProductGroup {

    public static function initTables(): void {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            // 1. Product Groups Table
            $db->exec("CREATE TABLE IF NOT EXISTS `product_groups` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `category_id` INT UNSIGNED NOT NULL,
                `name` VARCHAR(150) NOT NULL,
                `slug` VARCHAR(150) NOT NULL UNIQUE,
                `description` TEXT NULL,
                `main_image` VARCHAR(255) NULL,
                `min_quantity` INT UNSIGNED NOT NULL DEFAULT 100,
                `sort_order` INT NOT NULL DEFAULT 0,
                `status` ENUM('active', 'inactive') DEFAULT 'active',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (`category_id`),
                INDEX (`slug`),
                INDEX (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 2. Add product_group_id column to products table if missing
            try {
                $db->exec("ALTER TABLE `products` ADD COLUMN `product_group_id` INT UNSIGNED NULL AFTER `category_id`");
            } catch (\Throwable $e) {
                // Column already exists
            }

            self::seedDefaultsIfEmpty($db);
        } catch (\Throwable $e) {
            // Ignore if tables exist
        }
    }

    public static function seedDefaultsIfEmpty($db): void {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM `product_groups`");
            if ((int)$stmt->fetchColumn() > 0) return;

            // Default Product Groups mapped to categories
            $defaultGroups = [
                // Category 1: Business Cards
                ['category_id' => 1, 'name' => '500 GSM Cards', 'slug' => '500-gsm-cards', 'description' => 'Ultra heavy premium 500 GSM art card stock with luxury finishes.', 'min_quantity' => 500, 'sort_order' => 1],
                ['category_id' => 1, 'name' => '400 GSM Cards', 'slug' => '400-gsm-cards', 'description' => 'Heavyweight 400 GSM velvet touch business cards.', 'min_quantity' => 100, 'sort_order' => 2],
                ['category_id' => 1, 'name' => '350 GSM Cards', 'slug' => '350-gsm-cards', 'description' => 'Standard commercial 350 GSM matte/gloss coated cards.', 'min_quantity' => 100, 'sort_order' => 3],
                ['category_id' => 1, 'name' => 'Regular Visiting Cards', 'slug' => 'regular-visiting-cards', 'description' => 'Economical 300 GSM daily networking cards.', 'min_quantity' => 100, 'sort_order' => 4],
                ['category_id' => 1, 'name' => 'NT / PVC Visiting Cards', 'slug' => 'pvc-visiting-cards', 'description' => 'Waterproof, tearproof non-tearable PVC visiting cards.', 'min_quantity' => 100, 'sort_order' => 5],

                // Category 2: Brochures & Flyers
                ['category_id' => 2, 'name' => 'Promotional Flyers', 'slug' => 'promotional-flyers', 'description' => 'High-impact 170 GSM gloss promotional flyers for marketing.', 'min_quantity' => 500, 'sort_order' => 1],
                ['category_id' => 2, 'name' => 'Tri-Fold Brochures', 'slug' => 'trifold-brochures', 'description' => '250 GSM tri-fold company profile brochures.', 'min_quantity' => 250, 'sort_order' => 2],
                ['category_id' => 2, 'name' => 'Bi-Fold Leaflets', 'slug' => 'bifold-leaflets', 'description' => '2-panel folded corporate informational leaflets.', 'min_quantity' => 250, 'sort_order' => 3],

                // Category 3: Packaging & Boxes
                ['category_id' => 3, 'name' => 'Rigid Magnetic Boxes', 'slug' => 'rigid-magnetic-boxes', 'description' => 'Luxury structural rigid boxes with magnetic flap closure.', 'min_quantity' => 50, 'sort_order' => 1],
                ['category_id' => 3, 'name' => 'Product Packaging Cartons', 'slug' => 'packaging-cartons', 'description' => 'Custom folding artcard product cartons and sleeve boxes.', 'min_quantity' => 250, 'sort_order' => 2],

                // Category 5: Corporate Stationery
                ['category_id' => 5, 'name' => 'Presentation Folders', 'slug' => 'presentation-folders', 'description' => '350 GSM die-cut document presentation pocket folders.', 'min_quantity' => 100, 'sort_order' => 1],
                ['category_id' => 5, 'name' => 'Corporate Letterheads', 'slug' => 'corporate-letterheads', 'description' => '100 GSM premium Sunshine/Bond paper letterheads.', 'min_quantity' => 500, 'sort_order' => 2]
            ];

            $insertStmt = $db->prepare("INSERT INTO `product_groups` (`category_id`, `name`, `slug`, `description`, `min_quantity`, `sort_order`, `status`) VALUES (:category_id, :name, :slug, :description, :min_quantity, :sort_order, 'active')");
            foreach ($defaultGroups as $grp) {
                $insertStmt->execute($grp);
            }

            // Map existing products to their corresponding groups
            $map = [
                1 => 2, // Product 1 (Gold Foil Cards) -> 400 GSM Cards (group id 2)
                2 => 7, // Product 2 (Tri-Fold Brochure) -> Tri-Fold Brochures (group id 7)
                3 => 9, // Product 3 (Magnetic Gift Box) -> Rigid Magnetic Boxes (group id 9)
                4 => 6, // Product 4 (Promotional Flyers) -> Promotional Flyers (group id 6)
                5 => 11 // Product 5 (Presentation Folder) -> Presentation Folders (group id 11)
            ];

            foreach ($map as $prodId => $grpId) {
                $db->exec("UPDATE `products` SET `product_group_id` = {$grpId} WHERE `id` = {$prodId}");
            }
        } catch (\Throwable $e) {
            // Ignore seeding errors
        }
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return [];

        try {
            $sql = "SELECT g.*, c.name as category_name, c.slug as category_slug, 
                           (SELECT COUNT(*) FROM products p WHERE p.product_group_id = g.id AND p.status = 'published') as product_count
                    FROM product_groups g
                    LEFT JOIN product_categories c ON g.category_id = c.id
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['category_id'])) {
                $sql .= " AND g.category_id = :cat_id";
                $params['cat_id'] = (int)$filters['category_id'];
            }

            if (!empty($filters['category_slug'])) {
                $sql .= " AND c.slug = :cat_slug";
                $params['cat_slug'] = $filters['category_slug'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND g.status = :status";
                $params['status'] = $filters['status'];
            }

            $sql .= " ORDER BY g.sort_order ASC, g.name ASC";
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
            $stmt = $db->prepare("SELECT g.*, c.name as category_name, c.slug as category_slug
                                  FROM product_groups g
                                  LEFT JOIN product_categories c ON g.category_id = c.id
                                  WHERE g.slug = :slug LIMIT 1");
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
            $stmt = $db->prepare("SELECT g.*, c.name as category_name, c.slug as category_slug
                                  FROM product_groups g
                                  LEFT JOIN product_categories c ON g.category_id = c.id
                                  WHERE g.id = :id LIMIT 1");
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
                $stmt = $db->prepare("UPDATE product_groups SET 
                    category_id = :category_id, name = :name, slug = :slug, description = :description,
                    main_image = :main_image, min_quantity = :min_quantity, sort_order = :sort_order, status = :status
                    WHERE id = :id");
                return $stmt->execute([
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? '',
                    'main_image' => $data['main_image'] ?? null,
                    'min_quantity' => $data['min_quantity'] ?? 100,
                    'sort_order' => $data['sort_order'] ?? 0,
                    'status' => $data['status'] ?? 'active',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO product_groups 
                    (category_id, name, slug, description, main_image, min_quantity, sort_order, status) 
                    VALUES (:category_id, :name, :slug, :description, :main_image, :min_quantity, :sort_order, :status)");
                return $stmt->execute([
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? '',
                    'main_image' => $data['main_image'] ?? null,
                    'min_quantity' => $data['min_quantity'] ?? 100,
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
            $stmt = $db->prepare("DELETE FROM product_groups WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
