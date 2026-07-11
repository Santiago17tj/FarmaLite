<?php
require_once 'constant/layout/head.php';

$msg = "";
$msgType = "";

$backupDir = DATA_PATH . '/backups';
$currentDb = DATA_PATH . '/data/farmacia.db';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

if (isset($_POST['btn_restaurar'])) {
    $result = restore_sqlite_backup($_POST['backup_file'], $connect);
    $msg = $result['message'];
    $msgType = $result['success'] ? 'success' : 'danger';
}

// Obtener lista de respaldos
$backups = [];
if (is_dir($backupDir)) {
    $files = scandir($backupDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'db') {
            $backups[] = [
                'name' => $file,
                'size' => filesize($backupDir . '/' . $file),
                'time' => filemtime($backupDir . '/' . $file)
            ];
        }
    }
    // Ordenar de más reciente a más antiguo
    usort($backups, function($a, $b) {
        return $b['time'] - $a['time'];
    });
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Restaurar Respaldo</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Restaurar</li>
                </ol>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-danger"><i class="fa fa-warning"></i> Advertencia</h4>
                        <p class="text-muted">Restaurar un respaldo reemplazará toda la información actual del sistema con la del archivo seleccionado. El sistema realizará una copia de seguridad automática antes de sobrescribir, por si acaso.</p>
                        
                        <?php if($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Seleccione el archivo a restaurar</label>
                                <select name="backup_file" class="form-control form-control-lg" required>
                                    <option value="">~~ Seleccione ~~</option>
                                    <?php foreach($backups as $bk): ?>
                                        <option value="<?php echo htmlspecialchars($bk['name']); ?>">
                                            <?php echo htmlspecialchars($bk['name']); ?> 
                                            (<?php echo date('d/m/Y H:i', $bk['time']); ?>) - 
                                            <?php echo number_format($bk['size'] / 1024, 2); ?> KB
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="btn_restaurar" class="btn btn-danger btn-lg btn-block mt-4" onclick="return confirm('¿Está absolutamente seguro de querer SOBRESCRIBIR la base de datos actual con este respaldo?')"><i class="fa fa-history"></i> Restaurar Ahora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>

