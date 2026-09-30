<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'B2B MEMBER LOGIN';
$heroTitle    = 'Member Portal Login';
$heroSubtitle = 'Access your order history, dynamic trade rates, artwork proofs, and account credit lines.';
$heroIcon     = 'fas fa-lock';
$breadcrumb   = ['Login' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';

$flash = get_flash();
?>

<section style="padding: 70px 0; background: #ffffff;">
    <div class="container" style="max-width: 520px; margin: 0 auto;">
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 24px; padding: 40px; box-shadow: var(--shadow-xl);">
            
            <div style="text-align: center; margin-bottom: 28px;">
                <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(0, 168, 232, 0.1); color: var(--cmyk-cyan); display: inline-flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 12px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 style="font-size: 1.65rem; font-weight: 900; color: #0f172a; margin-bottom: 4px;">Welcome Back</h2>
                <p style="color: #64748b; font-size: 0.9rem;">Sign in to your approved MD Printers B2B portal</p>
            </div>

            <?php if ($flash): 
                $fType = $flash['type'] ?? 'info';
                $fBg = ($fType === 'success') ? '#dcfce7' : (($fType === 'danger' || $fType === 'error') ? '#fee2e2' : '#fef3c7');
                $fColor = ($fType === 'success') ? '#15803d' : (($fType === 'danger' || $fType === 'error') ? '#991b1b' : '#92400e');
                $fBorder = ($fType === 'success') ? '#bbf7d0' : (($fType === 'danger' || $fType === 'error') ? '#fecaca' : '#fef08a');
                $fIcon = ($fType === 'success') ? 'fa-check-circle' : (($fType === 'danger' || $fType === 'error') ? 'fa-exclamation-triangle' : 'fa-clock');
            ?>
                <div style="background: <?= $fBg ?>; color: <?= $fColor ?>; border: 1px solid <?= $fBorder ?>; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 0.88rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <i class="fas <?= $fIcon ?>" style="font-size: 1.1rem; flex-shrink: 0;"></i> 
                    <div><?= e($flash['message']) ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= SITE_URL ?>/login" method="POST">
                <?= csrf_field() ?>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Email Address or Mobile Number *</label>
                    <input type="text" name="email_or_mobile" required placeholder="e.g. rahul@company.com / +91 9876543210" class="form-control" value="<?= e($_POST['email_or_mobile'] ?? '') ?>">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" style="margin-bottom: 0;">Password *</label>
                        <a href="<?= SITE_URL ?>/forgot-password" style="font-size: 0.82rem; color: var(--cmyk-cyan); font-weight: 700;">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" required placeholder="Enter account password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 900; font-size: 1.05rem; padding: 14px;">
                    <i class="fas fa-sign-in-alt"></i> Log In to Customer Portal
                </button>

                <div style="text-align: center; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 0.9rem; color: #64748b;">
                    Don't have a B2B printing account? <br>
                    <a href="<?= SITE_URL ?>/register" style="color: var(--cmyk-magenta); font-weight: 800; display: inline-block; margin-top: 6px;">Register New B2B Account <i class="fas fa-arrow-right"></i></a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
