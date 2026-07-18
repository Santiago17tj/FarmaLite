<?php
require 'www/constant/connect.php';
try {
    $connect->exec("ALTER TABLE inventory_movements ADD COLUMN balance INTEGER DEFAULT 0");
    echo "Added balance to inventory_movements.";
} catch(Exception $e) {
    echo "Column already exists or error: " . $e->getMessage();
}
