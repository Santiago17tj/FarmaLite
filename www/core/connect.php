<?php
// core/connect.php - Punto de entrada principal (Core)

// 1. Iniciar sesión si no está iniciada (necesario para caché de settings)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cargar Configuración Global
require_once __DIR__ . '/config.php';

// 3. Cargar versión del software (ahora manejado en config.php y version.php legacy si existe)
if (file_exists(CORE_PATH . '/version.php')) {
    require_once CORE_PATH . '/version.php';
}

// 4. Iniciar Conexión a Base de Datos (PDO)
try {
    $dbPath = DATA_PATH . '/data/farmacia.db';
    
    // Si la BD no existe en DATA_PATH, intentamos copiarla desde la plantilla original (modo primera ejecución)
    if (!file_exists($dbPath) && file_exists(ROOT_PATH . '/database/farmacia_template.db')) {
        copy(ROOT_PATH . '/database/farmacia_template.db', $dbPath);
    }
    
    $connect = new PDO("sqlite:" . $dbPath);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Habilitar claves foráneas
    $connect->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    // Registrar error en log físico
    error_log("[" . date('Y-m-d H:i:s') . "] Error de BD: " . $e->getMessage() . "\n", 3, DATA_PATH . "/logs/error.log");
    die("Error de conexión a la base de datos.");
}

// 5. Cargar Funciones Core
require_once CORE_PATH . '/settings.php';
require_once CORE_PATH . '/migration.php';
require_once CORE_PATH . '/audit.php';
require_once CORE_PATH . '/backup.php';

// 6. Ejecutar migraciones automáticas al iniciar
run_migrations_if_needed($connect);

// 7. Cargar configuraciones en caché
load_settings($connect);
