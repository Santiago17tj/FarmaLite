<?php 	

require_once 'core.php';

$orderId = (int) $_POST['orderId'];

$valid = array('order' => array(), 'order_item' => array());

$stmt = $connect->prepare("SELECT id, orderDate, clientName, clientContact, subTotal, gstn, totalAmount, discount, grandTotalValue, paid, dueValue, paymentType, paymentStatus FROM orders WHERE id = ?");
$stmt->bindValue(1, $orderId, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_NUM);
$valid['order'] = $data;

echo json_encode($valid);
