 <?php
    require_once('./constant/connect.php');


    ?>


 <div class="left-sidebar">

     <div class="scroll-sidebar">

         <nav class="sidebar-nav">
             <ul id="sidebarnav">
                 <li class="nav-devider"></li>
                 <li class="nav-label">Menú</li>
                 <li> <a href="dashboard.php" aria-expanded="false"><i class="fa fa-eye"></i> Dashboard</a>
                 </li>

                 <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                     <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-rss"></i><span class="hide-menu">Proveedores</span></a>
                         <ul aria-expanded="false" class="collapse">

                             <li><a href="add-brand.php">Agregar Proveedor</a></li>

                             <li><a href="brand.php">Gestionar Proveedor</a></li>
                         </ul>
                     </li>
                 <?php } ?>
                 <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                     <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-list"></i><span class="hide-menu">Categorias</span></a>
                         <ul aria-expanded="false" class="collapse">

                             <li><a href="add-category.php">Agregar Categoría</a></li>

                             <li><a href="categories.php">Gestionar Categorías</a></li>
                         </ul>
                     </li>
                 <?php } ?>
                 <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                     <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-medkit"></i><span class="hide-menu">Medicina</span></a>
                         <ul aria-expanded="false" class="collapse">

                             <li><a href="add-product.php">Agregar Medicina</a></li>

                             <li><a href="product.php">Gestionar Medicinas</a></li>
                             <li><a href="import-product.php">Importar desde Excel</a></li>
                         </ul>
                     </li>
                 <?php } ?>
                 <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-money"></i><span class="hide-menu">Caja</span></a>
                     <ul aria-expanded="false" class="collapse">
                         <li><a href="apertura.php">Apertura de Caja</a></li>
                         <li><a href="cierre.php">Cierre de Caja</a></li>
                         <li><a href="historial_caja.php">Historial de Cierres</a></li>
                     </ul>
                 </li>
                 <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive"></i><span class="hide-menu">Inventario</span></a>
                     <ul aria-expanded="false" class="collapse">
                         <li><a href="kardex.php">Kardex</a></li>
                         <li><a href="devoluciones.php">Devoluciones</a></li>
                     </ul>
                 </li>
                 <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-file"></i><span class="hide-menu">Facturas</span></a>
                     <ul aria-expanded="false" class="collapse">

                         <li><a href="add-order.php">Crear Factura</a></li>

                         <li><a href="Order.php">Administrar Facturas</a></li>
                     </ul>
                 </li>

                 <li> <a href="consultar-precio.php" aria-expanded="false"><i class="fa fa-barcode"></i> Consultar Precio</a>
                 </li>

                 <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == 1) { ?>
                     <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-pie-chart"></i><span class="hide-menu">Reportes</span></a>
                     <ul aria-expanded="false" class="collapse">
                         <li><a href="reportes_unificados.php">Dashboard & Reportes</a></li>
                     </ul>
                 </li>
                     <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cogs"></i><span class="hide-menu">Configuración</span></a>
                         <ul aria-expanded="false" class="collapse">
                             <li><a href="configuracion.php">Configuración de Empresa</a></li>
                             <li><a href="auditoria.php">Auditoría del Sistema</a></li>
                             <li><a href="manual_backup.php">Respaldos</a></li>
                             <li><a href="restaurar_backup.php">Restaurar Respaldo</a></li>
                             <li><a href="optimizar.php">Optimizar Base de Datos</a></li>
                             <li><a href="info.php">Información</a></li>
                             <li><a href="benchmark_certification.php">Certificación y Benchmark</a></li>
                         </ul>
                     </li>
                 <?php } ?>



             </ul>
         </nav>

     </div>

 </div>