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
		$lcTitulo = utf8_decode('CONCEPTOS AUXILIARES');

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('CONC.'), 15);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 105);
		$lcEncabezado .= str_pad(utf8_decode('IMPUTACIÓN'), 30);
		$lcEncabezado .= str_pad(utf8_decode('MODO LIQ.'), 20);
		$lcEncabezado .= str_pad(utf8_decode('TIPO LIQ.'), 20);
		$lcEncabezado .= str_pad(utf8_decode('FACTOR'), 15);
	
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$Imputacion = getRegistro('PARAMETROS', $reg['imputacion'])['detalle']; 
			$ModoLiquidacion = getRegistro('PARAMETROS', $reg['modoliquidacion'])['detalle']; 
			$TipoLiquidacion = getRegistro('PARAMETROS', $reg['TipoLiquidacion'])['detalle']; 

			$PDF->Cell(15, 5, substr(utf8_decode($reg['Mayor'] . $reg['auxiliar']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(80, 5, substr(utf8_decode($reg['nombre']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(30, 5, substr(utf8_decode($Imputacion), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($ModoLiquidacion), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($TipoLiquidacion), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(10, 5, number_format($reg['factorconversion'], 4), 0, 0, 'R'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeConceptosAuxiliares.PDF', 'I'); 
	}
?>