<?php
require_once 'header.php';

// Set how many products to display per page
$limit = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Get total number of products
$totalQuery = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $totalQuery->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Fetch products for the current page
$stmt = $pdo->prepare("SELECT * FROM products LIMIT :start, :limit");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakersxStudio</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
</head>
<!-- Content specific to the Shop Page -->
<section id="page-header" class="section-p1">
  <h2>#ShopNow</h2>
  <p>Our Full Collection</p>
</section>

<section id="product1" class="section-p1">
  <h2>All Products</h2>
  <p>Our Full Collection</p>
  <div class="pro-container">
    <?php foreach ($products as $product): ?>
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

<section id="pagination" class="section-p1">
  <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>"><i class="fas fa-chevron-left"></i></a>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>"><i class="fas fa-chevron-right"></i></a>
  <?php endif; ?>
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

<?php require_once 'footer.php'; ?>
