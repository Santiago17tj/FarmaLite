<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_POST) {
    // Verify open box
    $checkOpen = $connect->query("SELECT id FROM cash_register_log WHERE status = 'OPEN'");
    if (!$checkOpen->fetchColumn()) {
        $valid['success'] = false;
        $valid['messages'] = "Debe abrir caja antes de poder registrar ventas.";
        echo json_encode($valid);
        exit;
    }

    $uno = $_POST["uno"];
    $orderDate = $_POST["orderDate"];
    $clientName = $_POST["clientName"];
    $clientContact = $_POST["clientContact"];
    $subTotal = $_POST["subTotalValue"];
    $totalAmount = $_POST["totalAmountValue"];
    $discount = $_POST["discount"];
    $grandTotal = $_POST["grandTotalValue"];
    $gstn = $_POST["gstn"];
    $paid = $_POST["paid"];
    $dueValue = $_POST["dueValue"];
    $paymentType = $_POST["paymentType"];
    $paymentStatus = $_POST["paymentStatus"];
    $paymentPlace = $_POST["paymentPlace"];
      
    $exact = "Otros";
    if($paymentType == 2) $exact = "Efectivo";
    if($paymentType == 3) $exact = "Tarjeta";
    if($paymentType == 4) $exact = "Transferencia";
    if($paymentType == 5 || $paymentType == 6 || $paymentType == 1) $exact = "Otros";

    $createdAt = date("Y-m-d H:i:s");

    $stmt = $connect->prepare(
        "INSERT INTO orders (uno, orderDate, clientName, gstn, clientContact, subTotal, totalAmount, discount, grandTotalValue, paid, dueValue, paymentType, paymentStatus, paymentPlace, exact_payment_type, created_at) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bindValue(1, $uno);
    $stmt->bindValue(2, $orderDate);
    $stmt->bindValue(3, $clientName);
    $stmt->bindValue(4, $gstn);
    $stmt->bindValue(5, $clientContact);
    $stmt->bindValue(6, $subTotal);
    $stmt->bindValue(7, $totalAmount);
    $stmt->bindValue(8, $discount);
    $stmt->bindValue(9, $grandTotal);
    $stmt->bindValue(10, $paid);
    $stmt->bindValue(11, $dueValue);
    $stmt->bindValue(12, $paymentType);
    $stmt->bindValue(13, $paymentStatus);
    $stmt->bindValue(14, $paymentPlace);
    $stmt->bindValue(15, $exact);
    $stmt->bindValue(16, $createdAt);

    try {
        $connect->beginTransaction();

        if ($stmt->execute()) {
            $lastid = $connect->lastInsertId();
            $checkbox1 = count($_POST["productName"]);

            $stmt1 = $connect->prepare(
                "INSERT INTO order_item (productName, quantity, rate, total, lastid, added_date) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $updateProductStmt = $connect->prepare(
                "UPDATE product SET quantity = quantity - ? WHERE product_id = ?"
            );

            $checkStockStmt = $connect->prepare("SELECT product_name, quantity, expdate FROM product WHERE product_id = ?");

            $added_date = date("Y-m-d");
            $allSuccess = true;
            $errorMsg = "";

            for ($i = 0; $i < $checkbox1; $i++) {
                $pId = $_POST["productName"][$i];
                $pQty = (int)$_POST["quantity"][$i];
                $pRate = (float)$_POST["rateValue"][$i];
                
                // 1. Validar precio y cantidad válidos
                if ($pQty <= 0) {
                    throw new Exception("Cantidad inválida para el producto.");
                }
                if ($pRate <= 0) {
                    throw new Exception("Precio inválido para el producto.");
                }

                // 2. Verificar Stock y Vencimiento
                $checkStockStmt->bindValue(1, $pId, PDO::PARAM_INT);
                $checkStockStmt->execute();
                $pData = $checkStockStmt->fetch(PDO::FETCH_ASSOC);

                if (!$pData) {
                    throw new Exception("Producto no encontrado en la base de datos.");
                }

                if ($pData['quantity'] < $pQty) {
                    throw new Exception("Stock insuficiente para: " . $pData['product_name'] . " (Disponible: " . $pData['quantity'] . ")");
                }

                $today = date("Y-m-d");
                if ($pData['expdate'] < $today) {
                    $settingStmt = $connect->query("SELECT value FROM settings WHERE key = 'block_expired'");
                    $blockExpired = $settingStmt->fetchColumn();
                    if ($blockExpired === '1' || $blockExpired === false) {
                        throw new Exception("El producto " . $pData['product_name'] . " está vencido.");
                    }
                }
                
                // 3. Registrar Order Item
                $stmt1->bindValue(1, $pId);
                $stmt1->bindValue(2, $pQty);
                $stmt1->bindValue(3, $pRate);
                $stmt1->bindValue(4, $_POST["totalValue"][$i]);
                $stmt1->bindValue(5, $lastid);
                $stmt1->bindValue(6, $added_date);
                if (!$stmt1->execute()) {
                    throw new Exception("Error al guardar detalle de factura.");
                }

                // 4. Actualizar Inventario
                $updateProductStmt->bindValue(1, $pQty, PDO::PARAM_INT);
                $updateProductStmt->bindValue(2, $pId, PDO::PARAM_INT);
                if (!$updateProductStmt->execute()) {
                    throw new Exception("Error al descontar stock.");
                }
                
                // Obtener el nuevo saldo
                $balanceStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
                $balanceStmt->execute([$pId]);
                $balance = $balanceStmt->fetchColumn();

                // 5. Registrar movimiento de inventario
                $movStmt = $connect->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, date, reference, balance) VALUES (?, 'SALE', ?, ?, ?, ?)");
                $movStmt->execute([$pId, -$pQty, $createdAt, "Factura #$lastid", $balance]);
            }

            if ($allSuccess) {
                $connect->commit();
                $valid["success"] = true;
                $valid["messages"] = "Successfully Added";
                $valid["order_id"] = $lastid;
            } else {
                $connect->rollBack();
                $valid["success"] = false;
                $valid["messages"] = "Error while adding the members";
            }
        } else {
            $connect->rollBack();
            $valid["success"] = false;
            $valid["messages"] = "Error while adding the members";
        }
    } catch (Exception $e) {
        $connect->rollBack();
        $valid["success"] = false;
        $valid["messages"] = "Error: " . $e->getMessage();
    }

    echo json_encode($valid);
} // /if $_POST

