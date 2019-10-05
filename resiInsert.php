<?php

	// Es idéntico al de insertar/actualizar, sólo que usamos una llamada a un procedimiento
	include ("mySql_residencias.php");

	$residb = conectarResi();
	
	// Esto es mejor hacerlo en la parte del cliente con JavaScript, pero de momento lo hacemos aquí
	if( $_POST["NombreResi"] == "" ) {
		$residb = null;
		die("No se ha introducido ningun nombre para la residencia");
	} else {
		$nombre = $_POST["NombreResi"];
	}

	$comedor = (!isset($_POST["comedor"]) || $_POST["comedor"] == "off") ? 0 : 1; 
	
	if( isset($_POST["precio"])) {
		$precio = $_POST["precio"];
	} else {
		$precio = 0;
	}


	$datos = $residb->prepare(" call sp_insertResidencia(:nombre, :uni, :precio, :comedor, @uniExiste, @resiInsertada)");
	

	$datos->bindValue(":nombre", $nombre);
	$datos->bindValue(":uni", $_POST["uni"]); 
	$datos->bindValue(":precio", $precio);
	$datos->bindValue(":comedor", $comedor);


	$datos->execute();

	// Ahora obtenemos los resultados de los parámetros de salida
	$datos = $residb->query("select @uniExiste as uniExists, @resiInsertada as resiInserted");
	$datos->execute();
	$res = $datos->fetch();

	if( $res["uniExists"] == 0 ) {
		$datos = null;
		echo "<script type='text/javascript'>
					alert('La universidad introducida no existe');
				    window.location.href ='http://localhost/miweb/residencias.php';
			</script>";
	}

	else if( $res["resiInserted"] == 0 ) {
		$datos = null;
		echo "<script type='text/javascript'>
					alert('No se pudo insertar la residencia');
				    window.location.href ='http://localhost/miweb/residencias.php';
			</script>";
	}

	else {
		$datos = null;
		echo "<script type='text/javascript'>
					alert('Los datos se han introducido correctamente');
				    window.location.href ='http://localhost/miweb/residencias.php';
			</script>";
	}

	

?>