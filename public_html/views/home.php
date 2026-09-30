<?php
include INCLUDES_PATH . '/header.php';

$stats = [
    ['counter_value' => '18+', 'title' => 'Years of Printing Mastery', 'icon' => 'fas fa-award'],
    ['counter_value' => '25,000+', 'title' => 'Print Projects Delivered', 'icon' => 'fas fa-boxes'],
    ['counter_value' => '3,400+', 'title' => 'Corporate & Retail Clients', 'icon' => 'fas fa-building'],
    ['counter_value' => '99.8%', 'title' => 'G7 Certified Color Accuracy', 'icon' => 'fas fa-print']
];

$featuredServices = Service::getAll(['is_featured' => 1, 'status' => 'published']);
$featuredProducts = Product::getAll(['status' => 'published'], 3);
$testimonials = Testimonial::getAll();

// Product rating & badge metadata
$productMeta = [
    'visiting-cards'           => ['rating' => '4.9 (420+ reviews)', 'badge' => 'Best Seller'],
    't-shirt-printing'         => ['rating' => '4.8 (310+ reviews)', 'badge' => 'Express 24h'],
    'mug-printing'             => ['rating' => '4.9 (190+ reviews)', 'badge' => 'Popular Gift'],
    'packaging-boxes'          => ['rating' => '5.0 (150+ reviews)', 'badge' => 'Custom Die-Cut'],
    'gold-foil-business-cards' => ['rating' => '4.9 (510+ reviews)', 'badge' => 'Executive Gold'],
    'acrylic-boards'           => ['rating' => '4.9 (85+ reviews)', 'badge' => 'LED Backlit'],
];
?>

<!-- HERO BANNER SLIDER CAROUSEL -->
<?php require_once INCLUDES_PATH . '/hero-banner-slider.php'; ?>

<!-- 1. HERO SECTION -->
<section class="hero-section" style="padding: 80px 0 70px 0; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="hero-layout">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(0, 168, 232, 0.08); border: 1px solid rgba(0, 168, 232, 0.22); padding: 6px 16px; border-radius: 20px; color: var(--cmyk-cyan); font-size: 0.82rem; font-weight: 800; text-transform: uppercase; margin-bottom: 22px; letter-spacing: 0.5px;">
                    <i class="fas fa-certificate text-cyan"></i> India's Premier Commercial Print & Packaging Press
                </div>
                
                <h1 class="hero-headline" style="color: #0f172a; font-size: 3.4rem; font-weight: 900; line-height: 1.12; letter-spacing: -1.2px; margin-bottom: 22px;">
                    Print That Makes Your Brand <span class="text-gradient">Impossible to Ignore.</span>
                </h1>
                
                <p class="hero-description" style="color: #475569; font-size: 1.15rem; line-height: 1.75; margin-bottom: 32px; max-width: 640px;">
                    From executive visiting cards and custom rigid packaging to corporate apparel, ceramic mugs, PVC ID cards, and 3D LED acrylic boards — we engineer press quality with 24-hour delivery.
                </p>

                <!-- Value Props Bullet Strip -->
                <div style="display: flex; flex-wrap: wrap; gap: 14px 22px; margin-bottom: 38px; font-size: 0.9rem; font-weight: 700; color: #334155;">
                    <span style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10B981; font-size: 1.05rem;"></i> 100% Color Match Guarantee</span>
                    <span style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10B981; font-size: 1.05rem;"></i> Free Prepress Proofing</span>
                    <span style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-check-circle" style="color: #10B981; font-size: 1.05rem;"></i> Nationwide Doorstep Dispatch</span>
                </div>

                <div class="hero-ctas" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                    <a href="<?= SITE_URL ?>/request-quote" class="btn btn-primary btn-lg"><i class="fas fa-calculator"></i> Get Instant Quote</a>
                    <a href="tel:8146141450" class="btn btn-outline btn-lg" style="border-color: var(--cmyk-cyan); color: var(--cmyk-cyan);"><i class="fas fa-phone-alt"></i> Call: +91 81461 41450</a>
                </div>
            </div>

            <!-- Hero Stacked Cards Preview -->
            <div class="hero-visual-box" style="position: relative;">
                <div class="hero-mockup-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 24px; padding: 16px; box-shadow: var(--shadow-xl);">
                    <img src="<?= media_url('assets/images/services/offset-printing.jpg') ?>" alt="MD Printers Production Facility" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="border-radius: 16px; width: 100%; height: 380px; object-fit: cover;">
                </div>

                <div class="hero-badge-floating badge-1" style="top: -20px; left: -20px; background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; color: #0f172a; box-shadow: var(--shadow-lg); padding: 14px 20px; border-radius: 16px;">
                    <i class="fas fa-bolt" style="color: var(--cmyk-magenta); font-size: 1.6rem;"></i>
                    <div>
                        <strong style="display: block; font-size: 0.92rem; color: #0f172a; font-weight: 800;">24-Hour Express Run</strong>
                        <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Same-Day Production Available</span>
                    </div>
                </div>

                <div class="hero-badge-floating badge-2" style="bottom: -20px; right: -20px; background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; color: #0f172a; box-shadow: var(--shadow-lg); padding: 14px 20px; border-radius: 16px;">
                    <i class="fas fa-star" style="color: var(--cmyk-gold); font-size: 1.6rem;"></i>
                    <div>
                        <strong style="display: block; font-size: 0.92rem; color: #0f172a; font-weight: 800;">4.9 / 5 Rating</strong>
                        <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">3,400+ Corporate Press Clients</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. POPULAR PRODUCT CARDS -->
