<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    start_app_session();

    $pdo = db();

    echo "========================================\n";
    echo " MKANGA-ULIMI FARM MANAGEMENT SYSTEM\n";
    echo " DATABASE CONNECTION TEST\n";
    echo "========================================\n\n";

    echo "Connection: SUCCESS\n";
    echo "Host: " . DB_HOST . "\n";
    echo "Database: " . DB_NAME . "\n";
    echo "User: " . DB_USER . "\n";
    echo "PHP: " . PHP_VERSION . "\n\n";

    $stmt = $pdo->query("SELECT DATABASE() AS database_name");
    $database = $stmt->fetch();

    echo "Active database: " . ($database['database_name'] ?? 'UNKNOWN') . "\n";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    echo "\nTables found: " . count($tables) . "\n";

    foreach ($tables as $table) {
        echo " - " . $table . "\n";
    }

    echo "\n========================================\n";
    echo " MUFMS MYSQL CONNECTION IS WORKING\n";
    echo "========================================\n";

} catch (Throwable $e) {

    http_response_code(500);

    echo "========================================\n";
    echo " DATABASE CONNECTION FAILED\n";
    echo "========================================\n\n";

    echo "Error: " . $e->getMessage() . "\n";
}