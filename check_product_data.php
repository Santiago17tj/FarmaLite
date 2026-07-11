<?php
require 'www/constant/connect.php';
$stmt = $connect->query("SELECT product_id, product_name, bno, expdate FROM product LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
