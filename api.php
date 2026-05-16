<?php

declare(strict_types=1);

require_once __DIR__ . '/lib.php';

$action = clean_text($_GET['action'] ?? '', 80);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($action === 'login' && $method === 'POST') {
        $payload = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
        $email = clean_text($payload['email'] ?? '', 255);
        $password = clean_text($payload['password'] ?? '', 255);

        if (login_rate_limited()) {
            json_response(['error' => 'Zbyt wiele prób logowania. Spróbuj ponownie za kilka minut.'], 429);
        }

        if (!valid_login($email, $password)) {
            register_failed_login();
            json_response(['error' => 'Nieprawidłowy email lub hasło.'], 401);
        }

        register_successful_login();
        session_regenerate_id(true);
        $_SESSION['user_email'] = strtolower(trim($email));
        csrf_token();
        remember_login_session();
        json_response(['ok' => true, 'csrf' => csrf_token()]);
    }

    if ($action === 'logout' && $method === 'POST') {
        if (!empty($_SESSION['user_email'])) require_csrf();
        $_SESSION = [];
        session_destroy();
        forget_login_session();
        json_response(['ok' => true]);
    }

    if ($action === 'me') {
        json_response(['authenticated' => !empty($_SESSION['user_email']), 'email' => $_SESSION['user_email'] ?? null, 'csrf' => !empty($_SESSION['user_email']) ? csrf_token() : null]);
    }

    require_auth();
    if ($method !== 'GET') require_csrf();

    if ($action === 'machines' && $method === 'GET') {
        $status = validate_status((string)($_GET['status'] ?? 'available'));
        $stmt = db()->prepare('select * from machines where status = ? order by id desc');
        $stmt->execute([$status]);
        json_response(['machines' => $stmt->fetchAll()]);
    }

    if ($action === 'create' && $method === 'POST') {
        $images = [];
        for ($i = 1; $i <= 4; $i++) {
            $images[$i] = save_upload('image' . $i);
        }

        $data = [
            'name' => clean_text($_POST['name'] ?? '', 255),
            'index_number' => clean_text($_POST['index_number'] ?? '', 100),
            'purchase_price' => clean_text($_POST['purchase_price'] ?? '', 100),
            'vat_price' => clean_text($_POST['vat_price'] ?? '', 100),
            'gross_price' => clean_text($_POST['gross_price'] ?? '', 100),
            'description' => clean_multiline_text($_POST['description'] ?? '', 5000),
            'note' => clean_multiline_text($_POST['note'] ?? '', 2000),
        ];

        if ($data['name'] === '') {
            json_response(['error' => 'Wpisz nazwę maszyny.'], 400);
        }

        $stmt = db()->prepare(
            'insert into machines (name, index_number, purchase_price, vat_price, gross_price, description, note, image1, image2, image3, image4, status)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['index_number'],
            $data['purchase_price'],
            $data['vat_price'],
            $data['gross_price'],
            $data['description'],
            $data['note'],
            $images[1],
            $images[2],
            $images[3],
            $images[4],
            'available',
        ]);

        $id = (int)db()->lastInsertId();
        insert_history($id, 'Dodano maszynę', 'Nazwa: ' . $data['name']);
        json_response(['id' => $id], 201);
    }

    if ($action === 'update' && $method === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) json_response(['error' => 'Brak ID.'], 400);

        $stmt = db()->prepare('select * from machines where id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!$old) json_response(['error' => 'Nie znaleziono maszyny.'], 404);

        $fields = [
            'name' => clean_text($_POST['name'] ?? $old['name'], 255),
            'index_number' => clean_text($_POST['index_number'] ?? $old['index_number'], 100),
            'purchase_price' => clean_text($_POST['purchase_price'] ?? $old['purchase_price'], 100),
            'vat_price' => clean_text($_POST['vat_price'] ?? $old['vat_price'], 100),
            'gross_price' => clean_text($_POST['gross_price'] ?? $old['gross_price'], 100),
            'description' => clean_multiline_text($_POST['description'] ?? $old['description'], 5000),
            'note' => clean_multiline_text($_POST['note'] ?? $old['note'], 2000),
            'status' => validate_status((string)($_POST['status'] ?? $old['status'])),
        ];

        if ($fields['name'] === '') {
            json_response(['error' => 'Wpisz nazwę maszyny.'], 400);
        }

        $images = [];
        for ($i = 1; $i <= 4; $i++) {
            $column = 'image' . $i;
            $replacement = save_upload($column);
            if ($replacement !== '') {
                delete_upload($old[$column] ?? '');
                $images[$column] = $replacement;
            } else {
                $images[$column] = (string)($old[$column] ?? '');
            }
        }

        $stmt = db()->prepare(
            'update machines set name=?, index_number=?, purchase_price=?, vat_price=?, gross_price=?, description=?, note=?, image1=?, image2=?, image3=?, image4=?, status=? where id=?'
        );
        $stmt->execute([
            $fields['name'],
            $fields['index_number'],
            $fields['purchase_price'],
            $fields['vat_price'],
            $fields['gross_price'],
            $fields['description'],
            $fields['note'],
            $images['image1'],
            $images['image2'],
            $images['image3'],
            $images['image4'],
            $fields['status'],
            $id,
        ]);

        insert_history($id, clean_text($_POST['history_action'] ?? 'Edycja', 255), clean_text($_POST['history_details'] ?? 'Zaktualizowano maszynę.', 5000));
        json_response(['ok' => true]);
    }

    if ($action === 'delete' && $method === 'POST') {
        $payload = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
        $id = (int)($payload['id'] ?? 0);
        if ($id <= 0) json_response(['error' => 'Brak ID.'], 400);

        $stmt = db()->prepare('select * from machines where id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) json_response(['ok' => true]);

        foreach (image_columns($row) as $url) {
            delete_upload($url);
        }

        $stmt = db()->prepare('delete from machines where id = ?');
        $stmt->execute([$id]);
        json_response(['ok' => true]);
    }

    if ($action === 'history' && $method === 'GET') {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = db()->prepare('select * from machine_history where machine_id = ? order by created_at desc, id desc');
        $stmt->execute([$id]);
        json_response(['history' => $stmt->fetchAll()]);
    }

    json_response(['error' => 'Nieznana akcja.'], 404);
} catch (Throwable $error) {
    error_log($error->getMessage());
    json_response(['error' => 'Błąd serwera. Spróbuj ponownie za chwilę.'], 500);
}

