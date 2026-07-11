<?php
$files = [
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\add-order.php',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\editorder.php',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\custom\js\order.js',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\custom\js\barcode-invoice.js'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        // Remove decimal points for Colombian Pesos
        $c = str_replace('.toFixed(2)', '.toFixed(0)', $c);
        
        // Update IVA from 18 to 19%
        $c = str_replace('* 18', '* 19', $c);
        $c = str_replace('GST 18%', 'IVA 19%', $c);
        $c = str_replace('IGST 18%', 'IVA 19%', $c);
        $c = str_replace('IVA 18%', 'IVA 19%', $c);
        $c = str_replace('18%', '19%', $c);

        file_put_contents($f, $c);
    }
}
echo "COP Formatting fixed!";
