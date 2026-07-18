<?php
require 'www/constant/connect.php';

$testsPassed = 0;
$testsFailed = 0;
$totalTests = 9;

echo "--- INICIANDO BATERÍA DE PRUEBAS DEL NÚCLEO ---\n\n";

function assertTest($name, $condition, $message = "") {
    global $testsPassed, $testsFailed;
    if ($condition) {
        $testsPassed++;
        echo "[PASSED] $name\n";
    } else {
        $testsFailed++;
        echo "[FAILED] $name - $message\n";
    }
}

// Ensure test product exists
$connect->exec("INSERT INTO product (product_id, product_name, quantity, rate, active, status, expdate) VALUES (99991, 'TEST_SUFICIENTE', 10, 100, 1, 1, '2099-01-01') ON CONFLICT DO UPDATE SET quantity=10, expdate='2099-01-01'");
$connect->exec("INSERT INTO product (product_id, product_name, quantity, rate, active, status, expdate) VALUES (99992, 'TEST_VENCIDO', 10, 100, 1, 1, '2000-01-01') ON CONFLICT DO UPDATE SET quantity=10, expdate='2000-01-01'");

// Simulate order.php logic
function runOrder($pId, $pQty, $pRate) {
    global $connect;
    try {
        $connect->beginTransaction();
        
        if ($pQty <= 0) throw new Exception("Cantidad inválida para el producto.");
        if ($pRate <= 0) throw new Exception("Precio inválido para el producto.");
        
        $stmt = $connect->prepare("SELECT product_name, quantity, expdate FROM product WHERE product_id = ?");
        $stmt->execute([$pId]);
        $pData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pData) throw new Exception("Producto no encontrado.");
        if ($pData['quantity'] < $pQty) throw new Exception("Stock insuficiente.");
        
        $today = date("Y-m-d");
        if ($pData['expdate'] < $today) {
            $settingStmt = $connect->query("SELECT value FROM settings WHERE key = 'block_expired'");
            $blockExpired = $settingStmt->fetchColumn();
            if ($blockExpired === '1' || $blockExpired === false) {
                throw new Exception("Producto vencido.");
            }
        }
        
        // Update stock
        $update = $connect->prepare("UPDATE product SET quantity = quantity - ? WHERE product_id = ?");
        $update->execute([$pQty, $pId]);
        
        $connect->commit();
        return "SUCCESS";
    } catch (Exception $e) {
        $connect->rollBack();
        return $e->getMessage();
    }
}

// 1. Vender stock suficiente
$res = runOrder(99991, 2, 100);
assertTest("Venta Stock Suficiente", $res === "SUCCESS");

// Check real DB stock
$currentStock = $connect->query("SELECT quantity FROM product WHERE product_id=99991")->fetchColumn();
assertTest("Rollback/Commit correcto (Stock bajó a 8)", $currentStock == 8);

// 2. Vender stock insuficiente
$res = runOrder(99991, 20, 100);
assertTest("Venta Stock Insuficiente", $res === "Stock insuficiente.");

// 3. Vender producto vencido
$res = runOrder(99992, 1, 100);
assertTest("Venta Producto Vencido", $res === "Producto vencido.");

// 4. Vender cantidad negativa
$res = runOrder(99991, -5, 100);
assertTest("Venta Cantidad Negativa", $res === "Cantidad inválida para el producto.");

// 5. Vender cantidad cero
$res = runOrder(99991, 0, 100);
assertTest("Venta Cantidad Cero", $res === "Cantidad inválida para el producto.");

// 6. Vender precio cero
$res = runOrder(99991, 1, 0);
assertTest("Venta Precio Cero o Negativo", $res === "Precio inválido para el producto.");

// 7. Simular rollback en fallo
$res = runOrder(99991, 100, 100); // fails
$finalStock = $connect->query("SELECT quantity FROM product WHERE product_id=99991")->fetchColumn();
assertTest("Rollback en venta fallida no altera inventario", $finalStock == 8);

// 8. Integridad de DB
$res = $connect->query("PRAGMA integrity_check")->fetchColumn();
assertTest("Integridad SQLite (PRAGMA integrity_check)", $res === "ok");

echo "\n--- RESUMEN ---\n";
echo "Pruebas Ejecutadas: " . ($testsPassed + $testsFailed) . "\n";
echo "Aprobadas: $testsPassed\n";
echo "Fallidas: $testsFailed\n";

// Limpiar test
$connect->exec("DELETE FROM product WHERE product_id IN (99991, 99992)");

