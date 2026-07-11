<?php

/**
 * Rangos de fechas para reportes de ventas.
 */
function salesReportGetPeriod($period)
{
	$today = date('Y-m-d');

	switch ($period) {
		case 'week':
			return array(
				'key' => 'week',
				'start' => date('Y-m-d', strtotime('-6 days')),
				'end' => $today,
				'title' => 'Reporte Semanal',
				'subtitle' => 'Ventas de los últimos 7 días (' . date('d/m/Y', strtotime('-6 days')) . ' — ' . date('d/m/Y') . ')'
			);
		case 'month':
			return array(
				'key' => 'month',
				'start' => date('Y-m-01'),
				'end' => $today,
				'title' => 'Reporte Mensual',
				'subtitle' => 'Ventas del mes en curso (' . date('m/Y') . ')'
			);
		default:
			return array(
				'key' => 'day',
				'start' => $today,
				'end' => $today,
				'title' => 'Reporte del Día',
				'subtitle' => 'Ventas acumuladas hoy (' . date('d/m/Y') . ')'
			);
	}
}

function salesReportFetchSummary($connect, $startDate, $endDate)
{
	$startDate = ($startDate);
	$endDate = ($endDate);

	$summary = array(
		'total_income' => 0,
		'total_invoices' => 0,
		'top_products' => array()
	);

	$sqlOrders = "SELECT COUNT(*) AS total_invoices, COALESCE(SUM(grandTotalValue), 0) AS total_income
		FROM orders
		WHERE delete_status = 0
		AND orderDate >= '$startDate'
		AND orderDate <= '$endDate'";

	$result = $connect->query($sqlOrders);
	if ($result && $row = $result->fetch(PDO::FETCH_ASSOC)) {
		$summary['total_income'] = (float) $row['total_income'];
		$summary['total_invoices'] = (int) $row['total_invoices'];
	}

	$sqlTop = "SELECT p.product_name,
			SUM(CAST(oi.quantity AS INTEGER)) AS units_sold,
			SUM(CAST(oi.total AS REAL)) AS revenue
		FROM order_item oi
		INNER JOIN orders o ON oi.lastid = o.id
		INNER JOIN product p ON oi.productName = p.product_id
		WHERE o.delete_status = 0
		AND o.orderDate >= '$startDate'
		AND o.orderDate <= '$endDate'
		GROUP BY oi.productName, p.product_name
		ORDER BY units_sold DESC, revenue DESC
		LIMIT 10";

	$topResult = $connect->query($sqlTop);
	if ($topResult) {
		while ($product = $topResult->fetch(PDO::FETCH_ASSOC)) {
			$summary['top_products'][] = $product;
		}
	}

	return $summary;
}

function salesReportFormatMoney($amount)
{
	return '$ ' . number_format((float) $amount, 0, ',', '.');
}
