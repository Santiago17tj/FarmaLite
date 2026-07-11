<?php

ini_set('display_errors', '0');
require_once 'core.php';
require_once '../libraries/phpexcel/PHPExcel.php';
require_once '../libraries/phpexcel/PHPExcel/IOFactory.php';

$valid = array('success' => false, 'messages' => '');

if (!$_FILES || !isset($_FILES['productfile'])) {
	$valid['messages'] = 'No se recibió ningún archivo.';
	echo json_encode($valid);
	exit;
}

$type = explode('.', $_FILES['productfile']['name']);
$type = strtolower($type[count($type) - 1]);

if (!in_array($type, array('csv', 'xls', 'xlsx'))) {
	$valid['messages'] = 'Formato no válido. Use Excel (.xlsx, .xls) o CSV.';
	echo json_encode($valid);
	exit;
}

if (!is_uploaded_file($_FILES['productfile']['tmp_name'])) {
	$valid['messages'] = 'Error al subir el archivo.';
	echo json_encode($valid);
	exit;
}

$url = '../assets/myimages/' . uniqid('import_') . '.' . $type;
if (!move_uploaded_file($_FILES['productfile']['tmp_name'], $url)) {
	$valid['messages'] = 'No se pudo guardar el archivo temporal.';
	echo json_encode($valid);
	exit;
}

$imported = 0;
$skipped = 0;
$errors = array();

try {
	$objPHPExcel = PHPExcel_IOFactory::load($url);
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();

	for ($row = 2; $row <= $highestRow; $row++) {
		$barcode = trim((string) $sheet->getCell('A' . $row)->getValue());
		$name = trim((string) $sheet->getCell('B' . $row)->getValue());
		$brandName = trim((string) $sheet->getCell('C' . $row)->getValue());
		$categoryName = trim((string) $sheet->getCell('D' . $row)->getValue());
		$purchasePrice = (int) $sheet->getCell('E' . $row)->getValue();
		$salePrice = (int) $sheet->getCell('F' . $row)->getValue();
		$quantity = (int) $sheet->getCell('G' . $row)->getValue();
		$bno = trim((string) $sheet->getCell('H' . $row)->getValue());
		$expdate = trim((string) $sheet->getCell('I' . $row)->getValue());

		if ($name === '') {
			continue;
		}

		$barcodeEsc = ($barcode);
		$nameEsc = ($name);
		$bnoEsc = ($bno !== '' ? $bno : 'NA');
		$expdateEsc = ($expdate !== '' ? $expdate : date('Y-m-d', strtotime('+2 years')));

		if ($barcode !== '') {
			$dup = $connect->query("SELECT product_id FROM product WHERE barcode = '$barcodeEsc' LIMIT 1");
			if ($dup && $dup->fetch()) {
				$skipped++;
				$errors[] = "Fila $row: código $barcode ya existe.";
				continue;
			}
		}

		$brandId = 1;
		if ($brandName !== '') {
			$brandEsc = ($brandName);
			$br = $connect->query("SELECT brand_id FROM brands WHERE brand_name = '$brandEsc' AND brand_status = 1 LIMIT 1");
			if ($br && $br->fetch()) {
				$brandId = (int) $br->fetch(PDO::FETCH_ASSOC)['brand_id'];
			}
		}

		$categoryId = 1;
		if ($categoryName !== '') {
			$catEsc = ($categoryName);
			$cr = $connect->query("SELECT categories_id FROM categories WHERE categories_name = '$catEsc' AND categories_status = 1 LIMIT 1");
			if ($cr && $cr->fetch()) {
				$categoryId = (int) $cr->fetch(PDO::FETCH_ASSOC)['categories_id'];
			}
		}

		if ($salePrice <= 0) {
			$errors[] = "Fila $row: precio de venta inválido.";
			$skipped++;
			continue;
		}

		$barcodeSql = $barcode !== '' ? "'$barcodeEsc'" : 'NULL';
		$addedDate = date('Y-m-d');

		$sql = "INSERT INTO product (product_name, barcode, product_image, brand_id, categories_id, quantity, purchase_price, rate, mrp, bno, expdate, added_date, active, status)
			VALUES ('$nameEsc', $barcodeSql, '', $brandId, $categoryId, '$quantity', '$purchasePrice', '$salePrice', '$salePrice', '$bnoEsc', '$expdateEsc', '$addedDate', 1, 1)";

		if ($connect->query($sql)) {
			$imported++;
		} else {
			$skipped++;
			$errors[] = "Fila $row: error al guardar $name.";
		}
	}
} catch (Exception $e) {
	@unlink($url);
	$valid['messages'] = 'Error al leer el archivo: ' . $e->getMessage();
	echo json_encode($valid);
	exit;
}

@unlink($url);

$valid['success'] = $imported > 0;
$valid['messages'] = "Importados: $imported. Omitidos: $skipped.";
if (!empty($errors)) {
	$valid['messages'] .= ' ' . implode(' ', array_slice($errors, 0, 5));
}

header('location:../import-product.php?imported=' . $imported . '&skipped=' . $skipped);
exit;
