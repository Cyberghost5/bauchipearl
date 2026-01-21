<?php
require __DIR__ . '/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin');
}

$name = trim($_POST['name'] ?? '');
$role = trim($_POST['role'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if ($name === '' || $role === '' || $bio === '') {
    redirect('/admin/create?error=missing');
}

$imagePath = handle_upload('image', null, true);
if ($imagePath === null) {
    redirect('/admin/create?error=image');
}

// Slugify the name to give a link
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

$stmt = db()->prepare(
    'INSERT INTO profiles (name, role, bio, slug, image_path) VALUES (:name, :role, :bio, :slug, :image_path)'
);
$stmt->execute([
    'name' => $name,
    'role' => $role,
    'bio' => $bio,
    'slug' => $slug,
    'image_path' => $imagePath,
]);

redirect('/admin');
