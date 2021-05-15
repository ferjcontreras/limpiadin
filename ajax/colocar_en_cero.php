<?php
  //include_once("../etc/opendb.php");

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
      if ($arreglocarrito[$position]['Cantidad'] > 0) {
        $arreglocarrito[$position]['Cantidad'] = 0;
        $_SESSION['carrito'] = $arreglocarrito;
      }
    } //else  echo "0";
  }
  //else echo "0";
?>
