<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Ciudad = $_POST['Ciudad'];

	if (strlen($Ciudad) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					CIUDADES.Id, 
					CIUDADES.Ciudad, 
					CIUDADES.Nombre, 
					CIUDADES.Departamento 
				FROM CIUDADES 
				WHERE CIUDADES.Nombre LIKE '%$Ciudad%' OR CIUDADES.Ciudad LIKE '%$Ciudad%' 
				ORDER BY CIUDADES.Nombre, CIUDADES.Departamento;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>CIUDADES ENCONTRADAS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regCiudad) 
		{
			$IdCiudad = $regCiudad['Id'];
			$NombreCiudad = $regCiudad['Nombre'] . ' (' . $regCiudad['Departamento'] . ')';

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreCiudad" id="$IdCiudad">$NombreCiudad</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>