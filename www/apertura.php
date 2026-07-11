<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$userId = $_SESSION['userId'];
$msg = "";
$msgType = "";

// Verificar si ya hay caja abierta
$checkOpen = $connect->query("SELECT id FROM cash_register_log WHERE status = 'OPEN'");
$hasOpen = $checkOpen->fetchColumn();

if ($hasOpen) {
    echo "<script>alert('Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.'); window.location.href='dashboard.php';</script>";
    exit;
}

if (isset($_POST['btn_abrir_caja'])) {
    $base = (float)$_POST['opening_balance'];
    $date = date("Y-m-d H:i:s");
    
    $stmt = $connect->prepare("INSERT INTO cash_register_log (user_id, opening_time, opening_balance, status) VALUES (?, ?, ?, 'OPEN')");
    if ($stmt->execute([$userId, $date, $base])) {
        echo "<script>alert('Caja abierta exitosamente.'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        $msg = "Error al abrir la caja.";
        $msgType = "danger";
    }
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Apertura de Caja</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Apertura de Caja</li>
                </ol>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Fondo Inicial de Caja</h4>
                        <p class="text-muted text-center">Ingrese el efectivo base con el que inicia el turno.</p>
                        
                        <?php if($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Base en Efectivo ($)</label>
                                <input type="number" class="form-control form-control-lg" name="opening_balance" placeholder="Ej: 200000" min="0" required>
                            </div>
                            <button type="submit" name="btn_abrir_caja" class="btn btn-primary btn-lg btn-block mt-4"><i class="fa fa-unlock"></i> Abrir Caja Ahora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
