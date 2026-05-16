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
    $errorCode = (int)($file['error'] ?? UPLOAD_ERR_OK);
    if ($errorCode !== UPLOAD_ERR_OK) {
        if (in_array($errorCode, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            throw new RuntimeException('Zdjęcie jest za duże dla limitu serwera. Spróbuj wybrać mniejsze zdjęcie albo zmniejszyć je w telefonie przed dodaniem.');
        }
        throw new RuntimeException('Nie udało się wysłać zdjęcia.');
    }

    $sourceLimit = defined('MAX_SOURCE_IMAGE_SIZE') ? (int)MAX_SOURCE_IMAGE_SIZE : max((int)MAX_IMAGE_SIZE, 25 * 1024 * 1024);
    if (($file['size'] ?? 0) > $sourceLimit) {
        throw new RuntimeException('Zdjęcie jest za duże. Maksymalny rozmiar zdjęcia źródłowego to ' . (int)round($sourceLimit / 1024 / 1024) . ' MB.');
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

    $safeName = preg_replace('/[^a-zA-Z0-9.-]/', '-', pathinfo((string)($file['name'] ?? 'image'), PATHINFO_FILENAME)) ?: 'image';
    $baseName = time() . '-' . bin2hex(random_bytes(8)) . '-' . $safeName;

    if (function_exists('imagewebp')) {
        $converted = convert_uploaded_image_to_webp($tmp, $mime, UPLOAD_DIR . '/' . $baseName . '.webp');
        if ($converted !== '') {
            return UPLOAD_URL . '/' . basename($converted);
        }
    }

    $filename = $baseName . '.' . $allowed[$mime];
    $target = UPLOAD_DIR . '/' . $filename;

    if (($file['size'] ?? 0) > MAX_IMAGE_SIZE) {
        throw new RuntimeException('Serwer nie ma konwersji zdjęć, a plik po wysłaniu jest za duży. Wybierz mniejsze zdjęcie.');
    }

    if (!move_uploaded_file($tmp, $target)) {
        throw new RuntimeException('Nie udało się zapisać zdjęcia.');
    }

    return UPLOAD_URL . '/' . $filename;
}

function convert_uploaded_image_to_webp(string $sourcePath, string $mime, string $targetPath): string
{
    $image = match ($mime) {
        'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($sourcePath) : false,
        'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($sourcePath) : false,
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
        'image/gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($sourcePath) : false,
        default => false,
    };

    if (!$image) {
        return '';
    }

    if ($mime === 'image/jpeg') {
        $image = apply_image_orientation($image, jpeg_exif_orientation($sourcePath));
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $maxSide = 1600;
    $scale = min(1, $maxSide / max($width, $height));
    $newWidth = max(1, (int)round($width * $scale));
    $newHeight = max(1, (int)round($height * $scale));

    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
    imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
    imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $saved = imagewebp($canvas, $targetPath, 82);
    imagedestroy($canvas);
    imagedestroy($image);

    return $saved ? $targetPath : '';
}

function jpeg_exif_orientation(string $sourcePath): int
{
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($sourcePath);
        $orientation = (int)($exif['Orientation'] ?? 1);
        return $orientation >= 1 && $orientation <= 8 ? $orientation : 1;
    }

    $data = @file_get_contents($sourcePath, false, null, 0, 65536);
    if ($data === false || strlen($data) < 4 || substr($data, 0, 2) !== "\xFF\xD8") {
        return 1;
    }

    $offset = 2;
    $length = strlen($data);
    while ($offset + 4 < $length) {
        $marker = unpack('n', substr($data, $offset, 2))[1];
        $offset += 2;
        $segmentLength = unpack('n', substr($data, $offset, 2))[1] ?? 0;
        if ($marker === 0xFFE1 && substr($data, $offset + 2, 4) === 'Exif') {
            $tiff = $offset + 8;
            $littleEndian = substr($data, $tiff, 2) === 'II';
            $ifdOffset = read_exif_int($data, $tiff + 4, 4, $littleEndian);
            $ifd = $tiff + $ifdOffset;
            $entries = read_exif_int($data, $ifd, 2, $littleEndian);
            for ($i = 0; $i < $entries; $i++) {
                $entry = $ifd + 2 + $i * 12;
                if ($entry + 12 > $length) {
                    break;
                }
                if (read_exif_int($data, $entry, 2, $littleEndian) === 0x0112) {
                    $orientation = read_exif_int($data, $entry + 8, 2, $littleEndian);
                    return $orientation >= 1 && $orientation <= 8 ? $orientation : 1;
                }
            }
            return 1;
        }
        if (($marker & 0xFF00) !== 0xFF00 || $segmentLength < 2) {
            break;
        }
        $offset += $segmentLength;
    }

    return 1;
}

function read_exif_int(string $data, int $offset, int $bytes, bool $littleEndian): int
{
    $chunk = substr($data, $offset, $bytes);
    if (strlen($chunk) < $bytes) {
        return 0;
    }
    if ($bytes === 2) {
        return unpack($littleEndian ? 'v' : 'n', $chunk)[1];
    }
    return unpack($littleEndian ? 'V' : 'N', $chunk)[1];
}

function apply_image_orientation($image, int $orientation)
{
    if ($orientation === 2 && function_exists('imageflip')) {
        imageflip($image, IMG_FLIP_HORIZONTAL);
    } elseif ($orientation === 3) {
        $image = imagerotate($image, 180, 0) ?: $image;
    } elseif ($orientation === 4 && function_exists('imageflip')) {
        imageflip($image, IMG_FLIP_VERTICAL);
    } elseif ($orientation === 5) {
        if (function_exists('imageflip')) {
            imageflip($image, IMG_FLIP_VERTICAL);
        }
        $image = imagerotate($image, 270, 0) ?: $image;
    } elseif ($orientation === 6) {
        $image = imagerotate($image, 270, 0) ?: $image;
    } elseif ($orientation === 7) {
        if (function_exists('imageflip')) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }
        $image = imagerotate($image, 270, 0) ?: $image;
    } elseif ($orientation === 8) {
        $image = imagerotate($image, 90, 0) ?: $image;
    }

    return $image;
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

