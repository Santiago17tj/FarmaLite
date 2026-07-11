<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];
$productId = (int) $_GET["id"];

if ($_POST) {
    $productName = $_POST["editProductName"];
    $quantity = $_POST["editQuantity"];
    $rate = $_POST["editRate"];
    $purchasePrice = isset($_POST["purchase_price"])
        ? (int) $_POST["purchase_price"]
        : 0;
    $mrp = $rate;
    $brandName = $_POST["editBrandName"];
    $categoryName = $_POST["editCategoryName"];
    $productStatus = $_POST["editProductStatus"];
    $bno = $_POST["bno"];
    $expdate = $_POST["expdate"];
    $barcode = isset($_POST["barcode"]) ? trim($_POST["barcode"]) : "";
    $barcodeParam = $barcode !== "" ? $barcode : null;

    // Get old quantity
    $oldQtyStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
    $oldQtyStmt->execute([$productId]);
    $oldQty = $oldQtyStmt->fetchColumn();

    $stmt = $connect->prepare(
        "UPDATE product SET product_name = ?, barcode = ?, brand_id = ?, categories_id = ?, " .
            "quantity = ?, purchase_price = ?, rate = ?, mrp = ?, bno = ?, expdate = ?, " .
            "active = ?, status = 1 WHERE product_id = ?"
    );
    $stmt->bindValue(1, $productName);
    $stmt->bindValue(2, $barcodeParam);
    $stmt->bindValue(3, $brandName);
    $stmt->bindValue(4, $categoryName);
    $stmt->bindValue(5, $quantity);
    $stmt->bindValue(6, $purchasePrice, PDO::PARAM_INT);
    $stmt->bindValue(7, $rate);
    $stmt->bindValue(8, $mrp);
    $stmt->bindValue(9, $bno);
    $stmt->bindValue(10, $expdate);
    $stmt->bindValue(11, $productStatus);
    $stmt->bindValue(12, $productId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Si cambió la cantidad, registrar el movimiento
        if ($oldQty != $quantity) {
            $diff = $quantity - $oldQty;
            $createdAt = date('Y-m-d H:i:s');
            $movStmt = $connect->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, date, reference, balance) VALUES (?, 'ADJUSTMENT', ?, ?, 'Ajuste manual (Edición)', ?)");
            $movStmt->execute([$productId, $diff, $createdAt, $quantity]);
        }

        $valid["success"] = true;
        $valid["messages"] = "Actualizado exitosamente";
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error no se ha podido actualizar";
    }
} // /$_POST

echo json_encode($valid);