<section style="padding: 80px 0; background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 44px; flex-wrap: wrap; gap: 16px;">
            <div>
                <span class="section-tag"><i class="fas fa-fire" style="color: var(--cmyk-magenta);"></i> TOP CATALOGUE PRODUCTS</span>
                <h2 class="section-title" style="margin-bottom: 0;">Popular Print Products</h2>
            </div>

            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <!-- Layout View Switcher -->
                <div class="view-toggle-group">
                    <button type="button" class="view-toggle-btn active" data-grid="grid-3" title="3 Columns View"><i class="fas fa-th-large"></i> 3 Grid</button>
                    <button type="button" class="view-toggle-btn" data-grid="grid-4" title="4 Columns View"><i class="fas fa-th"></i> 4 Grid</button>
                    <button type="button" class="view-toggle-btn" data-grid="view-list" title="List View"><i class="fas fa-list"></i> List View</button>
                </div>
                <a href="<?= SITE_URL ?>/products" class="btn btn-outline"><i class="fas fa-boxes"></i> Full Catalogue</a>
            </div>
        </div>

        <div class="grid-3 product-grid-container" id="homeProductGrid">
            <?php foreach ($featuredProducts as $prod): 
                $slug = e($prod['slug']);
                $pm = $productMeta[$slug] ?? ['rating' => '4.9 (120+ reviews)', 'badge' => 'Popular'];
            ?>
                <div class="card-feature" style="background: #ffffff; padding: 20px; border: 1px solid #cbd5e1; border-radius: 20px; box-shadow: var(--shadow-md); display: flex; flex-direction: column;">
                    <a href="<?= SITE_URL ?>/order/product/<?= $slug ?>" style="position: relative; height: 210px; border-radius: 14px; overflow: hidden; margin-bottom: 18px; background: #f1f5f9; flex-shrink: 0; display: block;">
                        <img src="<?= media_url($prod['main_image']) ?>" alt="<?= e($prod['name']) ?>" onerror="this.onerror=null; this.src='<?= SITE_URL ?>/assets/images/placeholder.svg';" style="width: 100%; height: 100%; object-fit: cover;">
                        <span style="position: absolute; top: 12px; right: 12px; background: var(--cmyk-cyan); color: #fff; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 14px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <?= e($pm['badge']) ?>
                        </span>
                    </a>

                    <div class="card-feature-body" style="display: flex; flex-direction: column; flex-grow: 1;">
                        <div style="font-size: 0.78rem; color: #eab308; font-weight: 800; margin-bottom: 8px;">
                            <i class="fas fa-star"></i> <?= e($pm['rating']) ?>
                        </div>

                        <h3 style="font-size: 1.18rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; line-height: 1.35;">
                            <a href="<?= SITE_URL ?>/order/product/<?= $slug ?>" style="text-decoration: none; color: inherit;"><?= e($prod['name']) ?></a>
                        </h3>
                        <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin-bottom: 20px; flex-grow: 1; height: 42px; overflow: hidden;"><?= e($prod['short_desc']) ?></p>

                        <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                            <div>
                                <span style="font-size: 0.8rem; color: var(--cmyk-cyan); font-weight: 800; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-check-circle"></i> Custom Press Run
                                </span>
                            </div>
                            <a href="<?= SITE_URL ?>/order/product/<?= $slug ?>" class="btn btn-primary btn-sm"><i class="fas fa-cog"></i> Configure & Order</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 42px;">
            <a href="<?= SITE_URL ?>/products" class="btn btn-primary btn-lg">
                <i class="fas fa-boxes"></i> Explore All Products Catalog <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- 4. INSTANT PRINT PRICE CALCULATOR WIDGET -->
