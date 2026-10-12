<?php
require_once '../includes/bootstrap.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied");
}

$stmt = $conn->query("SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC");
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - The Cloth Hub Admin</title>
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
            <a href="orders.php">Orders</a>
            <a href="customers.php" style="font-weight: bold;">Customers</a>
            <a href="../index.php" style="float: right;">View Site</a>
        </div>
    </div>
    
    <div class="container" style="padding-top: 40px;">
        <h2>Manage Customers</h2>
        
        <div class="table-responsive"><table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['full_name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</body>
</html>
