<?php
require_once 'constant/check.php'; include('./constant/layout/head.php'); ?>
<?php include('./constant/layout/header.php'); ?>
<?php include('./constant/layout/sidebar.php'); ?>

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Importar Medicinas</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active">Importar Medicinas</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9 mx-auto">
                <?php if (isset($_GET['imported'])): ?>
                    <div class="alert alert-success">
                        Importación finalizada: <strong><?php echo (int) $_GET['imported']; ?></strong> medicinas agregadas,
                        <strong><?php echo (int) $_GET['skipped']; ?></strong> omitidas (duplicados o errores).
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h4 style="margin-bottom:5px;"><i class="fa fa-upload"></i> Carga masiva de medicamentos</h4>
                        <p class="text-muted" style="margin-bottom:20px;">Sube tu inventario de una sola vez usando un archivo Excel (.xlsx) o CSV. Ideal para cargar medicamentos al inicio o cuando llegue un pedido grande.</p>

                        <div class="alert alert-info" style="font-size:13px;">
                            <strong><i class="fa fa-lightbulb-o"></i> Tip:</strong>
                            Si el medicamento <strong>no tiene código de barras</strong>, deja la columna <strong>A</strong> en blanco. El sistema le asignará un código interno automáticamente.
                        </div>

                        <h5 style="margin-bottom:10px;">Referencia de columnas</h5>
                        <table class="table table-bordered table-sm" style="font-size:13px;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:60px;">Columna</th>
                                    <th>Campo</th>
                                    <th style="width:100px;">Requerido</th>
                                    <th>Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><strong>A</strong></td><td>Código de barras (EAN-13)</td><td><span class="label label-default">Opcional</span></td><td>7702001520039</td></tr>
                                <tr><td><strong>B</strong></td><td>Nombre del medicamento</td><td><span class="label label-danger">Requerido</span></td><td>Acetaminofen 500mg Tab x10</td></tr>
                                <tr><td><strong>C</strong></td><td>Proveedor (debe existir en el sistema)</td><td><span class="label label-danger">Requerido</span></td><td>Genfar</td></tr>
                                <tr><td><strong>D</strong></td><td>Categoría (debe existir en el sistema)</td><td><span class="label label-danger">Requerido</span></td><td>Analgesicos</td></tr>
                                <tr><td><strong>E</strong></td><td>Precio de compra (en pesos, sin puntos)</td><td><span class="label label-danger">Requerido</span></td><td>800</td></tr>
                                <tr><td><strong>F</strong></td><td>Precio de venta (en pesos, sin puntos)</td><td><span class="label label-danger">Requerido</span></td><td>1500</td></tr>
                                <tr><td><strong>G</strong></td><td>Stock inicial (cantidad)</td><td><span class="label label-danger">Requerido</span></td><td>200</td></tr>
                                <tr><td><strong>H</strong></td><td>No. de lote</td><td><span class="label label-warning">Recomendado</span></td><td>GF-2024-01</td></tr>
                                <tr><td><strong>I</strong></td><td>Fecha de vencimiento (AAAA-MM-DD)</td><td><span class="label label-warning">Recomendado</span></td><td>2027-06-30</td></tr>
                            </tbody>
                        </table>

                        <form action="php_action/createProductImport.php" method="POST" enctype="multipart/form-data" style="margin-top:20px;">
                            <div class="form-group">
                                <label>Selecciona tu archivo Excel o CSV</label>
                                <input type="file" class="form-control" name="productfile" accept=".csv,.xls,.xlsx" required />
                                <small class="text-muted">Formatos aceptados: .csv, .xls, .xlsx</small>
                            </div>
                            <a href="assets/import/medicinas_plantilla.csv" class="btn btn-default" download>
                                <i class="fa fa-download"></i> Descargar plantilla de ejemplo (CSV)
                            </a>
                            &nbsp;
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-upload"></i> Importar medicamentos
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php'); ?>
