<?php
// voidOrder.php - Anular factura y devolver stock al inventario
require_once "core.php";

header("Content-Type: application/json");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo json_encode(["success" => false, "message" => "ID de factura invalido."]);
    exit;
}

$orderId = (int)$_GET["id"];

try {
    $connect->beginTransaction();

    // 1. Verificar que la factura existe y no esta ya anulada
    $stmt = $connect->prepare("SELECT id, delete_status FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("La factura no existe.");
    }
    if ($order["delete_status"] == 2) {
        throw new Exception("Esta factura ya fue anulada anteriormente.");
    }

    // 2. Obtener los items de la factura
    $itemStmt = $connect->prepare("SELECT productName, quantity FROM order_item WHERE lastid = ?");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        throw new Exception("La factura no tiene productos asociados.");
    }

    // 3. Devolver stock al inventario
    $updateStock = $connect->prepare("UPDATE product SET quantity = quantity + ? WHERE product_id = ?");
    $insertMov = $connect->prepare(
        "INSERT INTO inventory_movements (product_id, movement_type, quantity, date, reference, balance)
         VALUES (?, \"RETURN\", ?, datetime(\"now\"), ?, (SELECT quantity FROM product WHERE product_id = ?))"
    );

    foreach ($items as $item) {
        $productId = (int)$item["productName"];
        $qty = (int)$item["quantity"];
        
        // Devolver stock
        $updateStock->execute([$qty, $productId]);
        
        // Registrar movimiento de devolucion
        $ref = "Anulacion factura #" . $orderId;
        $insertMov->execute([$productId, $qty, $ref, $productId]);
    }

    // 4. Marcar factura como anulada (delete_status = 2)
    $voidStmt = $connect->prepare("UPDATE orders SET delete_status = 2 WHERE id = ?");
    $voidStmt->execute([$orderId]);

    // 5. Log de auditoria
    $logStmt = $connect->prepare(
        "INSERT INTO system_log (date, user, module, action, details) VALUES (datetime(\"now\"), ?, \"Facturas\", \"ANULAR\", ?)"
    );
    $userId = isset($_SESSION["userId"]) ? $_SESSION["userId"] : "Sistema";
    $logStmt->execute([$userId, "Factura #$orderId anulada. " . count($items) . " productos devueltos al inventario."]);

    $connect->commit();

    echo json_encode([
        "success" => true,
        "message" => "Factura #$orderId anulada. Se devolvieron " . count($items) . " producto(s) al inventario."
    ]);

} catch (Exception $e) {
    $connect->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

