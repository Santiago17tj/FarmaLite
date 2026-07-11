<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<title>Factura <?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?></title>
	<style>
		* { box-sizing: border-box; margin: 0; padding: 0; }

		body {
			font-family: 'Courier New', Courier, monospace;
			font-size: 12px;
			line-height: 1.35;
			color: #000;
			background: #e8e8e8;
			padding: 12px 0;
		}

		.no-print {
			text-align: center;
			margin-bottom: 12px;
			font-family: 'Segoe UI', Arial, sans-serif;
		}

		.no-print button {
			background: #5c4ac7;
			color: #fff;
			border: none;
			padding: 10px 20px;
			font-size: 14px;
			border-radius: 4px;
			cursor: pointer;
			margin: 4px;
		}

		.no-print button.secondary { background: #666; }

		.no-print .hint {
			color: #555;
			font-size: 11px;
			margin-top: 8px;
			max-width: 58mm;
			margin-left: auto;
			margin-right: auto;
		}

		/* Ticket 58mm — vista previa en pantalla e impresión */
		.receipt {
			width: 58mm;
			max-width: 58mm;
			margin: 0 auto;
			padding: 3mm 2mm;
			background: #fff;
			box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
		}

		.receipt .center { text-align: center; }

		.receipt .logo {
			display: block;
			max-width: 42mm;
			max-height: 18mm;
			margin: 0 auto 4px;
		}

		.receipt .title {
			font-size: 14px;
			font-weight: bold;
			letter-spacing: 1px;
			margin-bottom: 2px;
		}

		.receipt .divider {
			border: none;
			border-top: 1px dashed #000;
			margin: 5px 0;
		}

		.receipt .divider-solid {
			border: none;
			border-top: 1px solid #000;
			margin: 5px 0;
		}

		.receipt .row {
			display: flex;
			justify-content: space-between;
			gap: 4px;
			margin: 2px 0;
			word-break: break-word;
		}

		.receipt .row .label { flex-shrink: 0; }

		.receipt .row .value {
			text-align: right;
			font-weight: bold;
		}

		.receipt .item {
			margin: 6px 0;
			padding-bottom: 4px;
			border-bottom: 1px dotted #999;
		}

		.receipt .item:last-of-type { border-bottom: none; }

		.receipt .item-name {
			font-weight: bold;
			font-size: 11px;
			margin-bottom: 2px;
		}

		.receipt .item-detail {
			font-size: 11px;
			display: flex;
			justify-content: space-between;
		}

		.receipt .item-meta {
			font-size: 9px;
			color: #333;
			margin-top: 1px;
		}

		.receipt .totals .row { font-size: 11px; }

		.receipt .totals .grand {
			font-size: 14px;
			font-weight: bold;
			margin-top: 4px;
			padding-top: 4px;
			border-top: 1px solid #000;
		}

		.receipt .footer {
			text-align: center;
			font-size: 10px;
			margin-top: 8px;
			padding-top: 4px;
		}

		@page {
			size: 58mm auto;
			margin: 2mm;
		}

		@media print {
			body {
				background: #fff;
				padding: 0;
			}

			.no-print { display: none !important; }

			.receipt {
				width: 58mm;
				max-width: 58mm;
				box-shadow: none;
				padding: 0;
			}
		}
	</style>
</head>

<body>
<?php
require_once('constant/connect.php');
require_once('constant/pharmacy.php');

if (!isset($_GET['id'])) {
	echo '<p>Factura no encontrada.</p></body></html>';
	exit;
}

$orderId = (int) $_GET['id'];
$autoPrint = isset($_GET['print']) && $_GET['print'] == '1';

$sql = "SELECT * FROM orders WHERE delete_status = 0 AND id = $orderId LIMIT 1";
$result = $connect->query($sql);

$order = $result ? $result->fetch(PDO::FETCH_ASSOC) : false;

if (!$order) {
	echo '<p>Factura no encontrada.</p></body></html>';
	exit;
}

$paymentLabels = array(
	1 => 'Cheque',
	2 => 'Efectivo',
	3 => 'Tarjeta',
	4 => 'Nequi',
	5 => 'Google Pay',
	6 => 'Amazon Pay'
);
$paymentLabel = isset($paymentLabels[$order['paymentType']]) ? $paymentLabels[$order['paymentType']] : 'Otro';

$userSql = "SELECT email, username FROM users LIMIT 1";
$userResult = $connect->query($userSql);
$pharmacy = $userResult ? $userResult->fetch(PDO::FETCH_ASSOC) : array('email' => '', 'username' => 'Farmacia');

$itemsSql = "SELECT oi.quantity, oi.rate, oi.total, p.product_name, p.bno, p.expdate
	FROM order_item oi
	INNER JOIN product p ON oi.productName = p.product_id
	WHERE oi.lastid = $orderId";
$itemsResult = $connect->query($itemsSql);

function fmtMoney($n) {
	return '$ ' . number_format((float) $n, 0, ',', '.');
}
?>

<div class="no-print">
	<button type="button" onclick="window.print()">Imprimir ticket 58mm</button>
	<button type="button" class="secondary" onclick="volver()">Cerrar / Volver al POS</button>
	<script>
		function volver() {
			if (window.history.length > 1 && window.opener) {
				window.close();
			} else {
				window.location.href = 'add-order.php';
			}
		}
	</script>
	<p class="hint">
		Formato optimizado para impresora térmica <strong>58mm</strong>.<br>
		En el diálogo de impresión elige tu impresora USB y, si aparece la opción, papel <strong>58mm</strong> o <strong>rollo térmico</strong>.
	</p>
</div>

<div class="receipt">
	<div class="center">
		<?php
		// Logo del negocio (si existe)
		$logoPath = ROOT_PATH . '/assets/runtime/logo.png';
		if (file_exists($logoPath)): ?>
		<img src="./assets/runtime/logo.png" class="logo" alt="<?php echo PHARMACY_NAME; ?>" />
		<?php endif; ?>
		<div class="title" style="font-size: 18px; margin-bottom: 5px;"><?php echo PHARMACY_NAME; ?></div>
		<div style="font-size:12px;"><?php echo PHARMACY_SUBTITLE; ?></div>
		<?php if (PHARMACY_NIT !== ''): ?><div style="font-size:11px; margin-top: 2px;">NIT: <?php echo PHARMACY_NIT; ?></div><?php endif; ?>
		<?php if (PHARMACY_ADDRESS !== ''): ?><div style="font-size:11px;"><?php echo PHARMACY_ADDRESS; ?></div><?php endif; ?>
		<?php if (PHARMACY_PHONE !== ''): ?><div style="font-size:12px; font-weight: bold; margin-top: 2px;">Tel: <?php echo PHARMACY_PHONE; ?></div><?php endif; ?>
	</div>

	<hr class="divider-solid" />

	<div class="center title">FACTURA</div>
	<div class="row"><span class="label">No.</span><span class="value"><?php echo htmlspecialchars($order['uno']); ?></span></div>
	<div class="row"><span class="label">Fecha y Hora</span><span class="value"><?php echo date('d/m/Y H:i'); ?></span></div>

	<hr class="divider" />

	<div class="row"><span class="label">Cliente</span><span class="value" style="max-width:32mm;text-align:right;"><?php echo htmlspecialchars($order['clientName']); ?></span></div>
	<div class="row"><span class="label">Pago</span><span class="value"><?php echo $paymentLabel; ?></span></div>

	<hr class="divider-solid" />

	<div style="font-size:10px;font-weight:bold;margin-bottom:4px;">DETALLE</div>

	<?php
	$items = $itemsResult ? $itemsResult->fetchAll(PDO::FETCH_ASSOC) : [];
	if (count($items) > 0) {
		foreach ($items as $item) {
	?>
		<div class="item">
			<div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
			<div class="item-detail">
				<span><?php echo htmlspecialchars($item['quantity']); ?> x <?php echo fmtMoney($item['rate']); ?></span>
				<span><?php echo fmtMoney($item['total']); ?></span>
			</div>
			<div class="item-meta">Lote: <?php echo htmlspecialchars($item['bno']); ?> | Vence: <?php echo htmlspecialchars($item['expdate']); ?></div>
		</div>
	<?php
		}
	} else {
		echo '<div class="item">Sin productos</div>';
	}
	?>

	<hr class="divider-solid" />

	<div class="totals">
		<div class="row"><span class="label">Subtotal</span><span class="value"><?php echo fmtMoney($order['subTotal']); ?></span></div>
		<div class="row"><span class="label">IVA</span><span class="value"><?php echo fmtMoney($order['gstn']); ?></span></div>
		<div class="row"><span class="label">Descuento</span><span class="value"><?php echo fmtMoney($order['discount']); ?></span></div>
		<div class="row grand"><span class="label">TOTAL</span><span class="value"><?php echo fmtMoney($order['grandTotalValue']); ?></span></div>
		<div class="row"><span class="label">Pagado</span><span class="value"><?php echo fmtMoney($order['paid']); ?></span></div>
		<div class="row"><span class="label">Cambio</span><span class="value"><?php echo fmtMoney($order['dueValue']); ?></span></div>
	</div>

	<hr class="divider" />

	<div class="footer">
		¡Gracias por tu compra, vuelve pronto!<br>
		<?php echo date('d/m/Y H:i'); ?>
	</div>
</div>

<?php if ($autoPrint): ?>
<script>
	window.addEventListener('load', function () {
		setTimeout(function () { window.print(); }, 500);
	});
</script>
<?php endif; ?>

</body>
</html>
