<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brandId = (int) $_POST["id"];

    if ($brandId) {
        $stmt = $connect->prepare(
            "UPDATE brands SET brand_status = 2 WHERE brand_id = ?"
        );
        $stmt->bindValue(1, $brandId, PDO::PARAM_INT);

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
