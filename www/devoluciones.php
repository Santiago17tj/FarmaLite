<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$msg = "";
$msgType = "";

// Procesar Devolución
if (isset($_POST['btn_devolver'])) {
    $orderId = (int)$_POST['order_id'];
    $productId = (int)$_POST['product_id'];
    $returnQty = (int)$_POST['return_qty'];
    $reason = trim($_POST['reason']);
    $createdAt = date('Y-m-d H:i:s');

    if ($returnQty > 0) {
        try {
            $connect->beginTransaction();
            
            // 1. Regresar al stock
            $updateStock = $connect->prepare("UPDATE product SET quantity = quantity + ? WHERE product_id = ?");
            $updateStock->execute([$returnQty, $productId]);
            
            // Obtener el nuevo saldo
            $balanceStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
            $balanceStmt->execute([$productId]);
            $balance = $balanceStmt->fetchColumn();

            // 2. Registrar movimiento
            $movStmt = $connect->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, date, reference, balance) VALUES (?, 'RETURN', ?, ?, ?, ?)");
            $movStmt->execute([$productId, $returnQty, $createdAt, "Devolución Fact. #$orderId - $reason", $balance]);
            
            $connect->commit();
            $msg = "Devolución procesada. El stock ha sido devuelto al inventario.";
            $msgType = "success";
        } catch (Exception $e) {
            $connect->rollBack();
            $msg = "Error al procesar devolución: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Cargar Factura y Productos
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$orderItems = [];

if ($orderId > 0) {
    $stmt = $connect->prepare("
        SELECT oi.*, p.product_name 
        FROM order_item oi
        JOIN product p ON oi.productName = p.product_id
        WHERE oi.lastid = ?
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($orderItems) === 0) {
        $msg = "Factura no encontrada o sin productos.";
        $msgType = "danger";
    }
}
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Devoluciones</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Devoluciones</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if($msg): ?>
                            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
                        <?php endif; ?>
                        
                        <form method="GET" action="" class="form-inline mb-4">
                            <label class="mr-2">Número de Factura:</label>
                            <input type="number" name="order_id" class="form-control mr-2" value="<?php echo $orderId > 0 ? $orderId : ''; ?>" required>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar Factura</button>
                        </form>

                        <?php if (count($orderItems) > 0): ?>
                        <hr>
                        <h4>Productos en Factura #<?php echo $orderId; ?></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Vendida</th>
                                        <th>Precio</th>
                                        <th>Total Línea</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orderItems as $item): ?>
                                    <tr>
                                        <form method="POST" action="">
                                            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $item['productName']; ?>">
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['rate'], 2); ?></td>
                                            <td>$<?php echo number_format($item['total'], 2); ?></td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="return_qty" class="form-control" max="<?php echo $item['quantity']; ?>" min="1" placeholder="Cant" required>
                                                    <input type="text" name="reason" class="form-control" placeholder="Motivo" required>
                                                    <div class="input-group-append">
                                                        <button type="submit" name="btn_devolver" class="btn btn-danger" onclick="return confirm('¿Confirma la devolución y reintegro al inventario?')">Devolver</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </form>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('./constant/layout/footer.php');?>
