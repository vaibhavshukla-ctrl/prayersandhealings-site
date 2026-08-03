<?php
require_once 'db-config.php';

// Simple password protection. Change this password before uploading.
define('ADMIN_PASSWORD', 'changeme123');

session_start();

if (isset($_POST['login_password'])) {
    if ($_POST['login_password'] === ADMIN_PASSWORD) {
        $_SESSION['prayer_admin'] = true;
    } else {
        $loginError = 'Incorrect password.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-prayers.php');
    exit;
}

$isLoggedIn = isset($_SESSION['prayer_admin']) && $_SESSION['prayer_admin'] === true;

if ($isLoggedIn) {
    $conn = getDbConnection();

    // Handle approve / delete actions
    if (isset($_POST['approve_id'])) {
        $id = (int) $_POST['approve_id'];
        $conn->query("UPDATE prayers SET is_approved = 1 WHERE id = $id");
    }
    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        $conn->query("DELETE FROM prayers WHERE id = $id");
    }

    $pending = $conn->query("SELECT * FROM prayers WHERE is_approved = 0 ORDER BY created_at ASC");
    $approved = $conn->query("SELECT * FROM prayers WHERE is_approved = 1 ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Prayer Admin | Prayers and Healings</title>
<style>
  body{font-family:Arial,sans-serif;background:#faf6ee;color:#3e2a15;max-width:900px;margin:40px auto;padding:0 20px;}
  h1{font-size:1.6rem;}
  .login-box{max-width:340px;margin:80px auto;background:#fff;padding:30px;border-radius:12px;border:1px solid #eee;}
  input[type=password]{width:100%;padding:10px;margin:10px 0;border-radius:8px;border:1px solid #ccc;box-sizing:border-box;}
  button{background:#3e2a15;color:#fff;border:none;padding:10px 18px;border-radius:8px;cursor:pointer;}
  .error{color:#a1442a;}
  .card{background:#fff;border:1px solid #eee;border-radius:10px;padding:16px 20px;margin-bottom:14px;}
  .tag{font-size:0.75rem;text-transform:uppercase;color:#a97328;font-weight:700;}
  .actions{margin-top:10px;display:flex;gap:10px;}
  .approve{background:#4f6f5c;color:#fff;border:none;padding:8px 14px;border-radius:6px;cursor:pointer;}
  .delete{background:#a1442a;color:#fff;border:none;padding:8px 14px;border-radius:6px;cursor:pointer;}
  .count{font-size:0.85rem;color:#6b543a;}
  .logout{float:right;font-size:0.85rem;}
</style>
</head>
<body>

<?php if (!$isLoggedIn): ?>
  <div class="login-box">
    <h1>Prayer Admin</h1>
    <?php if (isset($loginError)): ?><p class="error"><?php echo $loginError; ?></p><?php endif; ?>
    <form method="post">
      <input type="password" name="login_password" placeholder="Admin password" required>
      <button type="submit">Log In</button>
    </form>
  </div>
<?php else: ?>
  <a class="logout" href="?logout=1">Log out</a>
  <h1>Pending Prayer Requests (<?php echo $pending->num_rows; ?>)</h1>
  <?php if ($pending->num_rows === 0): ?>
    <p>Nothing waiting for review.</p>
  <?php endif; ?>
  <?php while ($row = $pending->fetch_assoc()): ?>
    <div class="card">
      <span class="tag"><?php echo htmlspecialchars($row['category']); ?></span>
      <p><?php echo nl2br(htmlspecialchars($row['intention_text'])); ?></p>
      <?php if ($row['contact_email']): ?><p class="count">Contact: <?php echo htmlspecialchars($row['contact_email']); ?></p><?php endif; ?>
      <div class="actions">
        <form method="post" style="display:inline;">
          <input type="hidden" name="approve_id" value="<?php echo (int)$row['id']; ?>">
          <button type="submit" class="approve">Approve &amp; Publish</button>
        </form>
        <form method="post" style="display:inline;" onsubmit="return confirm('Delete this request permanently?');">
          <input type="hidden" name="delete_id" value="<?php echo (int)$row['id']; ?>">
          <button type="submit" class="delete">Delete</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>

  <h1>Live on Prayer Wall (<?php echo $approved->num_rows; ?>)</h1>
  <?php while ($row = $approved->fetch_assoc()): ?>
    <div class="card">
      <span class="tag"><?php echo htmlspecialchars($row['category']); ?></span>
      <p><?php echo nl2br(htmlspecialchars($row['intention_text'])); ?></p>
      <p class="count"><?php echo (int)$row['prayer_count']; ?> people have prayed for this</p>
      <div class="actions">
        <form method="post" style="display:inline;" onsubmit="return confirm('Remove this from the public wall?');">
          <input type="hidden" name="delete_id" value="<?php echo (int)$row['id']; ?>">
          <button type="submit" class="delete">Remove</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</body>
</html>
