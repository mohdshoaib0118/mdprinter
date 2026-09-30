<?php
/**
 * MD Printers - Delete Product Handler
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $db = getDB();
    $db->delete('products', 'id = :id', ['id' => $id]);
}

header('Location: ' . ADMIN_URL . '/products/index.php');
exit;
