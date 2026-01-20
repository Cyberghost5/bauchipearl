<?php
require __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect('./');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '' && verify_admin_credentials($username, $password)) {
        login_admin($username);
        redirect('./');
    }

    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - Bauchi Pearl Magazine</title>
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
    <div class="admin-page login-page">
      <div class="login-card panel">
        <img src="../assets/bhp-logo.png" alt="Bauchi Pearl Magazine Logo" class="site-logo" style="width: 100px;" />
        <p class="kicker">Bauchi Pearl Magazine</p>
        <h1>Admin Login</h1>
        <?php if ($error !== '') : ?>
          <p class="login-error"><?php echo e($error); ?></p>
        <?php endif; ?>
        <form class="form" method="post" action="login.php">
          <label>
            Username
            <input type="text" name="username" autocomplete="username" required />
          </label>
          <label>
            Password
            <input type="password" name="password" autocomplete="current-password" required />
          </label>
          <button class="button" type="submit">Log In</button>
        </form>
      </div>
    </div>
  </body>
</html>
