<?php
require_once 'db.php';
session_start();

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['order_id'];

$stmt = $pdo->prepare("
    SELECT o.order_id, o.total_price, o.created_at, p.name, p.image_url, oi.quantity, oi.price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    echo "<h2>Invalid Order.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Summary</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<section class="section-p1">
  <h2>Thank you! Your order has been placed.</h2>
  <p>Order ID: <?= htmlspecialchars($order_id) ?> | Date: <?= htmlspecialchars($items[0]['created_at']) ?></p>
  <table width="100%">
    <thead>
      <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><img src="<?= htmlspecialchars($item['image_url']) ?>" width="60"></td>
        <td><?= htmlspecialchars($item['name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>$<?= number_format($item['price'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <h3>Total Paid: $<?= number_format($items[0]['total_price'], 2) ?></h3>
</section>
</body>
</html>
