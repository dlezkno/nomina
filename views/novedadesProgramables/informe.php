<?php 
	global $lcOrientacion;
	global $lcTitulo;
	global $lcSubTitulo;
	global $lcEncabezado;
	global $lcEncabezado2;

	$PDF = new PDF(); 
	$PDF->AliasNbPages();

	$lcOrientacion = 'P';
	
	if (count($data['rows']) > 0) 
	{
		$lcTitulo = utf8_decode('NOVEDADES PROGRAMABLES');

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 60);
		$lcEncabezado .= str_pad(utf8_decode('CONC.'), 10);
		$lcEncabezado .= str_pad(utf8_decode('DESCRIPCIÓN'), 45);
		$lcEncabezado .= str_pad(utf8_decode('HORAS'), 15);
		$lcEncabezado .= str_pad(utf8_decode('VALOR'), 10);
		$lcEncabezado .= str_pad(utf8_decode('APLICA'), 20);
		$lcEncabezado .= str_pad(utf8_decode('MODO LIQ.'), 10);

		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage($lcOrientacion);
		$PDF->SetFont('Tahoma', '', 8);

		$EmpleadoAnt = '';
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
			if (utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']) <> $EmpleadoAnt)
			{ 
				$PDF->Cell(55, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 50), 0, 0, 'L'); 
				$EmpleadoAnt = utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']);
			}
			else
				$PDF->Cell(55, 5, '', 0, 0, 'L'); 

			$PDF->Cell(10, 5, substr(utf8_decode($reg['Mayor']) . $reg['Auxiliar'], 0, 25), 0, 0, 'L'); 
			$PDF->Cell(40, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 25), 0, 0, 'L'); 
			// $PDF->Cell(15, 5, substr(utf8_decode($reg['NombreTipoEmpleado']), 0, 15), 0, 0, 'L'); 
			// $PDF->Cell(15, 5, substr(utf8_decode($reg['NombreCentro']), 0, 15), 0, 0, 'L'); 
			// $PDF->Cell(15, 5, substr(utf8_decode($reg['NombreCargo']), 0, 15), 0, 0, 'L'); 
			$PDF->Cell(15, 5, number_format($reg['horas'], 0), 0, 0, 'R'); 
			$PDF->Cell(15, 5, number_format($reg['valor'], 0), 0, 0, 'R'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['NombreAplica']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['NombreModoLiquidacion']), 0, 10), 0, 0, 'L'); 
			// $PDF->Cell(25, 5, substr(utf8_decode($reg['NombreEstado']), 0, 15), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeNovedadesProgramables.PDF', 'I'); 
	}
?>