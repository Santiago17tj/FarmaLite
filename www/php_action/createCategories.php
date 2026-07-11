<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	

	$categoriesName = $_POST['categoriesName'];
  $categoriesStatus = $_POST['categoriesStatus']; 

	$stmt = $connect->prepare("INSERT INTO categories (categories_name, categories_active, categories_status) VALUES (?, ?, 1)");
	$stmt->bindValue(1, $categoriesName);
	$stmt->bindValue(2, $categoriesStatus);

	if($stmt->execute()) {
	 	$valid['success'] = true;
		$valid['messages'] = "Successfully Added";
		// header removed	
	} else {
	 	$valid['success'] = false;
	 	$valid['messages'] = "Error while adding the members";
	}

	echo json_encode($valid);
 
} // /if $_POST
