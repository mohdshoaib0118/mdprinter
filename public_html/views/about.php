<?php 
include INCLUDES_PATH . '/header.php'; 

$heroBadge    = 'ABOUT MD PRINTERS';
$heroTitle    = 'Engineered for Printing Excellence';
$heroSubtitle = 'We bridge traditional craftsmanship with modern automated presses to power corporate & retail brands.';
$heroIcon     = 'fas fa-industry';
$breadcrumb   = ['About Us' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<!-- Company Story & Mission -->
<section style="padding: 80px 0; background: #ffffff;">
    <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
        <div>
            <span class="section-tag"><i class="fas fa-history"></i> OUR JOURNEY & CAPABILITIES</span>
            <h2 style="font-size: 2.2rem; font-weight: 900; color: #0f172a; margin-bottom: 20px;">18+ Years of Commercial Printing Mastery</h2>
            <p style="color: #475569; font-size: 1.05rem; line-height: 1.8; margin-bottom: 20px;">
                Founded with a passion for precise ink density and packaging structural integrity, <strong>MD Printers</strong> has grown into a trusted high-volume print production house serving over 3,400 corporate and retail clients across India.
            </p>
            <p style="color: #475569; font-size: 1.05rem; line-height: 1.8; margin-bottom: 30px;">
                We house multi-color offset printing presses, digital press suites, automatic die-cutting machines, and a dedicated hand-assembly unit. From 100 gold foil business cards to 500,000 custom packaging boxes, every order is executed with G7 color consistency.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="background: #f8fafc; padding: 22px; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <h4 style="color: var(--cmyk-cyan); font-weight: 800; font-size: 1.1rem; margin-bottom: 8px;"><i class="fas fa-eye"></i> Our Vision</h4>
                    <p style="font-size: 0.9rem; color: #64748b; margin: 0; line-height: 1.6;">To remain India's premier commercial printing press, setting the benchmark for precision and speed.</p>
                </div>

                <div style="background: #f8fafc; padding: 22px; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <h4 style="color: var(--cmyk-magenta); font-weight: 800; font-size: 1.1rem; margin-bottom: 8px;"><i class="fas fa-bullseye"></i> Our Mission</h4>
                    <p style="font-size: 0.9rem; color: #64748b; margin: 0; line-height: 1.6;">To provide sharp print quality, eco-conscious paper choices, transparent rates, and fast door delivery.</p>
                </div>
            </div>
        </div>

        <div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.06); padding: 12px;">
                <img src="<?= media_url('assets/images/services/offset-printing.jpg') ?>" alt="MD Printers Facility" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="width: 100%; height: 380px; object-fit: cover; border-radius: 14px;">
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section style="padding: 50px 0; background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; text-align: center;">
            <div>
                <strong style="font-size: 2.5rem; font-weight: 900; color: var(--cmyk-cyan); display: block;">18+</strong>
                <span style="font-size: 0.9rem; font-weight: 700; color: #475569;">Years Experience</span>
            </div>
            <div>
                <strong style="font-size: 2.5rem; font-weight: 900; color: var(--cmyk-magenta); display: block;">25,000+</strong>
                <span style="font-size: 0.9rem; font-weight: 700; color: #475569;">Projects Delivered</span>
            </div>
            <div>
                <strong style="font-size: 2.5rem; font-weight: 900; color: var(--cmyk-gold); display: block;">3,400+</strong>
                <span style="font-size: 0.9rem; font-weight: 700; color: #475569;">Corporate Clients</span>
            </div>
            <div>
                <strong style="font-size: 2.5rem; font-weight: 900; color: #10B981; display: block;">99.8%</strong>
                <span style="font-size: 0.9rem; font-weight: 700; color: #475569;">Color Accuracy</span>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

