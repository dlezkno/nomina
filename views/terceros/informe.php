<?php 
	global $lcOrientacion;
	global $lcTitulo;
	global $lcSubTitulo;
	global $lcEncabezado;
	global $lcEncabezado2;

	$PDF = new PDF(); 
	$PDF->AliasNbPages();

	if (count($data['rows']) > 0) 
	{
		$lcTitulo = utf8_decode('TERCEROS');
		$lcOrientacion = 'L';

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('DOCUM.'), 15);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 135);
		$lcEncabezado .= str_pad(utf8_decode('TIPO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('EPS'), 20);
		$lcEncabezado .= str_pad(utf8_decode('ARL'), 20);
		$lcEncabezado .= str_pad(utf8_decode('FC'), 20);
		$lcEncabezado .= str_pad(utf8_decode('FP'), 20);
		$lcEncabezado .= str_pad(utf8_decode('CCF'), 20);
		$lcEncabezado .= str_pad(utf8_decode('COD. SAP'), 20);
	
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage('L');
		$PDF->SetFont('Tahoma', '', 7);

		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			// $TipoCuenta = getRegistro('PARAMETROS', $reg['tipocuentabancaria'])['detalle']; 

			$PDF->Cell(15, 5, substr(utf8_decode($reg['documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(100, 5, substr(utf8_decode($reg['nombre']), 0, 70), 0, 0, 'L'); 

			$Tipo = '';

			if ($reg['esdeudor']) 
				$Tipo .= 'DEUD.';

			if ($reg['esacreedor'])
				$Tipo .= 'ACRE.';

			$PDF->Cell(15, 5, $Tipo, 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['cuentaeps']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['cuentaarl']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['cuentafondocesantias']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['cuentafondopensiones']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['cuentaccf']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(10, 5, substr(utf8_decode($reg['codigosap']), 0, 10), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeTerceros.PDF', 'I'); 
	}
?>