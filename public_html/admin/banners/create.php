<?php
/**
 * MD Printers Admin - Standalone Banners Create Redirect
 */
require_once dirname(dirname(__DIR__)) . '/config/config.php';
header('Location: ' . ADMIN_URL . '?view=banners&action=create');
exit;
