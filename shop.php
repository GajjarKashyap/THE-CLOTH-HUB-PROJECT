<?php
require_once 'includes/bootstrap.php';
require_once 'includes/header.php';

$category = isset($_GET['category']) ? sanitize_input($_GET['category']) : '';
$sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build dynamic query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $sql .= " AND (name LIKE ? OR brand LIKE ? OR description LIKE ?)";
    $search_term = "%" . $search . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($sort === 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY id ASC";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Category pill icons
$category_icons = [];

$stmt_cats = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $stmt_cats->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container">
    <!-- Breadcrumb & Title -->
    <div style="margin-bottom: 20px;">
        <div class="breadcrumbs">
            <a href="index.php">Home</a>
            <span>/</span>
            <span style="color: var(--dark); font-weight: 600;">Shop Catalog</span>
            <?php if ($category): ?>
                <span>/</span>
                <span style="color: var(--primary);"><?= htmlspecialchars($category) ?></span>
            <?php endif; ?>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <div>
                <?php if ($search): ?>
                    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">
                        Search: "<?= htmlspecialchars($search) ?>"
                    </h1>
                    <p style="color: var(--text-muted); font-size: 14px;">Found <?= count($products) ?> matching products</p>
                <?php else: ?>
                    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">
                        <?= $category ? htmlspecialchars($category) : 'All Clothing' ?>
                    </h1>
                    <p style="color: var(--text-muted); font-size: 14px;">Showing <?= count($products) ?> products</p>
                <?php endif; ?>
            </div>
            
            <!-- Search & Sort Row -->
            <form method="GET" action="shop.php" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <?php endif; ?>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search clothing..." aria-label="Search clothing" class="form-control" style="width: 200px; padding: 8px 12px; font-size: 13px;">
                <select name="sort" aria-label="Sort products" onchange="this.form.submit()" class="form-control" style="width: auto; padding: 8px 12px; font-size: 13px;">
                    <option value="">Default Sorting</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm" style="padding: 8px 14px;">Filter</button>
                <?php if ($category || $search || $sort): ?>
                    <a href="shop.php" class="btn btn-sm" style="padding: 8px 12px; background: #fee2e2; color: #b91c1c; text-decoration: none;">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Category Filter Bar (Pill Chips) -->
    <div class="category-filter-bar">
        <a href="shop.php<?= $sort ? '?sort='.urlencode($sort) : '' ?>" class="category-chip <?= empty($category) ? 'active' : '' ?>">
            
            <span>All Products</span>
        </a>
        <?php foreach($categories as $cat): ?>
            <?php 
                $icon = ''; 
                $url = "shop.php?category=" . urlencode($cat) . ($sort ? '&sort='.urlencode($sort) : '');
            ?>
            <a href="<?= $url ?>" class="category-chip <?= $category === $cat ? 'active' : '' ?>">
                
                <span><?= htmlspecialchars($cat) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Grid -->
    <?php if(empty($products)): ?>
        <div style="background: var(--bg-card); padding: 60px; text-align: center; border-radius: 0; border: 1px dashed var(--border-color); margin: 30px 0;">
            <div style="font-size: 48px; margin-bottom: 12px;"></div>
            <h3 style="color: var(--dark); margin-bottom: 8px;">No matching products found</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Try selecting another category or resetting your search filter.</p>
            <a href="shop.php" class="btn btn-primary">Browse All Products</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach($products as $product): ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
