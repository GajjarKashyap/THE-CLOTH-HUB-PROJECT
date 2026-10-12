<?php
$host = getenv('CLOTHHUB_DB_HOST') ?: '127.0.0.1';
$username = getenv('CLOTHHUB_DB_USER') ?: 'root';
$password = getenv('CLOTHHUB_DB_PASSWORD') ?: '';
$dbname = getenv('CLOTHHUB_DB_NAME') ?: 'clothhub';

try {
    if (!preg_match('/^[a-zA-Z0-9_]+$/D', $dbname)) {
        throw new RuntimeException('Invalid database name.');
    }
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) !== 1049) {
            throw $e;
        }
        $conn = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, $options);
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        $conn->exec("USE `$dbname`");
    }

    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (array_diff(['users', 'products', 'orders', 'order_items'], $tables)) {
        $conn->exec(file_get_contents(__DIR__ . '/setup.sql'));
    }
    if (!$conn->query("SHOW COLUMNS FROM order_items LIKE 'size'")->fetch()) {
        $conn->exec("ALTER TABLE order_items ADD COLUMN IF NOT EXISTS size VARCHAR(3) DEFAULT NULL");
    }
} catch (Throwable $e) {
    error_log('Cloth Hub database: ' . $e->getMessage());
    http_response_code(503);
    exit('Database unavailable. Start MySQL in XAMPP and check the database credentials in includes/database.php. Missing databases are created automatically when the account has permission.');
}
