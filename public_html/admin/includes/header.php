<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle ?? 'CMS Portal') ?> | MD Printers Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<?php include ADMIN_PATH . '/includes/sidebar.php'; ?>
<div class="admin-main">
    <div class="admin-topbar">
        <div class="admin-topbar-title">
            <i class="fas fa-sliders-h" style="color: var(--cmyk-cyan);"></i>
            <span><?= e($adminTitle ?? 'Dashboard Overview') ?></span>
        </div>
        <div class="admin-topbar-actions">
            <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-outline btn-sm" style="border-color: #cbd5e1; color: #334155; font-weight: 700;">
                <i class="fas fa-globe"></i> View Live Site
            </a>
            <div style="font-size: 0.88rem; color: #334155; font-weight: 700; display: flex; align-items: center; gap: 6px; background: #f1f5f9; padding: 6px 14px; border-radius: 20px; border: 1px solid #e2e8f0;">
                <i class="fas fa-user-circle" style="color: var(--cmyk-magenta); font-size: 1.1rem;"></i> 
                <span><?= e($_SESSION['admin_name'] ?? 'MD Admin') ?></span>
            </div>
            <a href="<?= ADMIN_URL ?>/logout.php" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 800;" title="Logout">
                <i class="fas fa-power-off"></i> Logout
            </a>
        </div>
    </div>
    <div class="admin-content">

