<?php
require_once './constant/layout/head.php';

$msg = "";
$error = "";

// 1. Manejo del POST (Guardar configuraciones)
if (isset($_POST['save_settings'])) {
    try {
        $connect->beginTransaction();
        
        $fields = [
            'business_name', 'nit', 'address', 'phone', 'email', 
            'currency', 'printer_width', 'backup_days', 'low_stock_threshold'
        ];
        
        $stmtUpdate = $connect->prepare("UPDATE settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE key = ?");
        $stmtInsert = $connect->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $val = $_POST[$field];
                $stmtUpdate->execute([$val, $field]);
                if ($stmtUpdate->rowCount() == 0) {
                    // Si por alguna razón la key no existía
                    $stmtInsert->execute([$field, $val]);
                }
                $_SESSION['settings'][$field] = $val; // Actualizar caché
            }
        }
        
        // Manejo del Logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $newLogoName = 'logo_' . time() . '.' . $ext;
            
            // 1. Guardar permanentemente en ProgramData
            $uploadPath = DATA_PATH . '/uploads/Logo/' . $newLogoName;
            
            // 2. Carpeta pública (runtime) en www
            $runtimeDir = ROOT_PATH . '/assets/runtime';
            if (!is_dir($runtimeDir)) @mkdir($runtimeDir, 0777, true);
            $runtimePath = $runtimeDir . '/logo.png';
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                // Sincronizar con la carpeta pública para el navegador
                copy($uploadPath, $runtimePath);
                
                $stmtUpdate->execute([$newLogoName, 'logo']);
                $_SESSION['settings']['logo'] = $newLogoName;
            } else {
                $error = "Error al subir el logo.";
            }
        }
        
        $connect->commit();
        log_system_event($connect, $_SESSION['userName'] ?? 'ADMIN', 'CONFIGURACION', 'ACTUALIZAR', 'Configuracion actualizada manualmente');
        
        if (!$error) $msg = "Configuración actualizada con éxito.";
        
    } catch (PDOException $e) {
        $connect->rollBack();
        $error = "Error al guardar la configuración: " . $e->getMessage();
    }
}

// 2. Manejo de Importación de JSON
if (isset($_POST['import_settings'])) {
    if (isset($_FILES['config_json']) && $_FILES['config_json']['error'] == 0) {
        $json = file_get_contents($_FILES['config_json']['tmp_name']);
        $data = json_decode($json, true);
        
        if ($data && is_array($data)) {
            try {
                $connect->beginTransaction();
                $stmtUpdate = $connect->prepare("UPDATE settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE key = ?");
                
                foreach ($data as $key => $val) {
                    // Evitar sobreescribir schema_version o id de instalacion con configuraciones antiguas
                    if ($key == 'schema_version' || $key == 'installation_id') continue;
                    
                    $stmtUpdate->execute([$val, $key]);
                    $_SESSION['settings'][$key] = $val;
                }
                $connect->commit();
                log_system_event($connect, $_SESSION['userName'] ?? 'ADMIN', 'CONFIGURACION', 'IMPORTAR', 'Configuración importada desde JSON');
                $msg = "Configuración importada exitosamente.";
            } catch (PDOException $e) {
                $connect->rollBack();
                $error = "Error importando: " . $e->getMessage();
            }
        } else {
            $error = "Archivo JSON inválido.";
        }
    }
}

// Cargar los valores actuales del caché de sesión
$settings = $_SESSION['settings'] ?? [];
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Configuración Global (FarmaLite)</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </div>
        </div>

        <?php if($msg): ?>
            <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger"><i class="fa fa-times"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-b-0">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs customtab" role="tablist">
                            <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#empresa" role="tab"><span class="hidden-sm-up"><i class="fa fa-building"></i></span> <span class="hidden-xs-down">Empresa</span></a> </li>
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#caja" role="tab"><span class="hidden-sm-up"><i class="fa fa-print"></i></span> <span class="hidden-xs-down">Caja e Impresión</span></a> </li>
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#sistema" role="tab"><span class="hidden-sm-up"><i class="fa fa-cogs"></i></span> <span class="hidden-xs-down">Sistema</span></a> </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Tab Empresa -->
                            <div class="tab-pane active" id="empresa" role="tabpanel">
                                <div class="p-20">
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nombre del Negocio (FarmaLite)</label>
                                                <input type="text" class="form-control" name="business_name" value="<?php echo htmlspecialchars($settings['business_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>NIT / Identificación Fiscal</label>
                                                <input type="text" class="form-control" name="nit" value="<?php echo htmlspecialchars($settings['nit'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label>Dirección</label>
                                                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($settings['address'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Teléfono</label>
                                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Logo del Ticket (.png)</label>
                                                <input type="file" class="form-control" name="logo" accept="image/png">
                                                <small class="text-muted">Logo actual: <?php echo htmlspecialchars($settings['logo'] ?? ''); ?></small>
                                            </div>
                                        </div>
                                        <hr>
                                        <button type="submit" name="save_settings" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Configuración</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Tab Caja e Impresión -->
                            <div class="tab-pane p-20" id="caja" role="tabpanel">
                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Símbolo de Moneda</label>
                                            <input type="text" class="form-control" name="currency" value="<?php echo htmlspecialchars($settings['currency'] ?? '$'); ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Ancho Impresora Térmica</label>
                                            <select class="form-control" name="printer_width">
                                                <option value="58" <?php echo ($settings['printer_width'] ?? '') == '58' ? 'selected' : ''; ?>>58 mm (Pequeña)</option>
                                                <option value="80" <?php echo ($settings['printer_width'] ?? '') == '80' ? 'selected' : ''; ?>>80 mm (Estándar)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Umbral de Stock Bajo (Alertas)</label>
                                            <input type="number" class="form-control" name="low_stock_threshold" value="<?php echo htmlspecialchars($settings['low_stock_threshold'] ?? '5'); ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Recordatorio de Respaldo (Días)</label>
                                            <input type="number" class="form-control" name="backup_days" value="<?php echo htmlspecialchars($settings['backup_days'] ?? '7'); ?>" required>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" name="save_settings" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Reglas de Caja</button>
                                </form>
                            </div>

                            <!-- Tab Sistema -->
                            <div class="tab-pane p-20" id="sistema" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Información de Licencia</h4>
                                        <ul class="list-group mb-4">
                                            <li class="list-group-item"><strong>Software Versión:</strong> <?php echo htmlspecialchars($software_version); ?> LTS</li>
                                            <li class="list-group-item"><strong>Esquema BD Versión:</strong> <?php echo htmlspecialchars($settings['schema_version'] ?? '0'); ?></li>
                                            <li class="list-group-item"><strong>ID Instalación:</strong> <?php echo htmlspecialchars($settings['installation_id'] ?? '-'); ?></li>
                                        </ul>
                                        
                                        <h4>Exportar Configuración</h4>
                                        <p class="text-muted">Guarda tus parámetros (Nombre, NIT, Moneda) en un archivo JSON seguro.</p>
                                        <a href="export_config.php" class="btn btn-outline-info"><i class="fa fa-download"></i> Descargar config.json</a>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Importar Configuración</h4>
                                        <p class="text-muted">Si estás migrando a otro PC, sube aquí tu archivo config.json.</p>
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <input type="file" class="form-control" name="config_json" accept=".json" required>
                                            </div>
                                            <button type="submit" name="import_settings" class="btn btn-outline-warning"><i class="fa fa-upload"></i> Importar Datos</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
