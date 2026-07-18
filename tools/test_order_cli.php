<?php
$_SESSION['userId'] = 1;
$_POST = [
    "uno" => "INV-999",
    "orderDate" => "2026-07-09",
    "clientName" => "Test",
    "clientContact" => "123",
    "subTotalValue" => "100",
    "totalAmountValue" => "100",
    "discount" => "0",
    "grandTotalValue" => "100",
    "gstn" => "0",
    "paid" => "100",
    "dueValue" => "0",
    "paymentType" => "2",
    "paymentStatus" => "1",
    "paymentPlace" => "1",
    "productName" => ["1"],
    "quantity" => ["1"],
    "rateValue" => ["100"],
    "totalValue" => ["100"]
];
// Change to correct dir so core.php works
chdir('C:\Users\Isabel\Desktop\Drogueria_La_Formula\www\php_action');
ob_start();
require 'order.php';
$out = ob_get_clean();
echo "OUTPUT: " . $out;
