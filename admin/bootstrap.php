<?php
require __DIR__ . '/../db.php';

session_start();

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function verify_admin_credentials(string $username, string $password): bool
{
    $expectedUser = getenv('ADMIN_USER') ?: 'admin';
    $expectedHash = getenv('ADMIN_PASS_HASH') ?: '';
    $expectedPass = getenv('ADMIN_PASS') ?: 'password';

    if (!hash_equals($expectedUser, $username)) {
        return false;
    }

    if ($expectedHash !== '') {
        return password_verify($password, $expectedHash);
    }

    return hash_equals($expectedPass, $password);
}

function login_admin(string $username): void
{
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $username;
}

function logout_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/admin/login');
    }
}

function uploads_dir(): string
{
    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

function placeholder_image(): string
{
    return '/assets/placeholder.svg';
}

function normalize_image_path(?string $imagePath): string
{
    if ($imagePath === null || $imagePath === '') {
        return placeholder_image();
    }
    $relativePath = ltrim($imagePath, '/');
    $absolutePath = __DIR__ . '/../' . $relativePath;
    if (!is_file($absolutePath)) {
        return placeholder_image();
    }
    return '/' . $relativePath;
}

function delete_image_file(?string $imagePath): void
{
    if ($imagePath === null || $imagePath === '') {
        return;
    }
    $absolutePath = __DIR__ . '/../' . $imagePath;
    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }
}

function handle_upload(string $fieldName, ?string $existingPath = null, bool $required = false): ?string
{
    if (!isset($_FILES[$fieldName])) {
        return $existingPath;
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            return null;
        }
        return $existingPath;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        return null;
    }

    $extension = $allowed[$mime];
    $filename = 'profile_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = uploads_dir() . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    if ($existingPath && $existingPath !== '') {
        delete_image_file($existingPath);
    }

    return 'uploads/' . $filename;
}