<section style="padding: 80px 0; background: #090d16; border-bottom: 1px solid #1e293b;">
    <div class="container">
        <div class="grid-estimator-card">
            <div>
                <span style="background: rgba(0, 174, 239, 0.2); color: #00aeef; border: 1px solid rgba(0, 174, 239, 0.4); font-size: 0.8rem; font-weight: 800; padding: 6px 16px; border-radius: 20px; text-transform: uppercase; display: inline-block; margin-bottom: 18px; letter-spacing: 1px;">
                    <i class="fas fa-calculator"></i> INSTANT PRICE ESTIMATOR
                </span>
                <h2 style="font-size: 2.4rem; font-weight: 900; margin-bottom: 16px; line-height: 1.2; color: #ffffff;">Get an Instant Estimate for Your Print Run</h2>
                <p style="color: #94a3b8; font-size: 1.05rem; line-height: 1.7; margin-bottom: 24px;">
                    Select your product parameters to estimate pricing. Receive formal digital press proofs and exact G7 print quotes within minutes.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 10px 22px; font-size: 0.9rem; color: #cbd5e1; margin-bottom: 24px; font-weight: 600;">
                    <span><i class="fas fa-check-circle" style="color: var(--cmyk-cyan, #00aeef);"></i> Zero Setup Fee</span>
                    <span><i class="fas fa-check-circle" style="color: var(--cmyk-magenta, #ec008c);"></i> Free File Check</span>
                    <span><i class="fas fa-check-circle" style="color: var(--cmyk-yellow, #fff200);"></i> Doorstep Delivery</span>
                </div>
            </div>

            <div class="estimator-form-card">
                <form action="<?= SITE_URL ?>/request-quote" method="GET">
                    <div class="form-group" style="margin-bottom: 18px;">
                        <label class="estimator-label"><i class="fas fa-layer-group" style="color: #00aeef;"></i> Select Product Category</label>
                        <select name="service" class="estimator-select">
                            <option value="Visiting Cards">Executive Visiting Cards</option>
                            <option value="T-Shirt Printing">Custom T-Shirt Printing</option>
                            <option value="Mug Printing">Custom Ceramic Mugs</option>
                            <option value="Packaging & Boxes">Custom Rigid Packaging Boxes</option>
                            <option value="ID Cards & Lanyards">PVC ID Cards & Satin Lanyards</option>
                            <option value="Acrylic Boards">LED Acrylic Signage Boards</option>
                            <option value="Brochures & Catalogs">Commercial Brochures & Catalogs</option>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 22px;">
                        <div>
                            <label class="estimator-label"><i class="fas fa-boxes" style="color: #ec008c;"></i> Quantity</label>
                            <select name="quantity" class="estimator-select">
                                <option value="100">100 Units</option>
                                <option value="500">500 Units</option>
                                <option value="1000">1,000 Units</option>
                                <option value="5000">5,000+ Bulk Run</option>
                            </select>
                        </div>
                        <div>
                            <label class="estimator-label"><i class="fas fa-magic" style="color: #dfba6e;"></i> Finishing</label>
                            <select name="finish" class="estimator-select">
                                <option value="Velvet + Gold Foil">Velvet Touch + Gold Foil</option>
                                <option value="Gloss Lamination">Gloss Lamination</option>
                                <option value="Matte + 3D Spot UV">Matte + 3D Spot UV</option>
                                <option value="Standard Finish">Standard Finish</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="estimator-btn-submit">
                        <i class="fas fa-calculator"></i> Calculate & Get Formal Quote
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 5. 4-STEP PRODUCTION WORKFLOW -->
<section style="padding: 80px 0; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <span class="section-tag"><i class="fas fa-cogs"></i> HOW IT WORKS</span>
            <h2 class="section-title">4 Simple Steps to Print</h2>
        </div>

        <div class="grid-4">
            <div style="background: #f8fafc; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; position: relative;">
                <span style="width: 48px; height: 48px; border-radius: 50%; background: var(--cmyk-cyan); color: #fff; font-weight: 900; font-size: 1.3rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">1</span>
                <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 8px; color: #0f172a;">Select & Upload</h4>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin: 0;">Choose product specs, quantity, and upload your vector PDF or artwork file.</p>
            </div>

            <div style="background: #f8fafc; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; position: relative;">
                <span style="width: 48px; height: 48px; border-radius: 50%; background: var(--cmyk-magenta); color: #fff; font-weight: 900; font-size: 1.3rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">2</span>
                <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 8px; color: #0f172a;">Free Proof Check</h4>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin: 0;">Prepress engineers audit bleed lines, CMYK colors, and send digital proof for your approval.</p>
            </div>

            <div style="background: #f8fafc; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; position: relative;">
                <span style="width: 48px; height: 48px; border-radius: 50%; background: var(--cmyk-gold); color: #fff; font-weight: 900; font-size: 1.3rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">3</span>
                <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 8px; color: #0f172a;">Press Run & Finish</h4>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin: 0;">Heidelberg offset or HP Indigo press engines print with gold foiling & spot UV finishes.</p>
            </div>

            <div style="background: #f8fafc; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; position: relative;">
                <span style="width: 48px; height: 48px; border-radius: 50%; background: #10B981; color: #fff; font-weight: 900; font-size: 1.3rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">4</span>
                <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 8px; color: #0f172a;">Doorstep Delivery</h4>
                <p style="font-size: 0.88rem; color: #64748b; line-height: 1.6; margin: 0;">Moisture-proof shrink-wrap packing and express courier dispatch right to your office.</p>
            </div>
        </div>
    </div>
