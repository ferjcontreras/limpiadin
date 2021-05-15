<?php
    include_once("etc/opendb.php");
    include_once("etc/register_globals.php");
    session_start();

    function InvertirFecha($Fecha) {
      return substr($Fecha,8,2)."/".substr($Fecha, 5,2)."/".substr($Fecha,0,4);
    }

    function DescontarStock($CodProducto, $Cantidad, $Comments) {
      global $db;
      $UserID = $_SESSION['UserID'];

      // Primero debemos verificar que exista el registro de Stock para ese producto
      $sqlqry = "SELECT ID FROM Stock WHERE IDProducto = '$CodProducto';";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }
      if (mysqli_num_rows($DBres) != 0) {
        $DBarr = mysqli_fetch_row($DBres);
        $IDStock = $DBarr[0];
      }
      else {
        // Tenemos que agregar el registro de Stock
        $sqlqry = "INSERT INTO Stock (IDProducto, Cantidad, UserID) VALUES('$CodProducto', 0, '$UserID');";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
        $IDStock = mysqli_insert_id($db);
      }

      $sqlqry = "UPDATE Stock SET Cantidad = Cantidad - $Cantidad, Detalle = '$Comments' WHERE ID = '$IDStock'";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }

      // Insertamos el registro de zHis_Stock
      $sqlqry = "INSERT INTO zHis_Stock SELECT * FROM Stock WHERE ID = '$IDStock'";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }
    }



    function ObtenerTotalCarrito(){
      if (isset($_SESSION['carrito'])) {
        $arreglocarrito = $_SESSION['carrito'];
        $total = 0;
        for ($i = 0; $i < count($arreglocarrito); $i++) {
          $total = $total + $arreglocarrito[$i]['Cantidad'] * $arreglocarrito[$i]['Precio'];
        }
        return $total;
      }
      else return 0;
    }

    function ObtenerCostoEnvio() {
      global $db;
      // Obtiene el costo de envío deacuerdo a la ubicación del cliente
      if (isset($_SESSION['UserID'])){
        // Obtenemos el valor de la distancia del cliente
        $UserID = $_SESSION['UserID'];
        $sqlqry = "SELECT Distancia FROM Cliente WHERE IDUsuario = '$UserID'";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          return "Error al consultar costo de envío";
        }
        $DBarr = mysqli_fetch_row($DBres);
        $distancia = $DBarr[0];


        // Ahora obtenemos el costo de envío de acuerdo a la distancia obtenida
        $sqlqry = "SELECT Distancia,Costo from CostoEnvio WHERE Distancia <= $distancia ORDER BY Distancia DESC LIMIT 1;";
        //return $sqlqry;
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          return "Error al consultar costo de envío";
        }
        $DBarr = mysqli_fetch_row($DBres);
        return $DBarr[1];
      }
      return "No se pudo obtener información del usuario";
    }

    if (!isset($_SESSION['UserID'])) {
      $error = 1;
      $message =  "Debe iniciar sesión para poder operar en esta sección";
    }
    else {
      // Obtenemos los datos del cliente
      $UserID = $_SESSION['UserID'];
      $sqlqry = "SELECT Cliente.ID, Cliente.Nombre, Cliente.Direccion, Cliente.Telefono, Cliente.Email, Depto.ID, Provincia.ID, Depto.Nombre, Provincia.Nombre, Minimo.Monto FROM Cliente, Provincia, Depto, Minimo WHERE Cliente.CodDepartamento = Depto.ID AND Depto.idProv = Provincia.ID AND Minimo.IdDepartamento = Depto.ID AND Cliente.IDUsuario = '$UserID';";
      //echo $sqlqry;
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }
      $DBarr = mysqli_fetch_row($DBres);
      $IDCliente = $DBarr[0];
      $NombreCliente = $DBarr[1];
      $DireccionCliente = $DBarr[2];
      $TelefonoCliente = $DBarr[3];
      $EmailCliente = $DBarr[4];
      $IDDepto = $DBarr[5];
      $IDProv = $DBarr[6];
      $NombreDepto = $DBarr[7];
      $NombreProv = $DBarr[8];
      $Minimo = $DBarr[9];


      // Vamos a ver si ya existe el registro de esta compra
      $sqlqry = "SELECT ID FROM Compra WHERE payment_id = '$payment_id'";
      //echo $sqlqry;
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }
      if (mysqli_num_rows($DBres) == 0) { // Si no hay compras con ese payment_id...
        // Aqui registramos la compra
        $Fecha = date('Y-m-d');
        $CostoEnvio = ObtenerCostoEnvio();
        $sqlqry = "INSERT INTO Compra(Fecha, CodCliente, Estado, CostoEnvio, payment_id) VALUES('$Fecha', '$IDCliente', '2', '$CostoEnvio', '$payment_id');";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          //echo "Error al registrar compra: $sqlqry";
          $error = 1;
          $message = "Error al registrar compra: $sqlqry";
        }
        $IDCompra = mysqli_insert_id($db);
        $arreglocarrito = $_SESSION['carrito'];
        for ($i=0; $i<count($arreglocarrito); $i++) {
          $CodProducto = $arreglocarrito[$i]['Id'];
          $Cantidad = $arreglocarrito[$i]['Cantidad'];
          $PrecioProducto = $arreglocarrito[$i]['Precio'];
          $sqlqry = "INSERT INTO DetalleCompra(Cantidad, CodProducto, Precio, IDCompra) VALUES('$Cantidad', '$CodProducto', '$PrecioProducto', '$IDCompra');";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            $error = 1;
            $message = "Error al registrar compra: $sqlqry";
          }

          // Descontar Stock
          DescontarStock($CodProducto, $Cantidad, "Compra a través del sitio N°Compra: $IDCompra");
        }
      }
      else {
        $error = 1;
        $message = "Está intentando registrar una compra ya registrada payment_id: $payment_id";
      }
    }


