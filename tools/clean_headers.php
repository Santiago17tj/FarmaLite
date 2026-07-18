<?php
$files = glob('C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\php_action\*.php');
foreach($files as $f) {
    $c = file_get_contents($f);
    // If it's a JSON API, it should not redirect via header()
    if (strpos($c, 'json_encode') !== false) {
        $c = preg_replace('/header\s*\(\s*[\'\"].*?location:.*?[\'\"]\s*\)\s*;/i', '// header removed', $c);
        file_put_contents($f, $c);
    }
}
echo "Redirects cleaned up!";
