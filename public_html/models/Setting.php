<?php
/**
 * MD Printers - Site Settings Model
 */
require_once CONFIG_PATH . '/database.php';

class Setting {
    private static array $cache = [];

    public static function get(string $key, string $default = ''): string {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $db = Database::getInstance();
        if (!$db) {
            return $default;
        }

        try {
            $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = :key LIMIT 1");
            $stmt->execute(['key' => $key]);
            $row = $stmt->fetch();
            $val = $row ? ($row['setting_value'] ?? $default) : $default;
            self::$cache[$key] = $val;
            return $val;
        } catch (PDOException $e) {
            return $default;
        }
    }

    public static function getAll(): array {
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $stmt = $db->query("SELECT setting_key, setting_value, setting_group FROM site_settings");
            $rows = $stmt->fetchAll();
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            return $settings;
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function set(string $key, string $value, string $group = 'general'): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group) 
                VALUES (:key, :val, :group) 
                ON DUPLICATE KEY UPDATE setting_value = :val2, setting_group = :group2");
            return $stmt->execute([
                'key' => $key,
                'val' => $value,
                'group' => $group,
                'val2' => $value,
                'group2' => $group
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
