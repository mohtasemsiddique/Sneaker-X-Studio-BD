<?php
require_once 'db.php';
$id = intval($_GET['post_id']);
$pdo->prepare("UPDATE posts SET is_deleted = 1 WHERE post_id = ?")->execute([$id]);
header("Location: forum.php");
