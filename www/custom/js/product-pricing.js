function updateProfitDisplay() {
	var purchaseInput = document.getElementById('purchase_price');
	var saleInput = document.getElementById('editRate') || document.getElementById('rate');
	var profitEl = document.getElementById('profitDisplay');
	var marginEl = document.getElementById('marginDisplay');

	if (!purchaseInput || !saleInput || !profitEl || !marginEl) {
		return;
	}

	var purchase = parseFloat(purchaseInput.value) || 0;
	var sale = parseFloat(saleInput.value) || 0;
	var profit = sale - purchase;

	profitEl.value = '$ ' + profit.toLocaleString('es-CO');
	if (sale > 0) {
		var margin = ((profit / sale) * 100).toFixed(1);
		marginEl.value = margin + ' %';
	} else {
		marginEl.value = '0 %';
	}

	if (profit < 0) {
		profitEl.style.color = '#c0392b';
		marginEl.style.color = '#c0392b';
	} else {
		profitEl.style.color = '#27ae60';
		marginEl.style.color = '#27ae60';
	}
}

document.addEventListener('DOMContentLoaded', function () {
	var purchaseInput = document.getElementById('purchase_price');
	var saleInput = document.getElementById('editRate') || document.getElementById('rate');

	if (purchaseInput) {
		purchaseInput.addEventListener('input', updateProfitDisplay);
	}
	if (saleInput) {
		saleInput.addEventListener('input', updateProfitDisplay);
	}

	updateProfitDisplay();
});
