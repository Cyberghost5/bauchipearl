<?php
require __DIR__ . '/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('/admin');
}

$stmt = db()->prepare('SELECT image_path FROM profiles WHERE id = :id');
$stmt->execute(['id' => $id]);
$profile = $stmt->fetch();

if ($profile) {
    delete_image_file($profile['image_path'] ?? '');
    $delete = db()->prepare('DELETE FROM profiles WHERE id = :id');
    $delete->execute(['id' => $id]);
}

redirect('/admin');
