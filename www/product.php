<?php
require_once 'constant/check.php'; include "./constant/layout/head.php"; ?>
<?php include "./constant/layout/header.php"; ?>

<?php include "./constant/layout/sidebar.php"; ?>

<?php
include "./constant/connect.php";
$sql = "SELECT p.product_id, p.product_name, p.product_image, p.rate, p.quantity, p.expdate, p.active, p.status, b.brand_name, c.categories_name
        FROM product p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.categories_id = c.categories_id
        WHERE p.status = 1";
$result = $connect->query($sql);

//echo $sql;exit;
?>
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary"> Gestionar Medicinas</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active">Gestionar Medicinas</li>
            </ol>
        </div>
    </div>


    <div class="container-fluid">




        <div class="card">
            <div class="card-body">

                <a href="add-product.php"><button class="btn btn-primary">Agregar Medicina</button></a>

                <div class="table-responsive m-t-40">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th style="width:10%;">Foto</th>

                                <th>Nombre Medicina</th>
                                <th>Cant Por Unidad</th>
                                <th>Cantidad</th>
                                <th>Fabricante</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $row) { ?>
                                <tr>


                                    <td class="text-center"><?php echo $row[
                                        "product_id"
                                    ]; ?></td>
                                    <td><img src="assets/myimages/<?php echo $row[
                                        "product_image"
                                    ]; ?>" style="width: 80px; height: 80px;"></td>


                                    <?php $d1 = date("Y-m-d"); ?>
                                    <?php if ($row["expdate"] >= $d1) { ?>
                                        <td><label class="label label-success"><?php echo htmlspecialchars(
                                            $row["product_name"],
                                        ); ?></label></td>
                                    <?php } else { ?>
                                        <td><label class="label label-danger"><?php echo htmlspecialchars(
                                            $row["product_name"],
                                        ); ?></label></td>
                                    <?php } ?>
                                    <td><?php echo $row["rate"]; ?></td>
                                    <td><?php echo $row["quantity"]; ?></td>
                                    <td><?php echo $row["brand_name"]; ?></td>
                                    <td><?php echo $row[
                                        "categories_name"
                                    ]; ?></td>
                                    <td><?php if ($row["active"] == 1) {
                                        $activeBrands =
                                            "<label class='label label-success' ><h4>Disponible</h4></label>";
                                        echo $activeBrands;
                                    } else {
                                        $activeBrands =
                                            "<label class='label label-danger'><h4>No disponible</h4></label>";
                                        echo $activeBrands;
                                    } ?></td>
                                    <td>

                                        <a href="editproduct.php?id=<?php echo $row[
                                            "product_id"
                                        ]; ?>"><button type="button" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></button></a>



                                        <form method="POST" action="php_action/removeProduct.php" style="display:inline;" onsubmit="return confirm('¿Deseas eliminar este registro?')">
                                            <input type="hidden" name="id" value="<?php echo (int) $row[
                                                "product_id"
                                            ]; ?>">
                                            <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                        </form>


                                    </td>
                                </tr>

                    <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <?php include "./constant/layout/footer.php"; ?>
