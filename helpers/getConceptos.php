<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Concepto = $_POST['Concepto'];

	if (strlen($Concepto) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					AUXILIARES.Id, 
					MAYORES.Mayor, 
					AUXILIARES.Auxiliar, 
					AUXILIARES.Nombre 
				FROM AUXILIARES 
					INNER JOIN MAYORES 
						ON AUXILIARES.IdMayor = MAYORES.Id 
				WHERE (AUXILIARES.Nombre LIKE '%$Concepto%' OR 
					MAYORES.Mayor + AUXILIARES.Auxiliar LIKE '%$Concepto%') AND 
					AUXILIARES.Borrado = 0 
				ORDER BY AUXILIARES.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>CONCEPTOS ENCONTRADOS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regConcepto) 
		{
			$IdConcepto = $regConcepto['Id'];
			$NombreConcepto = $regConcepto['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreConcepto" id="$IdConcepto">$NombreConcepto</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>