<?php
require __DIR__ . '/bootstrap.php';

$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Profile - Bauchi Pearl Magazine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="../assets/bph-favicon.png" type="image/png">
    <link rel="stylesheet" href="admin.css" />
  </head>
  <body>
    <div class="admin-page">
      <header class="admin-header">
        <div>
          <img src="../assets/bhp-logo.png" alt="Bauchi Pearl Magazine Logo" class="site-logo" style="width: 100px;" />
          <p class="kicker">Bauchi Pearl Magazine</p>
          <h1>Add Profile</h1>
        </div>
        <div class="admin-actions">
          <a class="button ghost" href="./">Back to Dashboard</a>
        </div>
      </header>

      <?php if ($error === 'missing') : ?>
        <div class="panel warning">Please fill in all required fields.</div>
      <?php elseif ($error === 'image') : ?>
        <div class="panel warning">Please upload a valid image file.</div>
      <?php endif; ?>

      <form class="panel form" method="post" action="store.php" enctype="multipart/form-data">
        <label>
          Name
          <input type="text" name="name" required />
        </label>
        <label>
          Role / Title
          <input type="text" name="role" required />
        </label>
        <label>
          Profile Bio
          <textarea name="bio" rows="6" required></textarea>
        </label>
        <label>
          Profile Photo
          <input type="file" name="image" accept="image/png,image/jpeg,image/webp" required />
        </label>
        <div class="form-actions">
          <button class="button" type="submit">Save Profile</button>
          <a class="button ghost" href="./">Cancel</a>
        </div>
      </form>
    </div>
  </body>
</html>
