<?php
    include_once("etc/opendb.php");
    include_once("etc/register_globals.php");
    session_start();

    function InvertirFecha($Fecha) {
      return substr($Fecha,8,2)."/".substr($Fecha, 5,2)."/".substr($Fecha,0,4);
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
        if (mysqli_errno($id)) {
          return "Error al consultar costo de envío";
        }
        $DBarr = mysqli_fetch_row($DBres);
        $distancia = $DBarr[0];


        // Ahora obtenemos el costo de envío de acuerdo a la distancia obtenida
        $sqlqry = "SELECT Distancia,Costo from CostoEnvio WHERE Distancia <= $distancia ORDER BY Distancia DESC LIMIT 1;";
        //return $sqlqry;
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($id)) {
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



      // Estamos Registrando el Pedido
      if ($Flag == 1) {
        // Primero verificamos que el cliente no tenga algun pedido pendiente, el sistema no dejará generar dos pedidos pendientes
        $sqlqry = "SELECT ID, Fecha, CostoEnvio FROM Pedido WHERE CodCliente = '$IDCliente' AND Estado = 1;";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error: $sqlqry";
        }
        if (mysqli_num_rows($DBres) != 0) {
          $error2 = 1;
          $message = "Ya existe un Pedido pendiente para este Cliente, no puede realizar un nuevo pedido hasta que sea confirmado por el vendedor. Si usted no desea recibir este pedido, comuníquese con el distribuidor para su cancelación.";

          $DBarr = mysqli_fetch_row($DBres);
          $IDPedido = $DBarr[0];
          $FechaPedido = $DBarr[1];
          $CostoEnvio = $DBarr[2];

          // Vamos a mostrar también el detalle
          $sqlqry = "SELECT DetallePedido.Cantidad, Producto.Nombre, Producto.Foto, DetallePedido.Precio FROM DetallePedido, Producto WHERE DetallePedido.CodProducto = Producto.ID AND DetallePedido.IDPedido = '$IDPedido';";
          $DBres = mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error: $sqlqry";
          }
          $MaxItems = 0;
          while($DBarr = mysqli_fetch_row($DBres)) {
            $DetalleCantidad[$MaxItems] = $DBarr[0];
            $DetalleProducto[$MaxItems] = $DBarr[1];
            $DetalleFoto[$MaxItems] = $DBarr[2];
            $DetallePrecio[$MaxItems] = $DBarr[3];
            $MaxItems++;
          }
        }
        else { // Si no hay Pedidos Pendientes, entonces acgregamos...
          $Fecha = date('Y-m-d');
          $CostoEnvio = ObtenerCostoEnvio();
          $sqlqry = "INSERT INTO Pedido (Fecha, CodCliente, Estado, CostoEnvio) VALUES ('$Fecha', '$IDCliente', '1', '$CostoEnvio' )";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error al agregar pedido: $sqlqry";
          }
          $IDPedido = mysqli_insert_id($db);
          $arreglocarrito = $_SESSION['carrito'];
          for ($i=0; $i<count($arreglocarrito); $i++) {
            $CodProducto = $arreglocarrito[$i]['Id'];
            $Cantidad = $arreglocarrito[$i]['Cantidad'];
            $PrecioProducto = $arreglocarrito[$i]['Precio'];
            $sqlqry = "INSERT INTO DetallePedido(Cantidad, CodProducto, Precio, IDPedido) VALUES('$Cantidad', '$CodProducto', '$PrecioProducto', '$IDPedido');";
            mysqli_query($db, $sqlqry);
            if (mysqli_errno($db)) {
              echo "Error al agregar pedido: $sqlqry";
            }
          }
          $message = "¡Su Pedido ha sido agregado con éxito! A la brevedad se comunicará un vendedor con Usted.";
        }
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
      function Cancelar() {
        document.forms['Pedido'].action = "index.php";
        document.forms['Pedido'].submit();
      }
      function Enviar() {
        document.forms['Pedido'].action = "realizar_pedido.php";
        document.forms['Pedido'].Flag.value = '1';
        document.forms['Pedido'].submit();
      }
      function Inicio() {
        document.forms['Pedido'].action = "index.php";
        document.forms['Pedido'].Flag.value = '0';
        document.forms['Pedido'].submit();
      }
    </script>
  </head>
  <body>
    <form name="Pedido" action="realizar_pedido.php" method="post">
      <input type="hidden" name="Flag" value="<?php echo $Flag ?>">
      <?php include_once("header.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <p id="titulo">Realizar Pedido</p>
          <?php if ($error != 1) { ?>
            <?php if ($Flag == 0 || empty($Flag)) { ?>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="dato_pedido">
                      <label for="nombre">
                        Nombre:
                        <input type="text" name="nombre" value="<?php echo $NombreCliente ?>" disabled>
                      </label>
                    </div>
                    <div class="dato_pedido">
                      <label for="direccion">
                        Dirección:
                        <input type="text" name="direccion" value="<?php echo $DireccionCliente ?>" disabled>
                      </label>
                    </div>
                    <div class="dato_pedido">
                      <label for="telefono">
                        Teléfono:
                        <input type="number" name="telefono" value="<?php echo $TelefonoCliente ?>" disabled>
                      </label>
                    </div>
                    <div class="dato_pedido">
                      <label for="email">
                        Email:
                        <input type="email" name="email" value="<?php echo $EmailCliente ?>" disabled>
                      </label>
                    </div>
                    <div class="dato_pedido">
                      <label for="provincia">
                        Provincia:
                        <select name="provincia" disabled>
                          <option value="" selected><?php echo $NombreProv ?></option>
                        </select>
                      </label>
                    </div>
                    <div class="dato_pedido">
                      <label for="departamento">
                        Departamento:
                        <select name="departamento" disabled>
                          <option value="" selected><?php echo $NombreDepto ?></option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="enviar_pedido">
                      <?php
                        $costo_de_envio = ObtenerCostoEnvio();
                        $costo_carrito = ObtenerTotalCarrito();
                        $total_compra = $costo_carrito + $costo_de_envio;
                      ?>
                      <p>Subtotal: $<?php echo $costo_carrito ?></p>
                      <p>Costo de Envío: $<?php echo $costo_de_envio ?></p>
                      <p><b>TOTAL: $<?php echo $total_compra ?> </b></p>

                      <?php if ($total_compra > $Minimo) { ?>
                        <div class="botones_pedido">
                          <input class="boton_enviar" type="button" name="" value="Enviar Pedido" onclick="javascript:Enviar();">
                          <input class="boton_enviar" type="button" name="" value="Cancelar Pedido" onclick="javascript:Cancelar();">
                        </div>
                        <p class="aviso_pedido">(Será contactado por un vendedor a la brevedad)</p>
                      <?php } else { ?>
                        <p>USTED NO PUEDE EFECTUAR ESTA COMPRA</p>
                        <p>La totalidad de artículos en el carrito no supera el mínimo de <b>$<?php echo $Minimo ?></b> establecido para el Departamento de <b><?php echo $NombreDepto ?></b> </p>
                      <?php } ?>
                    </div>
                  </div>
                </div>
            <?php } else if ($Flag == 1) {


                    echo $message;
                    if ($error2 == 1) {
                      //echo "Hola toto";
                    ?>
                    <div class="col-sm-8">
                      <div class="cabecera_pedido">
                        <p><b>N° Pedido:</b> <?php echo $IDPedido ?></p>
                        <p><b>Fecha:</b> <?php echo InvertirFecha($FechaPedido); ?></p>
                        <p><b>Costo de Envío:</b> $<?php echo $CostoEnvio ?></p>
                      </div>
                    </div>
                    <div class="detalle_pedido col-sm-8">


                    <?php
                      $TotalPedido = 0;
                      for ($i=0; $i<$MaxItems;$i++) {
                        $TotalPedido = $TotalPedido + $DetalleCantidad[$i]*$DetallePrecio[$i];
                    ?>
                            <div class="item_carrito" style="margin-top:1em;">
                                <p style="margin: auto;"><?php echo $DetalleCantidad[$i] ?></p>
                                <img src="pictures/productos/<?php echo $DetalleFoto[$i] ?>" alt="">
                                <p style="margin:auto;"><?php echo $DetalleProducto[$i] ?></p>
                                <p style="margin:auto;">$ <?php echo $DetallePrecio[$i] ?></p>
                            </div>
                    <?php
                      }
                      $TOTAL = $TotalPedido + $CostoEnvio;
                    ?>
                        <p class="total_pedido">TOTAL (con costo de envío):<b> $<?php echo $TOTAL; ?></b></p>

                    </div>
                    <?php
                    }
                    ?>
                        <div class="botones_pedido" style="margin-top:1em;">
                          <input class="boton_enviar" type="button" name="" value="Ir a Inicio" onclick="javascript:Inicio();">
                        </div>
                    <?php
                  }
            ?>
          <?php } else {
              // aqui se llega cuando no hay usuario logueado al sistema
              echo $message;
          } ?>
        </div> <!-- container -->
      </div> <!-- cuerpo -->
      <?php include_once("footer.php"); ?>
    </form>
  </body>
</html>
