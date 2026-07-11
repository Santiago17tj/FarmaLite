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
                        <p>Carga masiva desde Excel o CSV. Usa el <strong>código EAN-13 del empaque</strong> como identificador principal.</p>

                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Columna</th>
                                    <th>Campo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>A</td><td>Código de barras (EAN-13)</td></tr>
                                <tr><td>B</td><td>Nombre medicina</td></tr>
                                <tr><td>C</td><td>Proveedor (debe existir en el sistema)</td></tr>
                                <tr><td>D</td><td>Categoría (debe existir en el sistema)</td></tr>
                                <tr><td>E</td><td>Precio de compra</td></tr>
                                <tr><td>F</td><td>Precio de venta</td></tr>
                                <tr><td>G</td><td>Stock inicial</td></tr>
                                <tr><td>H</td><td>No. de lote</td></tr>
                                <tr><td>I</td><td>Fecha vencimiento (AAAA-MM-DD)</td></tr>
                            </tbody>
                        </table>

                        <form action="php_action/createProductImport.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Archivo Excel o CSV</label>
                                <input type="file" class="form-control" name="productfile" accept=".csv,.xls,.xlsx" required />
                            </div>
                            <a href="assets/import/medicinas_plantilla.csv" class="btn btn-link" download>Descargar plantilla CSV</a>
                            <br><br>
                            <button type="submit" class="btn btn-success">Importar medicinas</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./constant/layout/footer.php'); ?>
