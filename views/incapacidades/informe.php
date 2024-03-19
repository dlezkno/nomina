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
		global $lcOrientacion;
		global $lcTitulo;
		global $lcSubTitulo;
		global $lcEncabezado;
		global $lcEncabezado2;
	
		$PDF = new PDF(); 
		$PDF->AliasNbPages();

		$lcOrientacion = 'L';
	
		$lcTitulo = utf8_decode('INCAPACIDADES');
		$lcSubTitulo = '';

		$lcEncabezado = '';
		$lcEncabezado .= str_pad('', 220);
		$lcEncabezado .= str_pad(utf8_decode('FECHA'), 25);
		$lcEncabezado .= str_pad(utf8_decode('DÍAS'), 20);
		$lcEncabezado .= str_pad(utf8_decode('DÍAS'), 10);
		$lcEncabezado .= str_pad(utf8_decode('PORC.'), 10);
		
		$lcEncabezado2 = '';
		$lcEncabezado2 .= str_pad(utf8_decode('EMPLEADO'), 18);
		$lcEncabezado2 .= str_pad(utf8_decode('NOMBRE'), 70);
		$lcEncabezado2 .= str_pad(utf8_decode('CONC.'), 12);
		$lcEncabezado2 .= str_pad(utf8_decode('DETALLE'), 83);
		$lcEncabezado2 .= str_pad(utf8_decode('INICIO'), 25);
		$lcEncabezado2 .= str_pad(utf8_decode('INCAP.'), 20);
		$lcEncabezado2 .= str_pad(utf8_decode('CAUS.'), 10);
		$lcEncabezado2 .= str_pad(utf8_decode('AUXILIO'), 10);
		$lcEncabezado2 .= str_pad(utf8_decode('DIAGNÓSTICO'), 10);

		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddPage($lcOrientacion);
		
		// $PDF->SetTextColor(255, 255, 255);
		// $PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		// $PDF->Ln(); 
		// $PDF->Cell(0, 5, $lcEncabezado2, 0, 0, 'L', TRUE);
		// $PDF->SetTextColor(0, 0, 0);
		// $PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(55, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(13, 5, substr(utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 40), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['fechainicio']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, number_format($reg['diasincapacidad'], 0), 0, 0, 'R'); 
			$PDF->Cell(17, 5, number_format($reg['diascausados'], 0), 0, 0, 'R'); 
			$PDF->Cell(17, 5, number_format($reg['porcentajeauxilio'], 0), 0, 0, 'R'); 
			$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreDiagnostico']), 0, 40), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeIncapacidades.PDF', 'I'); 
	}
?>