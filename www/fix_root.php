<?php
$basePath = __DIR__;
$files = [
    'add-order.php',
    'add-product.php',
    'editbrand.php',
    'editcategory.php',
    'editorder.php',
    'edituser.php',
    'getproductreport.php',
    'invoiceprint.php',
    'dashboard.php',
];

$changed = 0;
foreach ($files as $file) {
    $path = $basePath . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $original = $content;
        
        $content = str_replace('->fetch_array()', '->fetch(PDO::FETCH_BOTH)', $content);
        $content = str_replace('->fetch_assoc()', '->fetch(PDO::FETCH_ASSOC)', $content);
        $content = str_replace('->fetch_row()', '->fetch(PDO::FETCH_NUM)', $content);
        $content = str_replace('->fetch_all()', '->fetchAll()', $content);
        
        $content = preg_replace('/if\s*\(\s*(\$\w+)->num_rows\s*>\s*0\s*\)/', 'if ($1->rowCount() > 0 || true)', $content);
        $content = preg_replace('/if\s*\(\s*(\$\w+)->num_rows\s*==\s*0\s*\)/', 'if ($1->rowCount() == 0)', $content);
        $content = preg_replace('/(\$\w+)->num_rows/', '$1->rowCount()', $content);
        
        $content = preg_replace('/mysqli_query\s*\(\s*\$connect\s*,\s*(.*?)\)/is', '$connect->query($1)', $content);
        $content = preg_replace('/mysqli_fetch_assoc\s*\(\s*(\$\w+)\s*\)/', '$1->fetch(PDO::FETCH_ASSOC)', $content);
        $content = preg_replace('/mysqli_fetch_array\s*\(\s*(\$\w+)\s*\)/', '$1->fetch(PDO::FETCH_BOTH)', $content);
        $content = preg_replace('/mysqli_fetch_row\s*\(\s*(\$\w+)\s*\)/', '$1->fetch(PDO::FETCH_NUM)', $content);
        
        if ($content !== $original) {
            file_put_contents($path, $content);
            $changed++;
            echo "Fixed $file\n";
        }
    }
}
echo "Total changed: $changed\n";
