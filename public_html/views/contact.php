<?php
include INCLUDES_PATH . '/header.php';

$heroBadge    = 'GET IN TOUCH';
$heroTitle    = 'Contact MD Printers';
$heroSubtitle = 'Speak directly with our press advisors, visit our facility, or send an enquiry.';
$heroIcon     = 'fas fa-envelope';
$breadcrumb   = ['Contact Us' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px;">
            <div>
                <span class="section-tag"><i class="fas fa-paper-plane"></i> SEND ENQUIRY</span>
                <h2 style="font-size: 2rem; font-weight: 900; color: #0f172a; margin-bottom: 20px;">Send Us a Message</h2>
                
                <div style="background: #ffffff; padding: 32px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                    <form action="<?= SITE_URL ?>/api/contact" method="POST" class="ajax-form">
                        <?= csrf_field() ?>
                        <div class="form-alert" style="display: none; padding: 16px; border-radius: 10px; margin-bottom: 20px;"></div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Your Name *</label>
                            <input type="text" name="customer_name" class="form-control" required placeholder="John Doe" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Email Address *</label>
                            <input type="email" name="email" class="form-control" required placeholder="john@example.com" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Phone / WhatsApp *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+91 81461 41450" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Message *</label>
                            <textarea name="message" rows="5" class="form-control" required placeholder="How can our press team help your brand?" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; padding: 14px; font-weight: 800; font-size: 1rem;"><i class="fas fa-paper-plane"></i> Send Message</button>
                    </form>
                </div>
            </div>

            <div>
                <span class="section-tag"><i class="fas fa-map-marked-alt"></i> VISIT OUR PRESS</span>
                <h2 style="font-size: 2rem; font-weight: 900; color: #0f172a; margin-bottom: 20px;">Facility & Location</h2>
                
                <div style="background: #f8fafc; padding: 32px; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
                    <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(0,133,178,0.1); color: var(--cmyk-cyan); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 4px;">Press Facility Address</strong>
                            <span style="color: #475569; font-size: 0.95rem; line-height: 1.6;"><?= e(Setting::get('business_address')) ?></span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(238,0,131,0.1); color: var(--cmyk-magenta); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 4px;">Email Support</strong>
                            <span style="color: #475569; font-size: 0.95rem;"><?= e(Setting::get('contact_email')) ?></span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(201,158,80,0.1); color: var(--cmyk-gold); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 4px;">Phone Hotline Numbers</strong>
                            <span style="color: #475569; font-size: 0.95rem; font-weight: 700;">
                                <a href="tel:8146141450" style="color: var(--cmyk-cyan); text-decoration: none;">+91 81461 41450</a> &nbsp;|&nbsp; 
                                <a href="tel:9878629214" style="color: var(--cmyk-magenta); text-decoration: none;">+91 98786 29214</a>
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(16,185,129,0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 4px;">Working Hours</strong>
                            <span style="color: #475569; font-size: 0.95rem;"><?= e(Setting::get('business_hours')) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Embed Container -->
                <div style="height: 250px; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                    <iframe src="<?= e(Setting::get('google_maps_embed')) ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

