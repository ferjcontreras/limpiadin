<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  $sqlqry = "INSERT INTO DetalleComboTemp(IDProducto, Cantidad) VALUES('$id_producto', '$cantidad')";
  mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  echo "ok";
?>
