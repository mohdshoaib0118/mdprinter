<?php
/**
 * MD Printers - Service Model
 */
require_once CONFIG_PATH . '/database.php';

class Service {
    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackServices();

        try {
            try { $db->exec("ALTER TABLE services ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug 
                    FROM services s 
                    LEFT JOIN service_categories c ON s.category_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND s.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['is_featured'])) {
                $sql .= " AND s.is_featured = 1";
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (s.name LIKE :search OR s.short_desc LIKE :search OR s.full_desc LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY s.id ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            return !empty($results) ? $results : self::getFallbackServices();
        } catch (PDOException $e) {
            return self::getFallbackServices();
        }
    }

    public static function getBySlug(string $slug): ?array {
        $aliasMap = [
            'offset-printing'          => 'printing',
            'digital-printing'         => 'printing',
            'packaging-printing'       => 'corporate-printing',
            'brochure-printing'        => 'printing',
            'business-cards-stationery'=> 'visiting-cards'
        ];

        $targetSlug = $aliasMap[$slug] ?? $slug;

        $db = Database::getInstance();
        if (!$db) {
            $fallbacks = self::getFallbackServices();
            foreach ($fallbacks as $s) {
                if ($s['slug'] === $targetSlug) return $s;
            }
            return $fallbacks[0] ?? null;
        }

        try {
            $stmt = $db->prepare("SELECT s.*, c.name as category_name, c.slug as category_slug 
                                  FROM services s 
                                  LEFT JOIN service_categories c ON s.category_id = c.id 
                                  WHERE s.slug = :slug LIMIT 1");
            $stmt->execute(['slug' => $targetSlug]);
            $res = $stmt->fetch();
            if ($res) return $res;

            // Secondary fallback search by name if slug mismatch
            $stmt = $db->prepare("SELECT s.*, c.name as category_name, c.slug as category_slug 
                                  FROM services s 
                                  LEFT JOIN service_categories c ON s.category_id = c.id 
                                  WHERE s.slug = 'printing' LIMIT 1");
            $stmt->execute();
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function save(array $data): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            try { $db->exec("ALTER TABLE services ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE services SET 
                    category_id = :category_id, name = :name, slug = :slug, icon = :icon, short_desc = :short_desc,
                    full_desc = :full_desc, main_image = :main_image, video_url = :video_url, is_featured = :is_featured, status = :status,
                    meta_title = :meta_title, meta_desc = :meta_desc 
                    WHERE id = :id");
                return $stmt->execute([
                    'category_id' => $data['category_id'] ?? null,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'icon' => $data['icon'] ?? 'fas fa-print',
                    'short_desc' => $data['short_desc'],
                    'full_desc' => $data['full_desc'],
                    'main_image' => $data['main_image'] ?? null,
                    'video_url' => $data['video_url'] ?? '',
                    'is_featured' => $data['is_featured'] ?? 0,
                    'status' => $data['status'] ?? 'published',
                    'meta_title' => $data['meta_title'] ?? '',
                    'meta_desc' => $data['meta_desc'] ?? '',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO services 
                    (category_id, name, slug, icon, short_desc, full_desc, main_image, video_url, is_featured, status, meta_title, meta_desc) 
                    VALUES (:category_id, :name, :slug, :icon, :short_desc, :full_desc, :main_image, :video_url, :is_featured, :status, :meta_title, :meta_desc)");
                return $stmt->execute([
                    'category_id' => $data['category_id'] ?? null,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'icon' => $data['icon'] ?? 'fas fa-print',
                    'short_desc' => $data['short_desc'],
                    'full_desc' => $data['full_desc'],
                    'main_image' => $data['main_image'] ?? null,
                    'video_url' => $data['video_url'] ?? '',
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
            $stmt = $db->prepare("DELETE FROM services WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallbackServices(): array {
        return [
            [
                'id' => 1,
                'name' => 'Printing',
                'slug' => 'printing',
                'badge' => 'P',
                'icon' => 'fas fa-print',
                'color' => 'gold',
                'short_desc' => 'High-volume commercial offset & digital printing with G7 certified CMYK color density for books, brochures, catalogs, and flyers.',
                'full_desc' => '<p>Our commercial printing press suite delivers razor-sharp text clarity, vibrant spot colors (Pantone PMS matching), and smooth solid coverage across large print quantities. Operating Heidelberg and HP Indigo presses, MD Printers ensures rapid turnarounds for corporate annual reports, product catalogs, brochures, and promotional literature.</p>',
                'main_image' => 'assets/images/services/offset-printing.jpg',
                'features_json' => '["G7 Color Certified", "Pantone Spot Matching", "High-Volume Heidelberg Press", "Substrate range: 70 - 450 GSM"]',
                'specifications_json' => '{"Press": "Heidelberg Speedmaster XL 106", "Resolution": "2400 DPI CTP", "Color Modes": "CMYK + 2 Spot Colors Inline"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 2,
                'name' => 'Visiting Cards',
                'slug' => 'visiting-cards',
                'badge' => 'VC',
                'icon' => 'fas fa-address-card',
                'color' => 'magenta',
                'short_desc' => 'Executive business cards with metallic gold foil, raised 3D spot UV, velvet touch lamination, and heavy 400+ GSM artboard.',
                'full_desc' => '<p>Make your networking impression unforgettable. MD Printers crafts executive visiting cards using 400 GSM silk artboard, velvet soft-touch coating, raised metallic foil stamping (gold, rose gold, silver), and 3D spot UV highlights on logos.</p>',
                'main_image' => 'assets/images/services/business-cards.jpg',
                'features_json' => '["400 GSM Heavy Artboard", "Gold / Silver Metallic Foil", "Raised 3D Spot UV", "Round Corners or Die-Cut Slits"]',
                'specifications_json' => '{"Dimensions": "3.5 x 2.0 inches", "Paper Weight": "400 GSM Silk", "Finishing": "Velvet Touch + Metallic Foil"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 3,
                'name' => 'T-Shirt Printing',
                'slug' => 't-shirt-printing',
                'badge' => 'TP',
                'icon' => 'fas fa-tshirt',
                'color' => 'magenta',
                'short_desc' => 'Custom corporate t-shirts, polo shirts, screen printing, DTG high-density prints, and embroidered staff uniforms.',
                'full_desc' => '<p>Transform your workforce into brand ambassadors. MD Printers offers custom t-shirt printing utilizing Direct-to-Garment (DTG), plastisol screen printing, heat transfer vinyl, and high-precision computer embroidery on premium combed cotton tees.</p>',
                'main_image' => 'assets/images/services/tshirt-printing.jpg',
                'features_json' => '["100% Bio-Washed Combed Cotton", "High-Density DTG & Screen Print", "Precision Computerized Embroidery", "Durable Fade-Resistant Inks"]',
                'specifications_json' => '{"Fabric": "180 - 240 GSM Cotton", "Print Methods": "DTG, Screen Print, Embroidery", "Sizes": "S to 5XL"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 4,
                'name' => 'Mug Printing',
                'slug' => 'mug-printing',
                'badge' => 'MP',
                'icon' => 'fas fa-mug-hot',
                'color' => 'yellow',
                'short_desc' => 'Custom ceramic coffee mugs, magic heat-sensitive mugs, travel tumblers, and corporate branded promotional drinkware.',
                'full_desc' => '<p>High-grade dye-sublimation ceramic mugs printed with vibrant full-color wrap graphics. Dishwasher safe, microwave safe, and engineered for long-lasting corporate gifting.</p>',
                'main_image' => 'assets/images/services/mug-printing.jpg',
                'features_json' => '["11 oz & 15 oz Ceramic Mugs", "HD Sublimation Full-Wrap Print", "Magic Heat-Changing Option", "Individual Gift Box Packaging"]',
                'specifications_json' => '{"Capacity": "325 ml / 11 oz", "Material": "Grade-A Ceramic", "Dishwasher Safe": "Yes"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 5,
                'name' => 'Badges',
                'slug' => 'badges',
                'badge' => 'B',
                'icon' => 'fas fa-certificate',
                'color' => 'gold',
                'short_desc' => 'Custom button badges, metallic lapel pins, magnetic event badges, and corporate identification pins.',
                'full_desc' => '<p>Custom promotional button badges, enamel metal pins, and magnetic name badges for corporate events, trade shows, school badges, and campaign branding.</p>',
                'main_image' => 'assets/images/services/badges.jpg',
                'features_json' => '["Glossy Mylar Protective Film", "Safety Pin & Magnetic Backing", "Custom Die-Cut Shapes", "Enamel Metal & Plastic Badges"]',
                'specifications_json' => '{"Sizes": "44mm, 58mm, 75mm", "Backing": "Pin, Magnet, Clip", "Finish": "High Gloss Mylar"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 6,
                'name' => 'Stickers',
                'slug' => 'stickers',
                'badge' => 'S',
                'icon' => 'fas fa-sticky-note',
                'color' => 'cyan',
                'short_desc' => 'Waterproof vinyl stickers, die-cut product labels, foil stamped sticker sheets, and clear window decals.',
                'full_desc' => '<p>Custom printed adhesive stickers, product packaging labels, roll stickers, kiss-cut sheets, holographic stickers, and waterproof outdoor vinyl decals.</p>',
                'main_image' => 'assets/images/services/stickers.jpg',
                'features_json' => '["Waterproof & UV Resistant Vinyl", "Die-Cut & Kiss-Cut Shapes", "Holographic & Metallic Foil Options", "Roll Labels for Auto Applicators"]',
                'specifications_json' => '{"Material": "White Vinyl, Clear PVC, Metallic Kraft", "Adhesive": "Strong Permanent", "UV Protection": "3 Years Outdoor"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 7,
                'name' => 'ID Cards',
                'slug' => 'id-cards',
                'badge' => 'IC',
                'icon' => 'fas fa-id-badge',
                'color' => 'magenta',
                'short_desc' => 'High-durability PVC employee ID cards, RFID smart cards, visitor passes, and barcode student access cards.',
                'full_desc' => '<p>CR80 standard thermal plastic PVC ID cards with high-definition thermal printing, magnetic stripes, RFID chip embedding, QR/barcodes, and protective holographic overlays.</p>',
                'main_image' => 'assets/images/services/id-cards.jpg',
                'features_json' => '["Heavy-Duty 30 Mil PVC Plastic", "HD Re-transfer Thermal Printing", "RFID & Magnetic Stripe Options", "Barcode & Holographic Security"]',
                'specifications_json' => '{"Card Standard": "CR80 (85.6 x 54 mm)", "Thickness": "0.76mm / 30 Mil", "Printing": "Dual Sided HD"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 8,
                'name' => 'Lanyards',
                'slug' => 'lanyards',
                'badge' => 'L',
                'icon' => 'fas fa-ribbon',
                'color' => 'yellow',
                'short_desc' => 'Custom satin lanyard straps, screen-printed lanyard ribbons, breakaway safety clips, and heavy-duty metal hooks.',
                'full_desc' => '<p>Custom woven and satin polyester lanyards for corporate staff, exhibitions, and security access. Features vibrant sublimation printing with safety breakaway buckles and swivel hooks.</p>',
                'main_image' => 'assets/images/services/lanyards.jpg',
                'features_json' => '["Premium Satin & Polyester Ribbon", "Full-Color Sublimation Printing", "Breakaway Safety Lock", "Heavy-Duty Dog Hook & Oval Clip"]',
                'specifications_json' => '{"Widths": "12mm, 16mm, 20mm", "Length": "900mm Standard", "Attachment": "Metal Hook, Badge Reel"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 9,
                'name' => 'Name Plates',
                'slug' => 'name-plates',
                'badge' => 'NP',
                'icon' => 'fas fa-signature',
                'color' => 'gold',
                'short_desc' => 'Executive brass & stainless steel door name plates, acrylic desk name signs, and laser-engraved office plaques.',
                'full_desc' => '<p>Laser-engraved acrylic, brushed metal stainless steel, and gold brass name plates for executive desks, office doors, conference rooms, and reception counters.</p>',
                'main_image' => 'assets/images/services/name-plates.jpg',
                'features_json' => '["Laser Engraved Metal & Acrylic", "Brushed Gold & Silver Stainless Steel", "Beveled Edge Glass / Acrylic Desk Holders", "Weatherproof UV Printing"]',
                'specifications_json' => '{"Materials": "Acrylic, Brass, Stainless Steel", "Mounting": "Adhesive Tape, Stand-off Bolts"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 10,
                'name' => 'Acrylic Boards',
                'slug' => 'acrylic-boards',
                'badge' => 'AB',
                'icon' => 'fas fa-shapes',
                'color' => 'cyan',
                'short_desc' => 'Cast acrylic LED light boards, 3D raised letter signage, clear acrylic display panels, and shopfront signboards.',
                'full_desc' => '<p>High-grade cast acrylic board printing and 3D laser-cut letter signages. Ideal for corporate office receptions, store branding, illuminated LED backlight signs, and directional wayfinding panels.</p>',
                'main_image' => 'assets/images/services/acrylic-boards.jpg',
                'features_json' => '["Imported Cast Acrylic Sheets", "3D Raised Laser-Cut Letters", "Backlit LED Module Lighting", "Stainless Steel Stand-off Mounts"]',
                'specifications_json' => '{"Thickness": "3mm, 5mm, 8mm, 10mm", "Lighting": "Samsung LED Backlight Modules"}',
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'id' => 11,
                'name' => 'Corporate Printing',
                'slug' => 'corporate-printing',
                'badge' => 'CP',
                'icon' => 'fas fa-briefcase',
                'color' => 'magenta',
                'short_desc' => 'Corporate stationery kits, custom presentation folders, annual reports, branded diaries, calendars, and welcome kits.',
                'full_desc' => '<p>Complete corporate printing solutions including pocket presentation folders, custom foil-stamped notebooks, desk calendars, company profile brochures, and executive welcome gift hampers.</p>',
                'main_image' => 'assets/images/services/corporate-printing.jpg',
                'features_json' => '["Complete Brand Welcome Kits", "Die-Cut Pocket Folders", "Custom Executive Leatherette Diaries", "Table Top Desk Calendars"]',
                'specifications_json' => '{"Kit Contents": "Folder, Notebook, Pen, Card, USB", "Packaging": "Custom Luxury Box"}',
                'is_featured' => 1,
                'status' => 'published'
            ]
        ];
    }
}
