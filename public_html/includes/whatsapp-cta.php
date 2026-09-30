<?php
$waNumber = Setting::get('whatsapp_number', '918146141450');
$waMessage = urlencode(Setting::get('whatsapp_cta_message', 'Hello MD Printers, I would like to request a quotation for printing services.'));
$waUrl = "https://wa.me/{$waNumber}?text={$waMessage}";
?>
<!-- Floating WhatsApp Widget -->
<a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="floating-wa-btn" aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Mobile Bottom Call to Action Bar -->
<div class="mobile-bottom-bar">
    <a href="tel:<?= e(Setting::get('contact_phone', '+91 81461 41450')) ?>" class="btn btn-outline btn-sm"><i class="fas fa-phone"></i> Call</a>
    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-magenta btn-sm"><i class="fab fa-whatsapp"></i> WhatsApp</a>
    <button type="button" data-modal="track-order" class="btn btn-outline btn-sm" style="color: #dfba6e; border-color: #dfba6e;"><i class="fas fa-search-location"></i> Track</button>
    <button type="button" data-modal="quick-quote" class="btn btn-primary btn-sm"><i class="fas fa-file-invoice"></i> Quote</button>
</div>
