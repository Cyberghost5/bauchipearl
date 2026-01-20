<?php
require __DIR__ . '/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$name = trim($_POST['name'] ?? '');
$role = trim($_POST['role'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if ($name === '' || $role === '' || $bio === '') {
    redirect('create.php?error=missing');
}

$imagePath = handle_upload('image', null, true);
if ($imagePath === null) {
    redirect('create.php?error=image');
}

$stmt = db()->prepare(
    'INSERT INTO profiles (name, role, bio, image_path) VALUES (:name, :role, :bio, :image_path)'
);
$stmt->execute([
    'name' => $name,
    'role' => $role,
    'bio' => $bio,
    'image_path' => $imagePath,
]);

redirect('index.php');
