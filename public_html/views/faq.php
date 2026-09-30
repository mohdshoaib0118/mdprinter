<?php
include INCLUDES_PATH . '/header.php';
$faqs = Faq::getAll();

$heroBadge    = 'HELP CENTER';
$heroTitle    = 'Frequently Asked Questions';
$heroSubtitle = 'Answers regarding artwork preparation, paper GSM, turnarounds, proofing, and shipping.';
$heroIcon     = 'fas fa-question-circle';
$breadcrumb   = ['FAQ' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 80px 0; background: #ffffff;">
    <div class="container" style="max-width: 920px;">
        <div style="display: flex; flex-direction: column; gap: 18px;">
            <?php foreach ($faqs as $f): ?>
                <details style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 22px 28px; box-shadow: var(--shadow-sm); transition: all 0.25s ease;" class="faq-accordion-item">
                    <summary style="font-size: 1.15rem; font-weight: 800; color: #0f172a; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 16px; list-style: none;">
                        <span style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-question-circle" style="color: var(--cmyk-cyan); font-size: 1.25rem;"></i> <?= e($f['question']) ?>
                        </span>
                        <i class="fas fa-chevron-down faq-icon" style="color: #64748b; font-size: 0.9rem; transition: transform 0.3s;"></i>
                    </summary>
                    <div style="color: #475569; font-size: 1rem; line-height: 1.75; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9; padding-left: 36px;">
                        <?= e($f['answer']) ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.faq-accordion-item[open] {
    border-color: var(--cmyk-cyan);
    box-shadow: var(--shadow-md);
    background: #f8fafc;
}
.faq-accordion-item[open] .faq-icon {
    transform: rotate(180deg);
    color: var(--cmyk-cyan);
}
.faq-accordion-item summary::-webkit-details-marker {
    display: none;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>

