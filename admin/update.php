<?php
require __DIR__ . '/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = trim($_POST['name'] ?? '');
$role = trim($_POST['role'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if (!$id || $name === '' || $role === '' || $bio === '') {
    redirect('/admin/edit?id=' . (int) $id . '&error=missing');
}

$stmt = db()->prepare('SELECT image_path FROM profiles WHERE id = :id');
$stmt->execute(['id' => $id]);
$profile = $stmt->fetch();

if (!$profile) {
    redirect('/admin');
}

$imagePath = handle_upload('image', $profile['image_path'] ?? '');
if ($imagePath === null) {
    redirect('/admin/edit?id=' . (int) $id . '&error=image');
}

// Slugify the name to give a link
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

$stmt = db()->prepare(
    'UPDATE profiles SET name = :name, role = :role, bio = :bio, slug = :slug, image_path = :image_path WHERE id = :id'
);
$stmt->execute([
    'id' => $id,
    'name' => $name,
    'role' => $role,
    'bio' => $bio,
    'slug' => $slug,
    'image_path' => $imagePath,
]);

redirect('/admin');
