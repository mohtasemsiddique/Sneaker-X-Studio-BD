<?php
session_start();
include 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartStmt = $pdo->prepare("SELECT c.*, p.name, p.price FROM cart_items c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
$cartStmt->execute([$user_id]);
$cartItems = $cartStmt->fetchAll();

if (empty($cartItems)) {
    echo "<p style='color:black; padding:20px;'>Your cart is empty.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $address = $_POST['address'] ?? '';
    $cardRaw = $_POST['card'] ?? '';

    if (!$fullname || !$address || strlen($cardRaw) < 4) {
        echo "<p style='color:red; text-align:center;'>Please fill in all fields correctly.</p>";
    } else {
        $card = str_repeat("*", max(0, strlen($cardRaw) - 4)) . substr($cardRaw, -4);
        $total = 0;
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Order Confirmation | SneakerXStudio</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>

        <section id="header">
            <a href="index.php"><img src="img/logo.jpg" alt="Logo"></a>
            <ul id="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="aboutUs.php">About</a></li>
            </ul>
        </section>

        <section id="order-summary">
            <h2>Thank you for your order, <?= htmlspecialchars($fullname) ?>!</h2>
            <p>Delivery address: <strong><?= htmlspecialchars($address) ?></strong></p>
            <p>Paid with card ending in <strong><?= htmlspecialchars($card) ?></strong></p>

            <h3>Order Summary</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach ($cartItems as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($subtotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>$<?= number_format($total, 2) ?></strong></td>
                </tr>
            </table>

            <p style="color: green; margin-top: 20px;">This is a simulated purchase. No payment has been processed.</p>
            <a href="index.php" class="normal">Back to Home</a>
        </section>

        </body>
        </html>

        <?php
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?")->execute([$user_id]);
        exit();
    }
}
?>

<!-- Checkout Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | SneakerXStudio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<section id="header">
    <a href="index.php"><img src="img/logo.jpg" alt="Logo"></a>
    <ul id="navbar">
        <li><a href="index.php">Home</a></li>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="cart.php" class="active">Cart</a></li>
        <li><a href="aboutUs.php">About</a></li>
    </ul>
</section>

<div class="section-p1" style="max-width: 600px; margin: 0 auto;">
    <h2>Checkout</h2>
    <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" required style="padding: 10px;">

        <label for="address">Delivery Address:</label>
        <textarea id="address" name="address" rows="3" required style="padding: 10px;"></textarea>

        <label for="card">Credit Card Number:</label>
        <input type="text" id="card" name="card" required minlength="12" maxlength="19" style="padding: 10px;">

        <button type="submit" class="normal" style="margin-top: 10px;">Place Order</button>
    </form>
</div>
</body>
</html>
