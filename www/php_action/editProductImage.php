<?php 	

require_once 'core.php';

$productId = (int) $_GET['id'];

if($_POST) {		

$image = $_FILES['productImage']['name'];
$target = "../assets/myimages/".basename($image);

if (move_uploaded_file($_FILES['productImage']['tmp_name'], $target)) {
      $msg = "Image uploaded successfully";
      echo $msg;
    }
    else{
      $msg = "Failed to upload image";
      echo $msg;exit;
    }		
			

	$stmt = $connect->prepare("UPDATE product SET product_image = ? WHERE product_id = ?");
	$stmt->bindValue(1, $image);
	$stmt->bindValue(2, $productId, PDO::PARAM_INT);
	if($stmt->execute()) {								
		$valid['success'] = true;
		$valid['messages'] = "Successfully Updated";
		// header removed
	} 
	else {
		$valid['success'] = false;
		$valid['messages'] = "Error while updating product image";
	}

	echo json_encode($valid);
 
} // /if $_POST
?>
