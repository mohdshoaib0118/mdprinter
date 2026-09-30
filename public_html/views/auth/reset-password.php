<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'NEW PASSWORD';
$heroTitle    = 'Set New Password';
$heroSubtitle = 'Create a strong replacement password for your MD Printers B2B customer account.';
$heroIcon     = 'fas fa-shield-alt';
$breadcrumb   = ['Reset Password' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$flash = get_flash();
?>

<section style="padding: 70px 0; background: #ffffff;">
    <div class="container" style="max-width: 480px; margin: 0 auto;">
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 24px; padding: 40px; box-shadow: var(--shadow-xl);">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <h2 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 4px;">Choose New Password</h2>
                <p style="color: #64748b; font-size: 0.88rem;">Password must be at least 6 characters long</p>
            </div>

            <?php if ($flash): ?>
                <div class="form-alert alert-<?= e($flash['type']) ?>" style="display: block; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.88rem;">
                    <i class="fas fa-exclamation-triangle"></i> <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= SITE_URL ?>/reset-password" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">New Password *</label>
                    <input type="password" name="password" required placeholder="Min 6 characters" class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label">Confirm New Password *</label>
                    <input type="password" name="confirm_password" required placeholder="Repeat new password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 900; font-size: 1rem; padding: 14px;">
                    <i class="fas fa-save"></i> Save New Password & Login
                </button>
            </form>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
