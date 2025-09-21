//this file is used to test the database connection
<?php
$host = '127.0.0.1';
$port = 3307; // your MariaDB port
$db   = 'notez_wiz';
$user = 'root';
$pass = 'Admin123'; //Use your actual password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected successfully to MariaDB!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
