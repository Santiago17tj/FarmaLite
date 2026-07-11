<?php
require_once 'core.php';

$term = $_POST['term'] ?? '';

if(empty($term)) {
    echo json_encode(['success' => false, 'messages' => 'Término de búsqueda vacío']);
    exit;
}

$sql = "SELECT product_name, rate, quantity, bno, expdate FROM product WHERE status = 1 AND active = 1 AND (barcode = ? OR product_name LIKE ?) AND quantity > 0 ORDER BY expdate ASC LIMIT 1";
$stmt = $connect->prepare($sql);
$stmt->bindValue(1, $term);
$stmt->bindValue(2, '%' . $term . '%');
$stmt->execute();
$result = $stmt->fetch();

if($result) {
    echo json_encode([
        'success' => true,
        'data' => [
            'name' => $result['product_name'],
            'price' => number_format($result['rate'], 2),
            'stock' => $result['quantity'],
            'bno' => $result['bno'],
            'expdate' => $result['expdate']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'messages' => 'Producto no encontrado']);
}
