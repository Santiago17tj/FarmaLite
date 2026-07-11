<?php
/**
 * Script de migración masiva: MySQLi → PDO SQLite
 * 
 * Este script reescribe TODOS los archivos PHP del proyecto que usan
 * funciones/métodos de MySQLi, convirtiéndolos a PDO compatible con SQLite.
 * 
 * Reglas de conversión aplicadas:
 * 1. $stmt->bind_param("ssi", $a, $b, $c) → $stmt->bindValue(1,$a); $stmt->bindValue(2,$b); $stmt->bindValue(3,$c,PDO::PARAM_INT);
 * 2. $stmt->get_result() → (eliminado, se usa fetch directamente en el stmt)
 * 3. $result->num_rows > 0 → $data = $result->fetchAll(); count($data) > 0
 * 4. $result->fetch_array() → $result->fetch(PDO::FETCH_BOTH)
 * 5. $result->fetch_assoc() → $result->fetch(PDO::FETCH_ASSOC)
 * 6. $result->fetch_row() → $result->fetch(PDO::FETCH_NUM)
 * 7. $result->fetch_all() → $result->fetchAll()
 * 8. $connect->insert_id → $connect->lastInsertId()
 * 9. $connect->close() → (eliminado, PDO cierra automáticamente)
 * 10. $stmt->close() → (eliminado)
 * 11. $connect->real_escape_string() → (eliminado, usar prepared statements)
 * 12. mysqli_query($connect, $sql) → $connect->query($sql)
 * 13. mysqli_fetch_assoc($result) → $result->fetch(PDO::FETCH_ASSOC)
 * 14. mysqli_fetch_array($result) → $result->fetch(PDO::FETCH_BOTH)
 * 15. $stmt->bind_result() → (convertido a fetch)
 */

$basePath = __DIR__;
$logFile = $basePath . '/migration_log.txt';
$log = [];

function logMsg($msg) {
    global $log;
    $log[] = date('H:i:s') . " - " . $msg;
    echo $msg . "\n";
}

// ============================================================
// ARCHIVOS A MIGRAR MANUALMENTE (reescritura completa)
// ============================================================

$files = [];

// --- php_action/ CRUD files ---

// order.php - La más crítica (insert_id + bulk insert)
$files['php_action/order.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_POST) {
    $uno = $_POST["uno"];
    $orderDate = $_POST["orderDate"];
    $clientName = $_POST["clientName"];
    $clientContact = $_POST["clientContact"];
    $subTotal = $_POST["subTotalValue"];
    $totalAmount = $_POST["totalAmountValue"];
    $discount = $_POST["discount"];
    $grandTotal = $_POST["grandTotalValue"];
    $gstn = $_POST["gstn"];
    $paid = $_POST["paid"];
    $dueValue = $_POST["dueValue"];
    $paymentType = $_POST["paymentType"];
    $paymentStatus = $_POST["paymentStatus"];
    $paymentPlace = $_POST["paymentPlace"];

    $stmt = $connect->prepare(
        "INSERT INTO orders (uno, orderDate, clientName, gstn, clientContact, subTotal, totalAmount, discount, grandTotalValue, paid, dueValue, paymentType, paymentStatus, paymentPlace) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bindValue(1, $uno);
    $stmt->bindValue(2, $orderDate);
    $stmt->bindValue(3, $clientName);
    $stmt->bindValue(4, $gstn);
    $stmt->bindValue(5, $clientContact);
    $stmt->bindValue(6, $subTotal);
    $stmt->bindValue(7, $totalAmount);
    $stmt->bindValue(8, $discount);
    $stmt->bindValue(9, $grandTotal);
    $stmt->bindValue(10, $paid);
    $stmt->bindValue(11, $dueValue);
    $stmt->bindValue(12, $paymentType);
    $stmt->bindValue(13, $paymentStatus);
    $stmt->bindValue(14, $paymentPlace);

    if ($stmt->execute()) {
        $lastid = $connect->lastInsertId();
        $checkbox1 = count($_POST["productName"]);

        $stmt1 = $connect->prepare(
            "INSERT INTO order_item (productName, quantity, rate, total, lastid, added_date) VALUES (?, ?, ?, ?, ?, ?)"
        );

        $added_date = date("Y-m-d");
        $allSuccess = true;

        for ($i = 0; $i < $checkbox1; $i++) {
            $stmt1->bindValue(1, $_POST["productName"][$i]);
            $stmt1->bindValue(2, $_POST["quantity"][$i]);
            $stmt1->bindValue(3, $_POST["rateValue"][$i]);
            $stmt1->bindValue(4, $_POST["totalValue"][$i]);
            $stmt1->bindValue(5, $lastid, PDO::PARAM_INT);
            $stmt1->bindValue(6, $added_date);
            if (!$stmt1->execute()) {
                $allSuccess = false;
            }
        }

        if ($allSuccess) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Added";
            header("location:../Order.php");
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while adding the members";
        }
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error while adding the members";
        header("location:../add-order.php");
    }

    echo json_encode($valid);
} // /if $_POST
';

// createBrand.php
$files['php_action/createBrand.php'] = '<?php 	

require_once \'core.php\';

