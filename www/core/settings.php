<?php
// core/settings.php - Gestión de caché y helper de configuración

function load_settings($connect, $force = false) {
    if ($force || !isset($_SESSION['settings'])) {
        try {
            $stmt = $connect->query("SELECT key, value FROM settings");
            if ($stmt) {
                $_SESSION['settings'] = [];
                while ($row = $stmt->fetch()) {
                    $_SESSION['settings'][$row['key']] = $row['value'];
                }
            }
        } catch (PDOException $e) {
            // Tabla settings podría no existir aún si la migración no ha corrido (primera vez absoluta)
            error_log("[" . date('Y-m-d H:i:s') . "] Settings no cargados: " . $e->getMessage() . "\n", 3, DATA_PATH . "/logs/error.log");
        }
    }
}

function setting($key, $default = null) {
    if (isset($_SESSION['settings']) && isset($_SESSION['settings'][$key])) {
        return $_SESSION['settings'][$key];
    }
    return $default;
}
