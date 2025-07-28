<?php
require_once 'db.php';

$stmt = $pdo->query("SELECT * FROM products LIMIT 8");
$products = $stmt->fetchAll();

$featuedQuary = $pdo->query("SELECT * FROM products WHERE isFeatured=1 LIMIT 4");
$featuredProducts = $featuedQuary->fetchAll();

$newArrivalQuary = $pdo->query("SELECT * FROM products WHERE isNewArrival=1 LIMIT 4");
$newProducts = $newArrivalQuary->fetchAll();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakersxStudio</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
</head>
<div id="cookie-popup" style="position: fixed; bottom: 20px; left: 20px; background: #121212; color: #fff; padding: 20px; border: 1px solid #00ffc3; border-radius: 8px; max-width: 400px; z-index: 1000; display: none;">
  <p>We use cookies to improve your experience. By continuing, you accept or reject cookies as per our <a href="privacy.php" style="color:#00ffc3;">Privacy Policy</a>.</p>
  <button onclick="acceptCookies()" style="margin-right:10px;">Accept</button>
  <button onclick="rejectCookies()">Reject</button>
</div>

<script>
function acceptCookies() {
    document.cookie = "cookies_accepted=true; path=/; max-age=" + 60*60*24*30;
    document.getElementById('cookie-popup').style.display = 'none';
}
function rejectCookies() {
    document.cookie = "cookies_accepted=false; path=/; max-age=" + 60*60*24*30;
    document.getElementById('cookie-popup').style.display = 'none';
}
window.onload = function() {
    if (!document.cookie.includes("cookies_accepted")) {
        document.getElementById('cookie-popup').style.display = 'block';
    }
}
</script>

<section id="header">
  <a href="index.php"><img src="img/Logo.jpg" alt="Logo"></a>
  <div>
    <ul id="navbar">
        <li><a class="active" href="index.php">Home</a></li>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="admin.php">Admin</a></li>
        <li><a href="aboutUS.php">About</a></li>
        <li><a href="blog.php">Blog</a></li>
        <li><a href="exclusive.php">Exclusive</a></li>
        <li><a href="login.php">Log In</a></li>
        <li><a href="cart.php"><i class="far fa-shopping-bag"></i></a></li>
        <li><a href="profile.php"><i class="fa fa-user"></i></a></li>
    </ul>
  </div>
</section>
<!-- Hero -->
<section id="hero">
  <h4>SneakerxStudio</h4>
  <h2>Heaven of Sneakers</h2>
  <h1>Shop the latest sneakers</h1>
  <p>A trustworthy shop for your next sneakers. Get the best deal with us.</p>
  <form action="shop.php" method="get">
  <button type="submit">Shop Now</button>
  </form>
</section>

<!-- Featured -->
<section id="product1" class="section-p1">
  <h2>Featured Product</h2>
  <p>Collection You Might Like</p>
  <div class="pro-container">
    <?php foreach ($featuredProducts as $product): ?>
      <div class="pro">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="product">
        <div class="des">
          <span><?= htmlspecialchars($product['name']) ?></span>
          <h5><?= htmlspecialchars($product['description']) ?></h5>
          <div class="star">
            <?php for ($i = 0; $i < 5; $i++): ?>
              <i class="fas fa-star"></i>
            <?php endfor; ?>
          </div>
          <h4>$<?= number_format($product['price'], 2) ?></h4>
        </div>
        <a href="/ecom/detailPage.php?productid=<?=($product['product_id']) ?>"><i class="fal fa-shopping-cart cart"></i></a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- New Arrivals (reuse same set) -->
<section id="product1" class="section-p1">
  <h2>New Arrivals</h2>
  <p>Our latest collection</p>
  <div class="pro-container">
    <?php foreach ($newProducts as $product): ?>
      <div class="pro">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="product">
        <div class="des">
          <span><?= htmlspecialchars($product['name']) ?></span>
          <h5><?= htmlspecialchars($product['description']) ?></h5>
          <div class="star">
            <?php for ($i = 0; $i < 5; $i++): ?>
              <i class="fas fa-star"></i>
            <?php endfor; ?>
          </div>
          <h4>$<?= number_format($product['price'], 2) ?></h4>
        </div>
        <a href="/ecom/detailPage.php?productid=1"><i class="fal fa-shopping-cart cart"></i></a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<section id="newsletter" class="section-p1 section-m1">
  <div class="newstext">
    <h4>Sign Up For Newsletter</h4>
    <p>Get E-mail updates about our latest shop and <span>Special Offers.</span></p>
  </div>
  <div class="form">
    <input type="text" placeholder="Your E-mail address">
    <button class="normal">Sign Up</button>
  </div>
</section>

<?php
require_once 'footer.php';
?>
