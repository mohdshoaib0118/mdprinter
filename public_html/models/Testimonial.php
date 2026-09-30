<?php
/**
 * MD Printers - Testimonial Model
 */
require_once CONFIG_PATH . '/database.php';

class Testimonial {
    public static function getAll(): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallback();

        try {
            $stmt = $db->query("SELECT * FROM testimonials WHERE status = 'published' ORDER BY sort_order ASC, id DESC");
            $rows = $stmt->fetchAll();
            return !empty($rows) ? $rows : self::getFallback();
        } catch (PDOException $e) {
            return self::getFallback();
        }
    }

    public static function save(array $data): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE testimonials SET client_name = :client_name, company = :company, designation = :designation, rating = :rating, testimonial = :testimonial, status = :status WHERE id = :id");
                return $stmt->execute([
                    'client_name' => $data['client_name'],
                    'company' => $data['company'] ?? '',
                    'designation' => $data['designation'] ?? '',
                    'rating' => $data['rating'] ?? 5,
                    'testimonial' => $data['testimonial'],
                    'status' => $data['status'] ?? 'published',
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO testimonials (client_name, company, designation, rating, testimonial, status) VALUES (:client_name, :company, :designation, :rating, :testimonial, :status)");
                return $stmt->execute([
                    'client_name' => $data['client_name'],
                    'company' => $data['company'] ?? '',
                    'designation' => $data['designation'] ?? '',
                    'rating' => $data['rating'] ?? 5,
                    'testimonial' => $data['testimonial'],
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
            $stmt = $db->prepare("DELETE FROM testimonials WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallback(): array {
        return [
            [
                'id' => 1,
                'client_name' => 'Vikram Malhotra',
                'company' => 'Malhotra Consumer Goods',
                'designation' => 'VP Marketing',
                'rating' => 5,
                'testimonial' => 'MD Printers executed our product packaging launch with absolute precision. The color fidelity on 50,000 retail folding cartons matched our digital proofs down to the exact Pantone shade.'
            ],
            [
                'id' => 2,
                'client_name' => 'Sarah Jenkins',
                'company' => 'Apex Financial Advisors',
                'designation' => 'Brand Director',
                'rating' => 5,
                'testimonial' => 'The foil business cards and presentation folders we ordered for our wealth management partners are stunning. Clients literally remark on the texture every time we hand them out.'
            ]
        ];
    }
}
