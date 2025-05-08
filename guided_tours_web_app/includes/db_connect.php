<?php
// Database Connection using PDO

// Include database configuration
require_once __DIR__ . '/../config/database.php';

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // In a real application, log this error and show a user-friendly message
    // For development, we can show the error directly (but disable in production)
    error_log("Database Connection Error: " . $e->getMessage()); // Log error
    die("Database connection failed. Please check configuration or contact support."); // User message
    // throw new \PDOException($e->getMessage(), (int)$e->getCode()); // Or re-throw
}

// The $pdo variable now holds the database connection object
// You can include this file where needed and use the $pdo object.
// Example: require_once 'includes/db_connect.php'; $stmt = $pdo->query(...);

// Note: Consider wrapping this in a function or class for better encapsulation
// function getDbConnection() { ... return $pdo; }
?>
