<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_POST) {
    $productName = $_POST["productName"];
    $productImage = $_POST["productImage"];
    $barcode = isset($_POST["barcode"]) ? trim($_POST["barcode"]) : "";
    $barcodeParam = $barcode !== "" ? $barcode : null;
    $quantity = $_POST["quantity"];
    $rate = $_POST["rate"];
    $purchasePrice = isset($_POST["purchase_price"])
        ? (int) $_POST["purchase_price"]
        : 0;
    $mrp = $rate;
    $brandName = $_POST["brandName"];
    $categoryName = $_POST["categoryName"];
    $bno = $_POST["bno"];
    $expdate = $_POST["expdate"];
    $productStatus = $_POST["productStatus"];

    $image = $_FILES["Medicine"]["name"];
    $target = "../assets/myimages/" . basename($image);
    $upload = move_uploaded_file($_FILES["Medicine"]["tmp_name"], $target);

    if ($upload) {
        $msg = "Image uploaded successfully";
        echo $msg;
    } else {
        $msg = "Failed to upload image";
        echo $msg;
        exit();
    }

    $orderDate = date("Y-m-d");

    $stmt = $connect->prepare(
        "INSERT INTO product (product_name, barcode, product_image, brand_id, categories_id, quantity, purchase_price, rate, mrp, bno, expdate, added_date, active, status) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
    );
    $stmt->bindValue(1, $productName);
    $stmt->bindValue(2, $barcodeParam);
    $stmt->bindValue(3, $image);
    $stmt->bindValue(4, $brandName);
    $stmt->bindValue(5, $categoryName);
    $stmt->bindValue(6, $quantity);
    $stmt->bindValue(7, $purchasePrice, PDO::PARAM_INT);
    $stmt->bindValue(8, $rate);
    $stmt->bindValue(9, $mrp);
    $stmt->bindValue(10, $bno);
    $stmt->bindValue(11, $expdate);
    $stmt->bindValue(12, $orderDate);
    $stmt->bindValue(13, $productStatus);

    if ($stmt->execute()) {
        $lastId = $connect->lastInsertId();
        
        // Log ENTRY movement
        $createdAt = date('Y-m-d H:i:s');
        $movStmt = $connect->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, date, reference, balance) VALUES (?, 'ENTRY', ?, ?, 'Ingreso inicial', ?)");
        $movStmt->execute([$lastId, $quantity, $createdAt, $quantity]);

        $valid["success"] = true;
        $valid["messages"] = "Agregado exitosamente";
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error no se ha podido guardar";
    }

    echo json_encode($valid);
} // /if $_POST
