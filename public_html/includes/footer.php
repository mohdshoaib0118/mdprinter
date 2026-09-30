<?php require_once INCLUDES_PATH . '/whatsapp-cta.php'; ?>
<?php require_once INCLUDES_PATH . '/modals.php'; ?>

<!-- Site Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="<?= SITE_URL ?>/" class="brand-logo" style="margin-bottom: 20px;">
                    <img src="<?= SITE_URL ?>/assets/images/logo-footer.svg" alt="MD Printers - Design Print Advertising" class="brand-logo-img" style="height: 52px;">
                </a>
                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px;">
                    Engineering high-precision commercial offset printing, luxury rigid packaging, variable digital press runs, and corporate advertising with G7 certified color density and ultra-fast turnarounds.
                </p>
                <div class="footer-social-group" style="display: flex; gap: 12px; align-items: center; margin-top: 20px;">
                    <?php 
                        $fbUrl = Setting::get('facebook_url', '#');
                        $instaUrl = Setting::get('instagram_url', '#');
                        $linkedinUrl = Setting::get('linkedin_url', '#');
                        $twitterUrl = Setting::get('twitter_url', '');
                        $ytUrl = Setting::get('youtube_url', '');
                        $waUrl = Setting::get('whatsapp_url', '');
                        if (empty($waUrl) && Setting::get('whatsapp_number')) {
                            $waUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', Setting::get('whatsapp_number'));
                        }
                    ?>
                    <a href="<?= e($fbUrl ?: '#') ?>" target="_blank" class="footer-social-btn social-fb" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= e($instaUrl ?: '#') ?>" target="_blank" class="footer-social-btn social-insta" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?= e($linkedinUrl ?: '#') ?>" target="_blank" class="footer-social-btn social-linkedin" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php if (!empty($twitterUrl)): ?>
                        <a href="<?= e($twitterUrl) ?>" target="_blank" class="footer-social-btn social-twitter" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($ytUrl)): ?>
                        <a href="<?= e($ytUrl) ?>" target="_blank" class="footer-social-btn social-yt" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($waUrl)): ?>
                        <a href="<?= e($waUrl) ?>" target="_blank" class="footer-social-btn social-wa" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h4 class="footer-col-title" style="color: #ffffff;">Quick Navigation</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/about" style="color: #94a3b8;">About Company</a></li>
                    <li><a href="<?= SITE_URL ?>/services" style="color: #94a3b8;">Printing Services</a></li>
                    <li><a href="<?= SITE_URL ?>/products" style="color: #94a3b8;">Products Catalog</a></li>
                    <li><a href="<?= SITE_URL ?>/portfolio" style="color: #94a3b8;">Print Portfolio</a></li>
                    <li><a href="<?= SITE_URL ?>/blog" style="color: #94a3b8;">Technical Blog</a></li>
                    <li><a href="<?= SITE_URL ?>/faq" style="color: #94a3b8;">FAQ Support</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-col-title" style="color: #ffffff;">Major Capabilities</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/services/printing" style="color: #94a3b8;">Commercial Offset Press</a></li>
                    <li><a href="<?= SITE_URL ?>/services/visiting-cards" style="color: #94a3b8;">Gold Foil Business Cards</a></li>
                    <li><a href="<?= SITE_URL ?>/services/corporate-printing" style="color: #94a3b8;">Rigid & Folding Packaging</a></li>
                    <li><a href="<?= SITE_URL ?>/services/t-shirt-printing" style="color: #94a3b8;">Custom Corporate Apparel</a></li>
                    <li><a href="<?= SITE_URL ?>/services/acrylic-boards" style="color: #94a3b8;">3D LED Acrylic Signage</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-col-title" style="color: #ffffff;">Contact & Support</h4>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 12px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--cmyk-cyan); margin-right: 8px;"></i>
                    <?= e(Setting::get('business_address', 'Plot No. 248, Industrial Area Phase 1, Panchkula, Haryana 134113')) ?>
                </p>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 12px;">
                    <i class="fas fa-envelope" style="color: var(--cmyk-magenta); margin-right: 8px;"></i>
                    <?= e(Setting::get('contact_email', 'info@mdprinters.com')) ?>
                </p>
                <p style="color: #94a3b8; font-size: 0.95rem; margin-bottom: 20px;">
                    <i class="fas fa-phone-alt" style="color: var(--cmyk-yellow); margin-right: 8px;"></i>
                    <a href="tel:8146141450" style="color: #ffffff; font-weight: 700;">+91 81461 41450</a> | <a href="tel:9878629214" style="color: #ffffff; font-weight: 700;">+91 98786 29214</a>
                </p>
                <button type="button" data-modal="quick-quote" class="btn btn-magenta btn-sm" style="width: 100%;"><i class="fas fa-file-signature"></i> Start Quotation</button>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; <?= date('Y') ?> MD Printers. All Rights Reserved. G7 Certified Press Suite.</div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px 20px; justify-content: center;">
                <a href="<?= SITE_URL ?>/privacy-policy">Privacy Policy</a>
                <a href="<?= SITE_URL ?>/terms">Terms of Service</a>
                <a href="<?= SITE_URL ?>/sitemap.xml" target="_blank">Sitemap</a>
                <a href="<?= SITE_URL ?>/admin" style="color: var(--cmyk-cyan);"><i class="fas fa-lock"></i> Admin Portal</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
