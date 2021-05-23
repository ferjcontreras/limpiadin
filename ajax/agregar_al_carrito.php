<?php
  include_once("../etc/opendb.php");

  session_start();
  $producto = $_POST['producto'];

  if (isset($_SESSION['carrito'])) { // si está definida, entonces tenemos que buscar el producto y sumarle
    $arreglocarrito = $_SESSION['carrito'];
    $econtro = false;
    for ($i=0; $i<count($arreglocarrito); $i++) {
      if ($producto == $arreglocarrito[$i]['Id']) {
        $encontro = true; //$arreglocarrito['Cantidad'] = $arreglocarrito['Cantidad'] + 1;
        $position = $i;
        break;
      }
    }
    if ($encontro == true) {
      $arreglocarrito[$position]['Cantidad'] = $arreglocarrito[$position]['Cantidad'] + 1;
      $_SESSION['carrito'] = $arreglocarrito;
      echo $arreglocarrito[$position]['Cantidad'];
    } else {
        $sqlqry = "SELECT ID, Nombre, Precio, Foto, IDCategoria FROM Producto WHERE ID = '$producto'";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "error"; // Error
        }
        $DBarr = mysqli_fetch_row($DBres);
        $arreglonuevo = array(
          'Id' => $DBarr[0],
          'Nombre' => $DBarr[1],
          'Precio' => $DBarr[2],
          'Foto' => $DBarr[3],
          'IDCategoria' => $DBarr[4],
          'Cantidad' => 1
        );
        array_push($arreglocarrito, $arreglonuevo);
        $_SESSION['carrito'] = $arreglocarrito;
        echo "1";
    }
  }
  else {
    $sqlqry = "SELECT ID, Nombre, Precio, Foto, IDCategoria FROM Producto WHERE ID = '$producto'";
    //echo $sqlqry;
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error"; // Error
    }
    $DBarr = mysqli_fetch_row($DBres);
    $arreglocarrito[] = array(
      'Id' => $DBarr[0],
      'Nombre' => $DBarr[1],
      'Precio' => $DBarr[2],
      'Foto' => $DBarr[3],
      'IDCategoria' => $DBarr[4],
      'Cantidad' => 1
    );
    $_SESSION['carrito'] = $arreglocarrito;
    echo "1";
  }


?>
