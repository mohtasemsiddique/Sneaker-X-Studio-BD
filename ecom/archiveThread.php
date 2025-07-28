<?php
require_once 'db.php';
$id = intval($_GET['thread_id']);
$pdo->prepare("UPDATE threads SET is_archived = 1 WHERE thread_id = ?")->execute([$id]);
header("Location: forum.php");
