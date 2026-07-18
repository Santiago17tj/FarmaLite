<?php
$file = 'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\add-order.php';
$c = file_get_contents($file);

// 1. Prevent default native submission strictly
$c = str_replace('bind(\'submit\', function() {', 'bind(\'submit\', function(event) { event.preventDefault();', $c);

// 2. Hide unwanted form groups by injecting inline styles
// clientName
$c = preg_replace('/<div class="form-group">\s*<label class="col-sm-2 control-label">Nombre Cliente<\/label>/i', '<div class="form-group" style="display:none;"> <label class="col-sm-2 control-label">Nombre Cliente</label>', $c);

// clientContact
$c = preg_replace('/<div class="form-group">\s*<label class="col-sm-2 control-label">MÃ³vil <\/label>/i', '<div class="form-group" style="display:none;"> <label class="col-sm-2 control-label">MÃ³vil </label>', $c);
$c = preg_replace('/<div class="form-group">\s*<label class="col-sm-2 control-label">Móvil <\/label>/i', '<div class="form-group" style="display:none;"> <label class="col-sm-2 control-label">Móvil </label>', $c);

// discount
$c = preg_replace('/<div class="form-group">\s*<label for="discount"/i', '<div class="form-group" style="display:none;"> <label for="discount"', $c);

// paymentType
$c = preg_replace('/<div class="form-group">\s*<label for="clientContact" class="col-sm-2 control-label">Tipo de Pago/i', '<div class="form-group" style="display:none;"> <label for="clientContact" class="col-sm-2 control-label">Tipo de Pago', $c);

// paymentStatus
$c = preg_replace('/<div class="form-group">\s*<label for="clientContact" class="col-sm-2 control-label">Estado del Pago/i', '<div class="form-group" style="display:none;"> <label for="clientContact" class="col-sm-2 control-label">Estado del Pago', $c);

// paymentPlace
$c = preg_replace('/<div class="form-group">\s*<label for="clientContact" class="col-sm-2 control-label">Lugar del Pago/i', '<div class="form-group" style="display:none;"> <label for="clientContact" class="col-sm-2 control-label">Lugar del Pago', $c);

file_put_contents($file, $c);
echo "UI simplificada y Submit nativo bloqueado!";
