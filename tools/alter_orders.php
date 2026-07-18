<?php
require 'www/constant/connect.php';

try {
    $connect->exec("ALTER TABLE orders ADD COLUMN created_at TEXT DEFAULT CURRENT_TIMESTAMP");
    echo "created_at added to orders.";
} catch(Exception $e) {
    echo "Column might already exist.";
}
