<?php

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


    function AumentarStock($CodProducto, $Cantidad, $Comments) {
      global $db;
      $UserID = $_SESSION['UserIDAdmin'];

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

      $sqlqry = "UPDATE Stock SET Cantidad = Cantidad + $Cantidad, Detalle = '$Comments' WHERE ID = '$IDStock'";
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

    function DescontarStockCombo($IDCombo, $Cantidad, $Comments) {
      global $db;
      $sqlqry = "SELECT IDProducto, Cantidad FROM DetalleCombo WHERE IDCombo = '$IDCombo'";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }
      while ($DBarr = mysqli_fetch_row($DBres)) {
        $cant = $Cantidad * $DBarr[1];
        DescontarStock($DBarr[0], $cant, $Comments." por Combo");
      }
    }

    function AumentarStockCombo($IDCombo, $Cantidad, $Comments) {
      global $db;
      $sqlqry = "SELECT IDProducto, Cantidad FROM DetalleCombo WHERE IDCombo = '$IDCombo'";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
      }
      while ($DBarr = mysqli_fetch_row($DBres)) {
        $cant = $Cantidad * $DBarr[1];
        AumentarStock($DBarr[0], $cant, $Comments." por Combo");
      }
    }
?>
