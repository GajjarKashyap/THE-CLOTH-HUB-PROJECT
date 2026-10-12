<?php
$cart_count = 0;
foreach ($_SESSION['cart'] ?? [] as $item) { $cart_count += $item['quantity']; }
$current_page = basename($_SERVER['PHP_SELF']);
$search_query = htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="The Cloth Hub — everyday shirts, t-shirts and trousers for men and women.">
    <title>The Cloth Hub | Men &amp; Women</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="main-header">
    <div class="container header-container">
        <a href="index.php" class="logo">THE <span class="logo-accent">CLOTH</span> HUB</a>
        <form action="shop.php" method="GET" class="nav-search-form" role="search">
            <label for="nav-search" class="sr-only">Search clothing</label>
            <div class="nav-search-wrapper">
                <input id="nav-search" type="search" name="search" placeholder="Search shirts, tees, trousers…" value="<?= $search_query ?>" class="nav-search-input">
                <button type="submit" class="nav-search-btn">Search</button>
            </div>
        </form>
        <nav class="main-nav" aria-label="Main navigation">
            <a href="shop.php" class="nav-link <?= $current_page === 'shop.php' ? 'active' : '' ?>">Shop</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="nav-link">Account</a>
                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><a href="admin/index.php" class="nav-link">Admin</a><?php endif; ?>
                <a href="logout.php" class="nav-link">Sign out</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Sign in</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-link">Bag <span class="cart-badge" id="cart-count"><?= $cart_count ?></span></a>
        </nav>
    </div>
    <div class="sub-header-bar">
        <nav class="container sub-header-container" aria-label="Collections">
            <a href="index.php" class="sub-nav-item">Home</a>
            <a href="shop.php" class="sub-nav-item">All clothing</a>
            <a href="shop.php?category=Men" class="sub-nav-item">Men</a>
            <a href="shop.php?category=Women" class="sub-nav-item">Women</a>
        </nav>
    </div>
</header>
<main class="main-content">
