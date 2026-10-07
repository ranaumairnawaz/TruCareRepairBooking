<?php
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/api/defaults.php';

$pdo = getDb();

// Create new tables
$pdo->exec("
CREATE TABLE IF NOT EXISTS app_settings (
    `key` VARCHAR(128) PRIMARY KEY,
    `value` LONGTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS app_services (
    id VARCHAR(64) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(64),
    price VARCHAR(64),
    image VARCHAR(255),
    seoTitle VARCHAR(255),
    seoDescription TEXT,
    seoKeywords VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS app_machines (
    id VARCHAR(64) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(64),
    image VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS app_locations (
    id VARCHAR(64) PRIMARY KEY,
    country VARCHAR(128) NOT NULL,
    cities LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

function flattenAndSave($prefix, $data, $pdo)
{
    if (is_array($data)) {
        // If it's a list of strings (like about.features), store as JSON string
        if (array_values($data) === $data) {
            $stmt = $pdo->prepare('REPLACE INTO app_settings (`key`, `value`) VALUES (?, ?)');
            $stmt->execute([$prefix, json_encode($data, JSON_UNESCAPED_UNICODE)]);
        } else {
            foreach ($data as $k => $v) {
                if (in_array($k, ['services', 'machines', 'serviceLocations']) && $prefix === '') {
                    continue; // Handled in relational tables
                }
                flattenAndSave($prefix === '' ? $k : $prefix . '.' . $k, $v, $pdo);
            }
        }
    } else {
        $stmt = $pdo->prepare('REPLACE INTO app_settings (`key`, `value`) VALUES (?, ?)');
        $stmt->execute([$prefix, (string) $data]);
    }
}

// 1. Read current public settings directly from kv_settings
$stmt = $pdo->query("SELECT `value` FROM kv_settings WHERE `key` = 'public'");
$row = $stmt->fetch();
$currentPublic = null;
if ($row) {
    $currentPublic = json_decode($row['value'], true);
}
if (!$currentPublic) {
    $currentPublic = getDefaultPublicSettings();
}

if ($currentPublic) {
    // 2. Save services
    if (!empty($currentPublic['services'])) {
        $stmt = $pdo->prepare('REPLACE INTO app_services (id, name, description, icon, price, image, seoTitle, seoDescription, seoKeywords, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($currentPublic['services'] as $i => $s) {
            $stmt->execute([
                $s['id'] ?? (string) ($i + 1),
                $s['name'] ?? '',
                $s['description'] ?? '',
                $s['icon'] ?? '',
                $s['price'] ?? '',
                $s['image'] ?? '',
                $s['seoTitle'] ?? '',
                $s['seoDescription'] ?? '',
                $s['seoKeywords'] ?? '',
                $i
            ]);
        }
    }

    // 3. Save machines
    if (!empty($currentPublic['machines'])) {
        $stmt = $pdo->prepare('REPLACE INTO app_machines (id, name, icon, image, sort_order) VALUES (?, ?, ?, ?, ?)');
        foreach ($currentPublic['machines'] as $i => $m) {
            $stmt->execute([
                $m['id'] ?? (string) ($i + 1),
                $m['name'] ?? '',
                $m['icon'] ?? '',
                $m['image'] ?? '',
                $i
            ]);
        }
    }

    // 4. Save locations
    if (!empty($currentPublic['serviceLocations'])) {
        $stmt = $pdo->prepare('REPLACE INTO app_locations (id, country, cities) VALUES (?, ?, ?)');
        foreach ($currentPublic['serviceLocations'] as $i => $l) {
            $stmt->execute([
                $l['id'] ?? (string) ($i + 1),
                $l['country'] ?? '',
                json_encode($l['cities'] ?? [], JSON_UNESCAPED_UNICODE)
            ]);
        }
    }

    // 5. Save flat settings
    flattenAndSave('', $currentPublic, $pdo);
}

echo "Migration successful!\n";
?>