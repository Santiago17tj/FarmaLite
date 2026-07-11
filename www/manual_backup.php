<?php
require_once 'constant/layout/head.php';

$backupResult = null;
$backupClass = 'info';
if (isset($_POST['run_backup'])) {
    $result = create_sqlite_backup($connect);
    $backupResult = $result['message'];
    $backupClass = $result['success'] ? 'success' : 'danger';
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Crear Respaldo Manual</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Herramientas</li>
                    <li class="breadcrumb-item active">Respaldo</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-body" style="padding: 50px;">
                        <h4 class="card-title">Copia de Seguridad de la Base de Datos</h4>
                        <p class="text-muted">Genera una copia verificada de los datos actuales (inventario, facturas, caja y configuración).</p>
                        
                        <?php if($backupResult): ?>
                            <div class="alert alert-<?php echo $backupClass; ?>">
                                <strong>Resultado:</strong> <?php echo htmlspecialchars($backupResult); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <button type="submit" name="run_backup" class="btn btn-primary btn-lg mt-3">
                                <i class="fa fa-database"></i> Iniciar Respaldo Ahora
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
