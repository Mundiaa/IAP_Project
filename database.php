<?php
require_once 'conf.php';

try {
    $dsn = "mysql:host={$conf['MariaDB']};dbname={$conf['notez_wiz']};charset=utf8mb4";
    $pdo = new PDO($dsn, $conf['root'], $conf['<!--your MariaDB password-->']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "MariaDB connected successfully!";
} catch (PDOException $e) {
    die("MariaDB connection failed: " . $e->getMessage());
}
