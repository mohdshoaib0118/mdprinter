<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'PASSWORD RECOVERY';
$heroTitle    = 'Forgot Account Password';
$heroSubtitle = 'Enter your registered email address to receive a secure password reset link.';
$heroIcon     = 'fas fa-key';
$breadcrumb   = ['Forgot Password' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';

$flash = get_flash();
?>

<section style="padding: 70px 0; background: #ffffff;">
    <div class="container" style="max-width: 480px; margin: 0 auto;">
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 24px; padding: 40px; box-shadow: var(--shadow-xl);">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(230, 0, 103, 0.1); color: var(--cmyk-magenta); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
                    <i class="fas fa-unlock-alt"></i>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 4px;">Reset Password</h2>
                <p style="color: #64748b; font-size: 0.88rem;">We will generate a 1-hour secure password reset link</p>
            </div>

            <?php if ($flash): ?>
                <div class="form-alert alert-<?= e($flash['type']) ?>" style="display: block; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.88rem;">
                    <i class="fas fa-info-circle"></i> <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= SITE_URL ?>/forgot-password" method="POST">
                <?= csrf_field() ?>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label">Registered Account Email *</label>
                    <input type="email" name="email" required placeholder="rahul@company.com" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 900; font-size: 1rem; padding: 14px;">
                    <i class="fas fa-paper-plane"></i> Send Password Reset Link
                </button>

                <div style="text-align: center; margin-top: 24px; font-size: 0.9rem;">
                    Remembered your password? <a href="<?= SITE_URL ?>/login" style="color: var(--cmyk-cyan); font-weight: 800;">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
