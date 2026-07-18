<?php
$files = [
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\php_action\createBrandImport.php',
    'C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\php_action\createProductImport.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        // Replace $result->rowCount() == 0 with !$result->fetch()
        $c = preg_replace('/if\s*\(\s*\$result->rowCount\(\)\s*==\s*0\s*\)/', 'if (!$result->fetch())', $c);
        $c = preg_replace('/if\s*\(\s*\$dup\s*&&\s*\$dup->rowCount\(\)\s*>\s*0\s*\)/', 'if ($dup && $dup->fetch())', $c);
        $c = preg_replace('/if\s*\(\s*\$br\s*&&\s*\$br->rowCount\(\)\s*>\s*0\s*\)/', 'if ($br && $br->fetch())', $c);
        $c = preg_replace('/if\s*\(\s*\$cr\s*&&\s*\$cr->rowCount\(\)\s*>\s*0\s*\)/', 'if ($cr && $cr->fetch())', $c);
        file_put_contents($f, $c);
    }
}
echo "rowCount fixed globally!";
