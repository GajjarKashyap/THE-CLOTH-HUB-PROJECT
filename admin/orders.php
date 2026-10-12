<?php
require_once '../includes/bootstrap.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied");
}

if (isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header("Location: orders.php");
    exit;
}

$stmt = $conn->query("
    SELECT o.*, u.full_name, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - The Cloth Hub Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav { background: #333; color: #fff; padding: 15px; }
        .admin-nav a { color: #fff; text-decoration: none; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="admin-nav">
        <div class="container">
            <a href="index.php">The Cloth Hub Admin</a>
            <a href="products.php">Products</a>
            <a href="orders.php" style="font-weight: bold;">Orders</a>
            <a href="customers.php">Customers</a>
            <a href="../index.php" style="float: right;">View Site</a>
        </div>
    </div>
    
    <div class="container" style="padding-top: 40px;">
        <h2>Manage Orders</h2>
        
        <div class="table-responsive"><table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['full_name']) ?><br><small><?= htmlspecialchars($o['email']) ?></small></td>
                        <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                        <td><?= format_price($o['total_amount']) ?></td>
                        <td style="text-transform: capitalize;"><?= $o['status'] ?></td>
                        <td>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <select name="status" onchange="this.form.submit()" class="form-control" style="width:auto; display:inline-block; padding: 5px;">
                                    <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= $o['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</body>
</html>
