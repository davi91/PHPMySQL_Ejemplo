<?php
	
	function conectarResi() 
	{
		try {

			$user = "root";
			$pass = null;
			$host = "localhost";
			$dbname = "bdresidenciasescolares";

			// Entramos en la base de datos
			$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

			// Para ver si da error
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

			return $pdo;
			
		} catch(PDOException $e) {
			echo $e->getMessage();
		} 
	}
?>