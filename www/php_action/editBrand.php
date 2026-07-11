<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];
$brandId = (int) $_GET["id"];

if ($_POST) {
    $brandName = $_POST["brandName"];
    $brandStatus = $_POST["brandStatus"];

    $stmt = $connect->prepare(
        "UPDATE brands SET brand_name = ?, brand_active = ? WHERE brand_id = ?"
    );
    $stmt->bindValue(1, $brandName);
    $stmt->bindValue(2, $brandStatus);
    $stmt->bindValue(3, $brandId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $valid["success"] = true;
        $valid["messages"] = "Successfully Updated";
        // header removed
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error while adding the members";
    }

    echo json_encode($valid);
} // /if $_POST
