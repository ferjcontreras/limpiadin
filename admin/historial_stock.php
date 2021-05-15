<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

  if (!isset($_SESSION['UserIDAdmin'])) {
    include_once("login.php");
  }


  function FormatoTimeStamp($timestamp) {
    return substr($timestamp, 8,2)."-".substr($timestamp, 5,2)."-".substr($timestamp,0,4)." ".substr($timestamp,11,strlen($timestamp));
  }


  $sqlqry = "SELECT Producto.ID, Producto.Nombre, zHis_Stock.Detalle, zHis_Stock.Cantidad , Usuario.Nombre ,zHis_Stock.UserID , zHis_Stock.inTime FROM Producto LEFT JOIN zHis_Stock ON Producto.ID = zHis_Stock.IDProducto LEFT JOIN Usuario ON zHis_Stock.UserID = Usuario.ID WHERE zHis_Stock.IDProducto  = '$id_producto' ORDER BY inTime DESC;";
  //echo $sqlqry;
  $DBres = mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  $MaxItems = 0;
  while($DBarr = mysqli_fetch_row($DBres)) {
    $IDProducto[$MaxItems] = $DBarr[0];
    $NombreProducto[$MaxItems] = $DBarr[1];
    $DetalleStock[$MaxItems] = $DBarr[2];
    $CantidadStock[$MaxItems] = $DBarr[3];
    $NombreUsuario[$MaxItems] = $DBarr[4];
    $IDUsuario[$MaxItems] = $DBarr[5];
    $inTime[$MaxItems] = $DBarr[6];
    $MaxItems++;
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
      function Regresar() {
        document.forms['Stock'].action = 'stock_articulos.php';
        document.forms['Stock'].Flag.value = 0;
        document.forms['Stock'].submit();
      }
    </script>
  </head>
  <body>
    <form name="Stock" action="stock_articulos.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1 || $Flag == 2) { ?><p id="titulo" style="color:green;">¡Cambios Registrados!</p><?php } ?>
          <p id="titulo">Historial de <b><?php echo $NombreProducto[0] ?></b></p>

          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-4">
                <p>Movimiento</p>
              </div>
              <div class="col-sm-2">
                <p>Cantidad Disponible</p>
              </div>
              <div class="col-sm-4">
                <p>Fecha y Hora</p>
              </div>
              <div class="col-sm-2">
                <p>Usuario</p>
              </div>
            </div>
          </div>
          <div id="contenido">
            <?php
              for ($i = 0; $i < $MaxItems; $i++) {
            ?>
              <div class="item_ingreso" >
                <div class="row">
                  <div class="col-sm-4">
                    <center><p style="margin:1em;width:100%;"><?php echo $DetalleStock[$i] ?></p></center>
                  </div>
                  <div class="col-sm-2">
                    <center><p  style="margin:1em;width:100%;"><?php if ($CantidadStock[$i] != "") echo $CantidadStock[$i]; else echo "0" ?></p></center>
                  </div>
                  <div class="col-sm-4">
                    <center><p  style="margin:1em;width:100%;"><?php echo FormatoTimeStamp($inTime[$i]) ?></p></center>
                  </div>
                  <div class="col-sm-2">
                    <p style="margin:1em;width:100%;"><?php echo $NombreUsuario[$i] ?></p>
                  </div>
                </div>
              </div>
            <?php
            }
            ?>
          </div>
          <div class="row" style="margin-top:1em;">
            <div class="col-sm-2">
              <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:Regresar();">
            </div>

          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
