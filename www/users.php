<?php
require_once 'constant/check.php'; include "./constant/layout/head.php"; ?>
<?php include "./constant/layout/header.php"; ?>

<?php include "./constant/layout/sidebar.php"; ?>

<?php
include "./constant/connect";
$sql = "SELECT * FROM users";
$result = $connect->query($sql);

//echo $sql;exit;
?>
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary"> Ver Usuarios</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active">Ver Usuarios</li>
            </ol>
        </div>
    </div>


    <div class="container-fluid">



        <div class="card">
            <div class="card-body">

                <a href="add-user.php"><button class="btn btn-primary">Agregar Usuario</button></a>

                <div class="table-responsive m-t-40">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre de Usuario</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $row) { ?>
                                <tr>
                                    <td><?php echo $row["user_id"]; ?></td>
                                    <td><?php echo $row["username"]; ?></td>

                                    <td>

                                        <a href="edituser.php?id=<?php echo $row[
                                            "user_id"
                                        ]; ?>"><button type="button" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></button></a>



                                        <form method="POST" action="php_action/removeUser.php" style="display:inline;" onsubmit="return confirm('¿Deseas eliminar este registro?')">
                                            <input type="hidden" name="id" value="<?php echo (int) $row[
                                                "user_id"
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
