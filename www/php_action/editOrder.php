<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$orderId = (int) $_POST['orderId'];

	$orderDate = date('Y-m-d', strtotime($_POST['orderDate']));
  $clientName = $_POST['clientName'];
  $clientContact = $_POST['clientContact'];
  $subTotalValue = $_POST['subTotalValue'];
  $totalAmountValue = $_POST['totalAmountValue'];
  $discount = $_POST['discount'];
  $grandTotalValue = $_POST['grandTotalValue'];
  $paid = $_POST['paid'];
  $dueValue = $_POST['dueValue'];
  $paymentType = $_POST['paymentType'];
  $paymentStatus = $_POST['paymentStatus'];
  $paymentPlace = $_POST['paymentPlace'];
  $gstn = $_POST['gstn'];
	$userid = $_SESSION['userId'];
				
	$stmtUpdate = $connect->prepare("UPDATE orders SET orderDate = ?, clientName = ?, clientContact = ?, subTotal = ?, totalAmount = ?, discount = ?, grandTotalValue = ?, paid = ?, paymentType = ?, paymentStatus = ?, paymentPlace = ?, gstn = ? WHERE id = ?");
	$stmtUpdate->bindValue(1, $orderDate);
	$stmtUpdate->bindValue(2, $clientName);
	$stmtUpdate->bindValue(3, $clientContact);
	$stmtUpdate->bindValue(4, $subTotalValue);
	$stmtUpdate->bindValue(5, $totalAmountValue);
	$stmtUpdate->bindValue(6, $discount);
	$stmtUpdate->bindValue(7, $grandTotalValue);
	$stmtUpdate->bindValue(8, $paid);
	$stmtUpdate->bindValue(9, $paymentType);
	$stmtUpdate->bindValue(10, $paymentStatus);
	$stmtUpdate->bindValue(11, $paymentPlace);
	$stmtUpdate->bindValue(12, $gstn);
	$stmtUpdate->bindValue(13, $orderId, PDO::PARAM_INT);
	$connect->query($stmtUpdate->execute());
	
	$readyToUpdateOrderItem = false;
	// add the quantity from the order item to product table
	for($x = 0; $x < count($_POST['productName']); $x++) {		
		$prodId = (int) $_POST['productName'][$x];
		$updateProductQuantityStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
		$updateProductQuantityStmt->bindValue(1, $prodId, PDO::PARAM_INT);
		$updateProductQuantityStmt->execute();
		$updateProductQuantityResult = $updateProductQuantityStmt->fetch(PDO::FETCH_NUM);
		
		if ($updateProductQuantityResult) {
			$orderItemStmt = $connect->prepare("SELECT quantity FROM order_item WHERE lastid = ?");
			$orderItemStmt->bindValue(1, $orderId, PDO::PARAM_INT);
			$orderItemStmt->execute();
			$orderItemData = $orderItemStmt->fetch(PDO::FETCH_NUM);

			$editQuantity = $updateProductQuantityResult[0] + ($orderItemData ? $orderItemData[0] : 0);

			$updateQtyStmt = $connect->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
			$updateQtyStmt->bindValue(1, $editQuantity);
			$updateQtyStmt->bindValue(2, $prodId, PDO::PARAM_INT);
			$updateQtyStmt->execute();
		}
		
		if(count($_POST['productName']) == count($_POST['productName'])) {
			$readyToUpdateOrderItem = true;			
		}
	} // /for quantity

	// remove the order item data from order item table
	$removeStmt = $connect->prepare("DELETE FROM order_item WHERE lastid = ?");
	$removeStmt->bindValue(1, $orderId, PDO::PARAM_INT);
	$removeStmt->execute();

	if($readyToUpdateOrderItem) {
		// insert the order item data 
		for($x = 0; $x < count($_POST['productName']); $x++) {
			$prodId = (int) $_POST['productName'][$x];
			$pqStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
			$pqStmt->bindValue(1, $prodId, PDO::PARAM_INT);
			$pqStmt->execute();
			$pqResult = $pqStmt->fetch(PDO::FETCH_NUM);
			
			if ($pqResult) {
				$updateQuantity = $pqResult[0] - $_POST['quantity'][$x];
				$upStmt = $connect->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
				$upStmt->bindValue(1, $updateQuantity);
				$upStmt->bindValue(2, $prodId, PDO::PARAM_INT);
				$upStmt->execute();

				$oiStmt = $connect->prepare("INSERT INTO order_item (lastid, productName, quantity, rate, total) VALUES (?, ?, ?, ?, ?)");
				$oiStmt->bindValue(1, $orderId, PDO::PARAM_INT);
				$oiStmt->bindValue(2, $_POST['productName'][$x]);
				$oiStmt->bindValue(3, $_POST['quantity'][$x]);
				$oiStmt->bindValue(4, $_POST['rateValue'][$x]);
				$oiStmt->bindValue(5, $_POST['totalValue'][$x]);
				$oiStmt->execute();
			}
		} // /for quantity
	}

	$valid['success'] = true;
	$valid['messages'] = "Successfully Updated";
	header('location:'.$_SERVER['HTTP_REFERER']);

	echo json_encode($valid);
 
} // /if $_POST
