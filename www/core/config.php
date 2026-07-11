<?php
// core/config.php - Configuración global y rutas de la aplicación

// 1. Ruta base del código fuente (C:\Program Files\FarmaLite\www)
define('ROOT_PATH', dirname(__DIR__));
define('CORE_PATH', __DIR__);

// 2. Ruta base de datos del usuario (C:\ProgramData\FarmaLite)
$programData = getenv('PROGRAMDATA');
if ($programData) {
    define('DATA_PATH', $programData . '/FarmaLite');
} else {
    // Modo portable / fallback
    define('DATA_PATH', ROOT_PATH . '/appdata');
}

// 3. Crear estructura de carpetas si no existe
$folders = ['data', 'backups', 'uploads', 'uploads/Logo', 'logs', 'cache', 'temp'];
foreach ($folders as $folder) {
    $path = DATA_PATH . '/' . $folder;
    if (!is_dir($path)) {
        @mkdir($path, 0777, true);
    }
}

// 4. Leer version.json
$versionFile = ROOT_PATH . '/config/version.json';
if (file_exists($versionFile)) {
    $configData = json_decode(file_get_contents($versionFile), true);
    define('SOFTWARE_VERSION', isset($configData['software_version']) ? $configData['software_version'] : '1.2.0');
} else {
    define('SOFTWARE_VERSION', '1.2.0');
}
