<?php
// Conexión PDO SQLite - Para scripts de php_action
require_once __DIR__ . '/../core/config.php';
$db_file = DATA_PATH . '/data/farmacia.db';
$store_url = "login.php";

try {
    $connect = new PDO("sqlite:" . $db_file);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $connect->exec("PRAGMA journal_mode=WAL;");
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}