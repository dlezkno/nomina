<?php 
	if (count($data['rows']) > 0) 
	{
		global $lcOrientacion;
		global $lcTitulo;
		global $lcSubTitulo;
		global $lcEncabezado;
		global $lcEncabezado2;
	
		$PDF = new PDF(); 
		$PDF->AliasNbPages();
	
		$lcTitulo = utf8_decode('DISPERSIÓN POR CENTRO DE COSTOS');
		$lcOrientacion = 'P';
		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('REF.'), 10);
		$lcEncabezado .= str_pad(utf8_decode('PERIODIC.'), 10);
		$lcEncabezado .= str_pad(utf8_decode('PER.'), 7);
		$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
		$lcEncabezado .= str_pad(utf8_decode('C.C.'), 12);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE CENTRO'), 50);
		$lcEncabezado .= str_pad(utf8_decode('PORCENTAJE'), 15);

		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage($lcOrientacion);
		$PDF->SetFont('Tahoma', '', 8);

		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(10, 5, substr(utf8_decode($reg['Referencia']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(17, 5, substr(utf8_decode($reg['Periodicidad']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(10, 5, substr(utf8_decode($reg['Periodo']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(50, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(10, 5, substr(utf8_decode($reg['Centro']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(55, 5, substr(utf8_decode($reg['NombreCentro']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(10, 5, number_format($reg['Porcentaje'], 2), 0, 0, 'R'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeDispersionPorCentro.PDF', 'I'); 
	}
?>