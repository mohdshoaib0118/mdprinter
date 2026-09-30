<?php
/**
 * MD Printers - Complete Interactive Modal Suite
 * Includes: Quick Quote Modal, Callback Modal, Track Order Modal, Sample Kit Modal
 */
?>

<!-- 1. Quick Quote Modal Popup -->
<div id="quickQuoteModal" class="site-modal-backdrop" style="display: none;">
    <div class="site-modal-dialog">
        <div class="site-modal-content">
            <div class="site-modal-header">
                <h3 class="site-modal-title"><i class="fas fa-file-invoice-dollar" style="color: var(--cmyk-cyan);"></i> Request Instant Print Quotation</h3>
                <button type="button" class="site-modal-close" data-close-modal="quickQuoteModal" aria-label="Close modal">&times;</button>
            </div>
            <div class="site-modal-body">
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                    Fill in your project requirements below. Our prepress team will send a custom estimation & launch WhatsApp specs immediately.
                </p>
                <form action="<?= SITE_URL ?>/api/quote" method="POST" enctype="multipart/form-data" class="ajax-form modal-form">
                    <?= csrf_field() ?>
                    <div class="form-alert" style="display: none; padding: 14px; border-radius: 10px; margin-bottom: 18px;"></div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Your Name *</label>
                            <input type="text" name="customer_name" class="form-control" required placeholder="e.g. Rahul Sharma" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Phone / WhatsApp *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+91 81461 41450" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="rahul@company.com" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Product / Service *</label>
                            <select name="product_service" id="modal_product_service" class="form-control" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: #ffffff;">
                                <option value="Visiting Cards">Executive Visiting Cards</option>
                                <option value="Packaging & Rigid Boxes">Custom Packaging & Rigid Boxes</option>
                                <option value="T-Shirt Printing">Corporate T-Shirts & Apparel</option>
                                <option value="Mug Printing">Custom Ceramic Mugs</option>
                                <option value="Acrylic Signage Boards">3D LED Acrylic Boards</option>
                                <option value="Brochures & Catalogs">Brochures & Product Catalogs</option>
                                <option value="Stickers & Labels">Custom Die-Cut Stickers</option>
                                <option value="ID Cards & Lanyards">Corporate ID Cards & Lanyards</option>
                                <option value="General Commercial Press">Other Commercial Printing</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Quantity</label>
                            <input type="number" name="quantity" class="form-control" placeholder="e.g. 500" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Size Specs</label>
                            <input type="text" name="size" class="form-control" placeholder="e.g. A4 / 3.5x2&quot;" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Material / GSM</label>
                            <input type="text" name="material_gsm" class="form-control" placeholder="e.g. 350 GSM Silk" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Artwork Attachment (Optional)</label>
                        <input type="file" name="artwork_file" class="form-control" accept=".pdf,.ai,.psd,.zip,.jpg,.jpeg,.png" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #cbd5e1;">
                        <small style="color: #64748b; font-size: 0.78rem;">Accepted formats: PDF, AI, PSD, ZIP, JPG, PNG (Max: 15MB)</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Additional Notes / Instructions</label>
                        <textarea name="message" rows="3" class="form-control" placeholder="Specify foil stamping, spot UV, lamination type, binding, or urgent delivery date..." style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; padding: 14px; font-weight: 800; font-size: 1rem;"><i class="fas fa-paper-plane"></i> Submit & Launch WhatsApp Specs</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 2. Instant Callback Modal Popup -->
<div id="callbackModal" class="site-modal-backdrop" style="display: none;">
    <div class="site-modal-dialog" style="max-width: 480px;">
        <div class="site-modal-content">
            <div class="site-modal-header" style="background: linear-gradient(135deg, #0f172a, #1e293b);">
                <h3 class="site-modal-title" style="color: #ffffff;"><i class="fas fa-phone-alt" style="color: var(--cmyk-gold);"></i> Request Instant Callback</h3>
                <button type="button" class="site-modal-close" data-close-modal="callbackModal" style="color: #ffffff;" aria-label="Close modal">&times;</button>
            </div>
            <div class="site-modal-body">
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                    Enter your phone number below and our press print specialist will call you immediately to assist with your printing order.
                </p>
                <form action="<?= SITE_URL ?>/api/callback" method="POST" class="ajax-form modal-form">
                    <?= csrf_field() ?>
                    <div class="form-alert" style="display: none; padding: 14px; border-radius: 10px; margin-bottom: 18px;"></div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Your Name *</label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="John Doe" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Phone Number *</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="+91 81461 41450" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Preferred Call Time</label>
                        <select name="preferred_time" class="form-control" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: #ffffff;">
                            <option value="Immediate Call (Within 10 Mins)">⚡ Immediate Call (Within 10 Mins)</option>
                            <option value="Morning (9:00 AM - 12:00 PM)">🌅 Morning (9:00 AM - 12:00 PM)</option>
                            <option value="Afternoon (12:00 PM - 4:00 PM)">☀️ Afternoon (12:00 PM - 4:00 PM)</option>
                            <option value="Evening (4:00 PM - 8:00 PM)">🌆 Evening (4:00 PM - 8:00 PM)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; padding: 14px; font-weight: 800; font-size: 1rem;"><i class="fas fa-headset"></i> Request Call Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 3. Track Order / Enquiry Status Modal Popup -->
