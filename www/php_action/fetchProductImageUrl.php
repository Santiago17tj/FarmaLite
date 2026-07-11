<?php 	

require_once 'core.php';

$productId = (int) $_GET['i'];

$stmt = $connect->prepare("SELECT product_image FROM product WHERE product_id = ?");
$stmt->bindValue(1, $productId, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_NUM);

echo "stock/" . $result[0];
