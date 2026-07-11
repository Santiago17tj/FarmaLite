<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productId = (int) $_POST["id"];

    if ($productId) {
        $stmt = $connect->prepare(
            "UPDATE product SET active = 2, status = 2 WHERE product_id = ?"
        );
        $stmt->bindValue(1, $productId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            // header removed
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the brand";
        }
    }
} // /if POST

echo json_encode($valid);
