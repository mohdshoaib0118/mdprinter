<?php
/**
 * MD Printers Admin - Standalone Banners Index Redirect
 */
require_once dirname(dirname(__DIR__)) . '/config/config.php';
header('Location: ' . ADMIN_URL . '?view=banners');
exit;
