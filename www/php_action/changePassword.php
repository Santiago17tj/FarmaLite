<?php

require_once "core.php";

if ($_POST) {
    $valid["success"] = ["success" => false, "messages" => []];

    $currentPasswordInput = $_POST["password"];
    $newPasswordInput = $_POST["npassword"];
    $confirmPasswordInput = $_POST["cpassword"];
    $userId = (int) $_POST["user_id"];

    // Fetch stored hash
    $stmt = $connect->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $storedPassword = $row ? $row["password"] : "";

    // Support both bcrypt (new) and legacy MD5 hashes
    $currentPasswordMatches =
        password_verify($currentPasswordInput, $storedPassword) ||
        md5($currentPasswordInput) === $storedPassword;

    if ($currentPasswordMatches) {
        if ($newPasswordInput === $confirmPasswordInput) {
            $newHash = password_hash($newPasswordInput, PASSWORD_BCRYPT);
            $updateStmt = $connect->prepare(
                "UPDATE users SET password = ? WHERE user_id = ?"
            );
            $updateStmt->bindValue(1, $newHash);
            $updateStmt->bindValue(2, $userId, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                $valid["success"] = true;
                $valid["messages"] = "Successfully Updated";
                // header removed
            } else {
                $valid["success"] = false;
                $valid["messages"] = "Error while updating the password";
            }
        } else {
            $valid["success"] = false;
            $valid["messages"] =
                "New password does not match with Conform password";
        }
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Current password is incorrect";
    }

    echo json_encode($valid);
}

?>
