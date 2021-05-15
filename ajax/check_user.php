<?php
  include_once("../etc/opendb.php");

  $usuario = $_POST['usuario'];
  $sqlqry = "SELECT ID FROM Usuario WHERE Usuario = '$usuario'";
  $DBres = mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "2"; // Error en consulta
  }
  if (mysqli_num_rows($DBres) != 0) echo "1";
  else echo "0";

?>
