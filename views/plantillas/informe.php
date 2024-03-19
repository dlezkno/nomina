<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('PLANTILLAS');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('ESTADO EMPLEADO'), 30);
		$lcEncabezado .= str_pad(utf8_decode('TIPO PLANTILLA'), 30);
		$lcEncabezado .= str_pad(utf8_decode('TIPO CONTRATO'), 50);
		$lcEncabezado .= str_pad(utf8_decode('ASUNTO'), 50);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(35, 5, substr(utf8_decode($reg['nombreestadoempleado']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(35, 5, substr(utf8_decode($reg['nombretipoplantilla']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nombretipocontrato']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['asunto']), 0, 60), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDePlantillas.PDF', 'I'); 
	}
?>