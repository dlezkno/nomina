<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('PRÉSTAMOS A EMPLEADOS');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('EMPLEADO'), 50);
		$lcEncabezado .= str_pad(utf8_decode('CONCEPTO'), 45);
		$lcEncabezado .= str_pad(utf8_decode('TIPO PRÉSTAMO'), 25);
		$lcEncabezado .= str_pad(utf8_decode('FECHA'), 15);
		$lcEncabezado .= str_pad(utf8_decode('VALOR'), 15);
		$lcEncabezado .= str_pad(utf8_decode('CUOTA'), 10);
		$lcEncabezado .= str_pad(utf8_decode('CUOTAS'), 10);
		$lcEncabezado .= str_pad(utf8_decode('SALDO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('ESTADO'), 20);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 

		$EmpleadoAnt = '';
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			if (utf8_decode($reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2']) <> $EmpleadoAnt)
			{ 
				$PDF->Cell(45, 5, substr(utf8_decode($reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2']), 0, 50), 0, 0, 'L'); 
				$EmpleadoAnt = utf8_decode($reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2']);
			}
			else
				$PDF->Cell(45, 5, '', 0, 0, 'L'); 

			$PDF->Cell(40, 5, substr(utf8_decode($reg['nombreconcepto']), 0, 25), 0, 0, 'L'); 
			$PDF->Cell(25, 5, substr(utf8_decode($reg['nombretipoprestamo']), 0, 15), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['fecha']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, number_format($reg['valorprestamo'], 0), 0, 0, 'R'); 
			$PDF->Cell(15, 5, number_format($reg['valorcuota'], 0), 0, 0, 'R'); 
			$PDF->Cell(10, 5, number_format($reg['cuotas'], 0), 0, 0, 'R'); 
			$PDF->Cell(15, 5, number_format($reg['saldoprestamo'], 0), 0, 0, 'R'); 
			$PDF->Cell(25, 5, substr(utf8_decode($reg['estadoprestamo']), 0, 10), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDePrestamosAEmpleados.PDF', 'I'); 
	}
?>