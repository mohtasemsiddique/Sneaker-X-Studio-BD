<?php
function log_action($pdo, $user_id, $action, $page) {
  $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action, page) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $action, $page]);
}
