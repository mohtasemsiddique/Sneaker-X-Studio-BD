<?php
session_start();
include("db.php");
include("header.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href='login.php'>log in</a> to use the discussion forum.</p>";
    include("footer.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle new thread creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_thread'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_url = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "img/forum/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $pdo->prepare("INSERT INTO threads (title, created_by) VALUES (?, ?)")
        ->execute([$title, $user_id]);
    $thread_id = $pdo->lastInsertId();

    $pdo->prepare("INSERT INTO posts (thread_id, user_id, content, image_url) VALUES (?, ?, ?, ?)")
        ->execute([$thread_id, $user_id, $content, $image_url]);
}

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $thread_id = $_POST['thread_id'];
    $content = trim($_POST['content']);
    $image_url = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "img/forum/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $pdo->prepare("INSERT INTO posts (thread_id, user_id, content, image_url) VALUES (?, ?, ?, ?)")
        ->execute([$thread_id, $user_id, $content, $image_url]);
}

// Handle delete (soft delete)
if (isset($_GET['delete_post']) && is_numeric($_GET['delete_post'])) {
    $post_id = $_GET['delete_post'];
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $result = $stmt->fetch();
    if ($result && $result['user_id'] == $user_id) {
        $pdo->prepare("UPDATE posts SET is_deleted = 1 WHERE post_id = ?")
            ->execute([$post_id]);
    }
}

// Filter clause
$filter_clause = '';
$params = [];

if (!empty($_GET['filter_text'])) {
    $filter_clause .= " AND (t.title LIKE ? OR p.content LIKE ?)";
    $filter_text = "%" . $_GET['filter_text'] . "%";
    $params[] = $filter_text;
    $params[] = $filter_text;
}
if (!empty($_GET['date_start'])) {
    $filter_clause .= " AND t.created_at >= ?";
    $params[] = $_GET['date_start'];
}
if (!empty($_GET['date_end'])) {
    $filter_clause .= " AND t.created_at <= ?";
    $params[] = $_GET['date_end'];
}

// Fetch threads
$sql = "SELECT DISTINCT t.thread_id, t.title, t.created_at, u.username
        FROM threads t
        JOIN users u ON t.created_by = u.user_id
        JOIN posts p ON p.thread_id = t.thread_id
        WHERE t.is_archived = 0 $filter_clause
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$threads = $stmt->fetchAll();
?>

<div class="container">
    <h2>Discussion Forum</h2>

    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <input type="text" name="filter_text" placeholder="Search..." value="<?= $_GET['filter_text'] ?? '' ?>">
        <input type="date" name="date_start" value="<?= $_GET['date_start'] ?? '' ?>">
        <input type="date" name="date_end" value="<?= $_GET['date_end'] ?? '' ?>">
        <button type="submit">Filter</button>
        <a href="blog.php">Clear</a>
    </form>

    <!-- New Thread Form -->
    <form method="POST" enctype="multipart/form-data" class="thread-form">
        <h4>Start a New Thread</h4>
        <input type="text" name="title" placeholder="Thread Title" required>
        <textarea name="content" placeholder="Post Content" required></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="new_thread">Post Thread</button>
    </form>

    <hr>

    <!-- Threads and Replies -->
    <?php foreach ($threads as $thread): ?>
        <div class="thread-box">
            <h4><?= htmlspecialchars($thread['title']) ?></h4>
            <p><small>Posted by <strong><?= htmlspecialchars($thread['username']) ?></strong> on <?= $thread['created_at'] ?></small></p>

            <?php
            $post_stmt = $pdo->prepare("SELECT p.post_id, p.content, p.created_at, p.image_url, u.username, u.user_id
                                        FROM posts p
                                        JOIN users u ON p.user_id = u.user_id
                                        WHERE p.thread_id = ? AND p.is_deleted = 0
                                        ORDER BY p.created_at ASC");
            $post_stmt->execute([$thread['thread_id']]);
            $posts = $post_stmt->fetchAll();
            ?>

            <div class="replies">
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <p><strong><?= htmlspecialchars($post['username']) ?>:</strong> <?= htmlspecialchars($post['content']) ?></p>
                        <?php if (!empty($post['image_url'])): ?>
                            <img src="<?= htmlspecialchars($post['image_url']) ?>" style="max-width: 200px;" alt="Post Image">
                        <?php endif; ?>
                        <small><?= $post['created_at'] ?></small>
                        <?php if ($post['user_id'] == $user_id): ?>
                            <a href="?delete_post=<?= $post['post_id'] ?>" onclick="return confirm('Delete this post?')">Delete</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Reply Form -->
            <form method="POST" enctype="multipart/form-data" class="reply-form">
                <input type="hidden" name="thread_id" value="<?= $thread['thread_id'] ?>">
                <textarea name="content" placeholder="Write a reply..." required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit" name="reply">Reply</button>
            </form>
        </div>
        <hr>
    <?php endforeach; ?>
</div>

<?php include("footer.php"); ?>
<style>
    /* Container */
.container {
    max-width: 900px;
    margin: 30px auto;
    padding: 20px;
    background: #fdfdfd;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
}

/* Headings */
.container h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #222;
}

h4 {
    margin: 20px 0 10px;
    font-weight: 600;
}

/* Forms */
form {
    margin-bottom: 30px;
}

input[type="text"],
input[type="date"],
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
}

input[type="file"] {
    margin: 5px 0 15px;
}

button {
    background-color: #111;
    color: #fff;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background-color: #333;
}

/* Filter Form */
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
}

.filter-form input,
.filter-form button {
    flex: 1;
}

/* Thread Box */
.thread-box {
    background: #fff;
    padding: 20px;
    margin-bottom: 25px;
    border-left: 4px solid #222;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* Posts */
.replies .post {
    padding: 12px;
    margin-top: 12px;
    background: #f3f3f3;
    border-left: 4px solid #666;
    border-radius: 6px;
    position: relative;
}

.post img {
    margin-top: 10px;
    max-width: 100%;
    border-radius: 6px;
}

.post small {
    display: block;
    margin-top: 5px;
    color: #555;
    font-size: 0.9rem;
}

.post a {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #c00;
    font-size: 0.85rem;
    text-decoration: none;
}

.post a:hover {
    text-decoration: underline;
}
</style>