<?php
$files = [
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\add-order.php',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\editorder.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        // Replace $_GET['o'] with (isset($_GET['o']) ? $_GET['o'] : '')
        $c = str_replace("\$_GET['o']", "(isset(\$_GET['o']) ? \$_GET['o'] : '')", $c);
        
        // Remove the fatal error typo line and the entire commented block
        // Just remove the specific typo to be safe: <? php
        $c = str_replace("<? php", "<?php", $c);
        
        file_put_contents($f, $c);
    }
}
echo "Fixed!";
