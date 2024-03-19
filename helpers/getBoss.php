<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$jefe = $_POST['NombreJefe'];

	if (strlen($jefe) >= 3)
	{
		$query = <<<EOD
		SELECT TOP 10 
			EMPLEADOS.Id, 
			EMPLEADOS.nombre1, 
			EMPLEADOS.nombre2,
			EMPLEADOS.apellido1,
			EMPLEADOS.apellido2,
			CARGOS.nombre AS nombrecargo
		FROM EMPLEADOS 
		INNER JOIN CARGOS ON EMPLEADOS.idcargo = CARGOS.id
		WHERE 
		EMPLEADOS.nombre1+EMPLEADOS.nombre2+EMPLEADOS.apellido1+EMPLEADOS.apellido2 LIKE '%$jefe%'
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>PERSONAL ENCONTRADOS</string></h5>
		EOD;


		foreach ($conn->query($query) as $reg) 
		{
			$id = $reg['Id'];
			$nombre = $reg['nombre1']." ".$reg['nombre2']." ".$reg['apellido1']." ".$reg['apellido2'];
			$nombrecargo = $reg['nombrecargo'];
			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$nombre" id="$id">$nombre ($nombrecargo)</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>