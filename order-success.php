<?php
require_once 'includes/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$order = null;
$order_items = [];

if ($order_id > 0) {
    $stmt = $conn->prepare("SELECT o.*, u.full_name, u.email, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if ($order) {
        $stmt_items = $conn->prepare("
            SELECT oi.*, p.name, p.image_url, p.brand, p.category 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt_items->execute([$order_id]);
        $order_items = $stmt_items->fetchAll();
    }
}

if (!$order) {
    header('Location: profile.php');
    exit;
}
require_once 'includes/header.php';
?>



<div class="container" style="max-width: 840px;">
    <!-- Success Banner -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 0; padding: 40px 30px; text-align: center; box-shadow: var(--shadow-sm); margin-bottom: 28px;">
        
        <!-- Animated Tick -->
        <p class="eyebrow">ORDER RECEIVED</p>

        <span class="badge badge-success" style="font-size: 13px; padding: 6px 14px; margin-bottom: 12px;">Order Received</span>
        <h1 style="font-size: 30px; font-weight: 800; color: var(--dark); margin: 8px 0;">Thank You for Your Order!</h1>
        <p style="color: var(--text-muted); font-size: 15px; max-width: 500px; margin: 0 auto 16px;">
            We've received your order <strong>#<?= $order_id ?></strong>. You can view its status in your account.
        </p>

        <!-- Estimated Delivery Banner -->
        <div style="display: inline-flex; align-items: center; gap: 10px; background: #f7f0ef; border: 1px solid #ddc6c5; color: #782c3b; padding: 12px 22px; border-radius: 0; font-size: 14px; font-weight: 600;">
            <span></span>
            <span>Order status is available in your account</span>
        </div>
    </div>

    <?php if($order): ?>
        <!-- Order Breakdown Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 0; padding: 32px; box-shadow: var(--shadow-sm); margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-light); flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 800; color: var(--dark);">Order Summary (#<?= $order['id'] ?>)</h3>
                    <span style="font-size: 13px; color: var(--text-muted);">Placed on <?= date('d F Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <div>
                    <span class="badge" style="background: #ecfdf5; color: #047857; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                        Status: <?= htmlspecialchars($order['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Items Purchased List -->
            <div style="margin-bottom: 24px;">
                <?php foreach($order_items as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-light); gap: 14px;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <?php if(!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" width="52" height="52" style="object-fit: contain; border-radius: 0; border: 1px solid var(--border-color); background: #fff; padding: 4px; flex-shrink: 0;">
                            <?php endif; ?>
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--primary);"><?= htmlspecialchars($item['brand']) ?></span>
                                <h4 style="font-size: 14px; font-weight: 700; color: var(--dark); line-height: 1.3;"><?= htmlspecialchars($item['name']) ?></h4>
                                <span style="font-size: 12px; color: var(--text-muted);">Size: <?= htmlspecialchars($item['size'] ?? 'Not recorded') ?> &bull; Qty: <?= $item['quantity'] ?> &bull; Unit: <?= format_price($item['price']) ?></span>
                            </div>
                        </div>
                        <span style="font-weight: 800; font-size: 15px; color: var(--dark); white-space: nowrap;">
                            <?= format_price($item['price'] * $item['quantity']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Shipping Destination & Financials -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; background: var(--bg-page); padding: 20px; border-radius: 0; border: 1px solid var(--border-color);">
                <div>
                    <h5 style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;"> Delivering To</h5>
                    <p style="font-weight: 700; color: var(--dark); font-size: 14px; margin-bottom: 4px;"><?= htmlspecialchars($order['full_name']) ?></p>
                    <p style="font-size: 13px; color: var(--dark-muted); line-height: 1.5;"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>

                <div style="display: flex; flex-direction: column; justify-content: flex-end; text-align: right;">
                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Payment Method: <strong>Cash on Delivery (COD)</strong></div>
                    <div style="font-size: 13px; color: var(--success); font-weight: 600; margin-bottom: 8px;">Delivery Charges: <strong>FREE</strong></div>
                    <div style="font-size: 22px; font-weight: 800; color: var(--dark);">
                        Total: <span style="color: var(--primary);"><?= format_price($order['total_amount']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
        <a href="shop.php" class="btn btn-primary" style="padding: 13px 26px; font-size: 15px;">
            &larr; Continue Shopping
        </a>
        <a href="profile.php" class="btn btn-secondary" style="padding: 13px 24px; font-size: 15px;">
             View in Order History
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
