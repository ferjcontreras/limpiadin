<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    $distancia = $_POST['distancia'];
    $costo = $_POST['costo'];

    // Verificamos que no exista antes de agregarlo
    $sqlqry = "SELECT ID FROM CostoEnvio WHERE Distancia = '$distancia' AND Costo = '$costo'";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    if (mysqli_num_rows($DBres) == 0) {
      $sqlqry = "INSERT INTO CostoEnvio(Distancia, Costo) VALUES ('$distancia', '$costo');";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "error";
      }
    } else echo "error";

?>
