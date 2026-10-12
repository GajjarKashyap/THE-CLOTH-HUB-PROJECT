<?php
// includes/functions.php
const PRODUCT_SIZES = ['S', 'M', 'L', 'XL', 'XXL'];

function verify_csrf_token() {
    if (!is_string($_POST['csrf_token'] ?? null) ||
        !hash_equals(generate_csrf_token(), $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Your session token is invalid. Reload the page and try again.');
    }
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function get_all_products($conn, $limit = null) {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function get_products_by_category($conn, $category) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

// Generate CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function format_price($price) {
    return "₹" . number_format($price, 2);
}
?>
