<!DOCTYPE html>
<html>
<head>
	<title>Visualización residencias escolares</title>
	<meta charset="utf-8">

	<!-- Para la comunicación entre servidor y cliente necesitamos Ajax -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<style type="text/css">
		
		.tableBt {

			width: 100%;
			background-color: LightBlue;
			border-radius: 2px;
		}

	</style>
</head>

<body>

	<h1>DAVID FERNÁNDEZ NIEVES, 2ºDAM A</h1>
	<form name="resiFormVisual" style="background-color: MintCream" method="POST" id="form">
		<fieldset>

			<legend><u><b>Residencias escolares</b></u></legend>

			<table border="1px solid gray">

				<tr>
					<th>Código residencia</th>
					<th>Nombre residencia</th>
					<th>Código universidad</th>
					<th>Precio Mensual</th>
					<th>Comedor</th>
					<th>Baja</th>
					<th>Modificación</th>
				</tr>

				<?php

					include ("mySql_residencias.php");

					$residb = conectarResi();

					// Preparamos lo que queremos
					$consulta = $residb->prepare("select * from residencias");

					// Ejecutamos
					$consulta->execute();

					$i = 0;
					// Vamos a ir recorriendo las filas
					while($row = $consulta->fetch()) { ?>

						<tr>	
							<td><?php echo $row["codResidencia"] ?></td>
							<td><?php echo $row["nomResidencia"] ?></td>
							<td><?php echo $row["codUniversidad"] ?></td>
							<td><?php echo $row["precioMensual"] ?></td>
							<td><?php echo ($row["comedor"] == 1) ? "Si" : "No" ?></td>

							<!-- Aquí usaremos un pequeño truco entre servidor-cliente usando JSON, que nos permite pasar array de PHP a JavaSCript -->
							<td><input type="button" <?php echo 'onClick=onDelete('.json_encode($row).')' ?> value="Dar de baja" class="tableBt"></td>
							<td><input type="button" <?php echo 'onClick=onModify('.json_encode($row).')' ?> value="Modificar" class="tableBt"></td>
						</tr>

					<?php $i++; }

					// Nos aseguramos de quitar la referencia al objeto
				?>

			</table>

			<br><br>

			<input type="button" name="goInsertBt" onClick="onGoInsert()" value="Insertar nuevo registro" style="border-radius: 10%; color: white; background-color: LightSlateGray" >

			<hr>
			<!-- Consulta para ver cuantas residencias por universidad hay con menor precio -->
			<br><b>Cuenta de residencias por universidad a un precio menor:</b><br><br>

			<select id="unis" name="universidades">
			<?php
					// Ahora vamos a ver las universidades disponibles
					$unis = $residb->prepare("select nomUniversidad from universidades") ;

					$unis->execute();
					
					while($row = $unis->fetch()) { 

						echo '<option value='.$row["nomUniversidad"].'>'.$row["nomUniversidad"].'</option>';
					}

					$residb = null;
			?>

			</select>

			&nbsp Precio: <input id="precio" type="number" name="precioSelect" value="0">

			&nbsp <input type="button" onClick="getNumResis()" name="selectResiCount" value="Consultar residencias">

			<br><br><p id="r_p1" style="visibility: hidden; font-weight: bold; font-style: italic; display: none"></p><br>
			<p id="r_p2" style="visibility: hidden; font-weight: bold; font-style: italic; display: none"></p>
			
			<hr>

			<p><b>Esancias por alumno</b></p>
			<br><input type="text" name="estanciasDNI" placeholder="DNI del alumno" id="dni_estancia">
			&nbsp <input type="button" name="consultaEstanaciasBt" value="Consultar estancias" onClick="onConsultaEstancia()">
			&nbsp <input type="button" name="tiempoBt" value="Consultar tiempo" onClick="onConsutltaTiempo()">

			<br><br><p id="p_time" style="visibility: hidden; font-weight: bold; font-style: italic; display: none"></p>
		</fieldset>
	</form>


	<script type="text/javascript">

		function onConsultaEstancia() {

			// Nuestra intención es abrir una nueva ventana que muestra los datos soliccitados
			var str = "muestraEstancias.php?dni=" + document.getElementById("dni_estancia").value;

			// Lo mostramos en una ventana aparte, no en una pestaña
			window.open(str, "_blank", "toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=800,height=600");
		}

		function onGoInsert() {

			window.location.href = 'http://localhost/miweb/altaResidencias.php'; // Vamos a la página de insercciones.
		}

		function onModify(row) {

			form = document.getElementById("form");

			/*
				 Si queremos pasarlo en forma de array, usamos esta forma:
				 ....+JSON.stringify(row);
				 y en el PHP:
				 $array = json_decode($_REQUEST[.....])
				 y para acceder a estos objetos especiales:
				 $array->{"nombreCampo"}....para acceder a objetos.
			*/

			// Por defecto, la enviamos por GET, el Ajax puede ser también una opción para la comunicación entre cliente y servidor
			form.action = "altaResidencias.PHP?resi="+row.nomResidencia+"&cod="+row.codUniversidad+"&prec="+row.precioMensual+"&com="+row.comedor+"&mod="+row.codResidencia;
			form.submit();
		}

		function onDelete(row) {

			if( !confirm("¿Está seguro de eliminar la residencia " + row.nomResidencia + "?") ) {
				return;
			}

			// Otra forma es usando $_SESSION o cookies
			// También desde JS podemos llamar a funciones del PHP, pero en nuestro caso vamos a hacerlo por separado
			form = document.getElementById("form");
			form.action = "deleteResi.php?resi="+row.codResidencia; // Enviamos nuestro elemento a eliminar -> Métood GET
			form.submit();
		}

		function getNumResis() {

			var p1 = document.getElementById("r_p1");
			var p2 = document.getElementById("r_p2");
			// Vamos a usar a nuestro aliado Ajax para así no cambiar de página
      		 var xmlhttp = new XMLHttpRequest(); // Abrimos el canal, además, con este objeto no hace falta refrescar la página
       		 xmlhttp.onreadystatechange = function() { // Vemos cuando cambia el estado de la conexión -> Función anónima de JS
         		   if (this.readyState == 4 && this.status == 200) { // 200 = "Ok", 4 = La petición ha sido enviada y la respuesta ha sido recibida
          			    // Hemos recibido los resultados, se guardan en la variable responseText
          				var res = this.responseText.split(';');

          				// En cuanto le damos a la consulta, el texto ya se queda anclado
          				p1.innerHTML = "El número de residencias coincidentes es " + res[0];
          				p1.style.visibility = "visible";
          				p1.style.display = "inline";
          				p2.innerHTML = "El número de residencias con menor precio a " + document.getElementById("precio").value + " es " + res[1];    
          				p2.style.visibility = "visible"; 		
          				p2.style.display = "inline";	  	
            }
	        };

	       	// Abrimos el PHP, el true se refiere a que es asíncrono, así el JS se puede ejecutar sin tener que esperar
	        xmlhttp.open("POST", "countResis.php?uni="+document.getElementById("unis").value+
	        								   "&precio="+document.getElementById("precio").value,
	        								   true); 

	        xmlhttp.send(); // Enviamos nuestra petición

		}

		
		function onConsutltaTiempo() {

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {

				if( this.readyState == 4 && this.status == 200 ) {
					var p = document.getElementById("p_time");

					p.style.display = "inline";
					p.style.visibility = "visible";
					p.innerHTML = "Tiempo de estancia: " + this.responseText + " meses";
				}
			};

			xmlhttp.open("POST", "tiempoEstudiante.php?dni="+document.getElementById("dni_estancia").value, true);
			xmlhttp.send();
		}


	</script>

</body>
</html>