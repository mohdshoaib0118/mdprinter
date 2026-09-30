<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once INCLUDES_PATH . '/functions.php';
require_once CONFIG_PATH . '/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . ADMIN_URL);
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'CSRF validation failed. Please refresh.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $error = 'Please fill in both email and password.';
        } else {
            $db = Database::getInstance();
            if ($db) {
                try {
                    $stmt = $db->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
                    $stmt->execute(['email' => $email]);
                    $admin = $stmt->fetch();

                    // Allow fallback default login if DB seed password matches or default password 'admin123'
                    $validPass = false;
                    if ($admin) {
                        if (password_verify($password, $admin['password']) || $password === 'admin123') {
                            $validPass = true;
                        }
                    } elseif ($email === 'admin@mdprinters.com' && $password === 'admin123') {
                        $admin = ['id' => 1, 'name' => 'MD Printers Admin', 'email' => $email, 'role' => 'superadmin'];
                        $validPass = true;
                    }

                    if ($validPass) {
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['admin_role'] = $admin['role'];

                        header('Location: ' . ADMIN_URL);
                        exit;
                    } else {
                        $error = 'Invalid admin email or password.';
                    }
                } catch (PDOException $e) {
                    $error = 'Database authentication error.';
                }
            } else {
                // Offline mock fallback login
                if ($email === 'admin@mdprinters.com' && $password === 'admin123') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = 1;
                    $_SESSION['admin_name'] = 'MD Printers Admin';
                    $_SESSION['admin_email'] = $email;
                    $_SESSION['admin_role'] = 'superadmin';

                    header('Location: ' . ADMIN_URL);
                    exit;
                } else {
                    $error = 'Invalid offline credentials.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MD Printers CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body style="background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">

<div style="width: 100%; max-width: 440px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 40px; box-shadow: 0 15px 45px rgba(0,0,0,0.06);">
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="<?= SITE_URL ?>/" class="brand-logo" style="display: flex; align-items: center; justify-content: center; gap: 12px; text-decoration: none; margin-bottom: 20px;">
            <img src="<?= SITE_URL ?>/assets/images/logo-official.png" alt="MD Printers" style="height: 54px; width: 54px; object-fit: contain;">
            <div style="display: flex; flex-direction: column; line-height: 1.1; text-align: left;">
                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.35rem; font-weight: 900; color: #0f172a; letter-spacing: 0.5px; text-transform: uppercase;">
                    MD <span style="color: var(--cmyk-cyan, #00aeef);">PRINTERS</span>
                </span>
                <span style="font-size: 0.65rem; font-weight: 800; color: #64748b; letter-spacing: 1.4px; text-transform: uppercase;">
                    CMS Control Portal
                </span>
            </div>
        </a>
        <h2 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">CMS Portal Login</h2>
        <p style="font-size: 0.88rem; color: #64748b; margin: 0;">Sign in to manage print catalogue, quotes & content.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; padding: 12px; border-radius: 10px; font-size: 0.88rem; font-weight: 700; margin-bottom: 20px; text-align: center;">
            <i class="fas fa-exclamation-triangle"></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 18px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Admin Email</label>
            <input type="email" name="email" value="admin@mdprinters.com" class="form-control" required placeholder="admin@mdprinters.com">
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Password</label>
            <input type="password" name="password" value="admin123" class="form-control" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; font-weight: 800; border-radius: 10px;">
            <i class="fas fa-sign-in-alt"></i> Login to CMS Dashboard
        </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 0.82rem; color: #64748b; background: #f1f5f9; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0;">
        Default Admin Credentials: <strong style="color: #0f172a;">admin@mdprinters.com</strong> / <strong style="color: #0f172a;">admin123</strong>
    </div>
</div>

</body>

</html>
