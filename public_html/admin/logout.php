<?php
require_once dirname(__DIR__) . '/config/config.php';
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_role']);
session_destroy();

header('Location: ' . ADMIN_URL . '/login.php');
exit;
