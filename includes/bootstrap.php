<?php
/**
 * Application bootstrap for PHP pages (not API).
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../api/helpers.php';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/../api/defaults.php';

if (session_status() === PHP_SESSION_NONE) {
    startAppSession();
}

bootstrapSettings();

// HTML pages must never be cached — otherwise open tabs keep stale markup/scripts.
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

function url(string $path = ''): string
{
    $base = rtrim(BASE_PATH, '/');
    if ($path === '' || $path === '/') {
        return $base === '' ? '/' : $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $relative = 'assets/' . ltrim($path, '/');
    $filePath = dirname(__DIR__) . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    $url = url($relative);
    if (is_file($filePath)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . 'v=' . filemtime($filePath);
    }
    return $url;
}

function getPublicSettings(): array
{
    return getSetting('public') ?? getDefaultPublicSettings();
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function hexToRgba(string $hex, float $alpha): string
{
    $clean = ltrim($hex, '#');
    if (strlen($clean) === 3) {
        $clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
    }
    $num = hexdec($clean);
    $r = ($num >> 16) & 255;
    $g = ($num >> 8) & 255;
    $b = $num & 255;
    return "rgba({$r}, {$g}, {$b}, {$alpha})";
}

function adjustHexBrightness(string $hex, int $percent): string
{
    $clean = ltrim($hex, '#');
    if (strlen($clean) === 3) {
        $clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
    }
    $num = hexdec($clean);
    $r = min(255, max(0, (($num >> 16) & 255) + $percent));
    $g = min(255, max(0, (($num >> 8) & 255) + $percent));
    $b = min(255, max(0, ($num & 255) + $percent));
    return sprintf('#%06x', ($r << 16) | ($g << 8) | $b);
}

function themeStyleBlock(array $settings): string
{
    $primary = $settings['primaryColor'] ?? '#2563eb';
    $secondary = $settings['secondaryColor'] ?? '#0f172a';
    $text = $settings['textColor'] ?? '#1e293b';
    $bg = $settings['backgroundColor'] ?? '#f8fafc';
    $headingFont = $settings['headingFont'] ?? 'Inter';
    $bodyFont = $settings['bodyFont'] ?? 'Inter';

    return '<style>:root{'
        . '--primary-color:' . e($primary) . ';'
        . '--primary-hover:' . e(adjustHexBrightness($primary, -20)) . ';'
        . '--primary-light:' . e(hexToRgba($primary, 0.05)) . ';'
        . '--primary-border:' . e(hexToRgba($primary, 0.15)) . ';'
        . '--secondary-color:' . e($secondary) . ';'
        . '--secondary-light:' . e(hexToRgba($secondary, 0.05)) . ';'
        . '--text-color:' . e($text) . ';'
        . '--background-color:' . e($bg) . ';'
        . '--heading-font:"' . e($headingFont) . '",sans-serif;'
        . '--body-font:"' . e($bodyFont) . '",sans-serif;'
        . '}</style>';
}

function googleFontsUrl(array $settings): string
{
    $heading = str_replace(' ', '+', $settings['headingFont'] ?? 'Inter');
    $body = str_replace(' ', '+', $settings['bodyFont'] ?? 'Inter');
    return "https://fonts.googleapis.com/css2?family={$heading}:wght@400;500;700;800;900&family={$body}:wght@400;500;600;700&display=swap";
}

/** Convert PascalCase Lucide name to kebab-case for data-lucide attribute */
function iconName(string $name): string
{
    $kebab = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
    return $kebab;
}

function renderIcon(string $name, string $class = 'w-5 h-5'): string
{
    return '<i data-lucide="' . e(iconName($name)) . '" class="' . e($class) . '"></i>';
}

function splitLogo(string $logo): array
{
    $parts = preg_split('/\s+/', trim($logo), 2);
    return [$parts[0] ?? '⚙️', $parts[1] ?? ''];
}

function parseFeature(string $feature): array
{
    $parts = explode(' — ', $feature, 2);
    return [
        'title' => $parts[0] ?? $feature,
        'description' => $parts[1] ?? '',
    ];
}

const FEATURE_ICONS = ['zap', 'shield-check', 'phone-call', 'heart-handshake', 'hard-hat'];

function pageSeo(array $settings, string $page): array
{
    $seo = $settings['seo'][$page] ?? $settings['seo']['home'];
    return $seo;
}

function sanitizeInput(?string $value, int $maxLen = 500): string
{
    if ($value === null) {
        return '';
    }
    $stripped = trim(strip_tags($value));
    $stripped = preg_replace('/\s+/', ' ', $stripped);
    return function_exists('mb_substr') ? mb_substr($stripped, 0, $maxLen) : substr($stripped, 0, $maxLen);
}
