<?php

	include ("mySql_residencias.php");

	// Aprovechando que el objeto se envía por parámetro
	$id = $_GET['resi'];

	$residb = conectarResi();

	// Preparamos lo que queremos
	$delete = $residb->prepare("delete from residencias where codResidencia=:id");
	$delete->bindValue(":id", $id);

	$delete->execute();

	$residb = null;

	// Volvemos a nuestra página
	header( "Location: residencias.php"); // Es ruta relativa a donde estás, para las absolutas usamos el protocolo  HTTP
?>

