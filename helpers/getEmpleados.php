<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Documento = $_POST['Documento'];

	if (strlen($Documento) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					EMPLEADOS.Id, 
					EMPLEADOS.Documento, 
					EMPLEADOS.Apellido1, 
					EMPLEADOS.Apellido2, 
					EMPLEADOS.Nombre1, 
					EMPLEADOS.Nombre2 
				FROM EMPLEADOS 
					INNER JOIN PARAMETROS 
						ON EMPLEADOS.Estado = PARAMETROS.Id 
				WHERE (EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2 LIKE '%$Documento%' OR EMPLEADOS.Documento LIKE '%$Documento%') AND 
					PARAMETROS.Detalle = 'ACTIVO' 
				ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>EMPLEADOS ENCONTRADOS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regEmpleado) 
		{
			$IdEmpleado = $regEmpleado['Id'];
			$NombreEmpleado = $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreEmpleado" id="$IdEmpleado">$NombreEmpleado</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>