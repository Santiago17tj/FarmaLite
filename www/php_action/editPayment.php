<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$orderId = (int) $_POST['orderId'];
	$payAmount = $_POST['payAmount']; 
  $paymentType = $_POST['paymentType'];
  $paymentStatus = $_POST['paymentStatus'];  
  $paidAmount = $_POST['paidAmount'];
  $grandTotal = $_POST['grandTotal'];

  $updatePaidAmount = $payAmount + $paidAmount;
  $updateDue = $grandTotal - $updatePaidAmount;

	$stmt = $connect->prepare("UPDATE orders SET paid = ?, dueValue = ?, paymentType = ?, paymentStatus = ? WHERE id = ?");
	$stmt->bindValue(1, $updatePaidAmount);
	$stmt->bindValue(2, $updateDue);
	$stmt->bindValue(3, $paymentType);
	$stmt->bindValue(4, $paymentStatus);
	$stmt->bindValue(5, $orderId, PDO::PARAM_INT);

	if($stmt->execute()) {
		$valid['success'] = true;
		$valid['messages'] = "Successfully Update";	
	} else {
		$valid['success'] = false;
		$valid['messages'] = "Error while updating product info";
	}

echo json_encode($valid);
 
} // /if $_POST
