<?php

    include_once("../etc/opendb.php");

    function AgregarCliente($NombreCliente, $TelefonoCliente, $EmailCliente, $DireccionCliente) {
      global $db;

      $sqlqry = "INSERT INTO Cliente (Nombre, Direccion, Telefono, Email) VALUES ('$NombreCliente', '$DireccionCliente', '$TelefonoCliente', '$EmailCliente');";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "1";
        die;
      }
      return mysqli_insert_id($db);
    }

    function DescontarStock($CodProducto, $Cantidad, $Comments, $Preventista) {
      global $db;
      //$UserID = $_SESSION['UserID'];

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
        $sqlqry = "INSERT INTO Stock (IDProducto, Cantidad, UserID) VALUES('$CodProducto', 0, '$Preventista');";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
        $IDStock = mysqli_insert_id($db);
      }

      $sqlqry = "UPDATE Stock SET Cantidad = Cantidad - $Cantidad, Detalle = '$Comments', UserID = '$Preventista' WHERE ID = '$IDStock'";
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



    $json = file_get_contents('php://input');
    $pedidos = array_values(json_decode($json, true));
    // Los pedidos vienen ordenados por cliente, por lo que consideramos un pedido diferente al cambiar el id de cliente
    $LastIDCliente = 0;

    for ($i = 0; $i < count($pedidos); $i++) {
        $IDPedido = $pedidos[$i]['idpedido'];
        $IDCliente = $pedidos[$i]['idcliente'];
        $NombreCliente = $pedidos[$i]['nombrec'];
        $DireccionCliente = $pedidos[$i]['direccionc'];
        $TelefonoCliente = $pedidos[$i]['telefonoc'];
        $EmailCliente = $pedidos[$i]['emailc'];
        $NuevoCliente = $pedidos[$i]['nuevoc'];
        $IDProducto = $pedidos[$i]['idproducto'];
        $Cantidad = $pedidos[$i]['cantidad'];
        $Precio = $pedidos[$i]['precio'];
        $Preventista = $pedidos[$i]['preventista'];

        if ($LastIDCliente != $IDCliente) { // Estamos ante un pedido nuevo
            if ($NuevoCliente == "1") {
              $IDNewCliente = AgregarCliente($NombreCliente, $TelefonoCliente, $EmailCliente, $DireccionCliente);
            }

            $fecha = date("Y-m-d");
            if ($NuevoCliente == 1) $sqlqry = "INSERT INTO Pedido(Fecha, CodCliente, Estado, Preventista)  VALUES ('$fecha', $IDNewCliente, 2, $Preventista);";
            else $sqlqry = "INSERT INTO Pedido(Fecha, CodCliente, Estado, Preventista)  VALUES ('$fecha', $IDCliente, 2, $Preventista);";
            mysqli_query($db, $sqlqry);
            if (mysqli_errno($db)) {
              echo "1";
              die;
            }
            $IDNewPedido = mysqli_insert_id($db);

        }

        $sqlqry = "INSERT INTO DetallePedido(Cantidad, CodProducto, Precio, IDPedido) VALUES('$Cantidad', '$IDProducto', '$Precio', '$IDNewPedido')";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "1";
          die;
        }


        // Descontamos del Stock dado que ya estamos guardando el pedido como "Confirmado"
        DescontarStock($IDProducto, $Cantidad, "Pedido N° $IDNewPedido - generado por sistema offline", $Preventista);


        $LastIDCliente = $IDCliente;
    }

    echo "0"; // no hubieron errores




    //echo $obj[4]['nombrec'];
    //system("echo '".$obj[0]['nombrec']."' > toto.txt");
?>
