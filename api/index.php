<?php
/**
 * index.php — front controller for the /api/* routes.
 *
 * Mirrors the Express routes in server.ts:
 *   POST /api/admin/verify
 *   GET  /api/settings
 *   GET  /api/settings/secrets            (admin)
 *   POST /api/settings/public             (admin)
 *   POST /api/settings/secrets            (admin)
 *   GET  /api/bookings                    (admin)
 *   POST /api/bookings/{id}/status        (admin)
 *   POST /api/booking                     (public, rate-limited)
 *   POST /api/upload                      (admin, rate-limited)
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/defaults.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/whatsapp.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/bookings.php';
require_once __DIR__ . '/booking.php';
require_once __DIR__ . '/upload.php';

bootstrapSettings();

// Figure out the path *relative to /api* regardless of whether the request
// arrived as /api/foo (via .htaccess rewrite) or /api/index.php/foo.
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

$path = $requestUri;
if ($scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
}
// If routed straight to /api/index.php, strip that too
$path = preg_replace('#^/index\.php#', '', $path);
// Strip a leading /api if present (e.g. when .htaccess passes the full URI through)
$path = preg_replace('#^/api#', '', $path);
$path = '/' . ltrim($path, '/');
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    // ---- POST /admin/verify ----
    if ($method === 'POST' && ($path === '/admin/login' || $path === '/admin/verify')) {
        handleAdminLogin();
    }
    if ($method === 'POST' && $path === '/admin/logout') {
        handleAdminLogout();
    }

    // ---- /settings ----
    if ($method === 'GET' && $path === '/settings') {
        handleGetPublicSettings();
    }
    if ($method === 'GET' && $path === '/settings/secrets') {
        handleGetSecretSettings();
    }
    if ($method === 'POST' && $path === '/settings/public') {
        handleUpdatePublicSettings();
    }
    if ($method === 'POST' && $path === '/settings/secrets') {
        handleUpdateSecretSettings();
    }

    // ---- /bookings ----
    if ($method === 'GET' && $path === '/bookings') {
        handleListBookings();
    }
    if ($method === 'POST' && preg_match('#^/bookings/([^/]+)/status$#', $path, $m)) {
        handleUpdateBookingStatus($m[1]);
    }

    // ---- /booking (public create) ----
    if ($method === 'POST' && $path === '/booking') {
        handleCreateBooking();
    }

    // ---- /upload ----
    if ($method === 'POST' && $path === '/upload') {
        handleUpload();
    }

    jsonResponse(['error' => 'Not found'], 404);
} catch (Throwable $e) {
    error_log('Unhandled API error: ' . $e->getMessage());
    jsonResponse(['error' => 'Something went wrong while processing your request.'], 500);
}
