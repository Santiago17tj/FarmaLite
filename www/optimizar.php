<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$msg = "";
$msgType = "";

if (isset($_POST['btn_optimizar'])) {
    try {
        $start = microtime(true);
        $connect->exec("VACUUM");
        $connect->exec("ANALYZE");
        $end = microtime(true);
        
        $time = round($end - $start, 4);
        $msg = "Base de datos optimizada correctamente en {$time} segundos.";
        $msgType = "success";
    } catch (Exception $e) {
        $msg = "Error al optimizar: " . $e->getMessage();
        $msgType = "danger";
    }
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Mantenimiento SQLite</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Optimizar</li>
                </ol>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="card-title">Optimizar Base de Datos</h4>
                        <p class="text-muted">Desfragmenta la base de datos y actualiza las estadísticas de los índices para que las búsquedas sigan siendo ultra rápidas durante años. Se recomienda ejecutar esto una vez al mes.</p>
                        
                        <?php if($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <button type="submit" name="btn_optimizar" class="btn btn-warning btn-lg mt-4"><i class="fa fa-database"></i> Ejecutar Mantenimiento Ahora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
