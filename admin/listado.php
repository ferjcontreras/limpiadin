<?php
    // Aqui determinamos si se trata de una sola hoja o si tenemos que imprimir mas...

    include_once("../etc/opendb.php");
    include_once("../etc/register_globals.php");


    session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      die;
    }


      $arreglopedidos = $_SESSION['pedidos'];

      for ($j = 0; $j<count($arreglopedidos); $j++) {
        $tipo = $arreglopedidos[$j]['tipo'];
        $id_comprobante = $arreglopedidos[$j]['id_comprobante'];

        if ($tipo == 'c') {
          $sqlqry = "SELECT Compra.ID, Compra.Fecha, Cliente.Nombre , Estado.Nombre, Compra.CostoEnvio, DetalleCompra.Cantidad , Producto.Nombre , DetalleCompra.Precio, Cliente.Direccion, Depto.nombre , Provincia.nombre, Cliente.Telefono, Cliente.Email, Estado.ID   FROM Compra, Cliente, Estado, DetalleCompra, Producto,Depto, Provincia WHERE Compra.ID= DetalleCompra.IDCompra AND Compra.CodCliente = Cliente.ID AND Compra.Estado = Estado.ID AND DetalleCompra.CodProducto = Producto.ID AND Cliente.CodDepartamento = Depto.ID AND Depto.idProv = Provincia.ID AND Compra.ID = '$id_comprobante';";
        }
        else if ($tipo == "p") {
          $sqlqry = "SELECT Pedido.ID, Pedido.Fecha, Cliente.Nombre , Estado.Nombre, Pedido.CostoEnvio, DetallePedido.Cantidad , Producto.Nombre , DetallePedido.Precio, Cliente.Direccion, Depto.nombre , Provincia.nombre, Cliente.Telefono, Cliente.Email, Estado.ID   FROM Pedido LEFT JOIN Cliente ON Pedido.CodCliente = Cliente.ID LEFT JOIN Estado ON Pedido.Estado = Estado.ID LEFT JOIN DetallePedido ON Pedido.ID = DetallePedido.IDPedido LEFT JOIN Producto ON DetallePedido.CodProducto = Producto.ID LEFT JOIN Depto ON Cliente.CodDepartamento = Depto.ID LEFT JOIN Provincia ON Provincia.ID = Depto.idProv WHERE Pedido.ID = '$id_comprobante';";
        }
        $DBres = mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "error";
          }
          $MaxItems[$j] = 0;
          while($DBarr = mysqli_fetch_row($DBres)) {
            $IDComp[$j][$MaxItems[$j]] = $DBarr[0];
            $Fecha[$j][$MaxItems[$j]] = $DBarr[1];
            $NombreCliente[$j][$MaxItems[$j]] = $DBarr[2];
            $Estado[$j][$MaxItems[$j]] = $DBarr[3];
            $CostoEnvio[$j][$MaxItems[$j]] = $DBarr[4];
            $Cantidad[$j][$MaxItems[$j]] = $DBarr[5];
            $Articulo[$j][$MaxItems[$j]] = $DBarr[6];
            $Precio[$j][$MaxItems[$j]] = $DBarr[7];
            $Direccion[$j][$MaxItems[$j]] = $DBarr[8];
            $Departamento[$j][$MaxItems[$j]] = $DBarr[9];
            $Provincia[$j][$MaxItems[$j]] = $DBarr[10];
            $Telefono[$j][$MaxItems[$j]] = $DBarr[11];
            $Email[$j][$MaxItems[$j]] = $DBarr[12];
            $IDEstado[$j][$MaxItems[$j]] = $DBarr[13];
            $MaxItems[$j]++;
          }
      }




?>

<!DOCTYPE HTML>
<html lang="es">
    <head>
          <meta charset="UTF-8"/>
          <title>Generar PDF con PHP</title>
          <style type="text/css">
            #cabecera{
                background:#eee;
                padding:20px;
            }
            h2,h3{
                float:center;
            }
            table{
              width: 100%;
            }
          </style>
    </head>
    <body>
      <?php
        for ($j=0; $j < count($arreglopedidos) ; $j++) {
          $SubTotal = 0;
      ?>
        <!--page-->
          <div id="cabecera">
              <h2><?php echo $NombreCliente[$j][0] ?></h2>
              <h3><?php echo $Direccion[$j][0] ?>, <?php echo $Departamento[$j][0] ?>, <?php echo $Provincia[$j][0] ?></h3>
              <h4><?php echo $Telefono[$j][0] ?> - <?php echo $Email[$j][0] ?></h4>
          </div>
          <table>
            <tr>
              <th width="100">Cantidad</th>
              <th>Detalle</th>
              <th width="100">Precio U</th>
              <th width="100">Precio Total</th>
            </tr>
            <?php for ($i=0; $i < $MaxItems[$j] ; $i++) { ?>

              <tr>
                <td><?php echo $Cantidad[$j][$i] ?></td>
                <td><?php echo $Articulo[$j][$i] ?></td>
                <td><?php echo $Precio[$j][$i] ?></td>
                <td>
                  <?php
                      $PrecioTotal = $Cantidad[$j][$i] * $Precio[$j][$i];
                      $SubTotal += $PrecioTotal;
                      echo $PrecioTotal;
                  ?>
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td colspan="4"><hr></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>SUBTOTAL</td>
              <td><?php echo $SubTotal ?></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>COSTO DE ENVÍO</td>
              <td><?php echo $CostoEnvio[$j][0] ?></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>TOTAL</td>
              <td>
                <?php
                  $Total = $CostoEnvio[$j][0] + $SubTotal;
                  echo $Total;
                ?>
              </td>
            </tr>
          </table>
        <!--/page-->
      <?php
        }
        unset($_SESSION['pedidos']);
        //$_SESSION['carrito'] = "";
        //session_unset();
      ?>

    </body>
</html>
