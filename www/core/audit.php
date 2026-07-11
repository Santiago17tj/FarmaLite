<?php
// core/audit.php - Registro en System Log

function log_system_event($connect, $user, $module, $action, $details = '') {
    try {
        $stmt = $connect->prepare("INSERT INTO system_log (user, module, action, details) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user, $module, $action, $details]);
    } catch (PDOException $e) {
        // En caso de que falle (ej. BD bloqueada temporalmente), lo enviamos al log físico como plan B
        error_log("[" . date('Y-m-d H:i:s') . "] LOG FALLIDO: $user | $module | $action | $details | " . $e->getMessage() . "\n", 3, DATA_PATH . "/logs/error.log");
    }
}
