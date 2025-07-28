<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'superadmin')) {
    die("Access denied.");
}

$userId = $_GET['id'] ?? 0;
if ($userId > 0) {
    $stmt = $pdo->prepare("UPDATE users SET is_locked = 1 WHERE user_id = ?");
    $stmt->execute([$userId]);
}

header("Location: admin.php");
exit();
?>