</section>

<!-- 6. VERIFIED CLIENT REVIEWS -->
<section style="padding: 80px 0; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <span class="section-tag"><i class="fas fa-star" style="color: var(--cmyk-gold);"></i> VERIFIED REVIEWS</span>
            <h2 class="section-title">Trusted by Business Leaders</h2>
        </div>

        <div class="grid-3">
            <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card">
                    <div>
                        <div style="color: #eab308; margin-bottom: 14px; font-size: 0.95rem;">
                            <?php for($i=0; $i<$t['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                        </div>
                        <p style="color: #475569; font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px; font-style: italic;">
                            "<?= e($t['testimonial']) ?>"
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 14px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--cmyk-cyan); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            <?= strtoupper(substr($t['client_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 0.95rem; color: #0f172a;"><?= e($t['client_name']) ?></strong>
                            <span style="font-size: 0.8rem; color: #64748b;"><?= e($t['designation']) ?>, <?= e($t['company']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 7. CTA BANNER -->
<section style="padding: 85px 0; background: linear-gradient(135deg, #090d16 0%, #1e293b 100%); color: #ffffff;">
    <div class="container" style="text-align: center;">
        <h2 style="font-size: 2.4rem; font-weight: 900; color: #ffffff; margin-bottom: 16px;">Ready to Start Your Printing Project?</h2>
        <p style="font-size: 1.15rem; color: #cbd5e1; max-width: 620px; margin: 0 auto 32px auto; line-height: 1.6;">Send us your artwork files or quote requirements. Our press team responds within 2 business hours.</p>
        <div style="display: flex; justify-content: center; gap: 18px; flex-wrap: wrap;">
            <a href="<?= SITE_URL ?>/request-quote" class="btn btn-primary btn-lg" style="font-weight: 800; padding: 16px 32px;"><i class="fas fa-file-invoice-dollar"></i> Get Custom Quote</a>
            <a href="https://wa.me/918146141450" target="_blank" class="btn btn-magenta btn-lg" style="font-weight: 800; padding: 16px 32px;"><i class="fab fa-whatsapp"></i> WhatsApp Us Now</a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
