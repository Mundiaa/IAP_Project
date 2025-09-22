<?php
require_once 'conf.php';

$conn = new mysqli($conf['db_host'], $conf['db_user'], $conf['db_pass'], $conf['db_name'], $conf['db_port']);

if ($conn->connect_error) {
    die("MariaDB connection failed: " . $conn->connect_error);
}
 //echo "MariaDB connected successfully!"; // optional
