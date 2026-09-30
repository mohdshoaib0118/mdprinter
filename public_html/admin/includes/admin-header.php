<?php
/**
 * MD Printers - Admin Top Header Layout
 */
$adminUser = current_admin_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle ?? 'Admin Dashboard') ?> | MD Printers CMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

    <?php include __DIR__ . '/admin-sidebar.php'; ?>

    <div class="admin-wrapper">
        <header class="admin-header">
            <div>
                <h3 style="margin: 0; font-size: 1.1rem;"><?= e($adminTitle ?? 'Dashboard Overview') ?></h3>
            </div>

            <div style="display: flex; align-items: center; gap: 20px;">
                <a href="<?= SITE_URL ?>" target="_blank" class="btn-admin btn-admin-outline">
                    <i class="fas fa-globe"></i> View Website
                </a>
                <div style="font-size: 0.9rem;">
                    <i class="fas fa-user-circle" style="color: var(--admin-primary); margin-right: 6px;"></i>
                    <strong><?= e($adminUser['name'] ?? 'Admin') ?></strong>
                </div>
            </div>
        </header>

        <main class="admin-content">
