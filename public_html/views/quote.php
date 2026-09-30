<?php
include INCLUDES_PATH . '/header.php';
$selectedProduct = $_GET['product'] ?? '';
$selectedService = $_GET['service'] ?? '';

$heroBadge    = 'INSTANT PRINT ESTIMATOR';
$heroTitle    = 'Request Custom Print Quotation';
$heroSubtitle = 'Select specs, upload artwork (PDF, AI, PSD, ZIP), and receive formal press quotation in 2 business hours.';
$heroIcon     = 'fas fa-calculator';
$breadcrumb   = ['Request Quote' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 40px; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                <form action="<?= SITE_URL ?>/api/quote" method="POST" enctype="multipart/form-data" class="ajax-form">
                    <?= csrf_field() ?>

                    <div class="form-alert" style="display: none; padding: 16px; border-radius: 10px; margin-bottom: 24px;"></div>

                    <!-- Step 1: Product Selection & Specs -->
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; color: var(--cmyk-cyan);">
                        <i class="fas fa-cubes"></i> 1. Printing Requirements
                    </h3>

                    <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Product / Service Name *</label>
                            <input type="text" name="product_service" value="<?= e($selectedProduct ?: $selectedService) ?>" placeholder="e.g. Gold Foil Business Cards, Rigid Box, Brochure" class="form-control" required style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Quantity Needed *</label>
                            <input type="number" name="quantity" placeholder="e.g. 500" class="form-control" min="1" required style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                    </div>



                    <!-- Step 2: Artwork Upload -->
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin-top: 30px; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; color: var(--cmyk-magenta);">
                        <i class="fas fa-file-upload"></i> 2. Artwork File Attachment
                    </h3>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label for="artwork_file" style="display: block; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 32px; text-align: center; cursor: pointer; background: #f8fafc; transition: all 0.2s;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--cmyk-cyan); margin-bottom: 12px; display: block;"></i>
                            <span class="dropzone-filename" style="font-size: 0.95rem; color: #475569; font-weight: 600;">
                                Drag & drop artwork file (Vector PDF, AI, PSD, ZIP up to 15MB) or click to browse
                            </span>
                            <input type="file" name="artwork_file" id="artwork_file" accept=".pdf,.ai,.psd,.eps,.tiff,.zip,.jpg,.png" style="display: none;">
                        </label>
                    </div>

                    <!-- Step 3: Contact Info -->
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin-top: 30px; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; color: var(--cmyk-gold);">
                        <i class="fas fa-user-check"></i> 3. Contact Details
                    </h3>

                    <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Your Full Name *</label>
                            <input type="text" name="customer_name" placeholder="John Doe" class="form-control" required style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Company / Brand Name</label>
                            <input type="text" name="company_name" placeholder="Acme Corporate Ltd" class="form-control" style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                    </div>

                    <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Work Email *</label>
                            <input type="email" name="email" placeholder="john@company.com" class="form-control" required style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Phone / WhatsApp Number *</label>
                            <input type="tel" name="phone" id="quote_phone" placeholder="+91 81461 41450" class="form-control" required style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 28px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Additional Instructions / Project Notes</label>
                        <textarea name="message" rows="4" placeholder="Specify any target delivery deadline, PMS spot color codes, or special packaging assembly requirements..." class="form-control" style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;"></textarea>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; padding: 16px; font-weight: 900; font-size: 1.05rem;">
                            <i class="fas fa-paper-plane"></i> Submit Quotation Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>

