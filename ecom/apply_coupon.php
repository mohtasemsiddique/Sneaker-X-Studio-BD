<?php
session_start();

$code = trim($_POST['coupon_code'] ?? '');

if ($code === 'DAVID50') {
    $_SESSION['coupon_code'] = $code;
    $_SESSION['discount'] = 0.50;
    $_SESSION['message'] = 'Coupon applied: 50% off!';
} else {
    unset($_SESSION['coupon_code'], $_SESSION['discount']);
    $_SESSION['message'] = 'Invalid coupon code.';
}

header("Location: cart.php");
exit;
