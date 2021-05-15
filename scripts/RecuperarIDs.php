<?php
  include_once("../etc/opendb.php");

  $filename = "IDs_a_recuperar.txt";
  $handle = fopen($filename, 'r');
  $size = 2048;
  while($data = fgetcsv($handle, $size, ';')) {
    $sqlqry = "SELECT Producto.Nombre, Producto.Detalle, Producto.Precio, Producto.IDCategoria, Producto.CodLimpiadin FROM Producto WHERE ID = '".$data[0]."'";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry\n";
    }
    $DBarr = mysqli_fetch_row($DBres);
    $sqlqry = "UPDATE Producto SET Nombre = '".$DBarr[0]."', Detalle = '".$DBarr[1]."', Precio = '".$DBarr[2]."', IDCategoria = '".$DBarr[3]."', CodLimpiadin = '".$DBarr[4]."' WHERE ID = '".$data[0]."';";
    //echo $sqlqry."\n";
    echo $DBarr[0]."\n";
  }
?>
