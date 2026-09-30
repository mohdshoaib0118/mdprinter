<?php
/**
 * MD Printers - FAQ Model
 */
require_once CONFIG_PATH . '/database.php';

class Faq {
    public static function getAll(): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallback();

        try {
            $stmt = $db->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
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
                $stmt = $db->prepare("UPDATE faqs SET question = :question, answer = :answer, category = :category, is_active = :is_active WHERE id = :id");
                return $stmt->execute([
                    'question' => $data['question'],
                    'answer' => $data['answer'],
                    'category' => $data['category'] ?? 'General',
                    'is_active' => $data['is_active'] ?? 1,
                    'id' => $data['id']
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO faqs (question, answer, category, is_active) VALUES (:question, :answer, :category, :is_active)");
                return $stmt->execute([
                    'question' => $data['question'],
                    'answer' => $data['answer'],
                    'category' => $data['category'] ?? 'General',
                    'is_active' => $data['is_active'] ?? 1
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
            $stmt = $db->prepare("DELETE FROM faqs WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallback(): array {
        return [
            [
                'id' => 1,
                'question' => 'What artwork file formats do you accept for printing?',
                'answer' => 'We accept high-resolution vector PDF (preferred), Adobe Illustrator (.AI), Adobe Photoshop (.PSD), TIFF, and EPS files. Artwork should be set to 300 DPI resolution in CMYK color mode with 3mm bleed allowance on all sides.',
                'category' => 'Artwork & Prepress'
            ],
            [
                'id' => 2,
                'question' => 'What is the standard turnaround time for printing orders?',
                'answer' => 'Standard digital print runs are delivered in 24 to 48 hours. Large volume multi-color offset runs and custom rigid packaging require 4 to 7 business days depending on finishing specs.',
                'category' => 'Turnaround & Shipping'
            ],
            [
                'id' => 3,
                'question' => 'Do you offer custom die-cutting and special finishing options?',
                'answer' => 'Absolutely. We offer spot UV, 3D raised UV, metallic foil stamping (gold, silver, rose gold), matte/gloss lamination, embossing, debossing, and custom shape die-cutting.',
                'category' => 'Finishing & Options'
            ]
        ];
    }
}
