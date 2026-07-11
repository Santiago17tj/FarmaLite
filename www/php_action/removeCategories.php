<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $categoriesId = (int) $_POST["id"];

    if ($categoriesId) {
        $stmt = $connect->prepare(
            "UPDATE categories SET categories_status = 2 WHERE categories_id = ?"
        );
        $stmt->bindValue(1, $categoriesId, PDO::PARAM_INT);

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
