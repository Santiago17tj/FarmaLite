<?php
require 'www/constant/connect.php';
$stmt = $connect->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='product'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
