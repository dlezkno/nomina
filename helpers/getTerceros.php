<?php
	require_once('../config/config.php');
	
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	$html = '';
	$Tercero = $_POST['Tercero'];
	
	if (isset($_POST['EPS']))
		$query = 'WHERE TERCEROS.EsEPS = 1 AND ';
	elseif (isset($_POST['FC']))
		$query = 'WHERE TERCEROS.EsFondoCesantias = 1 AND ';
	elseif (isset($_POST['FP']))
		$query = 'WHERE TERCEROS.EsFondoPensiones = 1 AND ';
	elseif (isset($_POST['CCF']))
		$query = 'WHERE TERCEROS.EsCCF = 1 AND ';
	elseif (isset($_POST['ARL']))
		$query = 'WHERE TERCEROS.EsARL = 1 AND ';
	else
		$query = 'WHERE ';

	if (strlen($Tercero) >= 3)
	{
		$query = <<<EOD
			SELECT TOP 10 
					TERCEROS.Id, 
					TERCEROS.Documento, 
					TERCEROS.Nombre 
				FROM TERCEROS 
				$query  
					TERCEROS.Nombre LIKE '%$Tercero%' OR TERCEROS.Documento LIKE '%$Tercero%' 
				ORDER BY TERCEROS.Nombre;
		EOD;

		$aDatos = array();

		$html .= <<<EOD
			<div class="collection with-header">
				<h5><string>TERCEROS ENCONTRADOS</string></h5>
		EOD;

		foreach ($conn->query($query) as $regTercero) 
		{
			$IdTercero = $regTercero['Id'];
			$NombreTercero = $regTercero['Nombre'];

			$html .= <<<EOD
					<a class="suggest-element collection-item" data="$NombreTercero" id="$IdTercero">$NombreTercero</a>
			EOD;
		}

		$html .= <<<EOD
			</div>
		EOD;
	}

	echo $html;
?>