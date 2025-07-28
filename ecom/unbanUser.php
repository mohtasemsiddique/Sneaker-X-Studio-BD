<?php
require_once 'db.php';
$id = intval($_GET['id']);
$pdo->prepare("UPDATE users SET is_archived = 0 WHERE user_id = ?")->execute([$id]);
header("Location: admin.php");
