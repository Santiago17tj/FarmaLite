<?php
require 'www/constant/connect.php';

$response = ['success' => false, 'messages' => []];

try {
    // 1. Create a backup first
    $backupDir = __DIR__ . '/www/backups';
    if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
    
    $sourceDb = __DIR__ . '/www/farmacia.db';
    $backupDb = $backupDir . '/farmacia_backup_pre_migration.db';
    if(!copy($sourceDb, $backupDb)) {
        throw new Exception("No se pudo crear el backup previo a la migración.");
    }

    $connect->beginTransaction();

    // 2. Create new table with INTEGER quantity and REAL for prices
    $connect->exec('
        CREATE TABLE "product_new" (
          "product_id" INTEGER PRIMARY KEY AUTOINCREMENT,
          "product_name" TEXT NOT NULL,
          "barcode" TEXT DEFAULT NULL UNIQUE,
          "product_image" TEXT NOT NULL DEFAULT "",
          "brand_id" INTEGER NOT NULL DEFAULT 0,
          "categories_id" INTEGER NOT NULL DEFAULT 0,
          "quantity" INTEGER NOT NULL DEFAULT 0,
          "purchase_price" REAL NOT NULL DEFAULT 0,
          "rate" REAL NOT NULL DEFAULT 0,
          "mrp" REAL NOT NULL DEFAULT 0,
          "bno" TEXT NOT NULL DEFAULT "",
          "expdate" TEXT NOT NULL DEFAULT "",
          "added_date" TEXT NOT NULL DEFAULT "",
          "active" INTEGER NOT NULL DEFAULT 0,
          "status" INTEGER NOT NULL DEFAULT 0
        )
    ');

    // 3. Copy data
    $connect->exec('
        INSERT INTO product_new (product_id, product_name, barcode, product_image, brand_id, categories_id, quantity, purchase_price, rate, mrp, bno, expdate, added_date, active, status)
        SELECT product_id, product_name, barcode, product_image, brand_id, categories_id, CAST(quantity AS INTEGER), purchase_price, CAST(rate AS REAL), mrp, bno, expdate, added_date, active, status
        FROM product
    ');

    // 4. Drop old table
    $connect->exec('DROP TABLE product');

    // 5. Rename new table
    $connect->exec('ALTER TABLE product_new RENAME TO product');

    // 6. Recreate indexes
    $queries = [
        "CREATE INDEX IF NOT EXISTS idx_product_name ON product(product_name)",
        "CREATE INDEX IF NOT EXISTS idx_product_bno ON product(bno)",
        "CREATE INDEX IF NOT EXISTS idx_product_expdate ON product(expdate)"
    ];
    foreach ($queries as $q) {
        $connect->exec($q);
    }

    $connect->commit();

    // 7. Integrity Check
    $res = $connect->query("PRAGMA integrity_check;")->fetchColumn();
    if($res !== 'ok') {
        throw new Exception("Integrity check failed: " . $res);
    }

    echo "Migración completada y verificada exitosamente.\n";

} catch (Exception $e) {
    if($connect->inTransaction()) {
        $connect->rollBack();
    }
    echo "Error en la migración: " . $e->getMessage() . "\n";
    // Si hubo error, sugerir restaurar el backup
    echo "Restaure manualmente desde: " . $backupDb . "\n";
}
