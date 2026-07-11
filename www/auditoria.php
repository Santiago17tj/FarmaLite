<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

// 1. Integridad de BD
$integrityQuery = $connect->query("PRAGMA integrity_check");
$integrityResult = $integrityQuery->fetchColumn();

// 2. Facturas Huérfanas (orders sin order_item, o order_item sin orders)
$orphanOrders = $connect->query("SELECT COUNT(*) FROM orders WHERE order_id NOT IN (SELECT lastid FROM order_item)")->fetchColumn();
$orphanItems = $connect->query("SELECT COUNT(*) FROM order_item WHERE lastid NOT IN (SELECT order_id FROM orders)")->fetchColumn();

// 3. Stock Negativo
$negativeStock = $connect->query("SELECT COUNT(*) FROM product WHERE quantity < 0 AND active = 1 AND status = 1")->fetchColumn();

// 4. Lotes Vencidos
$today = date('Y-m-d');
$expiredProducts = $connect->query("SELECT COUNT(*) FROM product WHERE expdate < '$today' AND active = 1 AND status = 1")->fetchColumn();

// 5. Códigos de barra vacíos o duplicados
$emptyBarcodes = $connect->query("SELECT COUNT(*) FROM product WHERE (barcode IS NULL OR barcode = '') AND active = 1 AND status = 1")->fetchColumn();
$dupBarcodes = $connect->query("
    SELECT COUNT(*) FROM (
        SELECT barcode FROM product 
        WHERE barcode IS NOT NULL AND barcode != '' AND active = 1 AND status = 1
        GROUP BY barcode HAVING COUNT(*) > 1
    )
")->fetchColumn();

?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Auditoría del Sistema</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Herramientas</li>
                    <li class="breadcrumb-item active">Auditoría</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Resultados de Integridad</h4>
                        <div class="table-responsive m-t-40">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Área Validada</th>
                                        <th>Estado</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Integridad de Base de Datos -->
                                    <tr>
                                        <td>Integridad de Base de Datos SQLite</td>
                                        <td>
                                            <?php if($integrityResult == 'ok'): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">ERROR</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $integrityResult; ?></td>
                                    </tr>
                                    
                                    <!-- Stock Negativo -->
                                    <tr>
                                        <td>Productos con Stock Negativo</td>
                                        <td>
                                            <?php if($negativeStock == 0): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">ERROR</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $negativeStock; ?> productos detectados</td>
                                    </tr>

                                    <!-- Productos Vencidos -->
                                    <tr>
                                        <td>Lotes Vencidos en Inventario</td>
                                        <td>
                                            <?php if($expiredProducts == 0): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">ALERTA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $expiredProducts; ?> lotes vencidos</td>
                                    </tr>

                                    <!-- Facturas Huérfanas -->
                                    <tr>
                                        <td>Consistencia de Facturas (Huérfanas)</td>
                                        <td>
                                            <?php if($orphanOrders == 0 && $orphanItems == 0): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">ERROR</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $orphanOrders; ?> facturas sin detalle, <?php echo $orphanItems; ?> detalles sin factura</td>
                                    </tr>
                                    
                                    <!-- Códigos de Barras Vacíos -->
                                    <tr>
                                        <td>Códigos de Barras Vacíos</td>
                                        <td>
                                            <?php if($emptyBarcodes == 0): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">ALERTA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $emptyBarcodes; ?> productos sin código</td>
                                    </tr>
                                    
                                    <!-- Códigos Duplicados -->
                                    <tr>
                                        <td>Códigos de Barras Duplicados</td>
                                        <td>
                                            <?php if($dupBarcodes == 0): ?>
                                                <span class="badge badge-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">ALERTA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $dupBarcodes; ?> códigos repetidos (exceptuando lotes intencionales)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php include('./constant/layout/footer.php');?>
