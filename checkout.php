<?php
require_once 'includes/bootstrap.php';

if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Fetch user profile data to prefill saved profile
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user_data = $user_stmt->fetch();

$prefilled_address = '';
if (!empty($user_data['address'])) {
    $parts = [
        $user_data['address'],
        $user_data['district'] ?? '',
        $user_data['state'] ?? '',
        !empty($user_data['pincode']) ? 'PIN: ' . $user_data['pincode'] : '',
        !empty($user_data['phone']) ? 'Mobile: ' . $user_data['phone'] : ''
    ];
    $prefilled_address = implode(', ', array_filter(array_map('trim', $parts)));
}

$total = 0;
foreach($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $address = sanitize_input($_POST['address']);
    
    if ($address) {
        try {
            $conn->beginTransaction();
            
            // Create order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)");
            $stmt->execute([$_SESSION['user_id'], $total, $address]);
            $order_id = $conn->lastInsertId();
            
            // Insert order items
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $item) {
                $stmt_item->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $item['size'] ?? null]);
            }
            
            $conn->commit();
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            header("Location: order-success.php?id=" . $order_id);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Failed to place order. Please try again.";
        }
    } else {
        $error = "Please provide a shipping address.";
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="breadcrumbs">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="cart.php">Cart</a>
        <span>/</span>
        <span style="color: var(--dark); font-weight: 600;">Checkout</span>
    </div>

    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 24px;">Checkout</h1>
    
    <?php if(isset($error)): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 18px; margin-bottom: 24px; border-radius: 0; border: 1px solid #fecaca; font-weight: 500;">
             <?= $error ?>
        </div>
    <?php endif; ?>
    
    <div class="checkout-flex" style="display: flex; gap: 32px; flex-wrap: wrap; align-items: flex-start;">
        <!-- Left: Shipping & Payment -->
        <div style="flex: 2; min-width: 320px;">
            <div style="background: var(--bg-card); padding: 32px; border: 1px solid var(--border-color); border-radius: 0; box-shadow: var(--shadow-sm); margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); display: flex; align-items: center; gap: 8px;">
                        <span></span> Delivery Address (Auto-Filled from Profile)
                    </h3>
                    <a href="profile.php" style="font-size: 12px; color: var(--primary); font-weight: 600; text-decoration: none;">Edit in Profile &rarr;</a>
                </div>
                
                <form action="checkout.php" method="POST" id="checkout-form">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Recipient Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user_data['full_name'] ?? $_SESSION['user_name']) ?>" readonly style="background: #faf8f5; font-weight: 600;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Mobile Number</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user_data['phone'] ?? 'Not provided') ?>" readonly style="background: #faf8f5; font-weight: 600;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Shipping Destination (Review or modify before placing order)</label>
                        <textarea name="address" class="form-control" rows="4" placeholder="Flat / House No., Street, Landmark, District, State, PIN Code, Phone" required><?= htmlspecialchars($prefilled_address ?: ($user_data['address'] ?? '')) ?></textarea>
                    </div>

                    <div style="background: var(--primary-light); padding: 16px 20px; border-radius: 0; border: 1px solid #e6d4d2; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-weight: 700; color: var(--primary); font-size: 14px;"> Payment method</span>
                            
                        </div>
                        <p style="font-size: 13px; color: var(--dark-muted); line-height: 1.5;">Cash on delivery. Pay when your order arrives.</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px;">
                         Place Order &bull; <?= format_price($total) ?>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Right: Order Summary -->
        <div style="flex: 1; min-width: 280px;">
            <div style="background: var(--bg-card); padding: 24px; border: 1px solid var(--border-color); border-radius: 0; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--dark); margin-bottom: 16px;">Order Summary (<?= count($_SESSION['cart']) ?> items)</h3>
                
                <div style="max-height: 320px; overflow-y: auto; padding-right: 4px;">
                    <?php foreach($_SESSION['cart'] as $item): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" width="42" height="42" style="object-fit: contain; border-radius: 0; border: 1px solid var(--border-color); background: #fff; padding: 2px; flex-shrink: 0;">
                                <?php endif; ?>
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--dark); line-height: 1.3;"><?= htmlspecialchars($item['name']) ?></div>
                                    <span style="color: var(--text-muted); font-size: 12px;">Size: <?= htmlspecialchars($item['size'] ?? 'Not selected') ?> &bull; Qty: <?= $item['quantity'] ?></span>
                                </div>
                            </div>
                            <span style="font-weight: 700; font-size: 14px; color: var(--dark); white-space: nowrap;"><?= format_price($item['price'] * $item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="border-top: 1px solid var(--border-color); margin: 16px 0; padding-top: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: var(--text-muted);">
                        <span>Delivery</span>
                        <span style="color: var(--success); font-weight: 700;">FREE</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 18px; font-weight: 800; color: var(--dark); margin-top: 10px;">
                        <span>Total Due</span>
                        <span style="color: var(--primary); font-size: 22px;"><?= format_price($total) ?></span>
                    </div>
                </div>

                <div style="font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 14px;">
                     Review your delivery details before placing the order.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
