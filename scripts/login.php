<?php

	// Geting data comming from application
	$usuario = $_REQUEST['usuario'];
  $clave = $_REQUEST['clave'];

	// Connection with the database
	include_once("../etc/opendb.php");



	//$sqlqry = "SELECT ID, Name, Status, Detail, Last FROM Monitor WHERE Parent='$parent' ORDER BY Status DESC, Name";
	$sqlqry = "SELECT Usuario.ID, Usuario.Nombre,Usuario.Perfil  FROM Usuario WHERE Usuario = '$usuario' AND Clave = '$clave' AND (Perfil = 1 || Perfil = 5);"; // solo el usuario admin y los preventistas pueden autenticarse
  //echo $sqlqry;
  $res = mysqli_query($db, $sqlqry);
	if (mysqli_errno($db)) {
		echo "-1";
	}
	else {
		$data = array();
		foreach ($res as $row) {
			$data[] = $row;
		}
		echo json_encode($data);
	}

?>
