<?php 	

require_once 'core.php';

$brandId = $_POST['brandId'];

$stmt = $connect->prepare("SELECT brand_id, brand_name, brand_active, brand_status FROM brands WHERE brand_id = ?");
$stmt->bindValue(1, $brandId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
