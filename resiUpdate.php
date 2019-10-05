<?php

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


	// Una vez cargados los datos empezamos a insertarlos en la base de datos
	if( isset($_REQUEST["subInfo"])) {
		$datos = $residb->prepare("update residencias set nomResidencia=:nombre, codUniversidad=:uni, precioMensual=:precio, comedor=:comedor where codResidencia=:resi");
		$datos->bindValue(":resi", $_REQUEST["subInfo"] );
	} else {
		$datos = $residb->prepare("insert into residencias values (null, :nombre, :uni, :precio, :comedor)");
	}
	

	$datos->bindValue(":nombre", $nombre);
	$datos->bindValue(":uni", $_POST["uni"]); 
	$datos->bindValue(":precio", $precio);
	$datos->bindValue(":comedor", $comedor);


	if (!$datos->execute() ) {
		$datos = null;
		echo "<script type='text/javascript'>
					alert('Error la insertar los datos');
				    window.location.href ='http://localhost/miweb/residencias.php';
			</script>";

	} else {
		$datos = null;
		echo "<script type='text/javascript'>
					alert('Los datos se han introducido correctamente');
				    window.location.href ='http://localhost/miweb/residencias.php';
			</script>";
	}

?>
