<?php
function runScenario($scenario) {
    echo "--- Ejecutando Escenario $scenario ---\n";
    $prodCount = 0; $orderCount = 0;
    if ($scenario == 'A') { $prodCount = 500; $orderCount = 5000; }
    if ($scenario == 'B') { $prodCount = 3000; $orderCount = 50000; }
    if ($scenario == 'C') { $prodCount = 15000; $orderCount = 100000; } // Adjusted for timeout
    
    $testDb = __DIR__ . '/benchmark.db';
    if (file_exists($testDb)) unlink($testDb);
    
    $sourceDb = new PDO("sqlite:" . __DIR__ . "/www/farmacia.db");
    $targetDb = new PDO("sqlite:" . $testDb);
    $targetDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = ['product', 'orders', 'order_item', 'inventory_movements'];
    foreach($tables as $tbl) {
        $stmt = $sourceDb->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$tbl'");
        $sql = $stmt->fetchColumn();
        if ($sql) $targetDb->exec($sql);
        $idxStmt = $sourceDb->query("SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name='$tbl'");
        while($idxSql = $idxStmt->fetchColumn()) {
            if ($idxSql) $targetDb->exec($idxSql);
        }
    }
    
    $targetDb->exec("PRAGMA synchronous = OFF");
    $targetDb->exec("PRAGMA journal_mode = MEMORY");
    
    // Seeding products
    $targetDb->beginTransaction();
    $insertProd = $targetDb->prepare("INSERT INTO product (product_id, product_name, barcode, quantity, purchase_price, rate, active, status) VALUES (?, ?, ?, ?, ?, ?, 1, 1)");
    for ($i = 1; $i <= $prodCount; $i++) {
        $insertProd->execute([$i, "Producto Prueba $i", "770" . str_pad($i, 9, "0", STR_PAD_LEFT), 1000, 500, 1000]);
    }
    $targetDb->commit();
    
    // Seeding orders chunked to avoid memory limits
    $insertOrder = $targetDb->prepare("INSERT INTO orders (id, uno, orderDate, clientName, subTotal, totalAmount, grandTotalValue, exact_payment_type, delete_status) VALUES (?, 'INV-TEST', '2023-01-01', 'Cliente Frecuente', 1000, 1000, 1000, 'Efectivo', 0)");
    $insertItem = $targetDb->prepare("INSERT INTO order_item (lastid, productName, quantity, rate, total) VALUES (?, ?, 1, 1000, 1000)");
    
    $chunkSize = 5000;
    $totalInserted = 0;
    while ($totalInserted < $orderCount) {
        $targetDb->beginTransaction();
        $limit = min($chunkSize, $orderCount - $totalInserted);
        for ($i = 1; $i <= $limit; $i++) {
            $id = $totalInserted + $i;
            $insertOrder->execute([$id]);
            $prodRand = rand(1, $prodCount);
            $insertItem->execute([$id, $prodRand]);
        }
        $targetDb->commit();
        $totalInserted += $limit;
    }
    
    // TESTS
    $start = microtime(true);
    $q1 = $targetDb->prepare("SELECT * FROM product WHERE barcode = ?");
    $q1->execute(["770" . str_pad(rand(1, $prodCount), 9, "0", STR_PAD_LEFT)]);
    $q1->fetchAll();
    echo "Busqueda (ms): " . round((microtime(true) - $start) * 1000, 2) . "\n";
    
    $start = microtime(true);
    $targetDb->beginTransaction();
    $newOrderId = $orderCount + 1;
    $targetDb->exec("INSERT INTO orders (id, uno, orderDate, clientName, subTotal, totalAmount, grandTotalValue, exact_payment_type, delete_status) VALUES ($newOrderId, 'INV-TEST', '2023-01-02', 'Test Venta', 1000, 1000, 1000, 'Efectivo', 0)");
    $targetDb->exec("INSERT INTO order_item (lastid, productName, quantity, rate, total) VALUES ($newOrderId, 1, 1, 1000, 1000)");
    $targetDb->exec("UPDATE product SET quantity = quantity - 1 WHERE product_id = 1");
    $targetDb->commit();
    echo "Venta (ms): " . round((microtime(true) - $start) * 1000, 2) . "\n";
    
    $start = microtime(true);
    $qReport = $targetDb->query("
        SELECT p.product_name, SUM(oi.quantity) as qty
        FROM order_item oi
        JOIN orders o ON oi.lastid = o.id
        JOIN product p ON oi.productName = p.product_id
        GROUP BY p.product_id
        ORDER BY qty DESC LIMIT 10
    ");
    $qReport->fetchAll();
    echo "Reporte (ms): " . round((microtime(true) - $start) * 1000, 2) . "\n";
    
    echo "Size (MB): " . round(filesize($testDb) / 1024 / 1024, 2) . "\n";
    
    // VACUUM Time
    $start = microtime(true);
    $targetDb->exec("VACUUM");
    echo "Vacuum (ms): " . round((microtime(true) - $start) * 1000, 2) . "\n\n";
    
}

runScenario('A');
runScenario('B');
runScenario('C');
