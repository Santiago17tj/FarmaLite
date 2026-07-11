<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['userId']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('location: ./login.php');
    exit;
}
include __DIR__ . "/../connect.php";
include __DIR__ . "/../pharmacy.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="<?php echo PHARMACY_NAME; ?>">
  <meta name="keywords" content="<?php echo PHARMACY_NAME; ?>, droguería, farmacia">
  <meta name="author" content="<?php echo PHARMACY_NAME; ?>">

  <link rel="icon" type="image/png" sizes="16x16" href="assets/uploadImage/Logo/favicon.png">
  <title><?php echo PHARMACY_NAME; ?> — <?php echo PHARMACY_SUBTITLE; ?></title>

  <link href="assets/css/lib/chartist/chartist.min.css" rel="stylesheet">
  <link href="assets/css/lib/owl.carousel.min.css" rel="stylesheet" />
  <link href="assets/css/lib/owl.theme.default.min.css" rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="assets/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">

  <link href="assets/css/helper.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/lib/html5-editor/bootstrap-wysihtml5.css" />
  <link href="assets/css/lib/calendar2/semantic.ui.min.css" rel="stylesheet">
  <link href="assets/css/lib/calendar2/pignose.calendar.min.css" rel="stylesheet">
  <link href="assets/css/lib/sweetalert/sweetalert.css" rel="stylesheet">
  <link href="assets/css/lib/datepicker/bootstrap-datepicker3.min.css" rel="stylesheet">



</head>

<body class="fix-header fix-sidebar">

  <div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
      <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
    </svg>
  </div>
