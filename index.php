<?php
require_once 'includes/bootstrap.php';
require_once 'includes/header.php';
$featured_products = $conn->query("SELECT * FROM products ORDER BY id ASC")->fetchAll();
?>
<div class="container">
    <section class="cloth-hero">
        <div class="cloth-hero-copy">
            <p class="eyebrow">THE CLOTH HUB / EVERYDAY COLLECTION</p>
            <h1>Good clothes.<br>Simple choices.</h1>
            <p>Shirts, tees and trousers for workdays and weekends. A small collection for men and women, easy to browse and easy to wear.</p>
            <div class="hero-actions">
                <a href="shop.php?category=Men" class="btn btn-primary">Shop Men &rarr;</a>
                <a href="shop.php?category=Women" class="btn btn-secondary">Shop Women &rarr;</a>
            </div>
        </div>
        <div class="cloth-hero-art" aria-label="Clothing collection illustrations">
            <img src="assets/images/men-shirt.jpg" alt="Sand cotton shirt">
            <img src="assets/images/women-formal-rose.jpg" alt="Rose formal shirt">
            <span>Everyday essentials / 10 pieces</span>
        </div>
    </section>
    <div class="cloth-note"><span>MEN &amp; WOMEN</span><span>SHIRTS / T-SHIRTS / TROUSERS</span><span>PRICES IN INR</span></div>
    <div class="section-header">
        <div><p class="eyebrow">A SMALL, CONSIDERED COLLECTION</p><h2 class="section-title">The everyday edit</h2></div>
        <a href="shop.php" class="text-link">Browse all <?= count($featured_products) ?> products &rarr;</a>
    </div>
    <div class="product-grid">
        <?php foreach ($featured_products as $product): ?>
            <?php include 'includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
