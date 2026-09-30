<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Banner.php';

$db = Database::getInstance();
if ($db) {
    $stmt = $db->query("SELECT id, badge FROM banners");
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        $badge = $r['badge'];
        $cleanBadge = preg_replace('/•\s*STARTING\s*@\s*₹[0-9.]+(\s*\/[^\s]+)?/i', '', $badge);
        $cleanBadge = preg_replace('/STARTING\s*@\s*₹[0-9.]+(\s*\/[^\s]+)?/i', '', $cleanBadge);
        $cleanBadge = trim(rtrim($cleanBadge, '•'));
        
        $u = $db->prepare("UPDATE banners SET badge = :badge WHERE id = :id");
        $u->execute(['badge' => $cleanBadge, 'id' => $r['id']]);
        echo "Updated banner {$r['id']}: {$cleanBadge}\n";
    }
}
echo "Done!\n";
