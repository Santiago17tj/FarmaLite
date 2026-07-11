<?php
require_once 'constant/check.php'; include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
  <div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <h3 class="text-primary"><i class="fa fa-barcode"></i> Consultar Precio</h3>
    </div>
    <div class="col-md-7 align-self-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
        <li class="breadcrumb-item active">Consultar Precio</li>
      </ol>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="card">
          <div class="card-body">
            <p class="text-muted">Escanea el código de barras para ver el precio y stock sin crear una factura.</p>

            <div class="form-group">
              <label class="control-label">Código de barras</label>
              <input type="text" class="form-control input-lg" id="barcodeLookup" placeholder="Escanea o escribe el código..." autocomplete="off" style="font-size:1.2em;" />
            </div>

            <div id="lookupError" class="alert alert-danger" style="display:none;"></div>

            <div id="lookupResult" style="display:none;">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th style="width:35%;">Medicina</th>
                      <td id="resName"></td>
                    </tr>
                    <tr>
                      <th>Código de barras</th>
                      <td id="resBarcode"></td>
                    </tr>
                    <tr>
                      <th>Precio de venta</th>
                      <td><strong id="resRate" class="text-success" style="font-size:1.3em;"></strong></td>
                    </tr>
                    <tr>
                      <th>Precio de compra</th>
                      <td id="resPurchase"></td>
                    </tr>
                    <tr>
                      <th>Ganancia por unidad</th>
                      <td><strong id="resProfit" class="text-primary"></strong></td>
                    </tr>
                    <tr>
                      <th>Stock disponible</th>
                      <td id="resQty"></td>
                    </tr>
                    <tr>
                      <th>No. Lote</th>
                      <td id="resBno"></td>
                    </tr>
                    <tr>
                      <th>Vencimiento</th>
                      <td id="resExp"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <a href="add-order.php" class="btn btn-primary"><i class="fa fa-file"></i> Ir a crear factura</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('./constant/layout/footer.php'); ?>

<script>
$(document).ready(function () {
  $("#barcodeLookup").focus();

  function lookupBarcode(barcode) {
    $("#lookupError").hide();
    $("#lookupResult").hide();

    $.ajax({
      url: 'php_action/fetchProductByBarcode.php',
      type: 'post',
      data: { barcode: barcode },
      dataType: 'json',
      success: function (response) {
        if (!response.success) {
          $("#lookupError").text(response.messages).show();
          return;
        }
        var p = response.product;
        var purchase = parseFloat(p.purchase_price) || 0;
        var sale = parseFloat(p.rate) || 0;
        var profit = sale - purchase;
        $("#resName").text(p.product_name);
        $("#resBarcode").text(p.barcode || '—');
        $("#resRate").text('$ ' + sale.toLocaleString('es-CO'));
        $("#resPurchase").text('$ ' + purchase.toLocaleString('es-CO'));
        $("#resProfit").text('$ ' + profit.toLocaleString('es-CO'));
        $("#resQty").text(p.quantity + ' unidades');
        $("#resBno").text(p.bno);
        $("#resExp").text(p.expdate);
        $("#lookupResult").show();
      },
      error: function () {
        $("#lookupError").text('Error al consultar el código').show();
      }
    });
  }

  $("#barcodeLookup").on('keypress', function (e) {
    if (e.which === 13) {
      e.preventDefault();
      var barcode = $(this).val().trim();
      $(this).val('');
      if (barcode) {
        lookupBarcode(barcode);
      }
      $(this).focus();
    }
  });
});
</script>
