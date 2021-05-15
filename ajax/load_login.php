<?php

  include_once("../etc/opendb.php");
  session_start();


  function UsuarioLogueado ($UserID, $UserNm, $UserPf) {
    echo "<p class='usuario_logueado'>¡Bienvenido! <br><b>$UserNm</b></p>";
    echo "<center><input class=\"boton\" type=\"button\"  value=\"¡Ir a Comprar!\" onclick=\"Javascript:CerrarLogin();\" style=\"margin-top:3em;\"></center>";
    echo "<center><input class=\"boton\" type=\"button\"  value=\"Cerrar Sesión\" onclick=\"Javascript:CerrarSession();\" style=\"margin-top:1em;\"></center>";
    echo "<form class=\"\" name=\"Session\">\n";
    echo "  <input type=\"hidden\" name=\"UserID\" value=\"".$_SESSION['UserID']."\">\n";
    echo "  <input type=\"hidden\" name=\"UserNm\" value=\"".$_SESSION['UserNm']."\">\n";
    echo "  <input type=\"hidden\" name=\"UserPf\" value=\"".$_SESSION['UserPf']."\">\n";
    echo "</form>\n";
  }



  $incorrecto = 0;
  if (isset($_SESSION['UserID'])) { // Ya tenemos usuario logueado
    $UserID = $_SESSION['UserID'];
    $UserNm = $_SESSION['UserNm'];
    $UserPf = $_SESSION['UserPf'];

    UsuarioLogueado($UserID, $UserNm, $UserPf);
    die;
  }
  else if (isset($_POST['usuario']) && isset($_POST['clave'])){ // estamos intentando validar el usuario
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $sqlqry = "SELECT ID, Nombre, Perfil FROM Usuario WHERE Usuario = '$usuario' AND Clave = '".md5($clave)."' AND Perfil = 10;";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    if (mysqli_num_rows($DBres) != 0) {
      $DBarr = mysqli_fetch_row($DBres);
      $_SESSION['UserID'] = $DBarr[0];
      $_SESSION['UserNm'] = $DBarr[1];
      $_SESSION['UserPf'] = $DBarr[2];

      UsuarioLogueado($DBarr[0], $DBarr[1], $DBarr[2]);

      die;
    }
    else $incorrecto = 1;
  }

?>
<p class="acceso_titulo">Acceso de Clientes</p>
<form class="" name="Session">
  <input type="hidden" name="UserID" value="<?php echo $_SESSION['UserID'] ?>">
  <input type="hidden" name="UserNm" value="<?php echo $_SESSION['UserNm'] ?>">
  <input type="hidden" name="UserPf" value="<?php echo $_SESSION['UserPf'] ?>">
</form>
<form class="" action="validar.php" method="post" name="Login">
  <center><input type="text" name="usuario" value="" placeholder="Usuario"></center>
  <center><input type="password" name="clave" value="" placeholder="Clave"></center>
  <?php if ($incorrecto == 1) {?>
    <div style="color: red; position: absolute; top:0;margin-top:1em;">¡Usuario Incorrecto!</div>
  <?php } ?>
  <center><a href="#">Olvidé mi contraseña</a></center>
  <center><input class="boton" type="button" name="" value="Acceder" onclick="Javascript:Acceder();"></center>
</form>
