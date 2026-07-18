<?php
require 'www/constant/connect.php';

$queries = [
    "CREATE INDEX IF NOT EXISTS idx_product_name ON product(product_name)",
    "CREATE INDEX IF NOT EXISTS idx_product_bno ON product(bno)",
    "CREATE INDEX IF NOT EXISTS idx_product_expdate ON product(expdate)",
    "CREATE INDEX IF NOT EXISTS idx_orders_date ON orders(orderDate)",
    "CREATE INDEX IF NOT EXISTS idx_orderitem_orderid ON order_item(lastid)"
];

foreach ($queries as $q) {
    echo "Ejecutando: $q\n";
    $connect->exec($q);
}
echo "Índices creados con éxito.\n";
