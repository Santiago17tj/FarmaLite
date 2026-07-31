<?php
require_once 'constant/check.php'; include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>

<?php include('./constant/layout/sidebar.php'); ?>


<?php include('./constant/connect.php');
$user = $_SESSION['userId'];
$sql = "SELECT  uno, orderDate, clientName, clientContact, paymentStatus, id, delete_status FROM orders ORDER BY id DESC";
$result = $connect->query($sql);

?>
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary"> Gestionar Facturas</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active">Gestionar Facturas</li>
            </ol>
        </div>
    </div>


    <div class="container-fluid">

        <div class="card">
            <div class="card-body">

                <a href="add-order.php"><button class="btn btn-primary"><i class="fa fa-plus"></i> Generar Factura</button></a>

                <div class="table-responsive m-t-40">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>No. Factura</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            foreach ($result as $row) {
                                $no += 1;
                                $isVoided = ($row['delete_status'] == 2);
                            ?>
                                <tr <?php if ($isVoided) echo 'style="opacity:0.5;"'; ?>>
                                    <td class="text-center"><?= $no; ?></td>
                                    <td><?php echo $row['uno'] ?></td>
                                    <td><?php echo $row['orderDate'] ?></td>
                                    <td><?php echo $row['clientName'] ?></td>
                                    <td>
                                    <?php if ($isVoided) { ?>
                                        <label class='label label-default'><h4>Anulada</h4></label>
                                    <?php } else if ($row['paymentStatus'] == 1) { ?>
                                        <label class='label label-success'><h4>Pago Completo</h4></label>
                                    <?php } else if ($row['paymentStatus'] == 2) { ?>
                                        <label class='label label-danger'><h4>Pago Parcial</h4></label>
                                    <?php } else { ?>
                                        <label class='label label-warning'><h4>Pago Pendiente</h4></label>
                                    <?php } ?>
                                    </td>
                                    <td>
                                    <?php if (!$isVoided) { ?>
                                        <a href="invoiceprint.php?id=<?php echo $row['id'] ?>&print=1" target="_blank"><button type="button" class="btn btn-xs btn-success" title="Imprimir"><i class="fa fa-print"></i> Imprimir</button></a>

                                        <button type="button" class="btn btn-xs btn-warning" title="Anular Factura" onclick="voidOrder(<?php echo $row['id']; ?>, '<?php echo $row['uno']; ?>')"><i class="fa fa-ban"></i> Anular</button>

                                        <a href="php_action/removeOrder.php?id=<?php echo $row['id'] ?>"><button type="button" class="btn btn-xs btn-danger" onclick="return confirm('¿Deseas ELIMINAR este registro permanentemente?')" title="Eliminar"><i class="fa fa-trash"></i></button></a>
                                    <?php } else { ?>
                                        <span class="text-muted"><i class="fa fa-ban"></i> Factura anulada</span>
                                    <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <?php include('./constant/layout/footer.php'); ?>

<script>
function voidOrder(orderId, invoiceNum) {
    if (!confirm('¿Estás seguro de ANULAR la factura ' + invoiceNum + '?\n\nEsto devolverá los productos al inventario y la factura quedará marcada como anulada.\n\nEsta acción NO se puede deshacer.')) {
        return;
    }
    $.ajax({
        url: 'php_action/voidOrder.php?id=' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('✅ ' + response.message);
                location.reload();
            } else {
                alert('❌ Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error de conexión. Intenta de nuevo.');
        }
    });
}
</script>