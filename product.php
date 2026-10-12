<?php
require_once 'includes/bootstrap.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = get_product_by_id($conn, $id);

if (!$product) {
    header("Location: shop.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="shop.php">Shop</a>
        <span>/</span>
        <a href="shop.php?category=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category']) ?></a>
        <span>/</span>
        <span style="color: var(--dark); font-weight: 600;"><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <!-- Product Showcase Card -->
    <div class="product-detail-card">
        <div class="product-detail-flex">
            <!-- Left: Media -->
            <div class="product-detail-media">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-detail-img">
            </div>

            <!-- Right: Information & Buy Box -->
            <div class="product-detail-info">
                <div class="detail-badge-row">
                    <span class="badge badge-primary"><?= htmlspecialchars($product['brand']) ?></span>
                    <span class="badge" style="background: #f3efeb; color: #475569;"><?= htmlspecialchars($product['category']) ?></span>
                    
                </div>

                <h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>
                


                <div class="detail-price">
                    <?= format_price($product['price']) ?>
                    <span style="font-size: 13px; font-weight: 500; color: var(--text-muted); display: block; margin-top: 2px;">Price in INR. Delivery is free in this demo.</span>
                </div>

                <p class="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                <!-- Value Perks List -->
                <div class="detail-features"><ul>
                        <li>Everyday clothing from The Cloth Hub</li>
                        <li>Choose your size: S, M, L, XL or XXL</li>
                        <li>Cash on delivery for this college project</li>
                    </ul></div>

                <!-- Add to Cart Form -->
                <form action="cart.php" method="POST" style="margin-top: auto;">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <?php include 'includes/size-options.php'; ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div style="display: flex; gap: 14px; align-items: center; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 0; overflow: hidden; background: #fff;">
                            <button type="button" onclick="let q=document.getElementById('p_qty'); if(q.value>1) q.value--;" style="padding: 10px 14px; border:none; background: #faf8f5; cursor:pointer; font-weight: bold; font-size: 16px;">-</button>
                            <input type="number" id="p_qty" name="quantity" value="1" min="1" max="10" style="width: 50px; text-align: center; border:none; font-weight: 700; font-size: 15px;" readonly>
                            <button type="button" onclick="let q=document.getElementById('p_qty'); if(q.value<10) q.value++;" style="padding: 10px 14px; border:none; background: #faf8f5; cursor:pointer; font-weight: bold; font-size: 16px;">+</button>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 13px 24px; font-size: 15px;">
                            <span> Add to Cart</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
