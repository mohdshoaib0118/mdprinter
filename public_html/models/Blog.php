<?php
/**
 * MD Printers - Blog Model
 */
require_once CONFIG_PATH . '/database.php';

class Blog {
    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackBlog();

        try {
            try { $db->exec("ALTER TABLE blog_posts ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                    FROM blog_posts b 
                    LEFT JOIN blog_categories c ON b.category_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (isset($filters['is_published'])) {
                $sql .= " AND b.is_published = :pub";
                $params['pub'] = $filters['is_published'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (b.title LIKE :search OR b.content LIKE :search OR b.excerpt LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY b.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            return !empty($results) ? $results : self::getFallbackBlog();
        } catch (PDOException $e) {
            return self::getFallbackBlog();
        }
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        if (!$db) {
            $posts = self::getFallbackBlog();
            foreach ($posts as $post) {
                if ($post['slug'] === $slug) return $post;
            }
            return $posts[0] ?? null;
        }

        try {
            $stmt = $db->prepare("SELECT b.*, c.name as category_name FROM blog_posts b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE b.slug = :slug LIMIT 1");
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
            try { $db->exec("ALTER TABLE blog_posts ADD COLUMN video_url VARCHAR(255) NULL"); } catch (\Throwable $ex) {}

            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE blog_posts SET category_id = :category_id, title = :title, slug = :slug, content = :content, excerpt = :excerpt, featured_image = :featured_image, video_url = :video_url, author = :author, is_published = :is_published, meta_title = :meta_title, meta_desc = :meta_desc WHERE id = :id");
                return $stmt->execute([
                    'category_id' => $data['category_id'] ?? null,
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'content' => $data['content'],
                    'excerpt' => $data['excerpt'],
                    'featured_image' => $data['featured_image'] ?? null,
                    'video_url' => $data['video_url'] ?? '',
                    'author' => $data['author'] ?? 'MD Printers Team',
                    'is_published' => $data['is_published'] ?? 1,
                    'meta_title' => $data['meta_title'] ?? '',
                    'meta_desc' => $data['meta_desc'] ?? '',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO blog_posts (category_id, title, slug, content, excerpt, featured_image, video_url, author, is_published, meta_title, meta_desc) VALUES (:category_id, :title, :slug, :content, :excerpt, :featured_image, :video_url, :author, :is_published, :meta_title, :meta_desc)");
                return $stmt->execute([
                    'category_id' => $data['category_id'] ?? null,
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'content' => $data['content'],
                    'excerpt' => $data['excerpt'],
                    'featured_image' => $data['featured_image'] ?? null,
                    'video_url' => $data['video_url'] ?? '',
                    'author' => $data['author'] ?? 'MD Printers Team',
                    'is_published' => $data['is_published'] ?? 1,
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
            $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallbackBlog(): array {
        return [
            [
                'id' => 1,
                'title' => 'How to Choose the Right Paper GSM for Commercial Printing',
                'slug' => 'choose-right-paper-gsm-commercial-printing',
                'category_name' => 'Print Knowledge',
                'author' => 'MD Printers Technical Team',
                'excerpt' => 'Learn how GSM paper weight affects tactile brand perception, brochure folding durability, and business card stiffness.',
                'content' => '<p>Paper weight measured in GSM (Grams per Square Meter) is one of the single most influential factors defining tactile perception and structural durability in commercial print.</p><h3>GSM Selection Quick Matrix</h3><ul><li><strong>70 - 90 GSM:</strong> Inner letterhead pages, transactional invoices, notepad sheets.</li><li><strong>130 - 170 GSM:</strong> High-volume promotional flyers, brochure inner pages, magazine text pages.</li><li><strong>250 - 300 GSM:</strong> Softcover catalog covers, premium invitation envelopes, heavy flyers.</li><li><strong>350 - 450 GSM:</strong> Executive business cards, presentation folders, product box covers.</li></ul>',
                'featured_image' => 'assets/images/blog/blog-1.jpg',
                'published_at' => '2026-02-15 10:00:00',
                'is_published' => 1
            ],
            [
                'id' => 2,
                'title' => 'RGB vs CMYK: Avoiding Color Shifts Before Going to Press',
                'slug' => 'rgb-vs-cmyk-avoiding-color-shifts',
                'category_name' => 'Design & Production',
                'author' => 'Prepress Engineering Team',
                'excerpt' => 'Discover why digital screen colors shift when printed on physical press machines and how to set up your prepress files correctly.',
                'content' => '<p>Designing in RGB color mode for digital displays and sending those files directly to an offset press is the #1 cause of muted, unexpected color reproduction.</p><h3>Understanding Color Spaces</h3><p>RGB (Red, Green, Blue) uses additive light suitable for computer monitors, while CMYK (Cyan, Magenta, Yellow, Key/Black) relies on subtractive ink pigments on paper substrate.</p>',
                'featured_image' => 'assets/images/blog/blog-2.jpg',
                'published_at' => '2026-01-20 14:30:00',
                'is_published' => 1
            ]
        ];
    }
}
