<?php
require 'www/constant/connect.php';

$connect->exec('
    CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        setting_key TEXT UNIQUE NOT NULL,
        setting_value TEXT NOT NULL
    )
');

$connect->exec("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES ('block_expired', '1')");

echo "Tabla settings creada.";
