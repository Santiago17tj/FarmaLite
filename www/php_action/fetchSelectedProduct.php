<?php 	

require_once 'core.php';

$productId = $_POST['productId'];

$stmt = $connect->prepare("SELECT product_id, product_name, product_image, brand_id, categories_id, quantity, rate, active, status FROM product WHERE product_id = ?");
$stmt->bindValue(1, $productId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
