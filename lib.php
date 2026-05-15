<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

ini_set('display_errors', '0');
ini_set('log_errors', '1');

session_name('maszyny_session');
session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 180,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function clean_text(mixed $value, int $max = 255): string
{
    $text = trim((string)($value ?? ''));
    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text) ?? '';
    return mb_substr($text, 0, $max, 'UTF-8');
}

function require_auth(): void
{
    if (empty($_SESSION['user_email'])) {
        json_response(['error' => 'Unauthorized'], 401);
    }
}

function valid_login(string $email, string $password): bool
{
    foreach (APP_USERS as $user) {
        $userEmail = strtolower(trim((string)($user['email'] ?? '')));
        $userPassword = (string)($user['password'] ?? '');
        if (hash_equals($userEmail, strtolower(trim($email))) && hash_equals($userPassword, $password)) {
            return true;
        }
    }

    return false;
}

function image_columns(array $row): array
{
    return array_values(array_filter([
        $row['image1'] ?? '',
        $row['image2'] ?? '',
        $row['image3'] ?? '',
        $row['image4'] ?? '',
    ]));
}

function validate_status(string $status): string
{
    return in_array($status, ['available', 'sold'], true) ? $status : 'available';
}

function save_upload(string $field): string
{
    if (empty($_FILES[$field]) || !is_array($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    $file = $_FILES[$field];
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Nie udało się wysłać zdjęcia.');
    }

    if (($file['size'] ?? 0) > MAX_IMAGE_SIZE) {
        throw new RuntimeException('Zdjęcie jest za duże. Maksymalny rozmiar to 5 MB.');
    }

    $tmp = (string)$file['tmp_name'];
    $mime = mime_content_type($tmp) ?: '';
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Dozwolone są tylko pliki graficzne.');
    }

    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
        throw new RuntimeException('Nie udało się utworzyć katalogu zdjęć.');
    }

    $name = preg_replace('/[^a-zA-Z0-9.-]/', '-', (string)($file['name'] ?? 'image')) ?: 'image';
    $filename = time() . '-' . bin2hex(random_bytes(8)) . '-' . $name . '.' . $allowed[$mime];
    $target = UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($tmp, $target)) {
        throw new RuntimeException('Nie udało się zapisać zdjęcia.');
    }

    return UPLOAD_URL . '/' . $filename;
}

function delete_upload(?string $url): void
{
    $url = (string)$url;
    if ($url === '' || !str_starts_with($url, UPLOAD_URL . '/')) {
        return;
    }

    $file = basename($url);
    if ($file === '' || str_contains($file, '..')) {
        return;
    }

    $path = UPLOAD_DIR . '/' . $file;
    if (is_file($path)) {
        @unlink($path);
    }
}

function insert_history(int $machineId, string $action, string $details = ''): void
{
    $stmt = db()->prepare('insert into machine_history (machine_id, action, details) values (?, ?, ?)');
    $stmt->execute([$machineId, clean_text($action, 255), clean_text($details, 5000)]);
}

