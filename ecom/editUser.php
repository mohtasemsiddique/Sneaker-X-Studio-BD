<?php
session_start();
require_once 'db.php';

// ✅ Only admin or superadmin can edit users
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'superadmin')) {
    die("Access denied.");
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    die("User ID missing.");
}

// ✅ Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $user_type = $_POST['user_type'] ?? 'user';

    if ($username && $email) {
        $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, user_type = ? WHERE user_id = ?");
        $updateStmt->execute([$username, $email, $user_type, $user_id]);

        header("Location: admin.php");
        exit();
    } else {
        echo "<p style='color:red;'>All fields are required.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<section id="header">
  <a href="index.php"><img src="img/logo.png" alt="Logo"></a>
  <ul id="navbar">
    <li><a href="index.php">Home</a></li>
    <li><a href="admin.php" class="active">Admin</a></li>
  </ul>
</section>

<div class="exclusive-container" style="max-width: 600px; margin: auto;">
  <h2>Edit User: <?= htmlspecialchars($user['username']) ?></h2>
  <form method="POST" style="display: flex; flex-direction: column; gap: 10px;">
    <label>Username:
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    </label>
    <label>Email:
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </label>
    <label>User Role:
      <select name="user_type">
        <option value="user" <?= $user['user_type'] === 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $user['user_type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="superadmin" <?= $user['user_type'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
      </select>
    </label>
    <button type="submit" class="bid-btn">Save Changes</button>
  </form>
</div>
</body>
</html>
