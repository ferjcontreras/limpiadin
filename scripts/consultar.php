<?php

	// Geting data comming from application
	//$parent = $_REQUEST['n'];

	// Connection with the database
	include_once("../etc/opendb.php");



	//$sqlqry = "SELECT ID, Name, Status, Detail, Last FROM Monitor WHERE Parent='$parent' ORDER BY Status DESC, Name";
	$sqlqry = "SELECT Producto.ID, Producto.Nombre, Producto.Detalle, Producto.Precio, Producto.Foto, Producto.IDCategoria FROM Producto WHERE Disponible = 1 ORDER BY Producto.IDCategoria , Producto.Nombre;";
	$res = mysqli_query($db, $sqlqry);
	if (mysqli_errno($db)) {
		echo "-1";
	}
	else {
		$data = array();
		foreach ($res as $row) {
			//if ($row['Last'] < 60) $row['Last'] = $row['Last']." min";
			//else if($row['Last'] < 86400) $row['Last'] = round($row['Last']/60)." hours";
			//else $row['Last'] = round($row['Last']/3600)." days";

			$data[] = $row;
		}
		echo json_encode($data);
	}

?>
