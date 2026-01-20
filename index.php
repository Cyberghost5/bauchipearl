<?php
require __DIR__ . '/db.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function excerpt(string $text, int $max = 160): string
{
    $text = trim(strip_tags($text));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        return mb_substr($text, 0, $max - 3) . '...';
    }
    if (strlen($text) <= $max) {
        return $text;
    }
    return substr($text, 0, $max - 3) . '...';
}

$error = null;
$profiles = [];

try {
    $stmt = db()->query('SELECT id, name, role, bio, image_path FROM profiles ORDER BY created_at DESC');
    $profiles = $stmt->fetchAll();
} catch (Throwable $throwable) {
    $error = 'Unable to load profiles right now. Please check your database connection.';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bauchi Pearl Magazine</title>
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
      <header class="site-header">
        <p class="kicker">Entertainment and Lifestyle Magazine</p>
        <center>
          <img src="assets/bhp-logo.png" alt="Bauchi Pearl Magazine Logo" class="site-logo" style="width: 200px;" />
        </center>
        <h1>Bauchi Pearl Magazine</h1>
        <p class="intro">
          Bauchi Pearl Entertainment and Lifestyle Magazine is an apparatus for
          propagation of business ideas, entertainment and lifestyle in the
          northern part of Nigeria.
        </p>
      </header>

      <main id="profiles" class="profiles">
        <div class="section-title">
          <h2>Distinguished Profiles</h2>
          <p>Meet the people shaping culture, business, and creativity.</p>
        </div>
        <?php if ($error !== null) : ?>
          <p class="status-message"><?php echo e($error); ?></p>
        <?php elseif (empty($profiles)) : ?>
          <p class="status-message">
            No profiles yet. Visit the admin dashboard to add your first profile.
          </p>
        <?php else : ?>
          <div class="profile-grid">
            <?php foreach ($profiles as $profile) : ?>
              <?php
                $imagePath = $profile['image_path'] ?? '';
                $absolutePath = $imagePath ? __DIR__ . '/' . $imagePath : '';
                if ($imagePath === '' || !is_file($absolutePath)) {
                    $imagePath = 'assets/placeholder.svg';
                }
              ?>
              <article class="profile-card">
                <img
                  src="<?php echo e($imagePath); ?>"
                  alt="<?php echo e($profile['name']); ?>"
                  class="profile-photo"
                />
                <div class="profile-body">
                  <h3><?php echo e($profile['name']); ?></h3>
                  <p class="profile-role"><?php echo e($profile['role']); ?></p>
                  <p class="profile-note"><?php echo e(excerpt($profile['bio'])); ?></p>
                  <a class="read-more" href="profile.php?id=<?php echo (int) $profile['id']; ?>">
                    Read more
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </main>

      <footer class="site-footer">
        <p class="update-link">
          Want your profile here too? <a href="profile-update.php">Reach out to us</a>.
        </p>
        <p>(c) <?php echo date('Y'); ?> Bauchi Pearl Magazine</p>
      </footer>
    </div>
  </body>
</html>
