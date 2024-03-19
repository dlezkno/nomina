<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Idioma = $_POST['Idioma'];

	if (strlen($Idioma) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					IDIOMAS.Id, 
					IDIOMAS.Idioma, 
					IDIOMAS.Nombre 
				FROM IDIOMAS 
				WHERE IDIOMAS.Nombre LIKE '%$Idioma%' OR IDIOMAS.Idioma LIKE '%$Idioma%' 
				ORDER BY IDIOMAS.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>IDIOMAS ENCONTRADAS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regIdioma) 
		{
			$IdIdioma = $regIdioma['Id'];
			$NombreIdioma = $regIdioma['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreIdioma" id="$IdIdioma">$NombreIdioma</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>