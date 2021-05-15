<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

  if ($usuario != "" && $clave != "") {
    $sqlqry = "SELECT ID, Nombre, Perfil FROM Usuario WHERE Usuario = '$usuario' AND Clave = '".md5($clave)."' AND Perfil <= 9;";
    //$sqlqry = "INSERT INTO Usuario(Nombre, Usuario, Clave, Perfil) VALUES ('Preventista', \"$usuario\", '".md5($clave)."', 5)";
    $DBres = mysqli_query($db, $sqlqry);
    //echo $sqlqry;
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry";
    }
    if (mysqli_num_rows($DBres) != 0) {
      $DBarr = mysqli_fetch_row($DBres);
      $_SESSION['UserIDAdmin'] = $DBarr[0];
      $_SESSION['UserNmAdmin'] = $DBarr[1];
      $_SESSION['UserPfAdmin'] = $DBarr[2];
      $reload = 1;
    }
    else {
      $message = "Usuario y Clave incorrectos";
      $error = 1;
    }
  }

?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Admin</title>
    <meta name="author" content="Fernando Contreras">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="../css/style.css">

    <!-- bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">



    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function LoginIn() {
        if (document.forms['Login'].usuario.value != "" && document.forms['Login'].clave.value != "") {
          document.forms['Login'].submit();
        }
        else alert("Ingrese Usuario y Clave");
      }
      function HacerSubmit() {
        document.forms['Login'].submit();
      }
    </script>
  </head>
  <body <?php if ($reload == 1) echo "onload=HacerSubmit();" ?>>
    <form name="Login" action="index.php" method="post">
      <input type="hidden" name="Flag" value="<?php echo $Flag ?>">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo" >
        <div class="container">
          <?php
            if ($error == 1) {
              echo "<p class=\"col-sm-4 offset-sm-4\">$message</p>\n";
            }
          ?>
          <div class="login_admin col-sm-4 offset-sm-4">
            <input type="text" name="usuario" value="" placeholder="Usuario" class="entrada">
            <input type="password" name="clave" value="" placeholder="Clave" class="entrada">
            <center><input style="margin-top:1em;" type="button" name="" value="Ingresar" class="boton" onclick="javascript:LoginIn();"></center>
          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
<?php die(); ?>
