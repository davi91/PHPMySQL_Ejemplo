<?php

	include ("mySql_residencias.php");

	if( isset($_REQUEST["dni"]) && !empty($_REQUEST["dni"])) {

		$resi = conectarResi();

		// Para llamar a una función usamos el select y luego usamos esa variable
		$stmt = $resi->prepare("select fn_tiempoResidencias( :dni ) as time");
		$stmt->bindValue(":dni", $_REQUEST["dni"]);

		$stmt->execute();
		$data = $stmt->fetch();

		echo $data["time"];

		$resi = null;
	}

	else {
		echo "-1";
		die("Error, no se introdojo correctamente el dni del alumno");
	}

?>