<?php
// php_action/searchProductByName.php
// Busqueda de medicamentos por nombre (para el modo sin codigo de barras)
require_once "core.php";

header("Content-Type: application/json");

$q = trim($_GET["q"] ?? "");

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $connect->prepare(
    "SELECT product_id, product_name, sell_price, quantity
     FROM product
     WHERE status = 1 AND active = 1 AND quantity > 0
       AND LOWER(product_name) LIKE LOWER(:q)
     ORDER BY product_name
     LIMIT 15"
);
$stmt->execute([":" . "q" => "%" . $q . "%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
