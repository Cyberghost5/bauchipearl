<?php
require __DIR__ . '/db.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$name = '';
$email = '';
$phone = '';
$message = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = db()->prepare(
                'INSERT INTO profile_requests (name, email, phone, message) VALUES (:name, :email, :phone, :message)'
            );
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
            ]);
            $success = true;
            $name = '';
            $email = '';
            $phone = '';
            $message = '';
        } catch (Throwable $throwable) {
            $error = 'Unable to submit your request right now. Please try again later.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile Update Request - Bauchi Pearl Magazine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="/assets/bph-favicon.png" type="image/png">
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body>
    <div class="page">
      <header class="site-header">
        <p class="kicker">Bauchi Pearl Magazine</p>
        <center>
          <img src="/assets/bhp-logo.png" alt="Bauchi Pearl Magazine Logo" class="site-logo" style="width: 200px;" />
        </center>
        <h1>Request a Profile Update</h1>
        <p class="intro">
          Share the details you'd like updated or added to your profile. Our
          editorial team will review and follow up with you.
        </p>
      </header>

      <main class="request-page">
        <?php if ($success) : ?>
          <p class="status-message">Thanks! Your request has been received.</p>
        <?php elseif ($error !== '') : ?>
          <p class="status-message"><?php echo e($error); ?></p>
        <?php endif; ?>

        <form class="request-form" method="post" action="/profile-update">
          <label>
            Full Name
            <input type="text" name="name" value="<?php echo e($name); ?>" required />
          </label>
          <label>
            Email Address
            <input type="email" name="email" value="<?php echo e($email); ?>" required />
          </label>
          <label>
            Phone Number (optional)
            <input type="text" name="phone" value="<?php echo e($phone); ?>" />
          </label>
          <label>
             Message
            <textarea name="message" rows="6" required><?php echo e($message); ?></textarea>
          </label>
          <div class="request-actions">
            <button class="read-more" type="submit">Send Request</button>
            <a class="read-more" href="/">Back to Profiles</a>
          </div>
        </form>
      </main>

      <footer class="site-footer">
        <p>(c) <?php echo date('Y'); ?> Bauchi Pearl Magazine</p>
      </footer>
    </div>
  </body>
</html>
