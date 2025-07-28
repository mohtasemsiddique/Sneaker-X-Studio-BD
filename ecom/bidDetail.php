<?php
include 'db.php';
session_start();
include 'log_user_action.php';
if (isset($_SESSION['user_id'])) {
    logUserAction($pdo, $_SESSION['user_id'], 'Page View', basename(__FILE__));
}
$product_id = null;

// Support both ?id and ?exclusive_id
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
} elseif (isset($_GET['exclusive_id'])) {
    $exclusiveStmt = $pdo->prepare("SELECT product_id FROM exclusive_products WHERE exclusive_id = ?");
    $exclusiveStmt->execute([$_GET['exclusive_id']]);
    $product_id = $exclusiveStmt->fetchColumn();
}

if (!$product_id) {
    echo "No product selected.";
    exit();
}

// Fetch product and bid data
$stmt = $pdo->prepare("
    SELECT p.*, e.exclusive_id, e.start_time, e.end_time 
    FROM products p 
    JOIN exclusive_products e ON p.product_id = e.product_id 
    WHERE p.product_id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit();
}

$exclusive_id = $product['exclusive_id'];

// Fetch highest bid
$bidStmt = $pdo->prepare("SELECT MAX(bid_amount) AS highest_bid FROM bids WHERE exclusive_id = ?");
$bidStmt->execute([$exclusive_id]);
$highestBid = $bidStmt->fetchColumn();

// Fetch user's bid
$userBid = 0;
if (isset($_SESSION['user_id'])) {
    $userStmt = $pdo->prepare("SELECT bid_amount FROM bids WHERE exclusive_id = ? AND user_id = ? ORDER BY bid_time DESC LIMIT 1");
    $userStmt->execute([$exclusive_id, $_SESSION['user_id']]);
    $userBid = $userStmt->fetchColumn() ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['name']); ?> | Bid Now</title>
  <meta http-equiv="refresh" content="15">
  <link rel="stylesheet" href="css/exclusive.css">
</head>
<body>

<section id="header">
  <a href="index.php"><img src="img/logo.jpg" alt="Logo"></a>
  <ul id="navbar">
    <li><a href="index.php">Home</a></li>
    <li><a href="shop.php">Shop</a></li>
    <li><a href="exclusive.php" class="active">Exclusive</a></li>
    <li><a href="aboutUs.php">About</a></li>
    <li><a href="blog.php">Blog</a></li>
  </ul>
</section>

<div class="exclusive-container" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; margin-top: 40px;">
  <div style="flex: 1; min-width: 300px; max-width: 450px;">
    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; border-radius: 12px;">
  </div>
  <div style="flex: 1; min-width: 300px; color: #fff;">
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <p><strong>Retail Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
    <p><strong>Current Bid:</strong> $<?php echo number_format($userBid, 2); ?></p>
    <p><strong>Highest Bid:</strong> $<?php echo number_format($highestBid, 2); ?></p>
    <p><strong>Bidding Ends In:</strong> <span id="countdown"></span></p>

    <form method="post" action="placeBid.php" style="margin-top: 20px;">
      <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
      <label for="size"><strong>Select Size:</strong></label>
      <select name="size" id="size" required style="margin-left: 10px; padding: 5px;">
        <option value="">--Select--</option>
        <option value="US 7">US 7</option>
        <option value="US 8">US 8</option>
        <option value="US 9">US 9</option>
        <option value="US 10">US 10</option>
        <option value="US 11">US 11</option>
      </select>

      <div class="bid-controls">
        <button type="button" class="adjust-bid" onclick="adjustBid(-10)">-</button>
        <input type="text" id="bidAmount" name="bid_amount" class="bid-increment" value="<?php echo max($highestBid + 10, $product['price']); ?>" readonly>
        <button type="button" class="adjust-bid" onclick="adjustBid(10)">+</button>
      </div>

      <button type="submit" class="bid-btn">Place Bid</button>
    </form>

    <div id="celebration-popup" style="display:none;">
      <img src="img/success.png" alt="Success">
      <h3>Bid Placed Successfully!</h3>
    </div>

    <?php
    if (isset($_SESSION['user_id'])) {
        $historyStmt = $pdo->prepare("
            SELECT bid_amount, size, bid_time 
            FROM bids 
            WHERE user_id = ? AND exclusive_id = ?
            ORDER BY bid_time DESC
        ");
        $historyStmt->execute([$_SESSION['user_id'], $exclusive_id]);
        $userBids = $historyStmt->fetchAll();

        if ($userBids):
    ?>
        <div style="margin-top: 30px;">
            <h4>Your Bid History</h4>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($userBids as $bid): ?>
                    <li style="margin-bottom: 6px;">
                        $<?php echo number_format($bid['bid_amount'], 2); ?> 
                        for <strong><?php echo htmlspecialchars($bid['size']); ?></strong> 
                        at <?php echo date("M d, H:i", strtotime($bid['bid_time'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; } ?>
  </div>
</div>

<script>
function adjustBid(amount) {
  const bidInput = document.getElementById('bidAmount');
  let current = parseFloat(bidInput.value);
  if (!isNaN(current)) {
    let updated = current + amount;
    if (updated >= 0) bidInput.value = updated.toFixed(2);
  }
}

// Countdown timer
const endTime = new Date("<?php echo $product['end_time']; ?>").getTime();
const countdown = document.getElementById('countdown');
const interval = setInterval(() => {
  const now = new Date().getTime();
  const distance = endTime - now;

  if (distance < 0) {
    clearInterval(interval);
    countdown.innerHTML = "Bidding Closed";
    document.querySelector('form').style.display = "none";
    return;
  }

  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const secs = Math.floor((distance % (1000 * 60)) / 1000);
  countdown.innerHTML = `${days}d ${hours}h ${mins}m ${secs}s`;
}, 1000);

// Show success popup
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('status') === 'success') {
  const popup = document.getElementById('celebration-popup');
  popup.style.display = 'block';
  setTimeout(() => popup.style.display = 'none', 2500);
}
</script>

</body>
</html>