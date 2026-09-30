<?php
/**
 * MD Printers - Full Screen Background Image & Video Hero Banner Carousel
 */
$slides = Banner::getAll(['status' => 'published']);
?>

<!-- Hero Banner Slider Container -->
<div class="hero-banner-slider-wrapper">
    <div class="banner-slider-container">
        <?php foreach ($slides as $idx => $s): 
            $imgUrl = media_url($s['image']);
            $videoUrl = !empty($s['video_url']) ? trim($s['video_url']) : '';
        ?>
            <div class="banner-slide <?= $idx === 0 ? 'active' : '' ?>">
                <!-- Full Screen Background Image / Video Container -->
                <div class="slide-bg-media">
                    <?php if (!empty($videoUrl)): ?>
                        <?php
                            $ytId = '';
                            $vimeoId = '';
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $ytMatch)) {
                                $ytId = $ytMatch[1];
                            } elseif (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $videoUrl, $vimeoMatch)) {
                                $vimeoId = $vimeoMatch[1];
                            }
                        ?>
                        <?php if ($ytId): ?>
                            <iframe class="slide-bg-video-frame" src="https://www.youtube.com/embed/<?= $ytId ?>?autoplay=1&mute=1&loop=1&playlist=<?= $ytId ?>&controls=0&modestbranding=1&enablejsapi=1" 
                                    frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        <?php elseif ($vimeoId): ?>
                            <iframe class="slide-bg-video-frame" src="https://player.vimeo.com/video/<?= $vimeoId ?>?autoplay=1&muted=1&loop=1&autopause=0" 
                                    frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        <?php else: ?>
                            <?php
                                $cleanVideo = ltrim(str_replace('\\', '/', $videoUrl), '/');
                                $videoSrc = (strpos($videoUrl, 'http://') === 0 || strpos($videoUrl, 'https://') === 0) ? $videoUrl : SITE_URL . '/' . $cleanVideo;
                                $ext = strtolower(pathinfo($cleanVideo, PATHINFO_EXTENSION));
                                $mimeType = ($ext === 'webm') ? 'video/webm' : (($ext === 'ogg') ? 'video/ogg' : 'video/mp4');
                            ?>
                            <video class="slide-bg-video" autoplay loop muted playsinline preload="auto" poster="<?= $imgUrl ?>">
                                <source src="<?= e($videoSrc) ?>" type="<?= e($mimeType) ?>">
                            </video>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="slide-bg-img" style="background-image: url('<?= $imgUrl ?>');"></div>
                    <?php endif; ?>
                    
                    <!-- Dark Gradient Overlay for Maximum Text Contrast -->
                    <div class="slide-bg-overlay"></div>
                </div>

                <!-- Text Content & CTA Buttons directly on top of the Full Screen Image -->
                <div class="container z-index-2">
                    <div class="slide-content-overlay">
                        <?php if (!empty($s['badge'])): ?>
                            <span class="slide-badge-pill"><i class="fas fa-star"></i> <?= e($s['badge']) ?></span>
                        <?php endif; ?>
                        
                        <h1 class="slide-headline"><?= e($s['title']) ?></h1>
                        <p class="slide-subtext"><?= e($s['subtitle']) ?></p>
                        
                        <div class="slide-actions">
                            <?php 
                                $pLink = trim($s['cta_primary_link'] ?? '#');
                                if ($pLink !== '#' && strpos($pLink, 'http://') !== 0 && strpos($pLink, 'https://') !== 0 && strpos($pLink, 'tel:') !== 0 && strpos($pLink, 'mailto:') !== 0) {
                                    $pLink = SITE_URL . '/' . ltrim($pLink, '/');
                                }
                                $sLink = trim($s['cta_secondary_link'] ?? '#');
                                if ($sLink !== '#' && strpos($sLink, 'http://') !== 0 && strpos($sLink, 'https://') !== 0 && strpos($sLink, 'tel:') !== 0 && strpos($sLink, 'mailto:') !== 0) {
                                    $sLink = SITE_URL . '/' . ltrim($sLink, '/');
                                }
                            ?>
                            <?php if (!empty($s['cta_primary_text'])): ?>
                                <a href="<?= e($pLink) ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-cart"></i> <?= e($s['cta_primary_text']) ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty(trim($s['cta_secondary_text'] ?? ''))): ?>
                                <a href="<?= e($sLink) ?>" class="btn btn-magenta btn-lg">
                                    <i class="fas fa-phone-alt"></i> <?= e($s['cta_secondary_text']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Slider Controls -->
        <button class="slider-arrow prev" aria-label="Previous Slide"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-arrow next" aria-label="Next Slide"><i class="fas fa-chevron-right"></i></button>

        <!-- Slider Dots -->
        <div class="slider-dots-container">
            <?php foreach ($slides as $idx => $s): ?>
                <button class="slider-dot <?= $idx === 0 ? 'active' : '' ?>" data-slide="<?= $idx ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</div>
