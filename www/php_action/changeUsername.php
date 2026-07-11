<?php 

require_once 'core.php';

if($_POST) {

	$valid['success'] = array('success' => false, 'messages' => array());

	$user_id = $_SESSION['userId'];
	$username = $_POST['username'];

	$stmt = $connect->prepare("UPDATE users SET username = ? WHERE user_id = ?");
	$stmt->bindValue(1, $username);
	$stmt->bindValue(2, $user_id, PDO::PARAM_INT);

	if($stmt->execute()) {
		$valid['success'] = true;
		$valid['messages'] = "Successfully Update";
		// header removed		
	} 
	else {
		$valid['success'] = false;
		$valid['messages'] = "Error while updating product info";
	}

	echo json_encode($valid);

}

?>
