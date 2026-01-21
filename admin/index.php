<?php
require __DIR__ . '/bootstrap.php';

require_login();

$profiles = db()->query('SELECT id, name, role, image_path, created_at FROM profiles ORDER BY created_at DESC')
    ->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - Bauchi Pearl Magazine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="/assets/bph-favicon.png" type="image/png">
    <link rel="stylesheet" href="/admin/admin.css" />
  </head>
  <body>
    <div class="admin-page">
      <header class="admin-header">
        <div>
          <img src="/assets/bhp-logo.png" alt="Bauchi Pearl Magazine Logo" class="site-logo" style="width: 100px;" />
          <p class="kicker">Bauchi Pearl Magazine</p>
          <h1>Profile Admin</h1>
        </div>
        <div class="admin-actions">
          <a class="button ghost" href="/">View Site</a>
          <a class="button ghost" href="/admin/logout">Log Out</a>
          <a class="button" href="/admin/create">Add Profile</a>
        </div>
      </header>

      <?php if (empty($profiles)) : ?>
        <div class="panel">
          <p>No profiles found. Add your first profile to get started.</p>
        </div>
      <?php else : ?>
        <div class="panel">
          <table class="profile-table">
            <thead>
              <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($profiles as $profile) : ?>
                <tr>
                  <td>
                    <img
                      class="table-photo"
                      src="<?php echo e(normalize_image_path($profile['image_path'] ?? '')); ?>"
                      alt="<?php echo e($profile['name']); ?>"
                    />
                  </td>
                  <td><?php echo e($profile['name']); ?></td>
                  <td><?php echo e($profile['role']); ?></td>
                  <td><?php echo e(date('Y-m-d', strtotime($profile['created_at']))); ?></td>
                  <td>
                    <div class="table-actions">
                      <a class="link" href="/admin/edit?id=<?php echo (int) $profile['id']; ?>">Edit</a>
                      <form method="post" action="/admin/delete" onsubmit="return confirm('Delete this profile?');">
                        <input type="hidden" name="id" value="<?php echo (int) $profile['id']; ?>" />
                        <button class="link danger" type="submit">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </body>
</html>
