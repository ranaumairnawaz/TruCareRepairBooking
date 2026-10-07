<?php
/**
 * One-time setup: creates MySQL database tables and seeds default settings.
 * Delete or protect this file after running in production.
 */
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . DB_NAME . '`');
    $sql = file_get_contents(__DIR__ . '/install.sql');
    $sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
    $sql = preg_replace('/USE[^;]+;/i', '', $sql);
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }

    require_once __DIR__ . '/api/db.php';
    require_once __DIR__ . '/api/defaults.php';
    bootstrapSettings();

    echo "Setup complete.\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Default admin login: username admin / password admin123\n";
    echo "Visit: " . (BASE_PATH ?: '') . "/index.php\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Setup failed: " . $e->getMessage() . "\n";
}
