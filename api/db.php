<?php
/**
 * db.php — PDO MySQL wrapper
 */

require_once __DIR__ . '/../config.php';

function getDb(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function unflatten(array $flatArray)
{
    $result = [];
    foreach ($flatArray as $key => $value) {
        $parts = explode('.', $key);
        $curr = &$result;
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $curr[$part] = $value;
            } else {
                if (!isset($curr[$part])) {
                    $curr[$part] = [];
                }
                $curr = &$curr[$part];
            }
        }
    }
    return $result;
}

function getSetting(string $key)
{
    if ($key === 'public') {
        $pdo = getDb();
        try {
            $stmt = $pdo->query('SELECT `key`, `value` FROM app_settings');
            $flat = [];
            while ($row = $stmt->fetch()) {
                $val = $row['value'];
                $decoded = json_decode($val, true);
                if (is_array($decoded)) {
                    $flat[$row['key']] = $decoded;
                } else {
                    $flat[$row['key']] = $val;
                }
            }
            $public = unflatten($flat);

            $stmt = $pdo->query('SELECT * FROM app_services ORDER BY sort_order ASC');
            $public['services'] = $stmt->fetchAll();

            $stmt = $pdo->query('SELECT * FROM app_machines ORDER BY sort_order ASC');
            $public['machines'] = $stmt->fetchAll();

            $stmt = $pdo->query('SELECT * FROM app_locations');
            $public['serviceLocations'] = [];
            while ($row = $stmt->fetch()) {
                $row['cities'] = json_decode($row['cities'], true) ?: [];
                $public['serviceLocations'][] = $row;
            }
            return !empty($public) ? $public : null;
        } catch (Exception $e) {
            // Tables might not exist yet
            return null;
        }
    }

    $stmt = getDb()->prepare('SELECT `value` FROM kv_settings WHERE `key` = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $decoded = json_decode($row['value'], true);
    return $decoded === null && json_last_error() !== JSON_ERROR_NONE ? null : $decoded;
}

function setSetting(string $key, $value): void
{
    if ($key === 'public') {
        $pdo = getDb();
        $pdo->beginTransaction();
        try {
            $pdo->exec('DELETE FROM app_settings');
            $stmt = $pdo->prepare('INSERT INTO app_settings (`key`, `value`) VALUES (?, ?)');

            $flatten = function ($prefix, $data) use (&$flatten, $stmt) {
                if (is_array($data)) {
                    if (array_values($data) === $data) {
                        $stmt->execute([$prefix, json_encode($data, JSON_UNESCAPED_UNICODE)]);
                    } else {
                        foreach ($data as $k => $v) {
                            if (in_array($k, ['services', 'machines', 'serviceLocations']) && $prefix === '')
                                continue;
                            $flatten($prefix === '' ? $k : $prefix . '.' . $k, $v);
                        }
                    }
                } else {
                    $stmt->execute([$prefix, (string) $data]);
                }
            };
            $flatten('', $value);

            $pdo->exec('DELETE FROM app_services');
            if (!empty($value['services'])) {
                $sStmt = $pdo->prepare('INSERT INTO app_services (id, name, description, icon, price, image, seoTitle, seoDescription, seoKeywords, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                foreach ($value['services'] as $i => $s) {
                    $sStmt->execute([
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

            $pdo->exec('DELETE FROM app_machines');
            if (!empty($value['machines'])) {
                $mStmt = $pdo->prepare('INSERT INTO app_machines (id, name, icon, image, sort_order) VALUES (?, ?, ?, ?, ?)');
                foreach ($value['machines'] as $i => $m) {
                    $mStmt->execute([
                        $m['id'] ?? (string) ($i + 1),
                        $m['name'] ?? '',
                        $m['icon'] ?? '',
                        $m['image'] ?? '',
                        $i
                    ]);
                }
            }

            $pdo->exec('DELETE FROM app_locations');
            if (!empty($value['serviceLocations'])) {
                $lStmt = $pdo->prepare('INSERT INTO app_locations (id, country, cities) VALUES (?, ?, ?)');
                foreach ($value['serviceLocations'] as $i => $l) {
                    $lStmt->execute([
                        $l['id'] ?? (string) ($i + 1),
                        $l['country'] ?? '',
                        json_encode($l['cities'] ?? [], JSON_UNESCAPED_UNICODE)
                    ]);
                }
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        return;
    }

    $stmt = getDb()->prepare('
        INSERT INTO kv_settings (`key`, `value`, updatedAt) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updatedAt = VALUES(updatedAt)
    ');
    $stmt->execute([
        $key,
        json_encode($value, JSON_UNESCAPED_UNICODE),
        gmdate('Y-m-d\TH:i:s.v\Z'),
    ]);
}

function insertBooking(array $booking): void
{
    $stmt = getDb()->prepare('
        INSERT INTO bookings (id, name, email, phone, machine, symptom, country, city, address, `date`, timeSlot, message, status, createdAt)
        VALUES (:id, :name, :email, :phone, :machine, :symptom, :country, :city, :address, :date, :timeSlot, :message, :status, :createdAt)
    ');
    $stmt->execute([
        ':id' => $booking['id'],
        ':name' => $booking['name'],
        ':email' => $booking['email'],
        ':phone' => $booking['phone'],
        ':machine' => $booking['machine'],
        ':symptom' => $booking['symptom'],
        ':country' => $booking['country'],
        ':city' => $booking['city'],
        ':address' => $booking['address'],
        ':date' => $booking['date'],
        ':timeSlot' => $booking['timeSlot'],
        ':message' => $booking['message'],
        ':status' => $booking['status'],
        ':createdAt' => $booking['createdAt'],
    ]);
}

function listBookings(): array
{
    $stmt = getDb()->query('SELECT * FROM bookings ORDER BY createdAt DESC');
    return $stmt->fetchAll();
}

function updateBookingStatus(string $id, string $status): void
{
    $stmt = getDb()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
}

function ensureAdminUsersTable(): void
{
    getDb()->exec('
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(64) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            createdAt VARCHAR(32) NOT NULL,
            updatedAt VARCHAR(32) NOT NULL,
            UNIQUE KEY uk_admin_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
}

function countAdminUsers(): int
{
    ensureAdminUsersTable();
    return (int) getDb()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
}

function getAdminByUsername(string $username): ?array
{
    ensureAdminUsersTable();
    $stmt = getDb()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function createAdminUser(string $username, string $plainPassword): void
{
    ensureAdminUsersTable();
    $now = gmdate('Y-m-d\TH:i:s.v\Z');
    $stmt = getDb()->prepare('
        INSERT INTO admin_users (username, password_hash, createdAt, updatedAt)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([
        $username,
        password_hash($plainPassword, PASSWORD_BCRYPT),
        $now,
        $now,
    ]);
}

function updateAdminPassword(int $userId, string $plainPassword): void
{
    ensureAdminUsersTable();
    $stmt = getDb()->prepare('UPDATE admin_users SET password_hash = ?, updatedAt = ? WHERE id = ?');
    $stmt->execute([
        password_hash($plainPassword, PASSWORD_BCRYPT),
        gmdate('Y-m-d\TH:i:s.v\Z'),
        $userId,
    ]);
}
