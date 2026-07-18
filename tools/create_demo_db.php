<?php
$sourceDb = __DIR__ . '/www/farmacia.db';
$demoDb = __DIR__ . '/farmacia_demo.db';

if (copy($sourceDb, $demoDb)) {
    echo "Copia creada. Limpiando datos...\n";
    $connect = new PDO("sqlite:" . $demoDb);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vaciar órdenes
    $connect->exec("DELETE FROM orders");
    $connect->exec("DELETE FROM order_item");
    // Vaciar cajas (en caso de que hayan)
    $connect->exec("DROP TABLE IF EXISTS cash_register_log");
    $connect->exec("DROP TABLE IF EXISTS inventory_movements");
    
    echo "¡Base de datos demo generada correctamente en: $demoDb !\n";
} else {
    echo "Error copiando farmacia.db\n";
}
