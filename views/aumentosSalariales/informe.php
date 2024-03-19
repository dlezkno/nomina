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

		$lcOrientacion = 'P';
	
		$lcTitulo = utf8_decode('AUMENTOS SALARIALES');
		$lcSubTitulo = '';

		$lcEncabezado = '';
		$lcEncabezado .= str_pad('', 110);
		$lcEncabezado .= str_pad(utf8_decode('FECHA'), 32);
		$lcEncabezado .= str_pad(utf8_decode('SUELDO'), 25);
		$lcEncabezado .= str_pad(utf8_decode('SUELDO'), 20);
		
		$lcEncabezado2 = '';
		$lcEncabezado2 .= str_pad(utf8_decode('EMPLEADO'), 18);
		$lcEncabezado2 .= str_pad(utf8_decode('NOMBRE'), 70);
		$lcEncabezado2 .= str_pad(utf8_decode('AUMENTO'), 30);
		$lcEncabezado2 .= str_pad(utf8_decode('BÁSICO'), 20);
		$lcEncabezado2 .= str_pad(utf8_decode('ANTERIOR'), 20);
		$lcEncabezado2 .= str_pad(utf8_decode('PROCESADO'), 10);

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
			$PDF->Cell(15, 5, substr(utf8_decode($reg['fechaaumento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(25, 5, number_format($reg['sueldobasico'], 0), 0, 0, 'R'); 
			$PDF->Cell(25, 5, number_format($reg['sueldobasicoanterior'], 0), 0, 0, 'R'); 
			$PDF->Cell(20, 5, ($reg['procesado'] == 1 ? 'SI' : 'NO'), 0, 0, 'R'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeAumentosSalariales.PDF', 'I'); 
	}
?>