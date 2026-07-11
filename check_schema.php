<?php
require 'www/constant/connect.php';
$stmt = $connect->query("PRAGMA table_info('orders')");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
