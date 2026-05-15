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

// Jedno lub wiecej kont.
const APP_USERS = [
    ['email' => 'admin@gliznowo.pl', 'password' => 'zmien-to-haslo'],
    // ['email' => 'pracownik@gliznowo.pl', 'password' => 'drugie-haslo'],
];

const MAX_IMAGE_SIZE = 5242880; // 5 MB
const UPLOAD_DIR = __DIR__ . '/uploads/machines';
const UPLOAD_URL = '/uploads/machines';

