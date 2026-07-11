<?php
ob_start();
session_start();
include "./constant/layout/head.php";
include "./constant/connect.php";
?>
<link rel="stylesheet" href="assets/css/popup_style.css">
<style>
  .footer1 {
    position: fixed;
    bottom: 0;
    width: 100%;
    color: #5c4ac7;
    text-align: center;
  }
</style>
<?php

function verifyPassword($inputPassword, $storedHash, $connect, $userId)
{
    // Try modern bcrypt first
    if (password_verify($inputPassword, $storedHash)) {
        return true;
    }
    // Fall back to legacy MD5 and upgrade hash on success
    if (md5($inputPassword) === $storedHash) {
        $newHash = password_hash($inputPassword, PASSWORD_BCRYPT);
        $stmt = $connect->prepare(
            "UPDATE users SET password = ? WHERE user_id = ?"
        );
        $stmt->bindValue(1, $newHash);
        $stmt->bindValue(2, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }
    return false;
}

if (isset($_SESSION["userId"])) {
    header("location: dashboard.php");
    exit();
}

$errors = [];

if ($_POST) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        if ($email == "") {
            $errors[] = "email is required";
        }

        if ($password == "") {
            $errors[] = "Password is required";
        }
    } else {
        $stmt = $connect->prepare(
            "SELECT user_id, password FROM users WHERE email = ?"
        );
        $stmt->bindValue(1, $email);
        $stmt->execute();
        $value = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($value) {

            if (
                verifyPassword(
                    $password,
                    $value["password"],
                    $connect,
                    $value["user_id"],
                )
            ) {

                // set session
                session_regenerate_id(true);
                $_SESSION["userId"] = $value["user_id"];
                ?>



        <div class="popup popup--icon -success js_success-popup popup--visible">
          <div class="popup__background"></div>
          <div class="popup__content">
            <h3 class="popup__content__title">
              Login
              </h3>
              <p>Acceso Exitoso</p>
              <p>

                <?php echo "<script>setTimeout(\"location.href = 'dashboard.php';\",1500);</script>"; ?>
              </p>
          </div>
        </div>
      <?php
            } else {
                 ?>


        <div class="popup popup--icon -error js_error-popup popup--visible">
          <div class="popup__background"></div>
          <div class="popup__content">
            <h3 class="popup__content__title">
              Error
              </h3>
              <p>Correo o Contraseña Incorrectos</p>
              <p>
                <a href="login.php"><button class="button button--error" data-for="js_error-popup">Close</button></a>
              </p>
          </div>
        </div>

      <?php
            } // /else
        } else {
?>
      <div class="popup popup--icon -error js_error-popup popup--visible">
        <div class="popup__background"></div>
        <div class="popup__content">
          <h3 class="popup__content__title">
            Error
            </h3>
            <p>Correo no existe</p>
            <p>
              <a href="login.php"><button class="button button--error" data-for="js_error-popup">Cerrar</button></a>
            </p>
        </div>
      </div>

<?php
        } // /else
    } // /else not empty email // password
}

// /if $_POST
?>

<div id="main-wrapper" style="background: linear-gradient(135deg, var(--primary-light), var(--surface)); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
  <div class="unix-login" style="width: 100%;">
    <div class="container-fluid">
      <div class="row justify-content-center align-items-center">
        <div class="col-md-7 col-lg-5 col-xl-4">
          <div class="login-content" style="background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(15,98,254,0.10);padding:48px 44px 40px;">
            <div class="login-form">
              <center><img src="./assets/runtime/logo.png" style="height: 120px; width: auto; margin-bottom: 32px; object-fit: contain;"></center>
              <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="loginForm" class="row">
                <div class="form-group col-md-12 mb-4">
                  <label class="control-label" style="font-weight: 600; font-size:15px; color: var(--text-main);">Correo Electrónico</label>
                  <input type="email" name="email" id="email" class="form-control mt-1" style="height:48px;font-size:15px;" placeholder="ejemplo@correo.com" required="">
                </div>
                <div class="form-group col-md-12 mb-4">
                  <label class="control-label" style="font-weight: 600; font-size:15px; color: var(--text-main);">Contraseña</label>
                  <input type="password" id="password" name="password" class="form-control mt-1" style="height:48px;font-size:15px;" placeholder="••••••••" required="">
                </div>
                <div class="col-md-12 mt-2">
                  <button type="submit" name="login" class="btn btn-primary text-white w-100" style="height:50px;font-size:16px;font-weight:700;border-radius:8px;letter-spacing:0.5px;">INGRESAR AL SISTEMA</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<script src="./assets/js/lib/jquery/jquery.min.js"></script>

<script src="./assets/js/lib/bootstrap/js/popper.min.js"></script>
<script src="./assets/js/lib/bootstrap/js/bootstrap.min.js"></script>

<script src="./assets/js/jquery.slimscroll.js"></script>

<script src="./assets/js/sidebarmenu.js"></script>

<script src="./assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>

<script src="./assets/js/custom.min.js"></script>



</div>
</body>

</html>
