<?php

require_once 'core.php';

$valid = array(
	'success' => false,
	'messages' => '',
	'product' => null
);

if (!isset($_POST['barcode']) || trim($_POST['barcode']) === '') {
	$valid['messages'] = 'Código de barras vacío';
	echo json_encode($valid);
	exit;
}

$barcode = trim($_POST['barcode']);

$stmt = $connect->prepare("SELECT product_id, product_name, product_image, brand_id, categories_id, quantity, purchase_price, rate, mrp, bno, expdate, barcode, active, status
		FROM product
		WHERE barcode = ? AND status = 1 AND active = 1 AND quantity > 0
        ORDER BY expdate ASC
		LIMIT 1");
$stmt->bindValue(1, $barcode);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
		$valid['success'] = true;
		$valid['product'] = $row;
		$valid['messages'] = $row['product_name'];
} else {
	$valid['messages'] = 'No se encontró medicamento con ese código de barras';
}

echo json_encode($valid);
