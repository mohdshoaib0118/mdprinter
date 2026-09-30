<?php
/**
 * MD Printers - Hero Banner Model
 */
require_once CONFIG_PATH . '/database.php';

class Banner {
    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackBanners();

        try {
            // Self-healing check for video_url column
            try { $db->exec("ALTER TABLE banners ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            $sql = "SELECT * FROM banners WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND status = :status";
                $params['status'] = $filters['status'];
            }

            $sql .= " ORDER BY sort_order ASC, id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            return !empty($results) ? $results : self::getFallbackBanners();
        } catch (\Throwable $e) {
            return self::getFallbackBanners();
        }
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM banners WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function save(array $data): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            // Self-healing column check
            try { $db->exec("ALTER TABLE banners ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE banners SET 
                    badge = :badge, title = :title, subtitle = :subtitle,
                    cta_primary_text = :cta_primary_text, cta_primary_link = :cta_primary_link,
                    cta_secondary_text = :cta_secondary_text, cta_secondary_link = :cta_secondary_link,
                    bg_gradient = :bg_gradient, image = :image, video_url = :video_url, sort_order = :sort_order, status = :status
                    WHERE id = :id");
                return $stmt->execute([
                    'badge'              => $data['badge'],
                    'title'              => $data['title'],
                    'subtitle'           => $data['subtitle'],
                    'cta_primary_text'   => $data['cta_primary_text'] ?? 'Get Quote',
                    'cta_primary_link'   => $data['cta_primary_link'] ?? SITE_URL . '/request-quote',
                    'cta_secondary_text' => $data['cta_secondary_text'] ?? 'Call Us',
                    'cta_secondary_link' => $data['cta_secondary_link'] ?? 'tel:8146141450',
                    'bg_gradient'        => $data['bg_gradient'] ?? 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))',
                    'image'              => $data['image'] ?? 'assets/images/services/offset-printing.jpg',
                    'video_url'          => $data['video_url'] ?? '',
                    'sort_order'         => (int)($data['sort_order'] ?? 0),
                    'status'             => $data['status'] ?? 'published',
                    'id'                 => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO banners 
                    (badge, title, subtitle, cta_primary_text, cta_primary_link, cta_secondary_text, cta_secondary_link, bg_gradient, image, video_url, sort_order, status) 
                    VALUES (:badge, :title, :subtitle, :cta_primary_text, :cta_primary_link, :cta_secondary_text, :cta_secondary_link, :bg_gradient, :image, :video_url, :sort_order, :status)");
                return $stmt->execute([
                    'badge'              => $data['badge'],
                    'title'              => $data['title'],
                    'subtitle'           => $data['subtitle'],
                    'cta_primary_text'   => $data['cta_primary_text'] ?? 'Get Quote',
                    'cta_primary_link'   => $data['cta_primary_link'] ?? SITE_URL . '/request-quote',
                    'cta_secondary_text' => $data['cta_secondary_text'] ?? 'Call Us',
                    'cta_secondary_link' => $data['cta_secondary_link'] ?? 'tel:8146141450',
                    'bg_gradient'        => $data['bg_gradient'] ?? 'linear-gradient(135deg, rgba(0,133,178,0.1), rgba(238,0,131,0.08))',
                    'image'              => $data['image'] ?? 'assets/images/services/offset-printing.jpg',
                    'video_url'          => $data['video_url'] ?? '',
                    'sort_order'         => (int)($data['sort_order'] ?? 0),
                    'status'             => $data['status'] ?? 'published'
                ]);
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function delete(int $id): bool {
        $db = Database::getInstance();
        if (!$db) return false;
        try {
            $stmt = $db->prepare("DELETE FROM banners WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getFallbackBanners(): array {
        return [
            [
                'id' => 1,
                'badge' => '🔥 BEST SELLER • EXECUTIVE VISITING CARDS',
                'title' => 'Executive Gold Foil Business Cards',
                'subtitle' => 'Premium 400 GSM Silk Artboard coated with Velvet Soft-Touch Lamination & raised metallic gold foil stamping.',
                'cta_primary_text' => 'Order Visiting Cards',
                'cta_primary_link' => SITE_URL . '/services/visiting-cards',
                'cta_secondary_text' => 'Call Us',
                'cta_secondary_link' => 'tel:8146141450',
                'bg_gradient' => 'linear-gradient(135deg, rgba(201,158,80,0.12), rgba(0,133,178,0.08))',
                'image' => 'assets/images/products/gold-foil-card.jpg',
                'video_url' => 'https://assets.mixkit.co/videos/preview/mixkit-printing-machine-in-a-factory-41566-large.mp4',
                'sort_order' => 1,
                'status' => 'published'
            ],
            [
                'id' => 2,
                'badge' => '📦 CUSTOM PACKAGING • RIGID & FOLDING BOXES',
                'title' => 'Custom Rigid Packaging & Product Boxes',
                'subtitle' => 'Bespoke folding cartons, rigid magnetic gift boxes, cosmetic sleeves, and e-commerce shipping boxes with CAD prototyping.',
                'cta_primary_text' => 'Explore Packaging',
                'cta_primary_link' => SITE_URL . '/services/corporate-printing',
                'cta_secondary_text' => 'Request Sample Kit',
                'cta_secondary_link' => SITE_URL . '/request-quote',
                'bg_gradient' => 'linear-gradient(135deg, rgba(238,0,131,0.1), rgba(0,133,178,0.08))',
                'image' => 'assets/images/services/packaging-printing.jpg',
                'video_url' => 'https://assets.mixkit.co/videos/preview/mixkit-close-up-of-a-printing-press-machine-41565-large.mp4',
                'sort_order' => 2,
                'status' => 'published'
            ],
            [
                'id' => 3,
                'badge' => '👕 CORPORATE APPAREL • CUSTOM T-SHIRTS & UNIFORMS',
                'title' => 'Custom Corporate T-Shirts & Uniforms',
                'subtitle' => '100% Bio-Washed Combed Cotton T-Shirts printed with high-density DTG, plastisol screen print & computerized embroidery.',
                'cta_primary_text' => 'Customize T-Shirts',
                'cta_primary_link' => SITE_URL . '/services/t-shirt-printing',
                'cta_secondary_text' => 'WhatsApp Us',
                'cta_secondary_link' => 'https://wa.me/918146141450',
                'bg_gradient' => 'linear-gradient(135deg, rgba(0,133,178,0.12), rgba(255,222,0,0.1))',
                'image' => 'assets/images/services/tshirt-printing.jpg',
                'video_url' => '',
                'sort_order' => 3,
                'status' => 'published'
            ],
            [
                'id' => 4,
                'badge' => '✨ ACRYLIC SIGNAGE • 3D LED LIGHT BOARDS',
                'title' => '3D LED Acrylic Boards & Name Plates',
                'subtitle' => 'Imported Cast Acrylic Sheets with 3D laser-cut letters, Samsung LED module backlight, and stainless steel stand-offs.',
                'cta_primary_text' => 'View Signage Boards',
                'cta_primary_link' => SITE_URL . '/services/acrylic-boards',
                'cta_secondary_text' => 'Call: 81461 41450',
                'cta_secondary_link' => 'tel:8146141450',
                'bg_gradient' => 'linear-gradient(135deg, rgba(238,0,131,0.1), rgba(201,158,80,0.12))',
                'image' => 'assets/images/services/acrylic-boards.jpg',
                'video_url' => '',
                'sort_order' => 4,
                'status' => 'published'
            ]
        ];
    }
}
