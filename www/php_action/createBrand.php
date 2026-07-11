<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	

	$brandName = $_POST['brandName'];
  $brandStatus = $_POST['brandStatus']; 

	$stmt = $connect->prepare("INSERT INTO brands (brand_name, brand_active, brand_status) VALUES (?, ?, 1)");
	$stmt->bindValue(1, $brandName);
	$stmt->bindValue(2, $brandStatus);

	if($stmt->execute()) {
	 	$valid['success'] = true;
		$valid['messages'] = "Successfully Added";
		// header removed	
	}  
	  
     else {
	 	$valid['success'] = false;
	 	$valid['messages'] = "Error while adding the members";
	 	// header removed
	}

	echo json_encode($valid);
 
} // /if $_POST
