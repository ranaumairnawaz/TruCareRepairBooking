<?php
/**
 * Repair Shop — PHP Configuration
 *
 * ── Changing the admin passcode ──────────────────────────────────────────────
 *   Admin credentials live in the admin_users MySQL table.
 *   Default after setup: username admin, password admin123
 *
 *   To set a new password hash manually:
 *   php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_BCRYPT) . PHP_EOL;"
 *
 * ── Generating a fresh JWT secret ───────────────────────────────────────────
 *   php -r "echo bin2hex(random_bytes(48)) . PHP_EOL;"
 *
 * ── BASE_PATH ────────────────────────────────────────────────────────────────
 *   This is now auto-detected below from where this file actually lives
 *   relative to your web server's document root — it works whether the app
 *   is installed at the domain root, in a folder called /repair-shop-php,
 *   or in any other subdirectory/subdomain, with no manual editing needed.
 *   This is what was previously hardcoded to '/repair-shop-php', which broke
 *   admin login (and every other API call, and uploaded image URLs) on any
 *   deployment that wasn't running from that exact path.
 *
 *   If your hosting environment doesn't populate $_SERVER['DOCUMENT_ROOT']
 *   correctly (rare, but happens on some misconfigured hosts) and the
 *   auto-detected value below is wrong, create a file named
 *   `config.local.php` next to this one containing:
 *     <?php define('BASE_PATH_OVERRIDE', '/your/actual/subfolder');
 *   That file is optional and is loaded automatically if present.
 */


define('BASE_PATH', '');

// ── MySQL ─────────────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'u344500041_trucare');
define('DB_USER', 'u344500041_trucare');
define('DB_PASS', 'ZedXSol@786');
define('DB_CHARSET', 'utf8mb4');


// ── Paths ─────────────────────────────────────────────────────────────────────
define('UPLOADS_DIR', __DIR__ . '/uploads');
