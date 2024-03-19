<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Cargo = $_POST['NombreCargo'];

	if (strlen($Cargo) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					CARGOS.Id, 
					CARGOS.Cargo, 
					CARGOS.Nombre 
				FROM CARGOS 
				WHERE CARGOS.Nombre LIKE '%$Cargo%' OR CARGOS.Cargo LIKE '%$Cargo%' 
				ORDER BY CARGOS.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>CARGOS ENCONTRADOS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regCargo) 
		{
			$IdCargo = $regCargo['Id'];
			$NombreCargo = $regCargo['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreCargo" id="$IdCargo">$NombreCargo</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>