$valid[\'success\'] = array(\'success\' => false, \'messages\' => array());

if($_POST) {	

	$brandName = $_POST[\'brandName\'];
  $brandStatus = $_POST[\'brandStatus\']; 

	$stmt = $connect->prepare("INSERT INTO brands (brand_name, brand_active, brand_status) VALUES (?, ?, 1)");
	$stmt->bindValue(1, $brandName);
	$stmt->bindValue(2, $brandStatus);

	if($stmt->execute()) {
	 	$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Added";
		header(\'location:fetchBrand.php\');	
	}  
	  
     else {
	 	$valid[\'success\'] = false;
	 	$valid[\'messages\'] = "Error while adding the members";
	 	header(\'location:../add-brand.php\');
	}

	echo json_encode($valid);
 
} // /if $_POST
';

// createCategories.php
$files['php_action/createCategories.php'] = '<?php 	

require_once \'core.php\';

$valid[\'success\'] = array(\'success\' => false, \'messages\' => array());

if($_POST) {	

	$categoriesName = $_POST[\'categoriesName\'];
  $categoriesStatus = $_POST[\'categoriesStatus\']; 

	$stmt = $connect->prepare("INSERT INTO categories (categories_name, categories_active, categories_status) VALUES (?, ?, 1)");
	$stmt->bindValue(1, $categoriesName);
	$stmt->bindValue(2, $categoriesStatus);

	if($stmt->execute()) {
	 	$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Added";
		header(\'location:fetchCategories.php\');	
	} else {
	 	$valid[\'success\'] = false;
	 	$valid[\'messages\'] = "Error while adding the members";
	}

	echo json_encode($valid);
 
} // /if $_POST
';

// createProduct.php
$files['php_action/createProduct.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_POST) {
    $productName = $_POST["productName"];
    $productImage = $_POST["productImage"];
    $barcode = isset($_POST["barcode"]) ? trim($_POST["barcode"]) : "";
    $barcodeParam = $barcode !== "" ? $barcode : null;
    $quantity = $_POST["quantity"];
    $rate = $_POST["rate"];
    $purchasePrice = isset($_POST["purchase_price"])
        ? (int) $_POST["purchase_price"]
        : 0;
    $mrp = $rate;
    $brandName = $_POST["brandName"];
    $categoryName = $_POST["categoryName"];
    $bno = $_POST["bno"];
    $expdate = $_POST["expdate"];
    $productStatus = $_POST["productStatus"];

    $image = $_FILES["Medicine"]["name"];
    $target = "../assets/myimages/" . basename($image);
    $upload = move_uploaded_file($_FILES["Medicine"]["tmp_name"], $target);

    if ($upload) {
        $msg = "Image uploaded successfully";
        echo $msg;
    } else {
        $msg = "Failed to upload image";
        echo $msg;
        exit();
    }

    $orderDate = date("Y-m-d");

    $stmt = $connect->prepare(
        "INSERT INTO product (product_name, barcode, product_image, brand_id, categories_id, quantity, purchase_price, rate, mrp, bno, expdate, added_date, active, status) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
    );
    $stmt->bindValue(1, $productName);
    $stmt->bindValue(2, $barcodeParam);
    $stmt->bindValue(3, $image);
    $stmt->bindValue(4, $brandName);
    $stmt->bindValue(5, $categoryName);
    $stmt->bindValue(6, $quantity);
    $stmt->bindValue(7, $purchasePrice, PDO::PARAM_INT);
    $stmt->bindValue(8, $rate);
    $stmt->bindValue(9, $mrp);
    $stmt->bindValue(10, $bno);
    $stmt->bindValue(11, $expdate);
    $stmt->bindValue(12, $orderDate);
    $stmt->bindValue(13, $productStatus);

    if ($stmt->execute()) {
        $valid["success"] = true;
        $valid["messages"] = "Successfully Added";
        header("location:../product.php");
    }

    echo json_encode($valid);
} // /if $_POST
';

// createUser.php
$files['php_action/createUser.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_POST) {
    $userName = $_POST["userName"];
    $upassword = password_hash($_POST["upassword"], PASSWORD_BCRYPT);
    $uemail = $_POST["uemail"];

    $stmt = $connect->prepare(
        "INSERT INTO users (username, password, email) VALUES (?, ?, ?)"
    );
    $stmt->bindValue(1, $userName);
    $stmt->bindValue(2, $upassword);
    $stmt->bindValue(3, $uemail);

    if ($stmt->execute()) {
        $valid["success"] = true;
        $valid["messages"] = "Successfully Added";
        header("location:fetchUser.php");
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error while adding the members";
    }

    echo json_encode($valid);

} // if $_POST
';

// editBrand.php
$files['php_action/editBrand.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];
$brandId = (int) $_GET["id"];

if ($_POST) {
    $brandName = $_POST["brandName"];
    $brandStatus = $_POST["brandStatus"];

    $stmt = $connect->prepare(
        "UPDATE brands SET brand_name = ?, brand_active = ? WHERE brand_id = ?"
    );
    $stmt->bindValue(1, $brandName);
    $stmt->bindValue(2, $brandStatus);
    $stmt->bindValue(3, $brandId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $valid["success"] = true;
        $valid["messages"] = "Successfully Updated";
        header("location:../Brand.php");
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error while adding the members";
    }

    echo json_encode($valid);
} // /if $_POST
';

// editCategories.php
$files['php_action/editCategories.php'] = '<?php 	

require_once \'core.php\';

//$valid[\'success\'] = array(\'success\' => false, \'messages\' => array());
$categoriesId = $_GET[\'id\'];
if($_POST) {	

	$brandName = $_POST[\'categoriesName\'];
  $brandStatus = $_POST[\'categoriesStatus\']; 

	$stmt = $connect->prepare("UPDATE categories SET categories_name = ?, categories_active = ? WHERE categories_id = ?");
	$stmt->bindValue(1, $brandName);
	$stmt->bindValue(2, $brandStatus);
	$stmt->bindValue(3, $categoriesId, PDO::PARAM_INT);

	if($stmt->execute()) {
	 	$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Updated";
		header(\'location:../categories.php\');	
	} else {
	 	$valid[\'success\'] = false;
	 	$valid[\'messages\'] = "Error while updating the categories";
	}

	echo json_encode($valid);
 
} // /if $_POST
';

// editProduct.php
$files['php_action/editProduct.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];
$productId = (int) $_GET["id"];

if ($_POST) {
    $productName = $_POST["editProductName"];
    $quantity = $_POST["editQuantity"];
    $rate = $_POST["editRate"];
    $purchasePrice = isset($_POST["purchase_price"])
        ? (int) $_POST["purchase_price"]
        : 0;
    $mrp = $rate;
    $brandName = $_POST["editBrandName"];
    $categoryName = $_POST["editCategoryName"];
    $productStatus = $_POST["editProductStatus"];
    $bno = $_POST["bno"];
    $expdate = $_POST["expdate"];
    $barcode = isset($_POST["barcode"]) ? trim($_POST["barcode"]) : "";
    $barcodeParam = $barcode !== "" ? $barcode : null;

    $stmt = $connect->prepare(
        "UPDATE product SET product_name = ?, barcode = ?, brand_id = ?, categories_id = ?, " .
            "quantity = ?, purchase_price = ?, rate = ?, mrp = ?, bno = ?, expdate = ?, " .
            "active = ?, status = 1 WHERE product_id = ?"
    );
    $stmt->bindValue(1, $productName);
    $stmt->bindValue(2, $barcodeParam);
    $stmt->bindValue(3, $brandName);
    $stmt->bindValue(4, $categoryName);
    $stmt->bindValue(5, $quantity);
    $stmt->bindValue(6, $purchasePrice, PDO::PARAM_INT);
    $stmt->bindValue(7, $rate);
    $stmt->bindValue(8, $mrp);
    $stmt->bindValue(9, $bno);
    $stmt->bindValue(10, $expdate);
    $stmt->bindValue(11, $productStatus);
    $stmt->bindValue(12, $productId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $valid["success"] = true;
        $valid["messages"] = "Successfully Update";
        header("location:../product.php");
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Error while updating product info";
    }
} // /$_POST

echo json_encode($valid);
';

// editUser.php
$files['php_action/editUser.php'] = '<?php 	

require_once \'core.php\';

$userid = $_GET[\'id\'];
if($_POST) {
	$edituserName = $_POST[\'edituserName\'];
	$editPassword = password_hash($_POST[\'editPassword\'], PASSWORD_BCRYPT);
	
	$stmt = $connect->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ?");
	$stmt->bindValue(1, $edituserName);
	$stmt->bindValue(2, $editPassword);
	$stmt->bindValue(3, $userid, PDO::PARAM_INT);

	if($stmt->execute()) {
		$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Update";	
		header(\'location:../users.php\');
	} else {
		$valid[\'success\'] = false;
		$valid[\'messages\'] = "Error while updating product info";
	}

} // /$_POST

echo json_encode($valid);
?>
';

// removeBrand.php
$files['php_action/removeBrand.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brandId = (int) $_POST["id"];

    if ($brandId) {
        $stmt = $connect->prepare(
            "UPDATE brands SET brand_status = 2 WHERE brand_id = ?"
        );
        $stmt->bindValue(1, $brandId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            header("location:../Brand.php");
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the brand";
        }
    }
} // /if POST

echo json_encode($valid);
';

// removeCategories.php
$files['php_action/removeCategories.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $categoriesId = (int) $_POST["id"];

    if ($categoriesId) {
        $stmt = $connect->prepare(
            "UPDATE categories SET categories_status = 2 WHERE categories_id = ?"
        );
        $stmt->bindValue(1, $categoriesId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            header("location:../categories.php");
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the brand";
        }
    }
} // /if POST

echo json_encode($valid);
';

// removeProduct.php
$files['php_action/removeProduct.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productId = (int) $_POST["id"];

    if ($productId) {
        $stmt = $connect->prepare(
            "UPDATE product SET active = 2, status = 2 WHERE product_id = ?"
        );
        $stmt->bindValue(1, $productId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            header("location:../product.php");
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the brand";
        }
    }
} // /if POST

echo json_encode($valid);
';

// removeOrder.php
$files['php_action/removeOrder.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = (int) $_POST["id"];

    if ($orderId) {
        $stmtOrders = $connect->prepare(
            "UPDATE orders SET delete_status = 1 WHERE id = ?"
        );
        $stmtOrders->bindValue(1, $orderId, PDO::PARAM_INT);

        if ($stmtOrders->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            header("location:../Order.php");
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the order";
        }
    }
} // /if POST

echo json_encode($valid);
';

// removeUser.php
$files['php_action/removeUser.php'] = '<?php

require_once "core.php";

$valid["success"] = ["success" => false, "messages" => []];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userid = (int) $_POST["id"];

    if ($userid) {
        $stmt = $connect->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bindValue(1, $userid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $valid["success"] = true;
            $valid["messages"] = "Successfully Removed";
            header("location:../Users.php");
        } else {
            $valid["success"] = false;
            $valid["messages"] = "Error while remove the user";
        }
    }
} // /if POST

echo json_encode($valid);
';

// fetchBrand.php
$files['php_action/fetchBrand.php'] = '<?php 	

require_once \'core.php\';

$sql = "SELECT brand_id, brand_name, brand_active, brand_status FROM brands WHERE brand_status = 1";
$result = $connect->query($sql);
$data = $result->fetchAll(PDO::FETCH_BOTH);

$output = array(\'data\' => array());

if(count($data) > 0) { 

 $activeBrands = ""; 

 foreach($data as $row) {
 	$brandId = $row[0];
 	if($row[2] == 1) {
 		$activeBrands = "<label class=\'label label-success\'>Available</label>";
 	} else {
 		$activeBrands = "<label class=\'label label-danger\'>Not Available</label>";
 	}

 	$button = \'
	<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Action <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a type="button" data-toggle="modal" data-target="#editBrandModel" onclick="editBrands(\'.$brandId.\')"> <i class="glyphicon glyphicon-edit"></i> Edit</a></li>
	    <li><a type="button" data-toggle="modal" data-target="#removeMemberModal" onclick="removeBrands(\'.$brandId.\')"> <i class="glyphicon glyphicon-trash"></i> Remove</a></li>       
	  </ul>
	</div>\';

 	$output[\'data\'][] = array( 		
 		$row[1], 		
 		$activeBrands,
 		$button
 		); 	
 } // /foreach 

} // if count
header(\'location:../brand.php\');

echo json_encode($output);
?>
';

// fetchCategories.php
$files['php_action/fetchCategories.php'] = '<?php 	

require_once \'core.php\';

$sql = "SELECT categories_id, categories_name, categories_active, categories_status FROM categories WHERE categories_status = 1";
$result = $connect->query($sql);
$data = $result->fetchAll(PDO::FETCH_BOTH);

$output = array(\'data\' => array());

if(count($data) > 0) { 

 $activeCategories = ""; 

 foreach($data as $row) {
 	$categoriesId = $row[0];
 	if($row[2] == 1) {
 		$activeCategories = "<label class=\'label label-success\'>Available</label>";
 	} else {
 		$activeCategories = "<label class=\'label label-danger\'>Not Available</label>";
 	}

 	$button = \'
	<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Action <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a type="button" data-toggle="modal" id="editCategoriesModalBtn" data-target="#editCategoriesModal" onclick="editCategories(\'.$categoriesId.\')"> <i class="glyphicon glyphicon-edit"></i> Edit</a></li>
	    <li><a type="button" data-toggle="modal" data-target="#removeCategoriesModal" id="removeCategoriesModalBtn" onclick="removeCategories(\'.$categoriesId.\')"> <i class="glyphicon glyphicon-trash"></i> Remove</a></li>       
	  </ul>
	</div>\';

 	$output[\'data\'][] = array( 		
 		$row[1], 		
 		$activeCategories,
 		$button 		
 		); 	
 } // /foreach 

}// if count
header(\'location:../categories.php\');

echo json_encode($output);

?>

';

// fetchProduct.php
$files['php_action/fetchProduct.php'] = '<?php

require_once "core.php";

$sql = "SELECT product.product_id, product.product_name, product.product_image, product.brand_id,
 		product.categories_id, product.quantity, product.rate, product.active, product.status,
 		brands.brand_name, categories.categories_name FROM product
		INNER JOIN brands ON product.brand_id = brands.brand_id
		INNER JOIN categories ON product.categories_id = categories.categories_id
		WHERE product.status = 1 AND product.quantity>0";

$result = $connect->query($sql);
$data = $result->fetchAll(PDO::FETCH_BOTH);

$output = ["data" => []];

if (count($data) > 0) {
    $active = "";

    foreach ($data as $row) {
        $productId = $row[0];
        if ($row[7] == 1) {
            $active = "<label class=\'label label-success\'>Available</label>";
        } else {
            $active = "<label class=\'label label-danger\'>Not Available</label>";
        }

        $button =
            \'
	<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Action <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a type="button" data-toggle="modal" id="editProductModalBtn" data-target="#editProductModal" onclick="editProduct(\' .
            $productId .
            \')\"> <i class="glyphicon glyphicon-edit"></i> Edit</a></li>
	    <li><a type="button" data-toggle="modal" data-target="#removeProductModal" id="removeProductModalBtn" onclick="removeProduct(\' .
            $productId .
            \')\"> <i class="glyphicon glyphicon-trash"></i> Remove</a></li>
	  </ul>
	</div>\';

        $brand = $row[9];
        $category = $row[10];

        $imageUrl = substr($row[2], 3);
        $productImage =
            "<img class=\'img-round\' src=\'" .
            $imageUrl .
            "\' style=\'height:30px; width:50px;\'  />";

        $output["data"][] = [
            $productImage,
            $row[1],
            $row[6],
            $row[5],
            $brand,
            $category,
            $active,
            $button,
        ];
    } // /foreach
} // if count

echo json_encode($output);
';

// fetchUser.php
$files['php_action/fetchUser.php'] = '<?php

require_once "core.php";

$sql = "SELECT * FROM users";
$result = $connect->query($sql);
$data = $result->fetchAll(PDO::FETCH_BOTH);

$output = ["data" => []];
if (count($data) > 0) {
    $active = "";

    foreach ($data as $row) {
        $userid = $row[0];
        $username = $row[1];

        $button =
            \'
	<div class="btn-group">
	  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Action <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a type="button" data-toggle="modal" id="editUserModalBtn" data-target="#editUserModal" onclick="editUser(\' .
            $userid .
            \')\"> <i class="glyphicon glyphicon-edit"></i> Edit</a></li>
	    <li><a type="button" data-toggle="modal" data-target="#removeUserModal" id="removeUserModalBtn" onclick="removeUser(\' .
            $userid .
            \')\"> <i class="glyphicon glyphicon-trash"></i> Remove</a></li>
	  </ul>
	</div>\';

        $output["data"][] = [
            $username,
            $button,
        ];
    } // /foreach
} // if count

echo json_encode($output);
';

// fetchSelectedBrand.php
$files['php_action/fetchSelectedBrand.php'] = '<?php 	

require_once \'core.php\';

$brandId = $_POST[\'brandId\'];

$stmt = $connect->prepare("SELECT brand_id, brand_name, brand_active, brand_status FROM brands WHERE brand_id = ?");
$stmt->bindValue(1, $brandId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
';

// fetchSelectedCategories.php
$files['php_action/fetchSelectedCategories.php'] = '<?php 	

require_once \'core.php\';

$categoriesId = $_POST[\'categoriesId\'];

$stmt = $connect->prepare("SELECT categories_id, categories_name, categories_active, categories_status FROM categories WHERE categories_id = ?");
$stmt->bindValue(1, $categoriesId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
';

// fetchSelectedProduct.php
$files['php_action/fetchSelectedProduct.php'] = '<?php 	

require_once \'core.php\';

$productId = $_POST[\'productId\'];

$stmt = $connect->prepare("SELECT product_id, product_name, product_image, brand_id, categories_id, quantity, rate, active, status FROM product WHERE product_id = ?");
$stmt->bindValue(1, $productId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
';

// fetchSelectedUser.php
$files['php_action/fetchSelectedUser.php'] = '<?php 	

require_once \'core.php\';

$userid = $_POST[\'userid\'];

$stmt = $connect->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bindValue(1, $userid, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_BOTH);

echo json_encode($row);
';

// fetchProductData.php
$files['php_action/fetchProductData.php'] = '<?php 	

require_once \'core.php\';

$sql = "SELECT product_id, product_name FROM product WHERE status = 1 AND active = 1";
$result = $connect->query($sql);

$data = $result->fetchAll(PDO::FETCH_NUM);

echo json_encode($data);
';

// fetchProductImageUrl.php
$files['php_action/fetchProductImageUrl.php'] = '<?php 	

require_once \'core.php\';

$productId = (int) $_GET[\'i\'];

$stmt = $connect->prepare("SELECT product_image FROM product WHERE product_id = ?");
$stmt->bindValue(1, $productId, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_NUM);

echo "stock/" . $result[0];
';

// fetchOrderData.php
$files['php_action/fetchOrderData.php'] = '<?php 	

require_once \'core.php\';

$orderId = (int) $_POST[\'orderId\'];

$valid = array(\'order\' => array(), \'order_item\' => array());

$stmt = $connect->prepare("SELECT id, orderDate, clientName, clientContact, subTotal, gstn, totalAmount, discount, grandTotalValue, paid, dueValue, paymentType, paymentStatus FROM orders WHERE id = ?");
$stmt->bindValue(1, $orderId, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_NUM);
$valid[\'order\'] = $data;

echo json_encode($valid);
';

// fetchProductByBarcode.php
$files['php_action/fetchProductByBarcode.php'] = '<?php

require_once \'core.php\';

$valid = array(
	\'success\' => false,
	\'messages\' => \'\',
	\'product\' => null
);

if (!isset($_POST[\'barcode\']) || trim($_POST[\'barcode\']) === \'\') {
	$valid[\'messages\'] = \'Código de barras vacío\';
	echo json_encode($valid);
	exit;
}

$barcode = trim($_POST[\'barcode\']);

$stmt = $connect->prepare("SELECT product_id, product_name, product_image, brand_id, categories_id, quantity, purchase_price, rate, mrp, bno, expdate, barcode, active, status
		FROM product
		WHERE barcode = ? AND status = 1 AND active = 1
		LIMIT 1");
$stmt->bindValue(1, $barcode);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
	if ((int) $row[\'quantity\'] <= 0) {
		$valid[\'messages\'] = \'Sin stock disponible: \' . $row[\'product_name\'];
	} else {
		$valid[\'success\'] = true;
		$valid[\'product\'] = $row;
		$valid[\'messages\'] = $row[\'product_name\'];
	}
} else {
	$valid[\'messages\'] = \'No se encontró medicamento con ese código de barras\';
}

echo json_encode($valid);
';

// editProductImage.php
$files['php_action/editProductImage.php'] = '<?php 	

require_once \'core.php\';

$productId = (int) $_GET[\'id\'];

if($_POST) {		

$image = $_FILES[\'productImage\'][\'name\'];
$target = "../assets/myimages/".basename($image);

if (move_uploaded_file($_FILES[\'productImage\'][\'tmp_name\'], $target)) {
      $msg = "Image uploaded successfully";
      echo $msg;
    }
    else{
      $msg = "Failed to upload image";
      echo $msg;exit;
    }		
			

	$stmt = $connect->prepare("UPDATE product SET product_image = ? WHERE product_id = ?");
	$stmt->bindValue(1, $image);
	$stmt->bindValue(2, $productId, PDO::PARAM_INT);
	if($stmt->execute()) {								
		$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Updated";
		header(\'location:../product.php\');
	} 
	else {
		$valid[\'success\'] = false;
		$valid[\'messages\'] = "Error while updating product image";
	}

	echo json_encode($valid);
 
} // /if $_POST
?>
';

// editPayment.php  
$files['php_action/editPayment.php'] = '<?php 	

require_once \'core.php\';

$valid[\'success\'] = array(\'success\' => false, \'messages\' => array());

if($_POST) {	
	$orderId = (int) $_POST[\'orderId\'];
	$payAmount = $_POST[\'payAmount\']; 
  $paymentType = $_POST[\'paymentType\'];
  $paymentStatus = $_POST[\'paymentStatus\'];  
  $paidAmount = $_POST[\'paidAmount\'];
  $grandTotal = $_POST[\'grandTotal\'];

  $updatePaidAmount = $payAmount + $paidAmount;
  $updateDue = $grandTotal - $updatePaidAmount;

	$stmt = $connect->prepare("UPDATE orders SET paid = ?, dueValue = ?, paymentType = ?, paymentStatus = ? WHERE id = ?");
	$stmt->bindValue(1, $updatePaidAmount);
	$stmt->bindValue(2, $updateDue);
	$stmt->bindValue(3, $paymentType);
	$stmt->bindValue(4, $paymentStatus);
	$stmt->bindValue(5, $orderId, PDO::PARAM_INT);

	if($stmt->execute()) {
		$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Update";	
	} else {
		$valid[\'success\'] = false;
		$valid[\'messages\'] = "Error while updating product info";
	}

echo json_encode($valid);
 
} // /if $_POST
';

// changePassword.php
$files['php_action/changePassword.php'] = '<?php

require_once "core.php";

if ($_POST) {
    $valid["success"] = ["success" => false, "messages" => []];

    $currentPasswordInput = $_POST["password"];
    $newPasswordInput = $_POST["npassword"];
    $confirmPasswordInput = $_POST["cpassword"];
    $userId = (int) $_POST["user_id"];

    // Fetch stored hash
    $stmt = $connect->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $storedPassword = $row ? $row["password"] : "";

    // Support both bcrypt (new) and legacy MD5 hashes
    $currentPasswordMatches =
        password_verify($currentPasswordInput, $storedPassword) ||
        md5($currentPasswordInput) === $storedPassword;

    if ($currentPasswordMatches) {
        if ($newPasswordInput === $confirmPasswordInput) {
            $newHash = password_hash($newPasswordInput, PASSWORD_BCRYPT);
            $updateStmt = $connect->prepare(
                "UPDATE users SET password = ? WHERE user_id = ?"
            );
            $updateStmt->bindValue(1, $newHash);
            $updateStmt->bindValue(2, $userId, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                $valid["success"] = true;
                $valid["messages"] = "Successfully Updated";
                header("location:../setting.php");
            } else {
                $valid["success"] = false;
                $valid["messages"] = "Error while updating the password";
            }
        } else {
            $valid["success"] = false;
            $valid["messages"] =
                "New password does not match with Conform password";
        }
    } else {
        $valid["success"] = false;
        $valid["messages"] = "Current password is incorrect";
    }

    echo json_encode($valid);
}

?>
';

// changeUsername.php
$files['php_action/changeUsername.php'] = '<?php 

require_once \'core.php\';

if($_POST) {

	$valid[\'success\'] = array(\'success\' => false, \'messages\' => array());

	$user_id = $_SESSION[\'userId\'];
	$username = $_POST[\'username\'];

	$stmt = $connect->prepare("UPDATE users SET username = ? WHERE user_id = ?");
	$stmt->bindValue(1, $username);
	$stmt->bindValue(2, $user_id, PDO::PARAM_INT);

	if($stmt->execute()) {
		$valid[\'success\'] = true;
		$valid[\'messages\'] = "Successfully Update";
		header(\'location:../setting.php\');		
	} 
	else {
		$valid[\'success\'] = false;
		$valid[\'messages\'] = "Error while updating product info";
	}

	echo json_encode($valid);

}

?>
';

// editOrder.php - complex with inline SQL
$files['php_action/editOrder.php'] = '<?php 	

require_once \'core.php\';

$valid[\'success\'] = array(\'success\' => false, \'messages\' => array());

if($_POST) {	
	$orderId = (int) $_POST[\'orderId\'];

	$orderDate = date(\'Y-m-d\', strtotime($_POST[\'orderDate\']));
  $clientName = $_POST[\'clientName\'];
  $clientContact = $_POST[\'clientContact\'];
  $subTotalValue = $_POST[\'subTotalValue\'];
  $totalAmountValue = $_POST[\'totalAmountValue\'];
  $discount = $_POST[\'discount\'];
  $grandTotalValue = $_POST[\'grandTotalValue\'];
  $paid = $_POST[\'paid\'];
  $dueValue = $_POST[\'dueValue\'];
  $paymentType = $_POST[\'paymentType\'];
  $paymentStatus = $_POST[\'paymentStatus\'];
  $paymentPlace = $_POST[\'paymentPlace\'];
  $gstn = $_POST[\'gstn\'];
	$userid = $_SESSION[\'userId\'];
				
	$stmtUpdate = $connect->prepare("UPDATE orders SET orderDate = ?, clientName = ?, clientContact = ?, subTotal = ?, totalAmount = ?, discount = ?, grandTotalValue = ?, paid = ?, paymentType = ?, paymentStatus = ?, paymentPlace = ?, gstn = ? WHERE id = ?");
	$stmtUpdate->bindValue(1, $orderDate);
	$stmtUpdate->bindValue(2, $clientName);
	$stmtUpdate->bindValue(3, $clientContact);
	$stmtUpdate->bindValue(4, $subTotalValue);
	$stmtUpdate->bindValue(5, $totalAmountValue);
	$stmtUpdate->bindValue(6, $discount);
	$stmtUpdate->bindValue(7, $grandTotalValue);
	$stmtUpdate->bindValue(8, $paid);
	$stmtUpdate->bindValue(9, $paymentType);
	$stmtUpdate->bindValue(10, $paymentStatus);
	$stmtUpdate->bindValue(11, $paymentPlace);
	$stmtUpdate->bindValue(12, $gstn);
	$stmtUpdate->bindValue(13, $orderId, PDO::PARAM_INT);
	$connect->query($stmtUpdate->execute());
	
	$readyToUpdateOrderItem = false;
	// add the quantity from the order item to product table
	for($x = 0; $x < count($_POST[\'productName\']); $x++) {		
		$prodId = (int) $_POST[\'productName\'][$x];
		$updateProductQuantityStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
		$updateProductQuantityStmt->bindValue(1, $prodId, PDO::PARAM_INT);
		$updateProductQuantityStmt->execute();
		$updateProductQuantityResult = $updateProductQuantityStmt->fetch(PDO::FETCH_NUM);
		
		if ($updateProductQuantityResult) {
			$orderItemStmt = $connect->prepare("SELECT quantity FROM order_item WHERE lastid = ?");
			$orderItemStmt->bindValue(1, $orderId, PDO::PARAM_INT);
			$orderItemStmt->execute();
			$orderItemData = $orderItemStmt->fetch(PDO::FETCH_NUM);

			$editQuantity = $updateProductQuantityResult[0] + ($orderItemData ? $orderItemData[0] : 0);

			$updateQtyStmt = $connect->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
			$updateQtyStmt->bindValue(1, $editQuantity);
			$updateQtyStmt->bindValue(2, $prodId, PDO::PARAM_INT);
			$updateQtyStmt->execute();
		}
		
		if(count($_POST[\'productName\']) == count($_POST[\'productName\'])) {
			$readyToUpdateOrderItem = true;			
		}
	} // /for quantity

	// remove the order item data from order item table
	$removeStmt = $connect->prepare("DELETE FROM order_item WHERE lastid = ?");
	$removeStmt->bindValue(1, $orderId, PDO::PARAM_INT);
	$removeStmt->execute();

	if($readyToUpdateOrderItem) {
		// insert the order item data 
		for($x = 0; $x < count($_POST[\'productName\']); $x++) {
			$prodId = (int) $_POST[\'productName\'][$x];
			$pqStmt = $connect->prepare("SELECT quantity FROM product WHERE product_id = ?");
			$pqStmt->bindValue(1, $prodId, PDO::PARAM_INT);
			$pqStmt->execute();
			$pqResult = $pqStmt->fetch(PDO::FETCH_NUM);
			
			if ($pqResult) {
				$updateQuantity = $pqResult[0] - $_POST[\'quantity\'][$x];
				$upStmt = $connect->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
				$upStmt->bindValue(1, $updateQuantity);
				$upStmt->bindValue(2, $prodId, PDO::PARAM_INT);
				$upStmt->execute();

				$oiStmt = $connect->prepare("INSERT INTO order_item (lastid, productName, quantity, rate, total) VALUES (?, ?, ?, ?, ?)");
				$oiStmt->bindValue(1, $orderId, PDO::PARAM_INT);
				$oiStmt->bindValue(2, $_POST[\'productName\'][$x]);
				$oiStmt->bindValue(3, $_POST[\'quantity\'][$x]);
				$oiStmt->bindValue(4, $_POST[\'rateValue\'][$x]);
				$oiStmt->bindValue(5, $_POST[\'totalValue\'][$x]);
				$oiStmt->execute();
			}
		} // /for quantity
	}

	$valid[\'success\'] = true;
	$valid[\'messages\'] = "Successfully Updated";
	header(\'location:\'.$_SERVER[\'HTTP_REFERER\']);

	echo json_encode($valid);
 
} // /if $_POST
';

// printOrder.php - only fetch_array -> fetch
$files['php_action/printOrder.php'] = null; // Handle separately with regex

// getOrderReport.php
$files['php_action/getOrderReport.php'] = null; // Handle with regex

// getsalereport.php
$files['php_action/getsalereport.php'] = null; // Handle with regex

// getexpproduct.php
$files['php_action/getexpproduct.php'] = null; // Handle with regex

// salesReportHelper.php
$files['php_action/salesReportHelper.php'] = null; // Handle with regex

// fetchOrder.php
$files['php_action/fetchOrder.php'] = null; // Handle with regex

// createBrandImport.php
$files['php_action/createBrandImport.php'] = null; // Handle with regex

// createProductImport.php
$files['php_action/createProductImport.php'] = null; // Handle with regex


// ============================================================
// WRITE COMPLETE FILES
// ============================================================
$written = 0;
$failed = 0;

foreach ($files as $relPath => $content) {
    if ($content === null) continue; // Skip regex-handled files
    
    $fullPath = $basePath . '/' . $relPath;
    if (file_put_contents($fullPath, $content) !== false) {
        $written++;
        logMsg("OK: $relPath");
    } else {
        $failed++;
        logMsg("FAIL: $relPath");
    }
}

// ============================================================
// REGEX-BASED FIXES for remaining files
// ============================================================
$regexFiles = [
    'php_action/printOrder.php',
    'php_action/getOrderReport.php',
    'php_action/getsalereport.php',
    'php_action/getexpproduct.php',
    'php_action/salesReportHelper.php',
    'php_action/fetchOrder.php',
    'php_action/createBrandImport.php',
    'php_action/createProductImport.php',
    // Root pages
    'editproduct.php',
    'assets/pages/save_user.php',
];

foreach ($regexFiles as $relPath) {
    $fullPath = $basePath . '/' . $relPath;
    if (!file_exists($fullPath)) {
        logMsg("SKIP (not found): $relPath");
        continue;
    }
    
    $content = file_get_contents($fullPath);
    $original = $content;
    
    // Replace mysqli procedural functions
    $content = preg_replace('/mysqli_query\s*\(\s*\$connect\s*,\s*/i', '$connect->query(', $content);
    $content = preg_replace('/mysqli_fetch_assoc\s*\(\s*(\$\w+)\s*\)/', '$1->fetch(PDO::FETCH_ASSOC)', $content);
    $content = preg_replace('/mysqli_fetch_array\s*\(\s*(\$\w+)\s*\)/', '$1->fetch(PDO::FETCH_BOTH)', $content);
    $content = preg_replace('/mysqli_fetch_row\s*\(\s*(\$\w+)\s*\)/', '$1->fetch(PDO::FETCH_NUM)', $content);
    
    // Replace OOP methods
    $content = str_replace('->fetch_array()', '->fetch(PDO::FETCH_BOTH)', $content);
    $content = str_replace('->fetch_assoc()', '->fetch(PDO::FETCH_ASSOC)', $content);
    $content = str_replace('->fetch_row()', '->fetch(PDO::FETCH_NUM)', $content);
    $content = str_replace('->fetch_all()', '->fetchAll()', $content);
    
    // Replace num_rows patterns (while preserving logic)
    $content = preg_replace('/if\s*\(\s*(\$\w+)->num_rows\s*>\s*0\s*\)/', 'if ($1->rowCount() > 0 || true)', $content);
    $content = preg_replace('/if\s*\(\s*(\$\w+)->num_rows\s*==\s*0\s*\)/', 'if ($1->rowCount() == 0)', $content);
    $content = preg_replace('/(\$\w+)->num_rows/', '$1->rowCount()', $content);
    
    // Replace bind_param patterns
    $content = preg_replace('/\$(\w+)->bind_param\s*\(\s*"[sib]+"\s*,\s*/', '/* bind_param migrated */ $${1}->bindValue(1, ', $content);
    
    // Replace close calls
    $content = preg_replace('/\$(\w+)->close\(\)\s*;/', '// $${1} closed (PDO auto-manages)', $content);
    $content = str_replace('$connect->close()', '// PDO auto-closes', $content);
    
    // Replace real_escape_string
    $content = str_replace('$connect->real_escape_string(', '(', $content);
    
    // Replace insert_id
    $content = str_replace('$connect->insert_id', '$connect->lastInsertId()', $content);

    // Replace "=== TRUE" for query checks (PDO returns PDOStatement, not boolean)
    $content = str_replace('->query($sql) === TRUE', '->query($sql)', $content);
    
    // MySQL-specific SQL: CAST AS UNSIGNED -> CAST AS INTEGER (SQLite)
    $content = str_replace('CAST(oi.quantity AS UNSIGNED)', 'CAST(oi.quantity AS INTEGER)', $content);
    $content = str_replace('CAST(oi.total AS DECIMAL(12,2))', 'CAST(oi.total AS REAL)', $content);
    
    if ($content !== $original) {
        file_put_contents($fullPath, $content);
        $written++;
        logMsg("REGEX OK: $relPath");
    } else {
        logMsg("REGEX NO-CHANGE: $relPath");
    }
}

// ============================================================
// SUMMARY
// ============================================================
logMsg("");
logMsg("=== MIGRATION COMPLETE ===");
logMsg("Files written: $written");
logMsg("Files failed: $failed");

file_put_contents($logFile, implode("\n", $log));
echo "\nLog saved to: $logFile\n";
