<?php
session_start();
function generateCaptcha($type) {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    $_SESSION[$type] = $captcha;
    echo $captcha;
}

// Use: captcha.php?type=captcha_login or captcha_signup
$type = $_GET['type'] ?? 'captcha_login';
generateCaptcha($type);
?>
