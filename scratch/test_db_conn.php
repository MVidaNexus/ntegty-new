<?php
$host = '135.125.190.148';
$db   = 'ntegty';
$user = 'ntegty';
$pass = 'H3r&md"j_7Z?A/+)';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connection successful!\n";
     
     // Get table count as proof
     $stmt = $pdo->query("SHOW TABLES");
     $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
     echo "Total tables: " . count($tables) . "\n";
     echo "Sample tables: " . implode(", ", array_slice($tables, 0, 5)) . "...\n";

} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage() . "\n";
}
