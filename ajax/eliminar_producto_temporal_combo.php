<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  $sqlqry = "DELETE FROM DetalleComboTemp WHERE IDProducto = '$id_producto';";
  mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  echo "ok";
?>
