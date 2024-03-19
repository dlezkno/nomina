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
		$lcTitulo = utf8_decode('NOVEDADES OCASIONALES');

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 70);
		$lcEncabezado .= str_pad(utf8_decode('CONC.'), 10);
		$lcEncabezado .= str_pad(utf8_decode('DESCRIPCIÓN'), 50);
		$lcEncabezado .= str_pad(utf8_decode('HORAS'), 15);
		$lcEncabezado .= str_pad(utf8_decode('VALOR'), 10);

		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage($lcOrientacion);
		$PDF->SetFont('Tahoma', '', 8);

		$EmpleadoAnt = '';
		
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			if (utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']) <> $EmpleadoAnt)
			{ 
				$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
				$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 50), 0, 0, 'L'); 
				$EmpleadoAnt = utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']);
			}
			else
			{
				$PDF->Cell(20, 5, '', 0, 0, 'L'); 
				$PDF->Cell(65, 5, '', 0, 0, 'L'); 
			}

			$PDF->Cell(10, 5, substr(utf8_decode($reg['Mayor']) . $reg['Auxiliar'], 0, 25), 0, 0, 'L'); 
			$PDF->Cell(40, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 25), 0, 0, 'L'); 
			$PDF->Cell(15, 5, number_format($reg['horas'], 0), 0, 0, 'R'); 
			$PDF->Cell(15, 5, number_format($reg['valor'], 0), 0, 0, 'R'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeNovedadesOcasionales.PDF', 'I'); 
	}
?>