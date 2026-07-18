<?php
require 'www/constant/connect.php';

// Crear cash_register_log
$connect->exec("
CREATE TABLE IF NOT EXISTS cash_register_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    opening_time TEXT NOT NULL,
    opening_balance REAL NOT NULL,
    closing_time TEXT,
    system_total REAL DEFAULT 0,
    cash_total REAL DEFAULT 0,
    card_total REAL DEFAULT 0,
    transfer_total REAL DEFAULT 0,
    other_total REAL DEFAULT 0,
    difference REAL DEFAULT 0,
    status TEXT NOT NULL DEFAULT 'OPEN',
    notes TEXT
)
");

// Crear inventory_movements
$connect->exec("
CREATE TABLE IF NOT EXISTS inventory_movements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    movement_type TEXT NOT NULL,
    quantity INTEGER NOT NULL,
    date TEXT NOT NULL,
    reference TEXT
)
");

// Add payment_type column to orders if it doesn't exist to store exact method
try {
    $connect->exec("ALTER TABLE orders ADD COLUMN exact_payment_type TEXT DEFAULT 'Efectivo'");
} catch(Exception $e) {
    // might already exist
}

echo "Tablas de la Fase 1.1 creadas con éxito.";
