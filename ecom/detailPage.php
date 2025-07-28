<?php

require_once 'header.php';

// Get product ID from URL
$product_id = isset($_GET['productid']) ? (int) $_GET['productid'] : 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($product['name']) ?> | SneakersxStudio</title>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
</head>
<body>

<section id="prodetails" class="section-p1">
  <div class="single-pro-image">
    <img src="<?= htmlspecialchars($product['image_url']) ?>" width="100%" id="mainImg" class="mainImg" alt="">
    <div class="small-img-group">
      <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="small-img-col">
          <img src="<?= htmlspecialchars($product['image_url']) ?>" width="100%" class="small-img" alt="">
        </div>
      <?php endfor; ?>
    </div>
  </div>
  <div class="single-pro-details">
    <h6>Home / Sneakers</h6>
    <h4><?= htmlspecialchars($product['name']) ?></h4>
    <h2>$<?= number_format($product['price'], 2) ?></h2>
    <select>
      <option>Select Size</option>
      <option>XXL</option>
      <option>XL</option>
      <option>L</option>
      <option>M</option>
      <option>S</option>
    </select>
    <form action="add_to_cart.php" method="POST">
  <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
  <label for="quantity">Quantity:</label>
  <input type="number" name="quantity" id="quantity" value="1" min="1" required>
  <button class="normal" type="submit">Add To Cart</button>
</form>

    <h4>Product Details</h4>
    <span><?= nl2br(htmlspecialchars($product['description'])) ?></span>
  </div>
</section>

<section id="product1" class="section-p1">
  <h2>Featured Products</h2>
  <p>Collection You Might Like</p>
  <div class="pro-container">
    <?php
      $related = $pdo->query("SELECT * FROM products WHERE product_id != $product_id LIMIT 4")->fetchAll();
      foreach ($related as $rel): ?>
        <div class="pro">
          <img src="<?= htmlspecialchars($rel['image_url']) ?>" alt="product">
          <div class="des">
            <span><?= htmlspecialchars($rel['name']) ?></span>
            <h5><?= htmlspecialchars(substr($rel['description'], 0, 30)) ?>...</h5>
            <div class="star">
              <?php for ($i = 0; $i < 5; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
            </div>
            <h4>$<?= number_format($rel['price'], 2) ?></h4>
          </div>
          <a href="detailPage.php?productid=<?= $rel['product_id'] ?>"><i class="fal fa-shopping-cart cart"></i></a>
        </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Review box omitted for brevity -->

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

<footer class="section-p1">
  <div class="col">
    <h4>Contact</h4>
    <p><strong>Email:</strong> s4125820@student.rmit.edu.au, s4043058@student.rmit.edu.au</p>
    <p><strong>Email:</strong> s4061087@student.rmit.edu.au, s4088056@student.rmit.edu.au</p>
    <p><strong>Location:</strong> Melbourne, Australia</p>
    <div class="follow">
      <h4>Follow Us</h4>
      <div class="icon">
        <i class="fab fa-facebook-f"></i>
        <i class="fab fa-twitter"></i>
        <i class="fab fa-instagram"></i>
        <i class="fab fa-pinterest-p"></i>
        <i class="fab fa-youtube"></i>
      </div>
    </div>
  </div>
  <div class="col">
    <h4>About</h4>
    <a href="#">About Us</a>
    <a href="#">Delivery Information</a>
    <a href="#">Privacy Policy</a>
    <a href="#">Terms & Conditions</a>
    <a href="#">Contact Us</a>
  </div>
  <div class="col">
    <h4>My Account</h4>
    <a href="#">Sign In</a>
    <a href="#">View Cart</a>
    <a href="#">My Wishlist</a>
    <a href="#">Track My Order</a>
    <a href="#">Help</a>
  </div>
</footer>

<script>
  const MainImg = document.getElementById("mainImg");
  const smallImg = document.getElementsByClassName("small-img");
  for (let i = 0; i < smallImg.length; i++) {
    smallImg[i].onclick = () => MainImg.src = smallImg[i].src;
  }
</script>
</body>
</html>
