<?php
$files = [
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\add-order.php',
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        
        // 1. Client Name
        $c = preg_replace('/<input type="text" class="form-control" id="clientName"([^>]+)>/i', '<input type="text" class="form-control" id="clientName"$1 value="Consumidor Final" >', $c);
        $c = preg_replace('/<div class="form-group">\s*<label for="clientName"/i', '<div class="form-group" style="display:none;"> <label for="clientName"', $c);
        
        // 2. Client Contact
        $c = preg_replace('/<input type="text" class="form-control" id="clientContact"([^>]+)>/i', '<input type="text" class="form-control" id="clientContact"$1 value="0000000000" >', $c);
        $c = preg_replace('/<div class="form-group">\s*<label for="clientContact"/i', '<div class="form-group" style="display:none;"> <label for="clientContact"', $c);
        
        // 3. Discount
        $c = preg_replace('/<input type="text" class="form-control" id="discount"([^>]+)>/i', '<input type="text" class="form-control" id="discount"$1 value="0" >', $c);
        $c = preg_replace('/<div class="form-group">\s*<label for="discount"/i', '<div class="form-group" style="display:none;"> <label for="discount"', $c);
        
        // 4. Payment Place
        $c = preg_replace('/<div class="form-group">\s*<label for="clientContact" class="col-sm-2 control-label">Lugar de pago<\/label>/i', '<div class="form-group" style="display:none;"> <label for="clientContact" class="col-sm-2 control-label">Lugar de pago</label>', $c);
        
        // 5. Payment Type (Select 2 = Cash)
        $c = preg_replace('/<option value="2">Efectivo<\/option>/i', '<option value="2" selected>Efectivo</option>', $c);
        $c = preg_replace('/<div class="form-group">\s*<label for="paymentType"/i', '<div class="form-group" style="display:none;"> <label for="paymentType"', $c);
        
        // 6. Payment Status (Select 1 = Full Payment)
        $c = preg_replace('/<option value="1">Pago Completo<\/option>/i', '<option value="1" selected>Pago Completo</option>', $c);
        $c = preg_replace('/<div class="form-group">\s*<label for="paymentStatus"/i', '<div class="form-group" style="display:none;"> <label for="paymentStatus"', $c);
        
        file_put_contents($f, $c);
    }
}
echo "UI Simplificada!";
