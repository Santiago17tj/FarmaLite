<?php
require_once 'constant/check.php'; include "./constant/layout/head.php"; ?>
<?php include "./constant/layout/header.php"; ?>

<?php include "./constant/layout/sidebar.php"; ?>



<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Agregar Medicina</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active">Agregar Medicina</li>
            </ol>
        </div>
    </div>


    <div class="container-fluid">




        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-title">

                    </div>
                    <div id="add-brand-messages"></div>
                    <div class="card-body">
                        <div class="input-states">
                            <form class="row" method="POST" id="submitProductForm" action="php_action/createProduct.php" enctype="multipart/form-data">

                                <input type="hidden" name="currnt_date" class="form-control">

                                <div class="form-group col-md-6">
                                    <label class="control-label">Imagen Medicina:</label>
                                    <div id="kv-avatar-errors-1" class="center-block" style="display:none;"></div>
                                    <div class="kv-avatar center-block">
                                        <input type="file" class="form-control" id="MedicineImage" placeholder="Nombre Medicina" name="Medicine" class="file-loading">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Nombre Medicina</label>
                                    <input type="text" class="form-control" id="productName" placeholder="Nombre Medicina" name="productName" autocomplete="off" required="" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Código de barras</label>
                                    <input type="text" class="form-control" id="barcode" placeholder="Escanear o escribir código EAN" name="barcode" autocomplete="off" />
                                    <small class="text-muted">Escanea con el lector USB al registrar el medicamento</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Cantidad en stock</label>
                                    <input type="text" class="form-control" id="quantity" placeholder="Cantidad" name="quantity" autocomplete="off" required="" pattern="^[0-9]+$" />
                                </div>

                                <div class="col-md-12"><hr><h4 class="text-primary">Precios y ganancia</h4></div>

                                <div class="form-group col-md-6">
                                    <label class="control-label">Precio de compra</label>
                                    <input type="number" min="0" step="1" class="form-control" id="purchase_price" name="purchase_price" placeholder="Costo del proveedor" autocomplete="off" value="0" />
                                    <small class="text-muted">A cuánto te sale el medicamento (lo que pagas)</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Precio de venta</label>
                                    <input type="number" min="0" step="1" class="form-control" id="rate" placeholder="Precio al cliente" name="rate" autocomplete="off" required="" />
                                    <small class="text-muted">Precio que cobras al cliente en la factura</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Ganancia por unidad</label>
                                    <input type="text" class="form-control" id="profitDisplay" readonly style="font-weight:bold;" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Margen de ganancia</label>
                                    <input type="text" class="form-control" id="marginDisplay" readonly style="font-weight:bold;" />
                                </div>

                                <div class="col-md-12"><hr></div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">No de Lote</label>
                                    <input type="text" class="form-control" id="Batch No" placeholder="Batch No" name="bno" autocomplete="off" required="" pattern="^[a-zA-Z0-9-]+$" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Fecha Expiración</label>
                                    <input type="date" class="form-control" id="expdate" placeholder="Expiry Date" name="expdate" autocomplete="off" required="" pattern="^[0-9]+$" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Nombre Proveedor</label>
                                    <select class="form-control" id="brandName" name="brandName">
                                        <option value="">~~Seleccionar~~</option>
                                        <?php
                                        $sql =
                                            "SELECT brand_id, brand_name, brand_active, brand_status FROM brands WHERE brand_status = 1 AND brand_active = 1";
                                        $result = $connect->query($sql);
                                        while ($row = $result->fetch(PDO::FETCH_BOTH)) {
                                            echo "<option value='" .
                                                $row[0] .
                                                "'>" .
                                                $row[1] .
                                                "</option>";
                                        }

// while
?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">

                                    <label class="control-label">Nombre Categoría</label>
                                    <select type="text" class="form-control" id="categoryName" name="categoryName">
                                        <option value="">~~Seleccionar~~</option>
                                        <?php
                                        $sql =
                                            "SELECT categories_id, categories_name, categories_active, categories_status FROM categories WHERE categories_status = 1 AND categories_active = 1";
                                        $result = $connect->query($sql);
                                        while ($row = $result->fetch(PDO::FETCH_BOTH)) {
                                            echo "<option value='" .
                                                $row[0] .
                                                "'>" .
                                                $row[1] .
                                                "</option>";
                                        }

// while
?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Estado</label>
                                    <select class="form-control" id="productStatus" name="productStatus">
                                        <option value="">~~Seleccionar~~</option>
                                        <option value="1">Disponible</option>
                                        <option value="2">No disponible</option>
                                    </select>
                                </div>

                                <div class="col-md-1 mx-auto">
                                    <button type="submit" name="create" id="createProductBtn" class="btn btn-primary btn-flat m-b-30 m-t-30">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>





        <?php include "./constant/layout/footer.php"; ?>

        <script src="custom/js/product-pricing.js"></script>
