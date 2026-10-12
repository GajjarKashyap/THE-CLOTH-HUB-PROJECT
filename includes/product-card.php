<?php
// includes/product-card.php
// Expects $product variable to be set
$brand = htmlspecialchars($product['brand']);
$category = htmlspecialchars($product['category']);
$name = htmlspecialchars($product['name']);
$price = format_price($product['price']);
$img_url = htmlspecialchars($product['image_url']);
?>
<div class="product-card">
    <span class="badge-brand"><?= $brand ?></span>
    <a href="product.php?id=<?= $product['id'] ?>" class="product-image-wrap">
        <img src="<?= $img_url ?>" alt="<?= $name ?>" class="product-image" loading="lazy">
    </a>
    <div class="product-info">
        <span class="product-category"><?= $category ?></span>
        <h3 class="product-title">
            <a href="product.php?id=<?= $product['id'] ?>"><?= $name ?></a>
        </h3>

        <div class="product-price-row">
            <span class="product-price"><?= $price ?></span>
            
        </div>
        <form action="cart.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <?php include __DIR__ . '/size-options.php'; ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <button type="submit" class="btn btn-primary btn-block">
                <span>Add to Cart</span>
                <span style="font-size: 16px;">+</span>
            </button>
        </form>
    </div>
</div>
