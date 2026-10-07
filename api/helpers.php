<?php
/**
 * helpers.php — small shared utilities used across the API endpoints.
 */

ini_set('display_errors', '0');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 0');
header('Referrer-Policy: no-referrer-when-downgrade');

/**
 * Send a JSON response and stop execution.
 */
function jsonResponse($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    // Every API response is dynamic (settings, bookings, etc.) and must never
    // be cached by the browser or any intermediary — otherwise an already-open
    // tab (or a shared-hosting edge cache) can keep serving stale data after
    // an admin update until the cache expires.
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo json_encode($data);
    exit;
}

/**
 * Read and JSON-decode the raw request body. Returns an empty array if the
 * body is missing or invalid.
 */
function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Strip tags/attributes from free-text customer input and trim to a max
 * length - equivalent to the sanitize-html + trim + slice used in server.ts.
 */
/**
 * Validate CSRF token from X-CSRF-Token header or JSON body field csrfToken.
 */
function startAppSession(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }
    if (!defined('BASE_PATH')) {
        require_once __DIR__ . '/../config.php';
    }
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => BASE_PATH ?: '/',
        'httponly' => true,
        'samesite' => 'Strict',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

function requireCsrf(): void
{
    $body = getJsonBody();
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($body['csrfToken'] ?? null);
    startAppSession();
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        jsonResponse(['error' => 'Invalid or missing CSRF token'], 403);
    }
}

function clean($value, int $maxLen = 500): string
{
    if (!is_string($value)) {
        return '';
    }
    $stripped = trim(strip_tags($value));
    // Collapse any stray whitespace left behind by stripped tags/scripts
    $stripped = preg_replace('/\s+/', ' ', $stripped);
    if (function_exists('mb_substr')) {
        return mb_substr($stripped, 0, $maxLen);
    }
    return substr($stripped, 0, $maxLen);
}

/**
 * Very small file-based rate limiter, since shared hosting has no Redis.
 * Buckets are keyed by (routeKey + client IP) and stored as JSON files in
 * a private tmp directory. Returns true if the request is allowed, false
 * if the caller has exceeded the limit within the window.
 */
function rateLimit(string $routeKey, int $windowSeconds, int $maxRequests): bool
{
    $dir = sys_get_temp_dir() . '/repairshop_ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    // If X-Forwarded-For contains a list, use the first (client) address
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }

    $safeKey = preg_replace('/[^a-zA-Z0-9_.:-]/', '_', $routeKey . '_' . $ip);
    $file = $dir . '/' . $safeKey . '.json';

    $now = time();
    $timestamps = [];

    $fp = @fopen($file, 'c+');
    if (!$fp) {
        // If we can't open the rate-limit file for some reason, fail open
        // rather than blocking legitimate traffic.
        return true;
    }

    flock($fp, LOCK_EX);
    $contents = stream_get_contents($fp);
    if ($contents) {
        $decoded = json_decode($contents, true);
        if (is_array($decoded)) {
            $timestamps = $decoded;
        }
    }

    // Drop timestamps outside the current window
    $timestamps = array_values(array_filter($timestamps, function ($t) use ($now, $windowSeconds) {
        return ($now - $t) < $windowSeconds;
    }));

    $allowed = count($timestamps) < $maxRequests;
    if ($allowed) {
        $timestamps[] = $now;
    }

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($timestamps));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return $allowed;
}
