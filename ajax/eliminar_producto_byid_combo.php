<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  $sqlqry = "DELETE FROM DetalleCombo WHERE IDProducto = '$id_producto' AND IDCombo = '$id_combo';";
  mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  echo "ok";
?>
