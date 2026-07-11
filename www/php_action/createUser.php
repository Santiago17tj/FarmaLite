<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_POST) {
    $userName = $_POST["userName"];
    $upassword = password_hash($_POST["upassword"], PASSWORD_BCRYPT);
    $uemail = $_POST["uemail"];

    $stmt = $connect->prepare(
        "INSERT INTO users (username, password, email) VALUES (?, ?, ?)"
    );
    $stmt->bindValue(1, $userName);
    $stmt->bindValue(2, $upassword);
    $stmt->bindValue(3, $uemail);

    if ($stmt->execute()) {
        $valid["success"] = true;
        $valid["messages"] = "Successfully Added";
        // header removed
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error while adding the members";
    }

    echo json_encode($valid);

} // if $_POST
