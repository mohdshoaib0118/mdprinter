<?php
/**
 * MD Printers Admin - Standalone Banners Delete Redirect
 */
require_once dirname(dirname(__DIR__)) . '/config/config.php';
$id = (int)($_GET['id'] ?? 0);
header('Location: ' . ADMIN_URL . '?view=banners&action=delete' . ($id > 0 ? '&id=' . $id : ''));
exit;
