<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

// Obtener datos
$dbFile = DATA_PATH . '/data/farmacia.db';
$dbSize = file_exists($dbFile) ? round(filesize($dbFile) / 1024 / 1024, 2) . " MB" : "No encontrada";

$prodStmt = $connect->query("SELECT COUNT(*) FROM product");
$totalProducts = $prodStmt->fetchColumn();

$factStmt = $connect->query("SELECT COUNT(*) FROM orders");
$totalFacturas = $factStmt->fetchColumn();

$backupDir = DATA_PATH . '/backups';
$lastBackup = "Ninguno";
if (is_dir($backupDir)) {
    $files = scandir($backupDir);
    $backups = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'db') {
            $backups[] = filemtime($backupDir . '/' . $file);
        }
    }
    if (!empty($backups)) {
        rsort($backups);
        $lastBackup = date("d/m/Y H:i:s", $backups[0]);
    }
}

$diskFree = round(disk_free_space(DATA_PATH) / 1024 / 1024 / 1024, 2) . " GB";
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Información del Sistema</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Información</li>
                </ol>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>Versión del Software</th>
                                    <td><?php echo SOFTWARE_VERSION; ?> (Estable)</td>
                                </tr>
                                <tr>
                                    <th>Motor de Base de Datos</th>
                                    <td>SQLite 3</td>
                                </tr>
                                <tr>
                                    <th>Tamaño de la Base de Datos</th>
                                    <td><?php echo $dbSize; ?></td>
                                </tr>
                                <tr>
                                    <th>Total de Productos Registrados</th>
                                    <td><?php echo number_format($totalProducts); ?></td>
                                </tr>
                                <tr>
                                    <th>Total de Facturas Generadas</th>
                                    <td><?php echo number_format($totalFacturas); ?></td>
                                </tr>
                                <tr>
                                    <th>Último Respaldo Automático</th>
                                    <td><?php echo $lastBackup; ?></td>
                                </tr>
                                <tr>
                                    <th>Espacio Libre en Disco</th>
                                    <td><?php echo $diskFree; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
