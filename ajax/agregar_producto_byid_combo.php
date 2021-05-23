<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  $sqlqry = "INSERT INTO DetalleCombo(IDProducto, Cantidad, IDCombo) VALUES('$id_producto', '$cantidad', '$id_combo')";
  mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  echo "ok";
?>