<div id="trackOrderModal" class="site-modal-backdrop" style="display: none;">
    <div class="site-modal-dialog" style="max-width: 540px;">
        <div class="site-modal-content">
            <div class="site-modal-header" style="background: linear-gradient(135deg, var(--cmyk-cyan), #005f80);">
                <h3 class="site-modal-title" style="color: #ffffff;"><i class="fas fa-search-location"></i> Track Quote & Order Status</h3>
                <button type="button" class="site-modal-close" data-close-modal="trackOrderModal" style="color: #ffffff;" aria-label="Close modal">&times;</button>
            </div>
            <div class="site-modal-body">
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                    Enter your Reference Number (e.g. <strong>MDQ-2026-000101</strong>) to view live progress from press queue to final delivery.
                </p>
                
                <form id="trackOrderForm" action="<?= SITE_URL ?>/api/track" method="POST" class="modal-form" style="margin-bottom: 24px;">
                    <?= csrf_field() ?>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="ref_no" required placeholder="MDQ-2026-XXXXXX" style="flex: 1; padding: 12px 16px; border-radius: 8px; border: 2px solid var(--cmyk-cyan); outline: none; font-weight: 700; text-transform: uppercase;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 20px;"><i class="fas fa-search"></i> Track</button>
                    </div>
                </form>

                <div id="trackResultContainer" style="display: none; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <!-- Result JSON rendered dynamically via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Request Physical Sample Box Modal Popup -->
<div id="sampleKitModal" class="site-modal-backdrop" style="display: none;">
    <div class="site-modal-dialog" style="max-width: 540px;">
        <div class="site-modal-content">
            <div class="site-modal-header" style="background: linear-gradient(135deg, var(--cmyk-magenta), #990055);">
                <h3 class="site-modal-title" style="color: #ffffff;"><i class="fas fa-box-open"></i> Request Physical Paper & Packaging Sample Box</h3>
                <button type="button" class="site-modal-close" data-close-modal="sampleKitModal" style="color: #ffffff;" aria-label="Close modal">&times;</button>
            </div>
            <div class="site-modal-body">
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                    Get an official MD Printers tactile sample box delivered to your office featuring gold foil cards, rigid boxes, spot UV swatches, and paper GSM samplers.
                </p>
                <form action="<?= SITE_URL ?>/api/sample-kit" method="POST" class="ajax-form modal-form">
                    <?= csrf_field() ?>
                    <div class="form-alert" style="display: none; padding: 14px; border-radius: 10px; margin-bottom: 18px;"></div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Your Name *</label>
                            <input type="text" name="customer_name" class="form-control" required placeholder="Priya Sharma" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+91 98786 29214" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Full Delivery Address *</label>
                        <textarea name="address" rows="3" class="form-control" required placeholder="Plot / Flat No., Building, Street, City, Pincode" style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-magenta btn-lg" style="width: 100%; padding: 14px; font-weight: 800; font-size: 1rem;"><i class="fas fa-shipping-fast"></i> Dispatch Sample Kit Box</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Trigger Button Suite -->
<div class="floating-cta-container">
    <button type="button" data-modal="track-order" class="floating-cta-btn btn-track">
        <i class="fas fa-search-location" style="color: var(--cmyk-yellow);"></i> Track Order
    </button>
    <button type="button" data-modal="quick-quote" class="floating-cta-btn btn-quote">
        <i class="fas fa-bolt" style="color: var(--cmyk-yellow);"></i> Quick Quote
    </button>
</div>

