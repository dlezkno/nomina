<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Sede = $_POST['Sede'];

	if (strlen($Sede) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					SEDES.Id, 
					SEDES.Sede, 
					SEDES.Nombre 
				FROM SEDES 
				WHERE SEDES.Nombre LIKE '%$Sede%' OR SEDES.Sede LIKE '%$Sede%' 
				ORDER BY SEDES.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>SEDES ENCONTRADAS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regSede) 
		{
			$IdSede = $regSede['Id'];
			$NombreSede = $regSede['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreSede" id="$IdSede">$NombreSede</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>