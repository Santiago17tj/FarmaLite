<?php
include "./constant/connect.php";
$stmt = $connect->prepare("SELECT user_id, password FROM users WHERE email = ?");
$stmt->bindValue(1, "laformula.salud@gmail.com");
$stmt->execute();
$value = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Password in DB: " . $value["password"] . "<br>";
echo "Verification: " . (password_verify("Claudiaol859", $value["password"]) ? "TRUE" : "FALSE") . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

