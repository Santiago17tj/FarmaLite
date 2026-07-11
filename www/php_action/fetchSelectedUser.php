<?php 	

require_once 'core.php';

$userid = $_POST['userid'];

$stmt = $connect->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bindValue(1, $userid, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
