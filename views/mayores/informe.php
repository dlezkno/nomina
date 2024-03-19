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
		$lcTitulo = utf8_decode('CONCEPTOS MAYORES');

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('MAYOR'), 15);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 50);
		$lcEncabezado .= str_pad(utf8_decode('LIQUIDA'), 20);
		$lcEncabezado .= str_pad(utf8_decode('CLASE'), 25);
		$lcEncabezado .= str_pad(utf8_decode('RET. FTE.'), 20);
		$lcEncabezado .= str_pad(utf8_decode('BP'), 10);
		$lcEncabezado .= str_pad(utf8_decode('BV'), 10);
		$lcEncabezado .= str_pad(utf8_decode('BC'), 10);
		$lcEncabezado .= str_pad(utf8_decode('AS'), 10);
		$lcEncabezado .= str_pad(utf8_decode('AL'), 10);
		$lcEncabezado .= str_pad(utf8_decode('CS'), 10);
		$lcEncabezado .= str_pad(utf8_decode('RC'), 10);

		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(15, 5, substr(utf8_decode($reg['mayor']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nombre']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['NombreTipoLiquidacion']), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['NombreClaseConcepto']), 0, 13), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['NombreTipoRetencion']), 0, 13), 0, 0, 'L'); 
			$PDF->Cell(10, 5, $reg['baseprimas'] == 1 ? 'SI' : '  ', 0, 0, 'L'); 
			$PDF->Cell(10, 5, $reg['basevacaciones'] == 1 ? 'SI' : '  ', 0, 0, 'L'); 
			$PDF->Cell(10, 5, $reg['basecesantias'] == 1 ? 'SI' : '  ', 0, 0, 'L'); 
			$PDF->Cell(10, 5, $reg['acumulasanciones'] == 1 ? 'SI' : '  ', 0, 0, 'L'); 
			$PDF->Cell(10, 5, $reg['acumulalicencias'] == 1 ? 'SI' : '  ', 0, 0, 'L'); 
			$PDF->Cell(10, 5, $reg['controlasaldos'] == 1 ? 'SI' : '  ', 0, 0, 'L'); 
			$PDF->Cell(10, 5, substr(utf8_decode($reg['rengloncertificado']), 0, 10), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		$PDF->Cell(15, 5, utf8_decode('BP-Base Primas  BV-Base Vacaciones  BC-Base Cesantías  AS-Acumula Sanciones  AL-Acumula Licencias  CS-Controla Saldos  RC-Renglón Certificado'), 0, 0, 'L');
		
		$PDF->Output('InformeDeConceptosMayores.PDF', 'I'); 
	}
?>