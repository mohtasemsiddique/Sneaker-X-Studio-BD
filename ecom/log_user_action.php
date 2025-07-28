<?php
// log_user_action.php
function logUserAction($pdo, $userId, $action, $page) {
    $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action, page) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $action, $page]);
}
?>
