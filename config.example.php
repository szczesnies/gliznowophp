<?php

declare(strict_types=1);

// Skopiuj ten plik jako config.php i wpisz dane z Hostingera.

const DB_HOST = 'localhost';
const DB_PORT = 3306;
const DB_NAME = 'uzytkownik_baza';
const DB_USER = 'uzytkownik_mysql';
const DB_PASSWORD = 'haslo_mysql';

// Minimum 32 znaki. Zmien na losowy sekret.
const AUTH_SECRET = 'wpisz-tu-dlugi-losowy-sekret-minimum-32-znaki';

// 30 dni zapamiętania logowania na telefonie i w przeglądarce.
const SESSION_LIFETIME = 2592000;

// Jedno lub wiecej kont. Najbezpieczniej uzywac password_hash.
// Wygeneruj hash na serwerze:
// php -r "echo password_hash('twoje-haslo', PASSWORD_DEFAULT) . PHP_EOL;"
const APP_USERS = [
    ['email' => 'admin@gliznowo.pl', 'password_hash' => 'wklej-tu-hash-hasla'],
    // ['email' => 'pracownik@gliznowo.pl', 'password_hash' => 'hash-drugiego-hasla'],
];

const MAX_IMAGE_SIZE = 10485760; // 10 MB, limit awaryjny bez konwersji
const MAX_SOURCE_IMAGE_SIZE = 26214400; // 25 MB, oryginał przed konwersją
const UPLOAD_DIR = __DIR__ . '/uploads/machines';
const UPLOAD_URL = '/uploads/machines';

