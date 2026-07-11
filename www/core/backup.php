<?php
// core/backup.php - Respaldo seguro para SQLite en modo portable

// 1. Aseguramos que tenemos acceso a la configuración
if (!defined('DATA_PATH')) {
    require_once __DIR__ . '/config.php';
}

function farmacia_db_path() {
    return DATA_PATH . '/data/farmacia.db';
}

function farmacia_backup_dir() {
    return DATA_PATH . '/backups';
}

function ensure_backup_dir() {
    $backupDir = farmacia_backup_dir();
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true);
    }
    return $backupDir;
}

function sqlite_checkpoint_if_possible($connect) {
    if ($connect instanceof PDO) {
        try {
            $connect->exec('PRAGMA wal_checkpoint(TRUNCATE);');
        } catch (Exception $e) {
            error_log('[' . date('Y-m-d H:i:s') . '] No se pudo ejecutar WAL checkpoint: ' . $e->getMessage() . "\n", 3, DATA_PATH . '/logs/error.log');
        }
    }
}

function create_sqlite_backup($connect = null, $prefix = 'farmacia_backup') {
    $backupDir = ensure_backup_dir();
    $sourceDb = farmacia_db_path();
    $dateStamp = date('Ymd_His');
    $fileName = $prefix . '_' . $dateStamp . '.db';
    $backupDb = $backupDir . '/' . $fileName;

    if (!file_exists($sourceDb)) {
        return ['success' => false, 'message' => 'No se encontró la base de datos principal.', 'file' => null, 'path' => null];
    }

    sqlite_checkpoint_if_possible($connect);

    if (!copy($sourceDb, $backupDb)) {
        return ['success' => false, 'message' => 'Error al copiar la base de datos.', 'file' => null, 'path' => null];
    }

    try {
        $verify = new PDO('sqlite:' . $backupDb);
        $check = $verify->query('PRAGMA integrity_check')->fetchColumn();
        $verify = null;
        if ($check !== 'ok') {
            @unlink($backupDb);
            return ['success' => false, 'message' => 'El respaldo no pasó la verificación de integridad.', 'file' => null, 'path' => null];
        }
    } catch (Exception $e) {
        @unlink($backupDb);
        return ['success' => false, 'message' => 'No se pudo verificar el respaldo: ' . $e->getMessage(), 'file' => null, 'path' => null];
    }

    return ['success' => true, 'message' => 'Respaldo creado con éxito: ' . $fileName, 'file' => $fileName, 'path' => $backupDb];
}

function restore_sqlite_backup($backupFile, $connect = null) {
    $backupDir = ensure_backup_dir();
    $sourceBackup = $backupDir . '/' . basename($backupFile);
    $currentDb = farmacia_db_path();

    if (!file_exists($sourceBackup) || !file_exists($currentDb)) {
        return ['success' => false, 'message' => 'El archivo de respaldo o la base actual no existe.'];
    }

    try {
        $verify = new PDO('sqlite:' . $sourceBackup);
        $check = $verify->query('PRAGMA integrity_check')->fetchColumn();
        $verify = null;
        if ($check !== 'ok') {
            return ['success' => false, 'message' => 'El respaldo seleccionado está corrupto o incompleto.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'No se pudo abrir el respaldo: ' . $e->getMessage()];
    }

    sqlite_checkpoint_if_possible($connect);

    $preRestore = create_sqlite_backup($connect, 'farmacia_PRE_RESTORE');
    if (!$preRestore['success']) {
        return ['success' => false, 'message' => 'No se restauró porque falló el respaldo preventivo: ' . $preRestore['message']];
    }

    if (!copy($sourceBackup, $currentDb)) {
        return ['success' => false, 'message' => 'Error al restaurar la base de datos.'];
    }

    @unlink($currentDb . '-wal');
    @unlink($currentDb . '-shm');

    return ['success' => true, 'message' => 'Base de datos restaurada con éxito desde ' . basename($backupFile) . '. Respaldo preventivo: ' . $preRestore['file']];
}

function check_backup_reminder() {
    $backupDir = farmacia_backup_dir();
    if (!is_dir($backupDir)) {
        return true;
    }

    $latest = 0;
    foreach (glob($backupDir . '/farmacia_backup_*.db') ?: [] as $file) {
        $latest = max($latest, filemtime($file));
    }

    $days = (int) setting('backup_days', 7);
    return $latest === 0 || $latest < strtotime('-' . max(1, $days) . ' days');
}
