<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Banco = $_POST['Banco'];

	if (strlen($Banco) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					BANCOS.Id, 
					BANCOS.Banco, 
					BANCOS.Nombre 
				FROM BANCOS 
				WHERE BANCOS.Nombre LIKE '%$Banco%' OR BANCOS.Banco LIKE '%$Banco%' 
				ORDER BY BANCOS.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>ENTIDADES BANCARIAS ENCONTRADAS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regBanco) 
		{
			$IdBanco = $regBanco['Id'];
			$NombreBanco = $regBanco['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreBanco" id="$IdBanco">$NombreBanco</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>