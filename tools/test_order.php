<?php
$data = [
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

$ch = curl_init('http://127.0.0.1:9001/php_action/order.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
curl_close($ch);

echo "RESPONSE:\n";
echo $response;
