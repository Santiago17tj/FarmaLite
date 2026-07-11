<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$movements = [];
$productName = "";

// Obtener lista de productos para el filtro
$prodStmt = $connect->query("SELECT product_id, product_name FROM product ORDER BY product_name ASC");
$productList = $prodStmt->fetchAll(PDO::FETCH_ASSOC);

if ($productId > 0) {
    // Obtener nombre
    $nameStmt = $connect->prepare("SELECT product_name FROM product WHERE product_id = ?");
    $nameStmt->execute([$productId]);
    $productName = $nameStmt->fetchColumn();
    
    // Obtener Kardex
    $movStmt = $connect->prepare("
        SELECT * FROM inventory_movements 
        WHERE product_id = ?
        ORDER BY id ASC
    ");
    $movStmt->execute([$productId]);
    $movements = $movStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Kardex de Inventario</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Kardex</li>
                </ol>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="GET" action="" class="form-inline mb-4">
                    <label class="mr-2">Seleccione un Producto:</label>
                    <select name="product_id" class="form-control mr-2" required>
                        <option value="">~~ Buscar Producto ~~</option>
                        <?php foreach($productList as $p): ?>
                            <option value="<?php echo $p['product_id']; ?>" <?php echo ($p['product_id'] == $productId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['product_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Consultar</button>
                </form>

                <?php if ($productId > 0): ?>
                    <h4 class="card-title mt-4">Historial de Movimientos: <strong class="text-info"><?php echo htmlspecialchars($productName); ?></strong></h4>
                    <div class="table-responsive m-t-40">
                        <table class="table table-bordered table-striped" id="kardexTable">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo de Movimiento</th>
                                    <th>Cantidad</th>
                                    <th>Saldo Final</th>
                                    <th>Referencia / Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($movements as $row): ?>
                                    <tr>
                                        <td><?php echo $row['date']; ?></td>
                                        <td>
                                            <?php 
                                                $type = $row['movement_type'];
                                                if ($type == 'SALE') echo "<span class='badge badge-danger'>VENTA</span>";
                                                else if ($type == 'RETURN') echo "<span class='badge badge-info'>DEVOLUCIÓN</span>";
                                                else if ($type == 'ENTRY') echo "<span class='badge badge-success'>INGRESO</span>";
                                                else if ($type == 'ADJUSTMENT') echo "<span class='badge badge-warning'>AJUSTE</span>";
                                                else if ($type == 'EXPIRED') echo "<span class='badge badge-dark'>VENCIDO</span>";
                                                else if ($type == 'DAMAGED') echo "<span class='badge badge-secondary'>DAÑADO</span>";
                                                else echo $type;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($row['quantity'] > 0) {
                                                echo "<span class='text-success'>+{$row['quantity']}</span>";
                                            } else {
                                                echo "<span class='text-danger'>{$row['quantity']}</span>";
                                            }
                                            ?>
                                        </td>
                                        <td><strong><?php echo $row['balance']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['reference']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
<script>
    $('#kardexTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        "order": [[0, "asc"]]
    });
</script>
