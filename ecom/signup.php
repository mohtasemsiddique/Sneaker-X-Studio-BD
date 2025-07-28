<?php
// signup.php
session_start();
require_once 'db.php'; // contains $pdo (PDO instance)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $captcha_input = $_POST['captcha'];
    $captcha_expected = $_SESSION['captcha_signup'] ?? '';

    if ($captcha_input !== $captcha_expected) {
        die("CAPTCHA failed. Please try again.");
    }

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        die("Email already registered.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, failed_logins, is_archived) VALUES (?, ?, ?, ?, 0, 0)");
    $stmt->execute([$first, $last, $email, $hashed]);

    echo "Registration successful. You may now <a href='login.php'>log in</a>.";
    exit;
}
?>
