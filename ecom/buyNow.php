<?php
session_start();
require_once  'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in first. <a href='login.php'>Login</a>");
}

$user_id = $_SESSION['user_id'];
$exclusive_id = intval($_POST['exclusive_id']);

// Validate time
$stmt = $pdo->prepare("SELECT end_time FROM exclusive_products WHERE exclusive_id = ?");
$stmt->execute([$exclusive_id]);
$end_time = $stmt->fetchColumn();

if (!$end_time || strtotime($end_time) < time()) {
    die("Bidding time has ended.");
}

// Simulate instant win by inserting a VERY high bid
$buyNowAmount = 999999.99;
$insert = $pdo->prepare("INSERT INTO bids (exclusive_id, user_id, bid_amount) VALUES (?, ?, ?)");
$insert->execute([$exclusive_id, $user_id, $buyNowAmount]);

// Optional: you could mark this item as sold (add a column `is_sold` if needed)

header("Location: bidDetail.php?exclusive_id=" . $exclusive_id . "&buy=success");
exit;
?>
