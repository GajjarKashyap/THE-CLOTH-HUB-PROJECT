<?php
// includes/bootstrap.php
session_name('clothhub_session');
session_start();

// Include core files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

// Initialize shopping cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
