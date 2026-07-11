<?php
require_once './constant/layout/head.php';

// Obtener configuración excepto schema_version y installation_id
$exportData = [];
foreach ($_SESSION['settings'] as $key => $val) {
    if ($key == 'schema_version' || $key == 'installation_id') {
        continue;
    }
    $exportData[$key] = $val;
}

$json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$filename = 'config_' . date('Ymd_His') . '.json';

log_system_event($connect, $_SESSION['userName'] ?? 'ADMIN', 'CONFIGURACION', 'EXPORTAR', 'Se ha exportado config.json');

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($json));
echo $json;
exit;
