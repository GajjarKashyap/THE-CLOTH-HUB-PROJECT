<?php
require_once 'includes/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

$indian_states = [
    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", 
    "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", 
    "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", 
    "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", 
    "West Bengal", "Delhi NCR", "Jammu and Kashmir", "Ladakh"
];

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = sanitize_input($_POST['name']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $district = sanitize_input($_POST['district']);
    $state = sanitize_input($_POST['state']);
    $pincode = sanitize_input($_POST['pincode']);

    if (empty($name)) {
        $error_msg = "Full name cannot be empty.";
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $error_msg = "Please enter a valid 10-digit mobile number.";
    } elseif (!empty($pincode) && !preg_match('/^[0-9]{6}$/', trim($pincode))) {
        $error_msg = "Please enter a valid 6-digit PIN code.";
    } else {
        $stmt_update = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, district = ?, state = ?, pincode = ? WHERE id = ?");
        if ($stmt_update->execute([$name, $phone, $address, $district, $state, $pincode, $user_id])) {
            $_SESSION['user_name'] = $name;
            $success_msg = "Your delivery address & personal details have been updated successfully!";
        } else {
            $error_msg = "Failed to update profile. Please try again.";
        }
    }
}

// Fetch fresh user details
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

// Fetch orders
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <div class="breadcrumbs">
        <a href="index.php">Home</a>
        <span>/</span>
        <span style="color: var(--dark); font-weight: 600;">My Account</span>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">Customer Profile</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Manage your delivery address, contact number and order history.</p>
        </div>
        <div style="background: var(--primary-light); padding: 8px 16px; border-radius: 0; font-weight: 700; color: var(--primary); font-size: 13px;">
            Member since: <?= date('M Y', strtotime($user['created_at'])) ?>
        </div>
    </div>

    <?php if($success_msg): ?>
        <div style="background: #ecfdf5; color: #065f46; padding: 14px 18px; margin-bottom: 24px; border-radius: 0; border: 1px solid #a7f3d0; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 18px;"></span> <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <?php if($error_msg): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 18px; margin-bottom: 24px; border-radius: 0; border: 1px solid #fecaca; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 18px;"></span> <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 32px; flex-wrap: wrap; align-items: flex-start;">
        <!-- Left: Edit Address & Profile Form -->
        <div style="flex: 1.2; min-width: 320px;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 0; padding: 28px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-light);">
                    <span style="font-size: 22px;"></span>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 800; color: var(--dark); margin-bottom: 2px;">Saved Delivery Address & Phone</h3>
                        <p style="font-size: 12px; color: var(--text-muted);">Used for auto-filling your 1-click checkout</p>
                    </div>
                </div>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="10-digit mobile" pattern="[0-9]{10}" maxlength="10">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background: #faf8f5; color: #716a64;">
                        <small style="color: var(--text-light); font-size: 11px;">Email is tied to your account login.</small>
                    </div>

                    <div class="form-group">
                        <label>Flat, House No., Building, Street</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter your full street address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label>District / City</label>
                            <input type="text" name="district" class="form-control" value="<?= htmlspecialchars($user['district'] ?? '') ?>" placeholder="City / District">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <select name="state" class="form-control">
                                <option value="">Select State</option>
                                <?php foreach($indian_states as $st): ?>
                                    <option value="<?= $st ?>" <?= ($user['state'] === $st) ? 'selected' : '' ?>>
                                        <?= $st ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="max-width: 220px;">
                        <label>PIN Code</label>
                        <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($user['pincode'] ?? '') ?>" placeholder="6-digit PIN" pattern="[0-9]{6}" maxlength="6">
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-size: 14px; margin-top: 10px;">
                         Save & Update Address
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Order History -->
        <div style="flex: 1.8; min-width: 320px;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 0; padding: 28px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-light);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 22px;"></span>
                        <div>
                            <h3 style="font-size: 17px; font-weight: 800; color: var(--dark); margin-bottom: 2px;">Your Order History</h3>
                            <p style="font-size: 12px; color: var(--text-muted);"><?= count($orders) ?> total orders placed</p>
                        </div>
                    </div>
                    <a href="shop.php" class="btn btn-secondary btn-sm">+ Shop More</a>
                </div>

                <?php if(empty($orders)): ?>
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <div style="font-size: 38px; margin-bottom: 10px;"></div>
                        <h4 style="color: var(--dark); margin-bottom: 6px;">No orders yet</h4>
                        <p style="font-size: 13px; margin-bottom: 18px;">When you purchase cases, chargers or smartwatches, they will appear here.</p>
                        <a href="shop.php" class="btn btn-primary btn-sm">Explore Collection</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Placed On</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Delivering To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <a href="order-success.php?id=<?= $order['id'] ?>" style="font-weight: 800; color: var(--primary); text-decoration: none;">
                                                #<?= $order['id'] ?>
                                            </a>
                                        </td>
                                        <td style="color: var(--text-muted); font-size: 13px; white-space: nowrap;">
                                            <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $status = strtolower($order['status']);
                                                $status_bg = '#f3efeb';
                                                $status_color = '#475569';
                                                if ($status === 'completed') { $status_bg = '#ecfdf5'; $status_color = '#047857'; }
                                                elseif ($status === 'pending') { $status_bg = '#fffbeb'; $status_color = '#b45309'; }
                                                elseif ($status === 'cancelled') { $status_bg = '#fef2f2'; $status_color = '#b91c1c'; }
                                            ?>
                                            <span class="badge" style="background: <?= $status_bg ?>; color: <?= $status_color ?>; font-size: 11px; text-transform: uppercase;">
                                                <?= htmlspecialchars($order['status']) ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 800; color: var(--dark); font-size: 15px; white-space: nowrap;">
                                            <?= format_price($order['total_amount']) ?>
                                        </td>
                                        <td style="font-size: 12px; color: var(--text-muted); max-width: 200px; line-height: 1.4;">
                                            <?= htmlspecialchars(mb_strimwidth($order['shipping_address'], 0, 75, '...')) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
