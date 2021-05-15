<?php
  $db = mysqli_connect("localhost", "limpiadin", "limpiadin", "limpiadin");
  //$db = mysqli_connect("localhost", "c1970902_inedita", "mu11vuWUra", "c1970902_inedita");

  if (!$db) {
      echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
      echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
      echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
      exit;
  }
?>
