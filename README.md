# Maszyny Gliznowo - wersja PHP pod Hostinger

Ta wersja nie wymaga Node.js ani Next.js. Dziala jako zwykly hosting PHP + MySQL w `public_html`.

## Wdrozenie

1. W Hostingerze utworz nowa baze MySQL.
2. W phpMyAdmin zaimportuj `database.sql`.
3. Skopiuj cala zawartosc folderu `hostinger-php` do:

```text
/home/u251717226/domains/gliznowo.pl/public_html
```

4. W `public_html` skopiuj:

```bash
cp config.example.php config.php
```

5. Edytuj `config.php` i wpisz:

- dane bazy MySQL,
- `AUTH_SECRET`,
- konta logowania w `APP_USERS`.

6. Upewnij sie, ze katalog uploadow istnieje:

```bash
mkdir -p uploads/machines
chmod 755 uploads uploads/machines
```

7. Otworz:

```text
https://gliznowo.pl
```

## Zalety tej wersji

- brak stalego procesu Node.js,
- brak reverse proxy,
- brak `_next/static`,
- mniejsze zuzycie RAM/CPU,
- naturalna praca na Hostinger shared hosting,
- upload zdjec zapisuje sie w `public_html/uploads/machines`.

## Pliki

- `index.php` - interfejs aplikacji,
- `api.php` - API PHP,
- `lib.php` - baza, sesje, upload,
- `config.php` - lokalna konfiguracja z haslami,
- `database.sql` - czysta struktura bazy,
- `uploads/machines` - zdjecia maszyn.

