<?php
require __DIR__ . '/db.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitize_html(string $html): string
{
    $allowed = '<p><br><strong><em><ul><ol><li>';
    $clean = strip_tags($html, $allowed);
    $clean = preg_replace('/<(\/?)(p|br|strong|em|ul|ol|li)\b[^>]*>/i', '<$1$2>', $clean);
    if ($clean === null) {
        return '';
    }
    return nl2br($clean);
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ../');
    exit;
}

$profile = null;
try {
    $stmt = db()->prepare('SELECT id, name, role, bio, image_path FROM profiles WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $profile = $stmt->fetch();
} catch (Throwable $throwable) {
    $profile = null;
}

if (!$profile) {
    http_response_code(404);
}

$imagePath = $profile['image_path'] ?? '';
$absolutePath = $imagePath ? __DIR__ . '/' . $imagePath : '';
if ($imagePath === '' || !is_file($absolutePath)) {
    $imagePath = 'assets/placeholder.svg';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $profile ? e($profile['name']) : 'Profile'; ?> - Bauchi Pearl Magazine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="assets/bph-favicon.png" type="image/png">
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="page">
      <header class="site-header site-header--detail">
        <a class="back-link" href="../">Back to profiles</a>
        <img src="assets/bhp-logo.png" alt="Bauchi Pearl Magazine Logo" class="site-logo" style="width: 100px;" />
        <p class="kicker">Bauchi Pearl Magazine</p>
        <h1>Profile</h1>
      </header>

      <main>
        <?php if (!$profile) : ?>
          <p class="status-message">
            Profile not found. Please return to the profile list.
          </p>
        <?php else : ?>
          <section class="profile-detail">
            <img
              src="<?php echo e($imagePath); ?>"
              alt="<?php echo e($profile['name']); ?>"
              class="profile-photo profile-photo--detail"
            />
            <div class="profile-detail-body">
              <h2><?php echo e($profile['name']); ?></h2>
              <p class="profile-role"><?php echo e($profile['role']); ?></p>
              <div class="profile-bio"><?php echo sanitize_html($profile['bio']); ?></div>
            </div>
          </section>
        <?php endif; ?>
      </main>

      <footer class="site-footer">
        <p>(c) <?php echo date('Y'); ?> Bauchi Pearl Magazine</p>
      </footer>
    </div>
  </body>
</html>
