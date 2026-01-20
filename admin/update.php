<?php
require __DIR__ . '/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = trim($_POST['name'] ?? '');
$role = trim($_POST['role'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if (!$id || $name === '' || $role === '' || $bio === '') {
    redirect('edit.php?id=' . (int) $id . '&error=missing');
}

$stmt = db()->prepare('SELECT image_path FROM profiles WHERE id = :id');
$stmt->execute(['id' => $id]);
$profile = $stmt->fetch();

if (!$profile) {
    redirect('index.php');
}

$imagePath = handle_upload('image', $profile['image_path'] ?? '');
if ($imagePath === null) {
    redirect('edit.php?id=' . (int) $id . '&error=image');
}

$stmt = db()->prepare(
    'UPDATE profiles SET name = :name, role = :role, bio = :bio, image_path = :image_path WHERE id = :id'
);
$stmt->execute([
    'id' => $id,
    'name' => $name,
    'role' => $role,
    'bio' => $bio,
    'image_path' => $imagePath,
]);

redirect('index.php');
