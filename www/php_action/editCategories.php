<?php 	

require_once 'core.php';

//$valid['success'] = array('success' => false, 'messages' => array());
$categoriesId = $_GET['id'];
if($_POST) {	

	$brandName = $_POST['categoriesName'];
  $brandStatus = $_POST['categoriesStatus']; 

	$stmt = $connect->prepare("UPDATE categories SET categories_name = ?, categories_active = ? WHERE categories_id = ?");
	$stmt->bindValue(1, $brandName);
	$stmt->bindValue(2, $brandStatus);
	$stmt->bindValue(3, $categoriesId, PDO::PARAM_INT);

	if($stmt->execute()) {
	 	$valid['success'] = true;
		$valid['messages'] = "Successfully Updated";
		// header removed	
	} else {
	 	$valid['success'] = false;
	 	$valid['messages'] = "Error while updating the categories";
	}

	echo json_encode($valid);
 
} // /if $_POST
