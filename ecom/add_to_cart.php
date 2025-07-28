<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int) ($_POST['product_id'] ?? 0);
$quantity = (int) ($_POST['quantity'] ?? 1);

if ($product_id > 0 && $quantity > 0) {
    // Check if the product is already in the cart
    $checkStmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$user_id, $product_id]);

    if ($checkStmt->rowCount() > 0) {
        // Update existing quantity
        $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?")
            ->execute([$quantity, $user_id, $product_id]);
    } else {
        // Insert new item
        $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)")
            ->execute([$user_id, $product_id, $quantity]);
    }

    $_SESSION['message'] = "Item added to cart!";
}

header("Location: cart.php");
exit;
