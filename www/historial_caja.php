<?php
require_once 'constant/layout/head.php';
require_once 'constant/connect.php';

$stmt = $connect->query("
    SELECT c.*, u.username 
    FROM cash_register_log c
    LEFT JOIN users u ON c.user_id = u.user_id
    ORDER BY c.id DESC
");
$cierres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Historial de Cierres de Caja</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Historial</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-40">
                    <table class="table table-bordered table-striped" id="cajaTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Apertura</th>
                                <th>Cierre</th>
                                <th>Base Inicial</th>
                                <th>Total Sistema</th>
                                <th>Diferencia</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cierres as $row): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['opening_time']; ?></td>
                                <td><?php echo $row['closing_time'] ?: '-'; ?></td>
                                <td>$<?php echo number_format($row['opening_balance'], 2); ?></td>
                                <td>$<?php echo number_format($row['system_total'], 2); ?></td>
                                <td>
                                    <?php 
                                    if ($row['status'] == 'OPEN') {
                                        echo "-";
                                    } else {
                                        if ($row['difference'] < 0) {
                                            echo "<span class='text-danger'>-$".number_format(abs($row['difference']), 2)."</span>";
                                        } else if ($row['difference'] > 0) {
                                            echo "<span class='text-success'>+$".number_format($row['difference'], 2)."</span>";
                                        } else {
                                            echo "<span class='text-muted'>$0.00</span>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'OPEN'): ?>
                                        <span class="badge badge-success">ABIERTA</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" title="<?php echo htmlspecialchars($row['notes']); ?>">CERRADA</span>
                                    <?php endif; ?>
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
    $('#cajaTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        "order": [[0, "desc"]]
    });
</script>
