<?php
// Uses a disposable database; never resets the application database.
$db = 'clothhub_test_' . bin2hex(random_bytes(5));
putenv('CLOTHHUB_DB_NAME=' . $db);
try {
    require __DIR__ . '/../includes/database.php';
    if ((int)$conn->query('SELECT COUNT(*) FROM products')->fetchColumn() !== 10) {
        throw new RuntimeException('First-run catalogue missing.');
    }
    $conn->exec("UPDATE products SET name = 'Preserved product' WHERE id = 1");
    $conn->exec('ALTER TABLE order_items DROP COLUMN size');
    require __DIR__ . '/../includes/database.php';
    if ($conn->query('SELECT name FROM products WHERE id = 1')->fetchColumn() !== 'Preserved product' ||
        !$conn->query("SHOW COLUMNS FROM order_items LIKE 'size'")->fetch()) {
        throw new RuntimeException('Migration must preserve existing data and add size.');
    }
    echo "PASS: automatic database creation, seeding, size migration and existing-data preservation\n";
} finally {
    if (isset($conn)) {
        $conn->exec("DROP DATABASE IF EXISTS `$db`");
    }
}
