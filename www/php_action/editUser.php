<?php 	

require_once 'core.php';

$userid = $_GET['id'];
if($_POST) {
	$edituserName = $_POST['edituserName'];
	$editPassword = password_hash($_POST['editPassword'], PASSWORD_BCRYPT);
	
	$stmt = $connect->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ?");
	$stmt->bindValue(1, $edituserName);
	$stmt->bindValue(2, $editPassword);
	$stmt->bindValue(3, $userid, PDO::PARAM_INT);

	if($stmt->execute()) {
		$valid['success'] = true;
		$valid['messages'] = "Successfully Update";	
		// header removed
	} else {
		$valid['success'] = false;
		$valid['messages'] = "Error while updating product info";
	}

} // /$_POST

echo json_encode($valid);
?>
