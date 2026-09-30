<?php
/**
 * MD Printers - Common Helper Functions
 */

/**
 * Escape HTML output for XSS prevention
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF hidden input HTML
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF Token
 */
function verify_csrf(?string $token): bool {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate SEO slug from text
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Generate unique quotation reference number (MDQ-2026-XXXXXX)
 */
function generate_quote_ref(): string {
    $year = date('Y');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    return "MDQ-{$year}-{$random}";
}

/**
 * Flash message helper
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'info', 'warning'
        'message' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Handle secure file upload
 */
function upload_file(array $file, string $subfolder = 'enquiries'): ?array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['error' => 'File size exceeds 15MB limit.'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['error' => 'Invalid file format. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS)];
    }

    $targetDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $newFilename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
    $targetPath = $targetDir . '/' . $newFilename;
    $relativePath = 'uploads/' . trim($subfolder, '/') . '/' . $newFilename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'filename' => $newFilename,
            'original_name' => $file['name'],
            'filepath' => $relativePath,
            'file_size' => $file['size'],
            'extension' => $extension
        ];
    }

    return ['error' => 'Failed to save uploaded file.'];
}

/**
 * JSON Response Helper
 */
function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Format bytes to readable size
 */
function format_bytes(int $bytes): string {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}

/**
 * Safe HTTP Header / JS Redirection Helper
 */
function redirect_to(string $url): void {
    if (ob_get_level() > 0) {
        @ob_clean();
    }
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    } else {
        echo '<script>window.location.href=' . json_encode($url) . ';</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . e($url) . '"></noscript>';
        exit;
    }
}

/**
 * Safe Image / Media URL Resolver Helper
 */
function media_url(?string $path, string $fallback = 'placeholder.svg'): string {
    if (empty($path)) {
        return SITE_URL . '/assets/images/' . $fallback;
    }
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
    $normalizedRoot = str_replace('\\', '/', ROOT_PATH);
    
    if (file_exists($normalizedRoot . '/' . $cleanPath) || file_exists(ROOT_PATH . '/' . $cleanPath)) {
        return SITE_URL . '/' . $cleanPath;
    }
    return SITE_URL . '/assets/images/' . $fallback;
}

/**
 * Render Responsive Image or Auto-Playing Video Element
 */
function render_media_element(?string $imagePath, ?string $videoUrl = '', string $alt = '', string $extraStyle = ''): string {
    $imgUrl = media_url($imagePath);
    $rawVideo = trim($videoUrl ?? '');
    
    if (empty($rawVideo)) {
        return '<img src="' . e($imgUrl) . '" alt="' . e($alt) . '" style="width: 100%; height: 100%; object-fit: cover; ' . e($extraStyle) . '" onerror="this.onerror=null; this.src=\'' . SITE_URL . '/assets/images/placeholder.svg\';">';
    }

    $ytId = '';
    $vimeoId = '';
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $rawVideo, $ytMatch)) {
        $ytId = $ytMatch[1];
    } elseif (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $rawVideo, $vimeoMatch)) {
        $vimeoId = $vimeoMatch[1];
    }

    $badge = '<span class="slide-video-badge" style="position: absolute; top: 10px; right: 10px; background: rgba(0, 133, 178, 0.9); color: #fff; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 16px; backdrop-filter: blur(4px); box-shadow: 0 4px 10px rgba(0,0,0,0.2); pointer-events: none; z-index: 5;"><i class="fas fa-play-circle"></i> HD Reel</span>';

    if ($ytId) {
        return '<div style="position: relative; width: 100%; height: 100%;">' . $badge . '<iframe class="slide-video-media" src="https://www.youtube.com/embed/' . e($ytId) . '?autoplay=1&mute=1&loop=1&playlist=' . e($ytId) . '&controls=0&modestbranding=1&enablejsapi=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="width:100%; height:100%; border-radius:12px; border:none; ' . e($extraStyle) . '"></iframe></div>';
    }

    if ($vimeoId) {
        return '<div style="position: relative; width: 100%; height: 100%;">' . $badge . '<iframe class="slide-video-media" src="https://player.vimeo.com/video/' . e($vimeoId) . '?autoplay=1&muted=1&loop=1&autopause=0" frameborder="0" allow="autoplay; fullscreen" allowfullscreen style="width:100%; height:100%; border-radius:12px; border:none; ' . e($extraStyle) . '"></iframe></div>';
    }

    $cleanVideo = ltrim(str_replace('\\', '/', $rawVideo), '/');
    $videoSrc = (strpos($rawVideo, 'http://') === 0 || strpos($rawVideo, 'https://') === 0) ? $rawVideo : SITE_URL . '/' . $cleanVideo;
    $ext = strtolower(pathinfo($cleanVideo, PATHINFO_EXTENSION));
    $mimeType = ($ext === 'webm') ? 'video/webm' : (($ext === 'ogg') ? 'video/ogg' : 'video/mp4');

    return '<div style="position: relative; width: 100%; height: 100%;">' . $badge . '<video class="slide-video-media" autoplay loop muted playsinline preload="auto" poster="' . e($imgUrl) . '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px; ' . e($extraStyle) . '"><source src="' . e($videoSrc) . '" type="' . e($mimeType) . '"><img src="' . e($imgUrl) . '" alt="' . e($alt) . '" onerror="this.onerror=null; this.src=\'' . SITE_URL . '/assets/images/placeholder.svg\';"></video></div>';
}

/**
 * Render Reusable HTML Pagination Control Bar
 */
function render_pagination(int $currentPage, int $totalPages, string $baseUrl = ''): string {
    if ($totalPages <= 1) return '';

    // Remove existing page param from baseUrl if present
    $cleanBase = preg_replace('/([?&])page=[0-9]+&?/', '$1', $baseUrl);
    $cleanBase = rtrim($cleanBase, '?&');
    $connector = (strpos($cleanBase, '?') !== false) ? '&' : '?';

    $html = '<div class="pagination-wrapper">';

    // Previous Button
    if ($currentPage > 1) {
        $prevUrl = $cleanBase . $connector . 'page=' . ($currentPage - 1);
        $html .= '<a href="' . e($prevUrl) . '" class="pagination-btn prev" aria-label="Previous Page"><i class="fas fa-chevron-left"></i> Prev</a>';
    } else {
        $html .= '<span class="pagination-btn disabled" aria-disabled="true"><i class="fas fa-chevron-left"></i> Prev</span>';
    }

    // Page Numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i === $currentPage) {
            $html .= '<span class="pagination-number active">' . $i . '</span>';
        } else {
            $pageUrl = $cleanBase . $connector . 'page=' . $i;
            $html .= '<a href="' . e($pageUrl) . '" class="pagination-number">' . $i . '</a>';
        }
    }

    // Next Button
    if ($currentPage < $totalPages) {
        $nextUrl = $cleanBase . $connector . 'page=' . ($currentPage + 1);
        $html .= '<a href="' . e($nextUrl) . '" class="pagination-btn next" aria-label="Next Page">Next <i class="fas fa-chevron-right"></i></a>';
    } else {
        $html .= '<span class="pagination-btn disabled" aria-disabled="true">Next <i class="fas fa-chevron-right"></i></span>';
    }

    $html .= '</div>';
    return $html;
}


