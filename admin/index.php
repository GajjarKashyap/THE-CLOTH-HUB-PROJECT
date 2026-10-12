<?php
require_once '../includes/bootstrap.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied");
}

$stmt_orders = $conn->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt_orders->fetchColumn();

$stmt_sales = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'");
$total_sales = $stmt_sales->fetchColumn();

$stmt_customers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$total_customers = $stmt_customers->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - The Cloth Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav { background: #333; color: #fff; padding: 15px; }
        .admin-nav a { color: #fff; text-decoration: none; margin-right: 15px; }
        .stat-card { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 0; text-align: center; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="admin-nav">
        <div class="container">
            <a href="index.php" style="font-weight: bold;">The Cloth Hub Admin</a>
            <a href="products.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="customers.php">Customers</a>
            <a href="../index.php" style="float: right;">View Site</a>
        </div>
    </div>
    
    <div class="container" style="padding-top: 40px;">
        <h2>Dashboard Overview</h2>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p style="font-size: 24px; color: var(--primary-color); font-weight: bold;"><?= $total_orders ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Sales (Completed)</h3>
                <p style="font-size: 24px; color: var(--primary-color); font-weight: bold;"><?= format_price($total_sales ?: 0) ?></p>
            </div>
            <div class="stat-card">
                <h3>Registered Customers</h3>
                <p style="font-size: 24px; color: var(--primary-color); font-weight: bold;"><?= $total_customers ?></p>
            </div>
        </div>
    </div>
</body>
</html>
