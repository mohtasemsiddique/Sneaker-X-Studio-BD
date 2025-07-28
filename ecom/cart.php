<?php
require_once 'db.php';
require_once 'header.php';
include 'log_user_action.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT p.product_id, p.name, p.price, p.image_url, c.quantity,
           (p.price * c.quantity) AS subtotal
    FROM cart_items c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['subtotal'];
}

// Handle discount
$discount = 0;
$discount_code = '';
if (isset($_SESSION['discount']) && isset($_SESSION['coupon_code'])) {
    $discount = $subtotal * $_SESSION['discount'];
    $discount_code = $_SESSION['coupon_code'];
}
$total = $subtotal - $discount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SneakersxStudio - Cart</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
</head>
<body>

<section id="cart" class="section-p1">
  <?php if (isset($_SESSION['message'])): ?>
    <p style="color: green;"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
  <?php endif; ?>

  <form method="post" action="update_cart.php">
    <table width="100%">
      <thead>
        <tr>
          <td>Remove</td>
          <td>Image</td>
          <td>Product</td>
          <td>Price</td>
          <td>Quantity</td>
          <td>Subtotal</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart_items as $item): ?>
          <tr>
            <td>
              <a href="remove_cart_item.php?product_id=<?= $item['product_id'] ?>">
                <i class="fas fa-times-circle"></i>
              </a>
            </td>
            <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="product" width="60"></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td>
              <input type="number" name="quantities[<?= $item['product_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" style="width: 50px;">
            </td>
            <td>$<?= number_format($item['subtotal'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <button type="submit" class="normal">Update Cart</button>
  </form>
</section>

<section id="cart-add" class="section-p1">
  <div id="coupon">
    <h3>Apply Coupon</h3>
    <form action="apply_coupon.php" method="POST">
      <input type="text" name="coupon_code" placeholder="Enter your Coupon" value="<?= htmlspecialchars($discount_code) ?>">
      <button class="normal" type="submit">Apply</button>
    </form>
  </div>

  <div id="subtotal">
    <h3>Cart Total</h3>
    <table>
      <tr>
        <td>Cart Subtotal</td>
        <td>$<?= number_format($subtotal, 2) ?></td>
      </tr>
      <?php if ($discount > 0): ?>
      <tr>
        <td>Coupon (<?= htmlspecialchars($discount_code) ?>)</td>
        <td>−$<?= number_format($discount, 2) ?></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td>Shipping</td>
        <td>Free</td>
      </tr>
      <tr>
        <td><strong>Total</strong></td>
        <td><strong>$<?= number_format($total, 2) ?></strong></td>
      </tr>
    </table>
    <form action="checkout.php" method="POST">
      <input type="hidden" name="total_price" value="<?= $total ?>">
      <button class="normal" type="submit">Proceed to Checkout</button>
    </form>
  </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
