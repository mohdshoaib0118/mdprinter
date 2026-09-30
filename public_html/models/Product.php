<?php
/**
 * MD Printers - Product Model
 */
require_once CONFIG_PATH . '/database.php';

class Product {
    public static function initColumns(): void {
        $db = Database::getInstance();
        if (!$db) return;
        try {
            $cols = [
                "ALTER TABLE products ADD COLUMN video_url VARCHAR(255) NULL",
                "ALTER TABLE products ADD COLUMN product_group_id INT UNSIGNED NULL AFTER category_id",
                "ALTER TABLE products ADD COLUMN production_time VARCHAR(100) DEFAULT '2-3 Business Days'",
                "ALTER TABLE products ADD COLUMN max_quantity INT UNSIGNED NULL",
                "ALTER TABLE products ADD COLUMN quantity_step INT UNSIGNED DEFAULT 1",
                "ALTER TABLE products ADD COLUMN sku VARCHAR(100) NULL",
                "ALTER TABLE products ADD COLUMN product_code VARCHAR(100) NULL",
                "ALTER TABLE products ADD COLUMN product_class VARCHAR(100) NULL",
                "ALTER TABLE products ADD COLUMN product_core VARCHAR(100) NULL",
                "ALTER TABLE products ADD COLUMN specialization TEXT NULL",
                "ALTER TABLE products ADD COLUMN points_to_note TEXT NULL",
                "ALTER TABLE products ADD COLUMN allow_pressline TINYINT(1) DEFAULT 0",
                "ALTER TABLE products ADD COLUMN allow_artwork_upload TINYINT(1) DEFAULT 1",
                "ALTER TABLE products ADD COLUMN free_delivery_min_qty INT UNSIGNED NULL",
                "ALTER TABLE products ADD COLUMN free_delivery_min_amount DECIMAL(12,2) NULL"
            ];
            foreach ($cols as $sql) {
                try { $db->exec($sql); } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {}
    }

    public static function getAll(array $filters = [], int $limit = 20, int $offset = 0): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackProducts();

        self::initColumns();

        try {
            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, g.name as group_name, g.slug as group_slug
                    FROM products p 
                    LEFT JOIN product_categories c ON p.category_id = c.id 
                    LEFT JOIN product_groups g ON p.product_group_id = g.id
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND p.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['category_slug'])) {
                $sql .= " AND c.slug = :cat_slug";
                $params['cat_slug'] = $filters['category_slug'];
            }

            if (!empty($filters['group_id'])) {
                $sql .= " AND p.product_group_id = :grp_id";
                $params['grp_id'] = (int)$filters['group_id'];
            }

            if (!empty($filters['is_featured'])) {
                $sql .= " AND p.is_featured = 1";
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (p.name LIKE :search OR p.short_desc LIKE :search OR p.full_desc LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY p.id DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            return !empty($results) ? $results : self::getFallbackProducts();
        } catch (PDOException $e) {
            return self::getFallbackProducts();
        }
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        if (!$db) return null;

        self::initColumns();

        try {
            $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug, g.name as group_name, g.slug as group_slug
                                  FROM products p 
                                  LEFT JOIN product_categories c ON p.category_id = c.id 
                                  LEFT JOIN product_groups g ON p.product_group_id = g.id
                                  WHERE p.id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $res = $stmt->fetch();
            if ($res && empty($res['production_time'])) {
                $res['production_time'] = '2-3 Business Days';
            }
            return $res ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        if (!$db) {
            $fallbacks = self::getFallbackProducts();
            foreach ($fallbacks as $p) {
                if ($p['slug'] === $slug) return $p;
            }
            return $fallbacks[0] ?? null;
        }

        self::initColumns();

        try {
            $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug, g.name as group_name, g.slug as group_slug 
                                  FROM products p 
                                  LEFT JOIN product_categories c ON p.category_id = c.id 
                                  LEFT JOIN product_groups g ON p.product_group_id = g.id
                                  WHERE p.slug = :slug LIMIT 1");
            $stmt->execute(['slug' => $slug]);
            $res = $stmt->fetch();
            if ($res && empty($res['production_time'])) {
                $res['production_time'] = '2-3 Business Days';
            }
            return $res ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function getProductsByGroup(int $groupId): array {
        $db = Database::getInstance();
        if (!$db) return [];

        self::initColumns();

        try {
            $stmt = $db->prepare("SELECT id, name, slug, main_image, min_quantity FROM products WHERE product_group_id = :grp_id AND status = 'published' ORDER BY name ASC");
            $stmt->execute(['grp_id' => $groupId]);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function getCategories(): array {
        $db = Database::getInstance();
        if (!$db) return [
            ['id' => 1, 'name' => 'Business Cards', 'slug' => 'business-cards'],
            ['id' => 2, 'name' => 'Brochures & Flyers', 'slug' => 'brochures-flyers'],
            ['id' => 3, 'name' => 'Packaging & Boxes', 'slug' => 'packaging-boxes'],
            ['id' => 4, 'name' => 'Posters & Banners', 'slug' => 'posters-banners'],
            ['id' => 5, 'name' => 'Corporate Stationery', 'slug' => 'stationery']
        ];

        try {
            $stmt = $db->query("SELECT * FROM product_categories WHERE status = 'active' ORDER BY sort_order ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function countAll(array $filters = []): int {
        $db = Database::getInstance();
        if (!$db) return count(self::getFallbackProducts());

        try {
            $sql = "SELECT COUNT(*) as total FROM products p LEFT JOIN product_categories c ON p.category_id = c.id WHERE 1=1";
            $params = [];
            if (!empty($filters['status'])) {
                $sql .= " AND p.status = :status";
                $params['status'] = $filters['status'];
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public static function save(array $data): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        self::initColumns();

        try {
            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE products SET 
                    category_id = :category_id, product_group_id = :product_group_id, name = :name, slug = :slug, short_desc = :short_desc,
                    full_desc = :full_desc, main_image = :main_image, video_url = :video_url, min_quantity = :min_quantity,
                    max_quantity = :max_quantity, quantity_step = :quantity_step, production_time = :production_time,
                    sku = :sku, product_code = :product_code, product_class = :product_class, product_core = :product_core,
                    specialization = :specialization, points_to_note = :points_to_note, allow_pressline = :allow_pressline,
                    allow_artwork_upload = :allow_artwork_upload, free_delivery_min_qty = :free_delivery_min_qty,
                    free_delivery_min_amount = :free_delivery_min_amount, specifications_json = :specifications_json,
                    is_featured = :is_featured, status = :status, meta_title = :meta_title, meta_desc = :meta_desc 
                    WHERE id = :id");
                return $stmt->execute([
                    'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
                    'product_group_id' => !empty($data['product_group_id']) ? (int)$data['product_group_id'] : null,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'short_desc' => $data['short_desc'] ?? '',
                    'full_desc' => $data['full_desc'] ?? '',
                    'main_image' => $data['main_image'] ?? null,
                    'video_url' => $data['video_url'] ?? '',
                    'min_quantity' => $data['min_quantity'] ?? 100,
                    'max_quantity' => !empty($data['max_quantity']) ? (int)$data['max_quantity'] : null,
                    'quantity_step' => !empty($data['quantity_step']) ? (int)$data['quantity_step'] : 1,
                    'production_time' => $data['production_time'] ?? '2-3 Business Days',
                    'sku' => $data['sku'] ?? null,
                    'product_code' => $data['product_code'] ?? null,
                    'product_class' => $data['product_class'] ?? null,
                    'product_core' => $data['product_core'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'points_to_note' => $data['points_to_note'] ?? null,
                    'allow_pressline' => isset($data['allow_pressline']) ? (int)$data['allow_pressline'] : 0,
                    'allow_artwork_upload' => isset($data['allow_artwork_upload']) ? (int)$data['allow_artwork_upload'] : 1,
                    'free_delivery_min_qty' => !empty($data['free_delivery_min_qty']) ? (int)$data['free_delivery_min_qty'] : null,
                    'free_delivery_min_amount' => !empty($data['free_delivery_min_amount']) ? (float)$data['free_delivery_min_amount'] : null,
                    'specifications_json' => $data['specifications_json'] ?? null,
                    'is_featured' => $data['is_featured'] ?? 0,
                    'status' => $data['status'] ?? 'published',
                    'meta_title' => $data['meta_title'] ?? '',
                    'meta_desc' => $data['meta_desc'] ?? '',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO products 
                    (category_id, product_group_id, name, slug, short_desc, full_desc, main_image, video_url, min_quantity, max_quantity, quantity_step, production_time, sku, product_code, product_class, product_core, specialization, points_to_note, allow_pressline, allow_artwork_upload, free_delivery_min_qty, free_delivery_min_amount, specifications_json, is_featured, status, meta_title, meta_desc) 
                    VALUES (:category_id, :product_group_id, :name, :slug, :short_desc, :full_desc, :main_image, :video_url, :min_quantity, :max_quantity, :quantity_step, :production_time, :sku, :product_code, :product_class, :product_core, :specialization, :points_to_note, :allow_pressline, :allow_artwork_upload, :free_delivery_min_qty, :free_delivery_min_amount, :specifications_json, :is_featured, :status, :meta_title, :meta_desc)");
                return $stmt->execute([
                    'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
                    'product_group_id' => !empty($data['product_group_id']) ? (int)$data['product_group_id'] : null,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'short_desc' => $data['short_desc'] ?? '',
                    'full_desc' => $data['full_desc'] ?? '',
                    'main_image' => $data['main_image'] ?? null,
                    'video_url' => $data['video_url'] ?? '',
                    'min_quantity' => $data['min_quantity'] ?? 100,
                    'max_quantity' => !empty($data['max_quantity']) ? (int)$data['max_quantity'] : null,
                    'quantity_step' => !empty($data['quantity_step']) ? (int)$data['quantity_step'] : 1,
                    'production_time' => $data['production_time'] ?? '2-3 Business Days',
                    'sku' => $data['sku'] ?? null,
                    'product_code' => $data['product_code'] ?? null,
                    'product_class' => $data['product_class'] ?? null,
                    'product_core' => $data['product_core'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'points_to_note' => $data['points_to_note'] ?? null,
                    'allow_pressline' => isset($data['allow_pressline']) ? (int)$data['allow_pressline'] : 0,
                    'allow_artwork_upload' => isset($data['allow_artwork_upload']) ? (int)$data['allow_artwork_upload'] : 1,
                    'free_delivery_min_qty' => !empty($data['free_delivery_min_qty']) ? (int)$data['free_delivery_min_qty'] : null,
                    'free_delivery_min_amount' => !empty($data['free_delivery_min_amount']) ? (float)$data['free_delivery_min_amount'] : null,
                    'specifications_json' => $data['specifications_json'] ?? null,
                    'is_featured' => $data['is_featured'] ?? 0,
                    'status' => $data['status'] ?? 'published',
                    'meta_title' => $data['meta_title'] ?? '',
                    'meta_desc' => $data['meta_desc'] ?? ''
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
            $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallbackProducts(): array {
        return [
            [
                'id' => 1,
                'category_id' => 1,
                'category_name' => 'Business Cards',
                'category_slug' => 'business-cards',
                'name' => 'Executive Gold Foil Business Cards',
                'slug' => 'gold-foil-business-cards',
                'short_desc' => 'Luxury 400 GSM velvet touch business cards with metallic gold precision foil stamping.',
                'full_desc' => '<p>Elevate networking into a lasting brand impression. Our executive foil business cards feature 400 GSM heavy artboard coated with silk velvet lamination, topped with raised metallic foil stamping on text or logos.</p>',
                'main_image' => 'assets/images/products/gold-foil-card.jpg',
                'specifications_json' => '{"Dimensions": "3.5 x 2.0 inches", "Paper Weight": "400 GSM Silk Board", "Corners": "Square or Round Corners"}',
                'paper_options_json' => '["400 GSM Silk Artboard", "350 GSM Cotton Textured", "600 GSM Triplex Colored Core"]',
                'finishing_options_json' => '["Metallic Gold Foil", "Rose Gold Foil", "Silver Foil", "3D Raised Spot UV"]',
                'min_quantity' => 100,
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'category_name' => 'Brochures & Flyers',
                'category_slug' => 'brochures-flyers',
                'name' => 'Luxury Corporate Tri-Fold Brochure',
                'slug' => 'corporate-trifold-brochure',
                'short_desc' => 'Professional 250 GSM tri-fold brochure with crisp scoring, high-gloss or matte lamination.',
                'full_desc' => '<p>Present your company overview, service offerings, and product lines with clean visual typography on 250 GSM premium art paper with precision machine scoring for zero spine cracking.</p>',
                'main_image' => 'assets/images/products/trifold-brochure.jpg',
                'specifications_json' => '{"Flat Size": "A4 (297 x 210 mm)", "Fold Type": "Tri-Fold", "Print": "Full Color 4/4 CMYK"}',
                'paper_options_json' => '["170 GSM Art Paper", "250 GSM Matt Art Board", "300 GSM Premium Silk"]',
                'finishing_options_json' => '["Matte Lamination", "Gloss Lamination", "Soft Touch + Spot UV"]',
                'min_quantity' => 250,
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 3,
                'category_id' => 3,
                'category_name' => 'Packaging & Boxes',
                'category_slug' => 'packaging-boxes',
                'name' => 'Custom Rigid Magnetic Gift Box',
                'slug' => 'custom-rigid-magnetic-box',
                'short_desc' => 'High-end structural gift box with magnetic flap closure, custom foam inserts, and debossed branding.',
                'full_desc' => '<p>Designed for luxury cosmetics, corporate onboarding hampers, electronics, and VIP press packages. Built with heavy 1200 GSM greyboard wrapped in custom printed art paper or textured leatherette.</p>',
                'main_image' => 'assets/images/products/magnetic-box.jpg',
                'specifications_json' => '{"Board Thickness": "1.5mm to 3.0mm Greyboard", "Closure": "Dual Hidden Magnets"}',
                'paper_options_json' => '["1200 GSM Heavy Greyboard", "1500 GSM Premium Rigid Board"]',
                'finishing_options_json' => '["Custom Foil Stamping", "Debossing", "Soft Touch Matte Lamination"]',
                'min_quantity' => 50,
                'is_featured' => 1,
                'status' => 'published'
            ]
        ];
    }
}
