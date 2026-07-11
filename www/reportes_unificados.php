<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// SQL base for date filtering
$dateCondition = "orderDate >= '$startDate' AND orderDate <= '$endDate'";

// KPI 1: Facturas y Total Vendido
$kpi1Stmt = $connect->query("SELECT COUNT(id) as facturas, SUM(grandTotalValue) as total_vendido FROM orders WHERE $dateCondition AND delete_status = 0");
$kpi1 = $kpi1Stmt->fetch(PDO::FETCH_ASSOC);
$totalVendido = $kpi1['total_vendido'] ?: 0;
$facturas = $kpi1['facturas'] ?: 0;
$ticketPromedio = $facturas > 0 ? $totalVendido / $facturas : 0;

// KPI 2: Costo y Ganancia
$kpi2Stmt = $connect->query("
    SELECT SUM(oi.quantity * p.purchase_price) as costo_total
    FROM order_item oi
    JOIN orders o ON oi.lastid = o.id
    JOIN product p ON oi.productName = p.product_id
    WHERE o.orderDate >= '$startDate' AND o.orderDate <= '$endDate' AND o.delete_status = 0
");
$costoTotal = $kpi2Stmt->fetchColumn() ?: 0;
$gananciaBruta = $totalVendido - $costoTotal;

// KPI 3: Producto más y menos vendido
$prodStmt = $connect->query("
    SELECT p.product_name, SUM(oi.quantity) as qty
    FROM order_item oi
    JOIN orders o ON oi.lastid = o.id
    JOIN product p ON oi.productName = p.product_id
    WHERE o.orderDate >= '$startDate' AND o.orderDate <= '$endDate' AND o.delete_status = 0
    GROUP BY p.product_id
    ORDER BY qty DESC
");
$productos = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
$prodMasVendido = count($productos) > 0 ? $productos[0]['product_name'] . " (" . $productos[0]['qty'] . ")" : "-";
$prodMenosVendido = count($productos) > 0 ? $productos[count($productos)-1]['product_name'] . " (" . $productos[count($productos)-1]['qty'] . ")" : "-";

// KPI 4: Hora Pico
$horaStmt = $connect->query("
    SELECT strftime('%H', created_at) as hora, COUNT(id) as ordenes
    FROM orders
    WHERE $dateCondition AND delete_status = 0 AND created_at IS NOT NULL
    GROUP BY hora
    ORDER BY ordenes DESC
    LIMIT 1
");
$horaRow = $horaStmt->fetch(PDO::FETCH_ASSOC);
$horaPico = $horaRow ? $horaRow['hora'] . ":00 (" . $horaRow['ordenes'] . " facturas)" : "-";

// Detalle de Facturas para la tabla
$facturasStmt = $connect->query("
    SELECT id, orderDate, clientName, exact_payment_type, grandTotalValue, created_at 
    FROM orders 
    WHERE $dateCondition AND delete_status = 0 
    ORDER BY id DESC
");
$facturasList = $facturasStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Dashboard y Reportes</h3>
                <ol class="breadcrumb" style="background: transparent; padding: 0; margin-top: 5px;">
                    <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary);">Inicio</a></li>
                    <li class="breadcrumb-item active">Reportes Unificados</li>
                </ol>
            </div>
            <div class="col-md-7 align-self-center">
                <form method="GET" class="form-inline float-right">
                    <div class="form-group mr-2">
                        <label class="mr-2">Desde:</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $startDate; ?>">
                    </div>
                    <div class="form-group mr-2">
                        <label class="mr-2">Hasta:</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo $endDate; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filtrar</button>
                    <a href="reportes_unificados.php" class="btn btn-secondary ml-2">Hoy</a>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Total Vendido</h5>
                    <h2 class="text-primary">$<?php echo number_format($totalVendido, 2); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Ganancia Bruta</h5>
                    <h2 class="text-success">$<?php echo number_format($gananciaBruta, 2); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Facturas</h5>
                    <h2 class="text-info"><?php echo $facturas; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Ticket Promedio</h5>
                    <h2 class="text-warning">$<?php echo number_format($ticketPromedio, 2); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Prod. Más Vendido</h5>
                    <h4 class="text-dark"><?php echo htmlspecialchars($prodMasVendido); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Prod. Menos Vendido</h5>
                    <h4 class="text-dark"><?php echo htmlspecialchars($prodMenosVendido); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h5 class="text-muted">Hora Pico</h5>
                    <h4 class="text-dark"><?php echo htmlspecialchars($horaPico); ?></h4>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detalle de Ventas</h4>
                <div class="table-responsive m-t-40">
                    <table class="table table-bordered table-striped" id="reportesTable">
                        <thead>
                            <tr>
                                <th>Factura #</th>
                                <th>Fecha (Sist)</th>
                                <th>Cliente</th>
                                <th>Medio de Pago</th>
                                <th>Total</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($facturasList as $row): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['created_at'] ?: $row['orderDate']; ?></td>
                                <td><?php echo htmlspecialchars($row['clientName']); ?></td>
                                <td><?php echo htmlspecialchars($row['exact_payment_type']); ?></td>
                                <td>$<?php echo number_format($row['grandTotalValue'], 2); ?></td>
                                <td>
                                    <a href="php_action/printOrder.php?orderId=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-info" title="Reimprimir">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
<script>
    $('#reportesTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        "order": [[0, "desc"]]
    });
</script>
