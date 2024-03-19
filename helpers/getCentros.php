<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Centro = $_POST['NombreCentro'];

	if (strlen($Centro) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					CENTROS.Id, 
					CENTROS.Centro, 
					CENTROS.Nombre 
				FROM CENTROS 
				WHERE CENTROS.Nombre LIKE '%$Centro%' OR CENTROS.Centro LIKE '%$Centro%' 
				ORDER BY CENTROS.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>CENTROS ENCONTRADOS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regCentro) 
		{
			$IdCentro = $regCentro['Id'];
			$NombreCentro = $regCentro['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreCentro" id="$IdCentro">$NombreCentro</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>