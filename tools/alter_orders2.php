<?php
require 'www/constant/connect.php';

try {
    $connect->exec("ALTER TABLE orders ADD COLUMN created_at TEXT");
    echo "created_at added to orders.";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
