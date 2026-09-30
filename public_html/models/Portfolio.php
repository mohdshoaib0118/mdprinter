<?php
/**
 * MD Printers - Portfolio Model
 */
require_once CONFIG_PATH . '/database.php';

class Portfolio {
    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackPortfolio();

        try {
            try { $db->exec("ALTER TABLE portfolio ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                    FROM portfolio p 
                    LEFT JOIN portfolio_categories c ON p.category_id = c.id 
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

            $sql .= " ORDER BY p.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            return !empty($results) ? $results : self::getFallbackPortfolio();
        } catch (PDOException $e) {
            return self::getFallbackPortfolio();
        }
    }

    public static function getCategories(): array {
        $db = Database::getInstance();
        if (!$db) return [
            ['id' => 1, 'name' => 'Packaging', 'slug' => 'packaging'],
            ['id' => 2, 'name' => 'Corporate Print', 'slug' => 'corporate-print'],
            ['id' => 3, 'name' => 'Event & Retail', 'slug' => 'event-retail'],
            ['id' => 4, 'name' => 'Luxury Stationery', 'slug' => 'luxury-stationery']
        ];

        try {
            $stmt = $db->query("SELECT * FROM portfolio_categories ORDER BY id ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        if (!$db) {
            $items = self::getFallbackPortfolio();
            foreach ($items as $item) {
                if ($item['slug'] === $slug) return $item;
            }
            return $items[0] ?? null;
        }

        try {
            $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM portfolio p LEFT JOIN portfolio_categories c ON p.category_id = c.id WHERE p.slug = :slug LIMIT 1");
            $stmt->execute(['slug' => $slug]);
            $res = $stmt->fetch();
            return $res ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function save(array $data): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            try { $db->exec("ALTER TABLE portfolio ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE portfolio SET category_id = :category_id, title = :title, slug = :slug, client_name = :client_name, project_date = :project_date, description = :description, image = :image, video_url = :video_url, services_used = :services_used, status = :status WHERE id = :id");
                return $stmt->execute([
                    'category_id' => $data['category_id'] ?? null,
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'client_name' => $data['client_name'] ?? '',
                    'project_date' => $data['project_date'] ?? '',
                    'description' => $data['description'],
                    'image' => $data['image'],
                    'video_url' => $data['video_url'] ?? '',
                    'services_used' => $data['services_used'] ?? '',
                    'status' => $data['status'] ?? 'published',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO portfolio (category_id, title, slug, client_name, project_date, description, image, video_url, services_used, status) VALUES (:category_id, :title, :slug, :client_name, :project_date, :description, :image, :video_url, :services_used, :status)");
                return $stmt->execute([
                    'category_id' => $data['category_id'] ?? null,
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'client_name' => $data['client_name'] ?? '',
                    'project_date' => $data['project_date'] ?? '',
                    'description' => $data['description'],
                    'image' => $data['image'],
                    'video_url' => $data['video_url'] ?? '',
                    'services_used' => $data['services_used'] ?? '',
                    'status' => $data['status'] ?? 'published'
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
            $stmt = $db->prepare("DELETE FROM portfolio WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallbackPortfolio(): array {
        return [
            [
                'id' => 1,
                'title' => 'Aura Skincare Rigid Box & Bottle Sleeves',
                'slug' => 'aura-skincare-packaging',
                'category_name' => 'Packaging',
                'category_slug' => 'packaging',
                'client_name' => 'Aura Botanicals',
                'project_date' => 'January 2026',
                'description' => 'Designed and produced 10,000 custom soft-touch rigid gift boxes with rose gold foil stamping and custom die-cut velvet insert trays for an organic luxury skincare line.',
                'image' => 'assets/images/portfolio/portfolio-1.jpg',
                'services_used' => 'Rigid Box Packaging, Foil Stamping, Spot UV',
                'status' => 'published'
            ],
            [
                'id' => 2,
                'title' => 'Horizon Tech Annual Report & Brand Kit',
                'slug' => 'horizon-tech-annual-report',
                'category_name' => 'Corporate Print',
                'category_slug' => 'corporate-print',
                'client_name' => 'Horizon Technologies Ltd',
                'project_date' => 'December 2025',
                'description' => 'Multi-page offset printed annual report featuring translucent metallic flysheets, silver foil cover titles, and perfect bound spine finishing for 5,000 shareholders.',
                'image' => 'assets/images/portfolio/portfolio-2.jpg',
                'services_used' => 'Offset Printing, Perfect Binding, Metallic Inks',
                'status' => 'published'
            ],
            [
                'id' => 3,
                'title' => 'Vanguard Capital Executive Stationery Set',
                'slug' => 'vanguard-capital-stationery',
                'category_name' => 'Luxury Stationery',
                'category_slug' => 'luxury-stationery',
                'client_name' => 'Vanguard Capital Group',
                'project_date' => 'February 2026',
                'description' => 'Bespoke 600 GSM triplex edge-painted business cards, watermarked cotton letterheads, and foil embossed presentation folders for executive board members.',
                'image' => 'assets/images/portfolio/portfolio-3.jpg',
                'services_used' => 'Letterpress, Triplex Business Cards, Edge Painting',
                'status' => 'published'
            ]
        ];
    }
}
