<?php
/**
 * MD Printers - Dynamic Quick Service Circle Bar Component
 * Positioned directly below the navigation bar
 */
$allServices = Service::getAll(['status' => 'published']);

$defaultMeta = [
    'printing'           => ['color' => 'gold',    'icon' => 'fas fa-print'],
    'visiting-cards'     => ['color' => 'gold',    'icon' => 'fas fa-address-card'],
    't-shirt-printing'   => ['color' => 'magenta', 'icon' => 'fas fa-tshirt'],
    'mug-printing'       => ['color' => 'yellow',  'icon' => 'fas fa-mug-hot'],
    'badges'             => ['color' => 'gold',    'icon' => 'fas fa-certificate'],
    'stickers'           => ['color' => 'cyan',    'icon' => 'fas fa-sticky-note'],
    'id-cards'           => ['color' => 'magenta', 'icon' => 'fas fa-id-badge'],
    'lanyards'           => ['color' => 'yellow',  'icon' => 'fas fa-ribbon'],
    'name-plates'        => ['color' => 'gold',    'icon' => 'fas fa-signature'],
    'acrylic-boards'     => ['color' => 'cyan',    'icon' => 'fas fa-shapes'],
    'corporate-printing' => ['color' => 'magenta', 'icon' => 'fas fa-briefcase'],
];

$itemsToDisplay = [];
foreach ($allServices as $s) {
    $slug = $s['slug'];
    $meta = $defaultMeta[$slug] ?? ['color' => 'cyan', 'icon' => 'fas fa-print'];
    
    // Choose icon
    $icon = !empty($s['icon']) && strpos($s['icon'], 'fa') !== false ? $s['icon'] : $meta['icon'];
    
    // Process image path
    $imgUrl = null;
    if (!empty($s['main_image'])) {
        $cleanImg = ltrim(str_replace('\\', '/', $s['main_image']), '/');
        if (file_exists(ROOT_PATH . '/' . $cleanImg)) {
            $imgUrl = SITE_URL . '/' . $cleanImg;
        }
    }

    $itemsToDisplay[] = [
        'name' => $s['name'],
        'slug' => $slug,
        'color' => $meta['color'],
        'icon' => $icon,
        'imgUrl' => $imgUrl
    ];
}
?>

<div class="quick-services-bar-wrapper">
    <div class="container position-relative">
        <button type="button" class="quick-scroll-btn quick-scroll-prev" id="quickScrollPrev" onclick="scrollQuickServices(-320)" aria-label="Scroll left">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="quick-services-scroll-container" id="quickServicesContainer">
            <?php foreach ($itemsToDisplay as $item): 
                $slug = e($item['slug']);
                $name = e($item['name']);
                $ringColor = e($item['color']);
                $iconClass = e($item['icon']);
                $imgUrl = $item['imgUrl'];
                
                $detailUrl = SITE_URL . '/services/' . $slug;
                $activeClass = (isset($currentService) && ($currentService['slug'] === $slug || (isset($_GET['url']) && strpos($_GET['url'], $slug) !== false))) ? 'active' : '';
            ?>
                <a href="<?= $detailUrl ?>" class="quick-service-pill-item <?= $activeClass ?>" title="Explore <?= $name ?> Services">
                    <div class="circle-gold-seal-frame ring-<?= $ringColor ?>">
                        <div class="circle-inner-content">
                            <?php if ($imgUrl): ?>
                                <img src="<?= $imgUrl ?>" alt="<?= $name ?>" class="circle-thumb-img">
                            <?php else: ?>
                                <i class="<?= $iconClass ?>"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="quick-service-label"><?= $name ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <button type="button" class="quick-scroll-btn quick-scroll-next" id="quickScrollNext" onclick="scrollQuickServices(320)" aria-label="Scroll right">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

<script>
function scrollQuickServices(amount) {
    const container = document.getElementById('quickServicesContainer');
    if (container) {
        container.scrollBy({ left: amount, behavior: 'smooth' });
    }
}
</script>
