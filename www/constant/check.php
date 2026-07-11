<?php
ob_start();
session_start();

require_once "connect.php";

if (!isset($_SESSION["userId"])) {
    header("location:./login.php");
    exit();
}
