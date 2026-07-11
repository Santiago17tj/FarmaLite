<?php
// core/migration.php - Motor de Migraciones Automáticas

function get_current_schema_version($connect) {
    try {
        $stmt = $connect->query("SELECT value FROM settings WHERE key = 'schema_version'");
        if ($stmt) {
            $row = $stmt->fetch();
            return $row ? (int)$row['value'] : 0;
        }
    } catch (PDOException $e) {
        // La tabla no existe
        return 0;
    }
    return 0;
}

function update_schema_version($connect, $version) {
    try {
        $stmt = $connect->prepare("UPDATE settings SET value = ? WHERE key = 'schema_version'");
        $stmt->execute([$version]);
        if ($stmt->rowCount() == 0) {
            // Insertar si no existía el key (poco probable si se corre 001, pero por seguridad)
            $ins = $connect->prepare("INSERT INTO settings (key, value) VALUES ('schema_version', ?)");
            $ins->execute([$version]);
        }
    } catch (PDOException $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Error actualizando schema_version: " . $e->getMessage() . "\n", 3, DATA_PATH . "/logs/migration.log");
    }
}

function run_migrations_if_needed($connect) {
    $current_version = get_current_schema_version($connect);
    $migrations_dir = ROOT_PATH . '/database/migrations/';
    
    if (!is_dir($migrations_dir)) return;
    
    $files = scandir($migrations_dir);
    $migrations_run = 0;
    
    foreach ($files as $file) {
        if (preg_match('/^(\d+)_.*\.sql$/', $file, $matches)) {
            $file_version = (int)$matches[1];
            
            if ($file_version > $current_version) {
                // Hay que ejecutar esta migración
                $sql = file_get_contents($migrations_dir . $file);
                
                try {
                    $connect->beginTransaction();
                    $connect->exec($sql);
                    $connect->commit();
                    
                    update_schema_version($connect, $file_version);
                    $current_version = $file_version;
                    $migrations_run++;
                    
                    error_log("[" . date('Y-m-d H:i:s') . "] Migración ejecutada con éxito: $file\n", 3, DATA_PATH . "/logs/migration.log");
                } catch (PDOException $e) {
                    if ($connect->inTransaction()) {
                        $connect->rollBack();
                    }
                    error_log("[" . date('Y-m-d H:i:s') . "] Error en migración $file: " . $e->getMessage() . "\n", 3, DATA_PATH . "/logs/migration.log");
                    // Detener ejecución si una migración falla para evitar inconsistencias
                    break; 
                }
            }
        }
    }
    
    // Si se ejecutó alguna migración y ya había sesión cargada, forzar recarga
    if ($migrations_run > 0 && isset($_SESSION['settings'])) {
        load_settings($connect, true);
    }
}
