<?php
// Script para crear farmacia.db a partir del SQL de SQLite
require_once __DIR__ . '/core/config.php';
$dbFile = DATA_PATH . '/data/farmacia.db';

// Si ya existe, borrarlo para crear uno limpio
if (file_exists($dbFile)) {
    unlink($dbFile);
}

$sqlFile = __DIR__ . '/farmacia_sqlite.sql';
$sql = file_get_contents($sqlFile);

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($sql);
    echo "farmacia.db creada exitosamente en: " . $dbFile . "\n";

    // Verificar las tablas
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    echo "\nTablas creadas:\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) as c FROM \"{$t['name']}\"")->fetch();
        echo "  - {$t['name']} ({$count['c']} registros)\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
