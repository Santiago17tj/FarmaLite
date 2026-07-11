<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$userId = $_SESSION['userId'];
$msg = "";
$msgType = "";

// 1. Verificar si hay caja abierta
$openBoxStmt = $connect->query("SELECT * FROM cash_register_log WHERE status = 'OPEN' ORDER BY id DESC LIMIT 1");
$currentBox = $openBoxStmt->fetch(PDO::FETCH_ASSOC);

if (!$currentBox) {
    echo "<script>alert('No hay ninguna caja abierta en este momento.'); window.location.href='dashboard.php';</script>";
    exit;
}

// 2. Calcular los totales desde la apertura
$openingTime = $currentBox['opening_time'];
$totalsStmt = $connect->prepare("
    SELECT exact_payment_type, SUM(grandTotalValue) as total 
    FROM orders 
    WHERE created_at >= ? AND delete_status = 0
    GROUP BY exact_payment_type
");
$totalsStmt->execute([$openingTime]);
$totals = $totalsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$cash = isset($totals['Efectivo']) ? $totals['Efectivo'] : 0;
$card = isset($totals['Tarjeta']) ? $totals['Tarjeta'] : 0;
$transfer = isset($totals['Transferencia']) ? $totals['Transferencia'] : 0;
$other = isset($totals['Otros']) ? $totals['Otros'] : 0;

$systemTotal = $cash + $card + $transfer + $other;
$expectedCash = $currentBox['opening_balance'] + $cash; // Base + Efectivo en caja

// 3. Procesar el Cierre
if (isset($_POST['btn_cerrar_caja'])) {
    $countedCash = (float)$_POST['counted_cash'];
    $notes = trim($_POST['notes']);
    $closingTime = date("Y-m-d H:i:s");
    
    // Difference only applies to cash for now
    $difference = $countedCash - $expectedCash; 

    $updateStmt = $connect->prepare("
        UPDATE cash_register_log 
        SET closing_time = ?, system_total = ?, cash_total = ?, card_total = ?, transfer_total = ?, other_total = ?, difference = ?, status = 'CLOSED', notes = ?
        WHERE id = ?
    ");
    
    if ($updateStmt->execute([$closingTime, $systemTotal, $cash, $card, $transfer, $other, $difference, $notes, $currentBox['id']])) {
        // Ejecutar backup automático verificado antes de salir del turno.
        $backupResult = create_sqlite_backup($connect, 'farmacia_cierre_caja');
        if (!$backupResult['success']) {
            $msg = 'La caja se cerró, pero el respaldo automático falló: ' . $backupResult['message'];
            $msgType = 'warning';
        }

        echo "<script>alert('Caja cerrada con éxito. Diferencia: $".number_format($difference,2)."'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        $msg = "Error al cerrar la caja.";
        $msgType = "danger";
    }
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Cierre de Caja</h3>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Resumen del Turno</h4>
                        <p><strong>Apertura:</strong> <?php echo $currentBox['opening_time']; ?></p>
                        <p><strong>Base Inicial:</strong> $<?php echo number_format($currentBox['opening_balance'], 2); ?></p>
                        <hr>
                        
                        <div class="row text-center mb-3">
                            <div class="col-md-3"><h5>Efectivo</h5><h4 class="text-success">$<?php echo number_format($cash, 2); ?></h4></div>
                            <div class="col-md-3"><h5>Tarjeta</h5><h4 class="text-info">$<?php echo number_format($card, 2); ?></h4></div>
                            <div class="col-md-3"><h5>Transferencia</h5><h4 class="text-primary">$<?php echo number_format($transfer, 2); ?></h4></div>
                            <div class="col-md-3"><h5>Otros</h5><h4 class="text-muted">$<?php echo number_format($other, 2); ?></h4></div>
                        </div>
                        
                        <div class="alert alert-warning text-center">
                            <h4>Total Sistema: $<?php echo number_format($systemTotal, 2); ?></h4>
                            <h3>Efectivo Esperado en Caja: $<?php echo number_format($expectedCash, 2); ?></h3>
                        </div>

                        <?php if($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Efectivo Físico Contado ($)</label>
                                <input type="number" step="0.01" class="form-control form-control-lg" name="counted_cash" placeholder="Ingrese el efectivo real que hay en el cajón" required>
                                <small>La diferencia (sobrante/faltante) se calculará comparando con el Efectivo Esperado.</small>
                            </div>
                            <div class="form-group">
                                <label>Observaciones del Cierre (Opcional)</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Ej: Faltan $10.000 porque un cliente pagó incompleto..."></textarea>
                            </div>
                            <button type="submit" name="btn_cerrar_caja" class="btn btn-danger btn-lg btn-block mt-4" onclick="return confirm('¿Está seguro de cerrar la caja definitivamente? Se generará un backup automático.')"><i class="fa fa-lock"></i> Cerrar Caja Ahora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>

