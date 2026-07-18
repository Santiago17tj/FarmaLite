<?php
require 'www/constant/connect.php';
$stmt = $connect->query("SELECT name FROM sqlite_master WHERE type='table'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
