<?php
$files = [
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\add-order.php',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\editorder.php',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\custom\js\order.js'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        $c = str_replace('orders.php?o=add', 'add-order.php', $c);
        file_put_contents($f, $c);
    }
}
echo 'Links fixed!';
