<?php
require_once 'constant/check.php'; include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<?php
require_once('./constant/connect.php');
require_once('./php_action/salesReportHelper.php');

$period = isset($_GET['period']) ? $_GET['period'] : 'day';
if (!in_array($period, array('day', 'week', 'month'), true)) {
	$period = 'day';
}

$periodInfo = salesReportGetPeriod($period);
$summary = salesReportFetchSummary($connect, $periodInfo['start'], $periodInfo['end']);
?>

<div class="page-wrapper">
	<div class="row page-titles">
		<div class="col-md-5 align-self-center">
			<h3 class="text-primary">Reporte de Ventas</h3>
		</div>
		<div class="col-md-7 align-self-center">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
				<li class="breadcrumb-item active">Reporte de Ventas</li>
			</ol>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row mb-3">
			<div class="col-12">
				<div class="btn-group" role="group">
					<a href="sales_report.php?period=day" class="btn btn-<?php echo $period === 'day' ? 'primary' : 'outline-primary'; ?>">
						<i class="fa fa-calendar"></i> Hoy
					</a>
					<a href="sales_report.php?period=week" class="btn btn-<?php echo $period === 'week' ? 'primary' : 'outline-primary'; ?>">
						<i class="fa fa-calendar-check-o"></i> Semanal (7 días)
					</a>
					<a href="sales_report.php?period=month" class="btn btn-<?php echo $period === 'month' ? 'primary' : 'outline-primary'; ?>">
						<i class="fa fa-calendar-o"></i> Mensual
					</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12 mb-3">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-primary mb-1"><?php echo htmlspecialchars($periodInfo['title']); ?></h4>
						<p class="text-muted mb-0"><?php echo htmlspecialchars($periodInfo['subtitle']); ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Total de ingresos</h6>
								<h2 class="text-success mb-0"><?php echo salesReportFormatMoney($summary['total_income']); ?></h2>
							</div>
							<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
								<i class="fa fa-money fa-lg"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Facturas realizadas</h6>
								<h2 class="text-primary mb-0"><?php echo (int) $summary['total_invoices']; ?></h2>
							</div>
							<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
								<i class="fa fa-file-text-o fa-lg"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">Productos más vendidos</strong>
					</div>
					<div class="card-body">
						<?php if (count($summary['top_products']) > 0): ?>
							<div class="table-responsive">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th class="text-center" style="width:60px;">#</th>
											<th>Medicina</th>
											<th class="text-center">Unidades vendidas</th>
											<th class="text-right">Ingresos</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$rank = 0;
										foreach ($summary['top_products'] as $product):
											$rank++;
										?>
											<tr>
												<td class="text-center"><?php echo $rank; ?></td>
												<td><?php echo htmlspecialchars($product['product_name']); ?></td>
												<td class="text-center"><?php echo (int) $product['units_sold']; ?></td>
												<td class="text-right"><?php echo salesReportFormatMoney($product['revenue']); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php else: ?>
							<div class="alert alert-info mb-0">
								No hay ventas registradas en este periodo.
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<strong class="card-title mb-0">Reporte detallado por fechas (imprimir)</strong>
					</div>
					<div class="card-body">
						<p class="text-muted">Opción anterior: elige un rango personalizado y genera un listado imprimible.</p>
						<form class="form-horizontal" action="php_action/getOrderReport.php" method="post" id="getOrderReportForm">
							<div class="form-group row">
								<label class="col-sm-2 control-label">Fecha inicio</label>
								<div class="col-sm-4">
									<input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo htmlspecialchars($periodInfo['start']); ?>" />
								</div>
								<label class="col-sm-2 control-label">Fecha fin</label>
								<div class="col-sm-4">
									<input type="date" class="form-control" id="endDate" name="endDate" value="<?php echo htmlspecialchars($periodInfo['end']); ?>" />
								</div>
							</div>
							<button type="submit" class="btn btn-secondary btn-flat">
								<i class="fa fa-print"></i> Generar e imprimir detalle
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$("#getOrderReportForm").on('submit', function(e) {
		e.preventDefault();
		var startDate = $("#startDate").val();
		var endDate = $("#endDate").val();
		if (!startDate || !endDate) {
			alert('Selecciona fecha de inicio y fin.');
			return false;
		}
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			success: function(response) {
				var w = window.open('', 'ReporteVentas', 'height=600,width=800');
				w.document.write('<html><head><title>Reporte de Ventas - LA FORMULA</title></head><body>');
				w.document.write(response);
				w.document.write('</body></html>');
				w.document.close();
				w.focus();
				w.print();
			}
		});
	});
});
</script>

<?php include('./constant/layout/footer.php'); ?>
