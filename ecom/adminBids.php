<?php
include_once 'db.php';
session_start();

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo "Access denied.";
    exit();
}

$product_id = $_GET['product_id'] ?? null;
if (!$product_id) {
    echo "No product selected.";
    exit();
}

$stmt = $pdo->prepare("
  SELECT b.bid_amount, b.size, b.bid_time, u.username 
  FROM bids b
  JOIN exclusive_products e ON b.exclusive_id = e.exclusive_id
  JOIN users u ON b.user_id = u.user_id
  WHERE e.product_id = ?
  ORDER BY b.bid_amount DESC
");
$stmt->execute([$product_id]);
$bids = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - Bid List</title>
  <link rel="stylesheet" href="exclusive.css">
</head>
<body>
  <section id="header">
    <a href="index.php"><img src="img/logo.png" alt="Logo"></a>
    <ul id="navbar">
      <li><a href="admin.php" class="active">Admin</a></li>
    </ul>
  </section>

  <div class="exclusive-container">
    <h2>All Bids for Product ID: <?php echo htmlspecialchars($product_id); ?></h2>
    <table style="width: 100%; color: white; border-collapse: collapse;">
      <thead>
        <tr style="border-bottom: 1px solid #00ffc3;">
          <th style="text-align: left;">Username</th>
          <th>Amount</th>
          <th>Size</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bids as $bid): ?>
          <tr>
            <td><?php echo htmlspecialchars($bid['username']); ?></td>
            <td>$<?php echo number_format($bid['bid_amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($bid['size']); ?></td>
            <td><?php echo date("M d, H:i", strtotime($bid['bid_time'])); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
