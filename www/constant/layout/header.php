<?php

require_once "./constant/connect.php";
require_once "./constant/pharmacy.php";
?>

<div id="main-wrapper">

    <div class="header">
        <div class="d-lg-none d-block" style="overflow:hidden;white-space:nowrap;">
            <div class="ml-lg-5 pl-lg-5 ">

                <b id="ti" class="ml-lg-5 pl-lg-5"></b>


            </div>
        </div>
        <nav class="navbar top-navbar navbar-expand-md navbar-light">

            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php" style="display:flex;align-items:center;padding:4px 8px;">
                    <img src="./assets/runtime/logo.png" alt="<?php echo PHARMACY_NAME; ?>" style="height:65px;width:auto;object-fit:contain;transform: scale(1.15);transform-origin: left center;" />
                </a>
            </div>

            <div class="navbar-collapse">

                <ul class="navbar-nav  mt-md-0">

                    <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted  " href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                    <li class="nav-item m-l-10"> <a class="nav-link sidebartoggler hidden-sm-down text-muted  " href="javascript:void(0)"><i class="ti-menu"></i></a> </li>

                    <?php
                    // Botón VOLVER contextual - rutas explícitas por módulo
                    $currentPage = basename($_SERVER['PHP_SELF']);
                    $backRoutes = [
                        'editproduct.php'    => 'product.php',
                        'add-product.php'    => 'product.php',
                        'editorder.php'      => 'Order.php',
                        'add-order.php'      => 'Order.php',
                        'editbrand.php'      => 'brand.php',
                        'add-brand.php'      => 'brand.php',
                        'editcategory.php'   => 'categories.php',
                        'add-category.php'   => 'categories.php',
                        'edituser.php'       => 'dashboard.php',
                        'import-product.php' => 'product.php',
                        'importbrand.php'    => 'brand.php',
                        'invoiceprint.php'   => 'Order.php',
                        'configuracion.php'  => 'dashboard.php',
                        'auditoria.php'      => 'dashboard.php',
                        'manual_backup.php'  => 'dashboard.php',
                        'restaurar_backup.php' => 'dashboard.php',
                        'optimizar.php'      => 'dashboard.php',
                        'info.php'           => 'dashboard.php',
                        'kardex.php'         => 'dashboard.php',
                        'devoluciones.php'   => 'dashboard.php',
                        'apertura.php'       => 'dashboard.php',
                        'cierre.php'         => 'dashboard.php',
                        'historial_caja.php' => 'dashboard.php',
                        'consultar-precio.php' => 'dashboard.php',
                        'reportes_unificados.php' => 'dashboard.php',
                        'benchmark_certification.php' => 'dashboard.php',
                        'product.php'        => 'dashboard.php',
                        'brand.php'          => 'dashboard.php',
                        'categories.php'     => 'dashboard.php',
                        'Order.php'          => 'dashboard.php',
                        'salesreport.php'    => 'dashboard.php',
                        'sales_report.php'   => 'dashboard.php',
                    ];
                    $backUrl = isset($backRoutes[$currentPage]) ? $backRoutes[$currentPage] : 'dashboard.php';
                    if ($currentPage !== 'dashboard.php'):
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $backUrl; ?>" style="font-weight:600;color:#0F62FE !important;font-size:15px;" title="Volver">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </li>
                    <?php endif; ?>

                </ul>

                <ul class="navbar-nav my-lg-0 ml-auto">


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted  " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <img src="./assets/uploadImage/Profile/usuario-admin.png" alt="user" class="profile-pic" /></a>
                        <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                            <ul class="dropdown-user">
                                <?php if (
                                    isset($_SESSION["userId"]) &&
                                    $_SESSION["userId"] == 1
                                ) { ?>
                                <?php } ?>

                                <li><a href="./constant/logout.php"><i class="fa fa-power-off"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

    </div>
    <script language="javascript">
        var today = new Date();
        document.getElementById('ti').innerHTML = today;


    </script>
