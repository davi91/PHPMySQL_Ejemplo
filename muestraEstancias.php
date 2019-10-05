<!DOCTYPE html>

<html>

<head>
	<title>Estancias</title>
	<meta charset="utf-8">

	<style type="text/css">
		
		table {
			
			width: 100%;
			background-color: black;
		}

		th {

			background-color: LightBlue;
		}

		td {

			background-color: Lavender;
		}

	</style>

</head>

<body>
	
	<h3>Estancias por estudiante</h3>

	<?php

		include ("mySql_residencias.php");
		
		if( isset($_REQUEST["dni"]) && !empty($_REQUEST["dni"]) ) {

				// Ningún misterio, igual que en las anteriores, pero esta vez llamando a un procedimiento
				$residb = conectarResi();
				$stmt = $residb->prepare( "call sp_estuEstancias( :dni )");
				$stmt->bindValue(":dni", $_REQUEST["dni"]); 
		?>

				<table border="1">

					<tr>
						<th>Nombre residencia</th>
						<th>Nombre universidad</th>
						<th>Fecha de inicio</th>
						<th>Fecha fin</th>
						<th>Precio</th>
					</tr>



			<?php

						// Hacemos lo mismo que en la visualización de residencias
						$stmt->execute();
						while($row = $stmt->fetch()) {?>

							<tr>
								<td><?php echo $row["nomResidencia"] ?></td>
								<td><?php echo $row["nomUniversidad"] ?></td>
								<td><?php echo $row["fechaInicio"] ?></td>
								<td><?php echo $row["fechaFin"] ?></td>
								<td><?php echo $row["preciopagado"] ?></td>
							</tr>

						<?php }
					
				
				$residb = null;

		} else {
			die("No se ha introducido un DNI válido");
		}

	?>

	</table>

</body>

</html>

