<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'superadmin')) {
    die("Access denied.");
}

$search = $_GET['search'] ?? '';
$current_user_role = $_SESSION['user_type'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE ?");
$stmt->execute(["%$search%"]);
$users = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM user_logs ORDER BY timestamp DESC LIMIT 100");
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel | SneakerXStudio</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<section id="header">
  <a href="index.php"><img src="img/logo.jpg" alt="Logo"></a>
  <ul id="navbar">
    <li><a href="index.php">Home</a></li>
    <li><a href="shop.php">Shop</a></li>
    <li><a href="exclusive.php">Exclusive</a></li>
    <li><a href="cart.php">Cart</a></li>
    <li><a href="admin.php" class="active">Admin</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</section>

<h1>👤 Admin - User Management</h1>

<form method="get">
  <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
  <button type="submit">Search</button>
</form>

<table>
  <tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th><th>Actions</th></tr>
  <?php foreach ($users as $user): ?>
    <tr>
      <td><?= $user['user_id'] ?></td>
      <td><?= htmlspecialchars($user['username']) ?></td>
      <td><?= htmlspecialchars($user['email']) ?></td>
      <td><?= $user['is_archived'] ? 'Banned' : 'Active' ?></td>
      <td>
        <a href="editUser.php?id=<?= $user['user_id'] ?>">Edit</a>
        <?php if ($current_user_role === 'superadmin'): ?>
          <?php if ($user['is_archived']): ?>
            <a href="unbanUser.php?id=<?= $user['user_id'] ?>">Unban</a>
          <?php else: ?>
            <a href="banUser.php?id=<?= $user['user_id'] ?>">Ban</a>
          <?php endif; ?>
        <?php else: ?>
          <span style="color:gray;">Restricted</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<h2>🌤 Weather API</h2>
<?php
$apiKey = 'e7fda8a796e44609998100816252206'; // Replace with real key
$location = 'Melbourne';
$response = @file_get_contents("https://api.weatherapi.com/v1/current.json?key=$apiKey&q=$location");
$data = json_decode($response, true);
?>
<div>
  <?php if ($data && isset($data['current'])): ?>
    <p><strong>Location:</strong> <?= htmlspecialchars($data['location']['name']) ?></p>
    <p><strong>Temperature:</strong> <?= $data['current']['temp_c'] ?>°C</p>
    <p><strong>Condition:</strong> <?= htmlspecialchars($data['current']['condition']['text']) ?></p>
  <?php else: ?>
    <p>Unable to load weather info.</p>
  <?php endif; ?>
</div>

<h2>📊 Recent User Activity Logs</h2>
<table>
  <tr><th>User ID</th><th>Action</th><th>Page</th><th>Time</th></tr>
  <?php foreach ($logs as $log): ?>
    <tr>
      <td><?= $log['user_id'] ?></td>
      <td><?= htmlspecialchars($log['action']) ?></td>
      <td><?= htmlspecialchars($log['page']) ?></td>
      <td><?= $log['timestamp'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
</body>
</html>
