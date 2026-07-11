<?php
include('./constant/connect.php');

?>
</div>

<!-- Modal Consulta Rápida de Precios (F3) -->
<div class="modal fade" id="priceCheckModal" tabindex="-1" role="dialog" aria-labelledby="priceCheckModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content text-center">
      <div class="modal-header bg-primary text-white">
        <h3 class="modal-title w-100" id="priceCheckModalLabel">Consulta de Precio (F3)</h3>
      </div>
      <div class="modal-body" style="padding: 30px;">
        <input type="text" id="priceCheckScanner" class="form-control form-control-lg text-center" placeholder="Escanea o escribe el producto y presiona ENTER" style="font-size: 20px; font-weight: bold; margin-bottom: 20px;" autocomplete="off">
        
        <div id="priceCheckResult" style="display: none;">
            <h2 id="pcName" class="text-info" style="font-size: 35px; font-weight: 800; margin-bottom: 15px;"></h2>
            <h1 id="pcPrice" class="text-success" style="font-size: 55px; font-weight: 900; margin-bottom: 25px;"></h1>
            
            <div class="row" style="font-size: 18px; font-weight: bold;">
                <div class="col-md-4">
                    <span class="text-muted">Stock</span><br>
                    <span id="pcStock" class="text-dark"></span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted">Lote</span><br>
                    <span id="pcLote" class="text-dark"></span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted">Vence</span><br>
                    <span id="pcVence" class="text-danger"></span>
                </div>
            </div>
        </div>
        
        <div id="priceCheckError" class="alert alert-danger" style="display: none; font-size: 20px; font-weight: bold;"></div>
      </div>
      <div class="modal-footer justify-content-center">
        <p class="text-muted" style="margin: 0; font-size: 14px;">Presiona <kbd>ESC</kbd> para salir</p>
      </div>
    </div>
  </div>
</div>
<!-- /Modal Consulta Rápida -->

</div>


<script src="assets/js/lib/jquery/jquery.min.js"></script>

<script src="assets/js/lib/bootstrap/js/popper.min.js"></script>
<script src="assets/js/lib/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/lib/bootstrap/js/bootstrap.js"></script>
<script src="assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>

<script src="assets/js/jquery.slimscroll.js"></script>

<script src="assets/js/sidebarmenu.js"></script>

<script src="assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>


<script src="assets/js/lib/sweetalert/sweetalert.min.js"></script>

<script src="assets/js/lib/sweetalert/sweetalert.init.js"></script>


<script src="assets/js/lib/weather/jquery.simpleWeather.min.js"></script>
<script src="assets/js/lib/weather/weather-init.js"></script>
<script src="assets/js/lib/owl-carousel/owl.carousel.min.js"></script>
<script src="assets/js/lib/owl-carousel/owl.carousel-init.js"></script>




<script src="assets/js/custom.min.js"></script>


<script src="assets/js/lib/datatables/datatables.min.js"></script>
<script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="assets/js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="assets/js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="assets/js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="assets/js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script src="assets/js/lib/datatables/datatables-init.js"></script>

<script>
    // F3 Listener Global
    $(document).keydown(function(e) {
        if (e.keyCode == 114) { // F3
            e.preventDefault();
            $('#priceCheckModal').modal('show');
        }
    });

    $('#priceCheckModal').on('shown.bs.modal', function () {
        $('#priceCheckScanner').val('');
        $('#priceCheckResult').hide();
        $('#priceCheckError').hide();
        $('#priceCheckScanner').focus();
    });

    $('#priceCheckScanner').on('keypress', function(e) {
        if(e.which == 13) { // Enter key
            e.preventDefault();
            var term = $(this).val();
            if(term.trim() === '') return;
            
            $.ajax({
                url: 'php_action/fetchProductForPriceCheck.php',
                type: 'post',
                data: {term: term},
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#priceCheckError').hide();
                        $('#pcName').text(response.data.name);
                        $('#pcPrice').text('$' + response.data.price);
                        $('#pcStock').text(response.data.stock);
                        $('#pcLote').text(response.data.bno);
                        $('#pcVence').text(response.data.expdate);
                        $('#priceCheckResult').show();
                    } else {
                        $('#priceCheckResult').hide();
                        $('#priceCheckError').text(response.messages).show();
                    }
                    $('#priceCheckScanner').val('').focus();
                }
            });
        }
    });
</script>

<script>
    function alphaOnly(event) {
        var key = event.keyCode;
        return ((key >= 65 && key <= 90) || key == 8);
    };
</script>
<script>
    // WRITE THE VALIDATION SCRIPT.
    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }
</script>



<!--/ Copy this code to have a working example -->

</div>
</div>
</body>

</html>
