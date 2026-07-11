<?php

session_start();

require_once "db_connect.php";

if (!isset($_SESSION["userId"])) {
    header("location:" . $store_url);
    exit();
}

?>
