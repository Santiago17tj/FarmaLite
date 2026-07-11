var quickLookupProductId = null;

function findRowByProductId(productId) {
	var found = null;
	$("#productTable tbody tr").each(function () {
		var rowId = $(this).attr('id');
		if (!rowId) return;
		var row = rowId.substring(3);
		if ($("#productName" + row).val() == productId) {
			found = row;
			return false;
		}
	});
	return found;
}

function findEmptyProductRow() {
	var found = null;
	$("#productTable tbody tr").each(function () {
		var rowId = $(this).attr('id');
		if (!rowId) return;
		var row = rowId.substring(3);
		if ($("#productName" + row).val() === '') {
			found = row;
			return false;
		}
	});
	return found;
}

function setProductOnRow(row, productId) {
	$("#productName" + row).val(productId);
	getProductData(row);
}

function showBarcodeFeedback(message, isError) {
	var $fb = $("#barcodeFeedback");
	$fb.removeClass('alert-info alert-success alert-danger');
	$fb.addClass(isError ? 'alert-danger' : 'alert-success');
	$fb.text(message).show();
	setTimeout(function () {
		$fb.fadeOut();
	}, 2500);
}

function addProductToInvoice(productId) {
	var existingRow = findRowByProductId(productId);
	if (existingRow) {
		var qty = Number($("#quantity" + existingRow).val()) + 1;
		var available = Number($("#available_quantity" + existingRow).text());
		if (qty > available) {
			showBarcodeFeedback('Stock insuficiente (disponible: ' + available + ')', true);
			return false;
		}
		$("#quantity" + existingRow).val(qty);
		getTotal(existingRow);
		showBarcodeFeedback('Cantidad actualizada', false);
		return true;
	}

	var emptyRow = findEmptyProductRow();
	if (emptyRow) {
		setProductOnRow(emptyRow, productId);
		showBarcodeFeedback('Medicina agregada a la factura', false);
		return true;
	}

	addRow(function (newRow) {
		setProductOnRow(newRow, productId);
		showBarcodeFeedback('Medicina agregada a la factura', false);
	});
	return true;
}

function handleBarcodeScan(barcode, forModal) {
	$.ajax({
		url: 'php_action/fetchProductByBarcode.php',
		type: 'post',
		data: { barcode: barcode },
		dataType: 'json',
		success: function (response) {
			if (!response.success) {
				if (forModal) {
					$('#modalLookupError').text(response.messages).show();
					$('#modalLookupResult').hide();
				} else {
					showBarcodeFeedback(response.messages, true);
				}
				return;
			}

			if (forModal) {
				showModalLookupResult(response.product);
			} else {
				addProductToInvoice(response.product.product_id);
				$("#barcodeScan").focus();
			}
		},
		error: function () {
			var msg = 'Error al buscar el código de barras';
			if (forModal) {
				$('#modalLookupError').text(msg).show();
			} else {
				showBarcodeFeedback(msg, true);
			}
		}
	});
}

function showModalLookupResult(p) {
	quickLookupProductId = p.product_id;
	var purchase = parseFloat(p.purchase_price) || 0;
	var sale = parseFloat(p.rate) || 0;
	var profit = sale - purchase;

	$('#modalLookupError').hide();
	$('#modalResName').text(p.product_name);
	$('#modalResBarcode').text(p.barcode || '—');
	$('#modalResSale').text('$ ' + sale.toLocaleString('es-CO'));
	$('#modalResPurchase').text('$ ' + purchase.toLocaleString('es-CO'));
	$('#modalResProfit').text('$ ' + profit.toLocaleString('es-CO'));
	$('#modalResQty').text(p.quantity + ' unidades');
	$('#modalLookupResult').show();
	$('#modalBarcodeLookup').val('').focus();
}

function openQuickPriceModal() {
	quickLookupProductId = null;
	$('#modalLookupError').hide();
	$('#modalLookupResult').hide();
	$('#modalBarcodeLookup').val('');
	$('#quickPriceModal').modal('show');
	setTimeout(function () { $('#modalBarcodeLookup').focus(); }, 400);
}

$(document).ready(function () {
	$("#barcodeScan").on('keypress', function (e) {
		if (e.which === 13) {
			e.preventDefault();
			var barcode = $(this).val().trim();
			$(this).val('');
			if (barcode) {
				handleBarcodeScan(barcode, false);
			}
		}
	});

	$("#barcodeScan").focus();

	$('#modalBarcodeLookup').on('keypress', function (e) {
		if (e.which === 13) {
			e.preventDefault();
			var barcode = $(this).val().trim();
			if (barcode) {
				handleBarcodeScan(barcode, true);
			}
		}
	});

	$('#modalAddToInvoice').on('click', function () {
		if (!quickLookupProductId) return;
		addProductToInvoice(quickLookupProductId);
		$('#quickPriceModal').modal('hide');
		$('#barcodeScan').focus();
	});

	$('#btnQuickPrice').on('click', openQuickPriceModal);

	$(document).on('keydown', function (e) {
		if (e.key === 'F2') {
			e.preventDefault();
			openQuickPriceModal();
		}
	});
});
