<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userid = (int) $_POST["id"];

    if ($userid) {
        $stmt = $connect->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bindValue(1, $userid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            // header removed
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the user";
        }
    }
} // /if POST

echo json_encode($valid);
