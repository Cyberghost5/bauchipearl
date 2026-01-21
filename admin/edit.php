<?php
require __DIR__ . '/bootstrap.php';

require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('/admin');
}

$stmt = db()->prepare('SELECT id, name, role, bio, image_path FROM profiles WHERE id = :id');
$stmt->execute(['id' => $id]);
$profile = $stmt->fetch();

if (!$profile) {
    redirect('/admin');
}

$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile - Bauchi Pearl Magazine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="/assets/bph-favicon.png" type="image/png">
    <link rel="stylesheet" href="/admin/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css"
    />
  </head>
  <body>
    <div class="admin-page">
      <header class="admin-header">
        <div>
          <p class="kicker">Bauchi Pearl Magazine</p>
          <h1>Edit Profile</h1>
        </div>
        <div class="admin-actions">
          <a class="button ghost" href="/admin">Back to Dashboard</a>
          <a class="button ghost" href="/admin/logout">Log Out</a>
        </div>
      </header>

      <?php if ($error === 'missing') : ?>
        <div class="panel warning">Please fill in all required fields.</div>
      <?php elseif ($error === 'image') : ?>
        <div class="panel warning">Please upload a valid image file.</div>
      <?php endif; ?>

      <form class="panel form" method="post" action="/admin/update" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo (int) $profile['id']; ?>" />
        <label>
          Name
          <input type="text" name="name" value="<?php echo e($profile['name']); ?>" required />
        </label>
        <label>
          Role / Title
          <input type="text" name="role" value="<?php echo e($profile['role']); ?>" required />
        </label>
        <label>
          Profile Bio
          <textarea id="bio" name="bio" rows="6" required><?php echo e($profile['bio']); ?></textarea>
        </label>
        <label>
          Replace Photo
          <input type="file" name="image" accept="image/png,image/jpeg,image/webp" />
        </label>
        <div class="current-photo">
          <p>Current photo:</p>
          <img
            src="<?php echo e(normalize_image_path($profile['image_path'] ?? '')); ?>"
            alt="<?php echo e($profile['name']); ?>"
          />
        </div>
        <div class="form-actions">
          <button class="button" type="submit">Update Profile</button>
          <a class="button ghost" href="/admin">Cancel</a>
        </div>
      </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script>
      $(function () {
        $("#bio").summernote({
          height: 220,
          toolbar: [
            ["style", ["bold", "italic", "underline"]],
            ["para", ["ul", "ol", "paragraph"]],
          ],
        });
      });
    </script>
  </body>
</html>
