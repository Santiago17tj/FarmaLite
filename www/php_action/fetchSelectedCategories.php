<?php 	

require_once 'core.php';

$categoriesId = $_POST['categoriesId'];

$stmt = $connect->prepare("SELECT categories_id, categories_name, categories_active, categories_status FROM categories WHERE categories_id = ?");
$stmt->bindValue(1, $categoriesId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