?>



<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Home</title>
    <meta name="author" content="Fernando Contreras">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/style.css">

    <!-- bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet"/>


    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <!-- Mercado Pago -->
    <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>


    <script type="text/javascript">
      function Inicio() {
        document.forms['Compra'].action = "index.php";
        document.forms['Compra'].submit();
      }
    </script>
  </head>
  <body>
    <form name="Compra" action="index.php" method="post">
      <?php include_once("header.php"); ?>
      <div class="cuerpo">
        <div class="container">

          <?php if ($error != 1) { ?>
            <p id="titulo" style="color:green;">¡Compra Realizada!</p>
            <p>Usted acaba de registrar una compra. En breve, un vendedor se comunicará para coordinar el envío.</p>
            <p class="titulos">Datos de Facturación</p>
            <p><b>Nombre: </b><?php echo $NombreCliente ?></p>
            <p><b>Dirección de Envío: </b><?php echo "$DireccionCliente, $NombreDepto, $NombreProv" ?></p>
            <p><b>Teléfono: </b><?php echo $TelefonoCliente ?></p>
            <p><b>Email: </b><?php echo $EmailCliente ?></p>
            <div class="botones_pedido" style="margin-top:1em;">
              <input class="boton_enviar" type="button" name="" value="Ir a Inicio" onclick="javascript:Inicio();">
            </div>
          <?php } else {
              // aqui se llega cuando no hay usuario logueado al sistema
              echo $message;
                ?>
                <div class="botones_pedido" style="margin-top:1em;">
                  <input class="boton_enviar" type="button" name="" value="Ir a Inicio" onclick="javascript:Inicio();">
                </div>
              <?php
                } ?>

        </div> <!-- container -->
      </div> <!-- cuerpo -->
      <?php include_once("footer.php"); ?>
    </form>
  </body>
</html>
