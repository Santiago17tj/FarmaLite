<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = (int) $_POST["id"];

    if ($orderId) {
        $stmtOrders = $connect->prepare(
            "UPDATE orders SET delete_status = 1 WHERE id = ?"
        );
        $stmtOrders->bindValue(1, $orderId, PDO::PARAM_INT);

        if ($stmtOrders->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            // header removed
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the order";
        }
    }
} // /if POST

echo json_encode($valid);
