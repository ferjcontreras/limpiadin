<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    $id_costo = $_POST['id_costo'];

    $sqlqry = "DELETE FROM CostoEnvio WHERE ID = '$id_costo'";
    mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
?>
