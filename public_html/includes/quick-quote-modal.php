<?php
/**
 * MD Printers - Global Quick Quote Pop-Up Modal Component
 * Included on all frontend pages
 */
$servicesList = Service::getAll(['status' => 'published']);
?>

<!-- Floating Quick Quote Trigger Button -->
<button type="button" id="openGlobalQuoteModal" class="floating-quote-trigger-btn" title="Request Instant Quote / Callback">
    <i class="fas fa-calculator"></i>
    <span>Quick Quote</span>
</button>

<!-- Global Quick Quote Modal Overlay -->
<div id="globalQuoteModal" class="global-modal-overlay" style="display: none;">
    <div class="global-modal-card">
        <button type="button" id="closeGlobalQuoteModal" class="global-modal-close-btn" aria-label="Close Modal">&times;</button>
        
        <div class="global-modal-header">
            <div class="modal-header-icon">
                <i class="fab fa-whatsapp"></i>
            </div>
            <div>
                <h3 class="modal-header-title">Request Instant Print Quotation</h3>
                <p class="modal-header-sub">Submit your specs to receive instant pricing & WhatsApp estimate.</p>
            </div>
        </div>

        <form action="<?= SITE_URL ?>/api/quote" method="POST" class="ajax-form">
            <?= csrf_field() ?>
            <div class="form-alert" style="display: none; padding: 14px; border-radius: 10px; margin-bottom: 18px;"></div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Your Name *</label>
                    <input type="text" name="customer_name" class="form-control" required placeholder="e.g. Vikram Malhotra" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Phone / WhatsApp Number *</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="e.g. +91 98765 43210" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="vikram@company.com" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Product / Printing Service</label>
                    <select name="product_service" class="form-control" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                        <option value="Gold Foil Business Cards">Executive Business Cards</option>
                        <option value="Custom Packaging Boxes">Custom Packaging Boxes</option>
                        <option value="Corporate T-Shirts & Apparel">T-Shirt & Apparel Printing</option>
                        <option value="3D Acrylic Signage Board">3D Acrylic & LED Signage</option>
                        <option value="Brochures & Catalogs">Brochures & Catalogs</option>
                        <option value="Stickers & Product Labels">Stickers & Labels</option>
                        <option value="ID Cards & Lanyards">ID Cards & Lanyards</option>
                        <option value="Other Printing Service">Other Custom Printing</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Estimated Quantity</label>
                <input type="number" name="quantity" class="form-control" placeholder="e.g. 500" min="1" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Design Notes / Special Instructions</label>
                <textarea name="message" rows="3" class="form-control" placeholder="Describe finishing, foil color, delivery urgency..." style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #25D366, #128C7E); color: #ffffff; font-weight: 900; font-size: 1.05rem; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.35);">
                <i class="fab fa-whatsapp" style="font-size: 1.3rem;"></i> Submit & Open in WhatsApp
            </button>
        </form>
    </div>
</div>

<style>
/* Floating Trigger Button */
.floating-quote-trigger-btn {
    position: fixed;
    bottom: 95px;
    right: 24px;
    z-index: 999;
    background: linear-gradient(135deg, #0085B2, #EE0083);
    color: #ffffff;
    border: none;
    padding: 12px 20px;
    border-radius: 30px;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(0, 133, 178, 0.4);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.floating-quote-trigger-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 12px 30px rgba(238, 0, 131, 0.5);
}

@media (max-width: 768px) {
    .floating-quote-trigger-btn {
        display: none !important;
    }
}

/* Modal Overlay & Card Styling */
.global-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.global-modal-card {
    background: #ffffff;
    width: 100%;
    max-width: 580px;
    border-radius: 20px;
    padding: 32px;
    position: relative;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid #e2e8f0;
    animation: modalPopIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalPopIn {
    from { opacity: 0; transform: scale(0.92) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.global-modal-close-btn {
    position: absolute;
    top: 18px;
    right: 20px;
    background: #f1f5f9;
    border: none;
    font-size: 1.6rem;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.global-modal-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.global-modal-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 22px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 16px;
}

.modal-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #dcfce7;
    color: #16a34a;
    font-size: 1.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.modal-header-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2px 0;
}

.modal-header-sub {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openGlobalQuoteModal');
    const closeBtn = document.getElementById('closeGlobalQuoteModal');
    const modal = document.getElementById('globalQuoteModal');

    if (openBtn && modal) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Attach click listener to any element with data-open-quote-modal attribute
    document.querySelectorAll('[data-open-quote-modal]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (modal) modal.style.display = 'flex';
        });
    });
});
</script>
