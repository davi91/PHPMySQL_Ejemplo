<!DOCTYPE html>
<html>
<head>
	<title>Alta residencias escolares</title>
	<meta charset="utf-8">
</head>

<body>

	<h1>DAVID FERNÁNDEZ NIEVES, 2ºDAM A</h1>
	<form  id="form" style="background-color: MintCream" method="POST" action="resiUpdate.php">
		
		<fieldset>

			<legend><u><b>Altas residencias escolares</b></u></legend>

			<?php 

				// Si es una modficación, ya ponemos los campos especificados
				if( !isset( $_REQUEST["resi"]))
					echo '<label>Nombre residencia: <input type="text" name="NombreResi" placeholder="Nombre residencia"><br><br></label>';
				else
					echo '<label>Nombre residencia: <input type="text" name="NombreResi" value='.$_REQUEST["resi"].'><br><br></label>';
			?>

			Universidad: <select name="uni">

				<?php

					include( "mySql_residencias.php");

					$residb = conectarResi();

					$unis = $residb->prepare("select * from universidades") ;

					$unis->execute();
					$i = 0;
					$id = $_REQUEST["cod"];
					while( $row = $unis->fetch() ) { 

						if( $id != $row["codUniversidad"]) {
							echo "<option value=".$row["codUniversidad"].">".$row["nomUniversidad"]."</option>";
						} else {
							echo "<option value=".$row["codUniversidad"]." selected>".$row["nomUniversidad"]."</option>"; // Only one will be selected
						}

						$i++;
					}

					$resid = null;
				?>

			</select><br><br>
			
			<?php 

				if( isset($_REQUEST["prec"])) {
					echo '<label>Precio: <input type="number" name="precio" value='.$_REQUEST["prec"].'><br><br></label>';
				} else {
					echo '<label>Precio: <input type="number" name="precio" value=0><br><br></label>';
				}

				if( isset($_REQUEST["com"]) && $_REQUEST["com"] == 1) {
					echo '<label>Comedor: <input type="checkbox" name="comedor" checked><br><br></label>';
				} else {
					echo '<label>Comedor: <input type="checkbox" name="comedor"><br><br></label>';
				}
			
				echo '<input type="button" name="backBt" value="Atrás" onClick=goBack() class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">&nbsp';

				if( isset($_REQUEST["mod"])) {  // Variable adicional que me permite observar que vamos a modificar
					echo  '<input type="submit" name="altaBt" value="Modificar fila" class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">';
					echo '<input type="hidden" name="subInfo" value='.$_REQUEST["mod"];
				} else {
					echo '<input type="submit" name="altaBt" value="Dar de alta" class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">';
					echo '&nbsp <input type="button" onClick="altaProc()" name="altaBt2" value="Dar de alta (procedimiento)" class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">';
				}

			?>


		</fieldset>

	</form>

			
		<script type="text/javascript">
				
			function goBack() {

				window.location.replace('http://localhost/miweb/residencias.php'); // Esto es para que no se refleje en el 'back' del navegador
				// En caso contrario, usaríamos window.loaction.hrefe
			}

			function altaProc() {

				// Alta usando el procedimiento
				var form = document.getElementById("form");
				form.action = "resiInsert.php";
				form.submit();
			}

		</script>

</body>
</html>