<?php
include INCLUDES_PATH . '/header.php';
?>

<section style="padding: 120px 0; text-align: center;">
    <div class="container">
        <span style="font-size: 6rem; font-weight: 800; color: var(--cmyk-cyan); line-height: 1; display: block; margin-bottom: 20px;">404</span>
        <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 16px;">Page Not Found</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 500px; margin: 0 auto 30px auto;">
            The requested printing page or service specification link does not exist or has been moved.
        </p>
        <a href="<?= SITE_URL ?>/" class="btn btn-primary btn-lg"><i class="fas fa-home"></i> Return to Homepage</a>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
