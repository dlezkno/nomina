<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('CARGOS DE EMPLEADOS');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('CARGO'), 20);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 50);
		$lcEncabezado .= str_pad(utf8_decode('SUELDO MÍNIMO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('SUELDO MÁXIMO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('CARGO SUPERIOR'), 50);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['cargo']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nombre']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(20, 5, number_format($reg['sueldominimo'], 0), 0, 0, 'R'); 
			$PDF->Cell(20, 5, number_format($reg['sueldomaximo'], 0), 0, 0, 'R'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nombrecargosuperior']), 0, 60), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeCargosDeEmpleados.PDF', 'I'); 
	}
?>