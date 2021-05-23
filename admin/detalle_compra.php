<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

  if (!isset($_SESSION['UserIDAdmin'])) {
    include_once("login.php");
  }

  include_once("StockFunctions.php");

  function FormatoFecha($fecha) {
    return substr($fecha, 8,2)."-".substr($fecha, 5,2)."-".substr($fecha,0,4);
  }

  if ($Flag != "") {
    //echo "Hacemos cambios";
    $sqlqry = "UPDATE Compra SET Estado = '$Flag' WHERE ID = '$id_compra';";
    mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error: $sqlqry";
    }
    if ($Flag == 2) { // Se confirmó el pedido
      // Debemos hacer descuento de stock en este punto dado que no se hizo al momento de realizar el pedido
      $sqlqry = "SELECT DetalleCompra.Cantidad, DetalleCompra.CodProducto, Producto.IDCategoria FROM DetalleCompra, Producto WHERE DetalleCompra.CodProducto = Producto.ID AND DetalleCompra.IDCompra = '$id_compra'";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "error: $sqlqry";
      }
      while($DBarr = mysqli_fetch_row($DBres)) {
        if ($DBarr[2] != 1) DescontarStock($DBarr[1], $DBarr[0], "Confirmación de Compra N° $id_compra");
        else DescontarStockCombo($DBarr[1], $DBarr[0], "Confirmación de Compra N° $id_compra por Combo");
      }
    }
    else if (($Flag == 1 && $estadoactual == 2) || ($Flag == 3 && $estadoactual == 2)) { // se colocó denuevo para pendiente ($estadonuevo == 1 && $estadoactual == 2) || ($estadonuevo == 3 && $estadoactual == 2)
      // Debemos aumentar el stock de productos
      $sqlqry = "SELECT DetalleCompra.Cantidad, DetalleCompra.CodProducto, Producto.IDCategoria FROM DetalleCompra, Producto WHERE DetalleCompra.CodProducto = Producto.ID AND DetalleCompra.IDCompra = '$id_compra'";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "error: $sqlqry";
      }
      while($DBarr = mysqli_fetch_row($DBres)) {
        if ($DBarr[2] != 1) AumentarStock($DBarr[1], $DBarr[0], "Pendiente de Compra N° $id_compra");
        else AumentarStockCombo($DBarr[1], $DBarr[0], "Pendiente de Compra N° $id_compra por Combo");
      }
    }
  }


  $sqlqry = "SELECT Compra.ID, Compra.Fecha, Cliente.Nombre , Estado.Nombre, Compra.CostoEnvio, DetalleCompra.Cantidad , Producto.Nombre , DetalleCompra.Precio, Cliente.Direccion, Depto.nombre , Provincia.nombre, Cliente.Telefono, Cliente.Email, Estado.ID   FROM Compra, Cliente, Estado, DetalleCompra, Producto,Depto, Provincia WHERE Compra.ID= DetalleCompra.IDCompra AND Compra.CodCliente = Cliente.ID AND Compra.Estado = Estado.ID AND DetalleCompra.CodProducto = Producto.ID AND Cliente.CodDepartamento = Depto.ID AND Depto.idProv = Provincia.ID AND Compra.ID = '$id_compra';";
  //echo $sqlqry;
  $DBres = mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  $MaxItems = 0;
  while($DBarr = mysqli_fetch_row($DBres)) {
    $IDCompra[$MaxItems] = $DBarr[0];
    $FechaCompra[$MaxItems] = $DBarr[1];
    $NombreCliente[$MaxItems] = $DBarr[2];
    $EstadoCompra[$MaxItems] = $DBarr[3];
    $CostoEnvio[$MaxItems] = $DBarr[4];
    $Cantidad[$MaxItems] = $DBarr[5];
    $Articulo[$MaxItems] = $DBarr[6];
    $Precio[$MaxItems] = $DBarr[7];
    $Direccion[$MaxItems] = $DBarr[8];
    $Departamento[$MaxItems] = $DBarr[9];
    $Provincia[$MaxItems] = $DBarr[10];
    $Telefono[$MaxItems] = $DBarr[11];
    $Email[$MaxItems] = $DBarr[12];
    $IDEstado[$MaxItems] = $DBarr[13];
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <!--script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function Regresar() {
        document.forms['DetalleCompra'].action = '<?php echo basename($HTTP_REFERER); ?>';
        document.forms['DetalleCompra'].Flag.value = 0;
        document.forms['DetalleCompra'].submit();
      }
      function CambiarEstado(estado) {
        document.forms['DetalleCompra'].Flag.value = estado;
        document.forms['DetalleCompra'].submit();
      }
    </script>
  </head>
  <body>
    <form name="DetalleCompra" action="detalle_compra.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <input type="hidden" name="estadoactual" value="<?php echo $IDEstado[0] ?>">
      <input type="hidden" name="id_compra" value="<?php echo $id_compra ?>">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag != "") { ?><p id="titulo" style="color:green;">¡Cambios Registrados!</p><?php } ?>

            <div class="col-sm-12">
              <center><p id="titulo">Compra N° <b><?php echo $IDCompra[0] ?></b></p></center>
            </div>
          <div class="encabezado_pedido">

            <div class="row">
              <div class="col-sm-4">
                <p>Fecha: <b><?php echo FormatoFecha($FechaCompra[0]) ?></b></p>
              </div>
              <div class="col-sm-4">
                <p>Estado: <b><?php echo $EstadoCompra[0] ?></b></p>
              </div>

            </div>
            <div class="row">
              <div class="col-sm-4">
                <p>Dirección: <b><?php echo $Direccion[0] ?></b></p>
              </div>
              <div class="col-sm-4">
                <p>Departamento: <b><?php echo $Departamento[0] ?></b></p>
              </div>
              <div class="col-sm-4">
                <p>Provincia: <b><?php echo $Provincia[0] ?></b></p>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <p>Cliente: <b><?php echo $NombreCliente[0] ?></b></p>
              </div>
              <div class="col-sm-4">
                <p>Telefono: <b><?php echo $Telefono[0] ?></b></p>
              </div>
              <div class="col-sm-4">
                <p>Email: <b><?php echo $Email[0] ?></b></p>
              </div>
            </div>
          </div>

          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-2">
                <p>Cantidad</p>
              </div>
              <div class="col-sm-8">
                <p>Producto</p>
              </div>
              <div class="col-sm-2">
                <p>Precio</p>
              </div>
            </div>
          </div>
          <div id="contenido">
            <?php
              $Total = 0;
              for ($i = 0; $i < $MaxItems; $i++) {
                $Total += $Cantidad[$i] * $Precio[$i];
            ?>
              <div class="item_ingreso" >
                <div class="row">
                  <div class="col-sm-2">
                    <center><p style="margin:1em;width:100%;"><?php echo $Cantidad[$i] ?></p></center>
                  </div>
                  <div class="col-sm-8">
                    <center><p  style="margin:1em;width:100%;"><?php echo $Articulo[$i] ?></p></center>
                  </div>
                  <div class="col-sm-2">
                    <center><p  style="margin:1em;width:100%;"><?php echo $Precio[$i] ?></p></center>
                  </div>
                </div>
              </div>
            <?php
            }
            ?>
          </div>
          <div class="pie_pedido">
            <div class="col-sm-3 offset-sm-9">
              <p>SUBTOTAL: $<b><?php echo $Total; $TT = $Total+ $CostoEnvio[0];?></b></p>
            </div>
            <div class="col-sm-3 offset-sm-9">
              <p>Costo de Envío: <b>$ <?php echo $CostoEnvio[0] ?></b></p>
            </div>
            <div class="col-sm-3 offset-sm-9">
              <p>TOTAL: $<b><?php echo $TT ?></b></p>
            </div>
          </div>
          <div class="caja row" style="margin-top:1em;">
            <div class="col-sm-1">
              <input type="button" name="" value="Regresar" class="boton_azul" onclick="javascript:Regresar();">
            </div>
            <?php if($IDEstado[0] == 1 || $IDEstado[0] == 3) { ?>
              <div class="col-sm-1">
                <input type="button" name="" value="Confirmar" class="boton_verde" onclick="javascript:CambiarEstado(2);">
              </div>
            <?php
              }
              if ($IDEstado[0] == 1 || $IDEstado[0] == 2) {
            ?>
              <div class="col-sm-1">
                <input type="button" name="" value="Rechazar" class="boton_rojo" onclick="javascript:CambiarEstado(3);">
              </div>
            <?php
              }
              if ($IDEstado[0] == 3 || $IDEstado[0] == 2) {
            ?>
              <div class="col-sm-1">
                <input type="button" name="" value="Colocar como Pendiente" class="boton_amarillo" onclick="javascript:CambiarEstado(1);">
              </div>
            <?php } ?>
          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
