<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Proyecto = $_POST['NombreProyecto'];

	if (strlen($Proyecto) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					CENTROS.Id, 
					CENTROS.Centro, 
					CENTROS.Nombre 
				FROM CENTROS 
				WHERE CENTROS.Nombre LIKE '%$Proyecto%' OR CENTROS.Centro LIKE '%$Proyecto%' 
				ORDER BY CENTROS.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>PROYECTOS ENCONTRADOS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regProyecto) 
		{
			$IdProyecto = $regProyecto['Id'];
			$NombreProyecto = $regProyecto['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreProyecto" id="$IdProyecto">$NombreProyecto</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>