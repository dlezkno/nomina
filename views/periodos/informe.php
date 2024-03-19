<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('PERÍODOS DE PAGO');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('REFERENCIA'), 20);
		$lcEncabezado .= str_pad(utf8_decode('PERIODICIDAD'), 20);
		$lcEncabezado .= str_pad(utf8_decode('PERÍODO'), 20);
		$lcEncabezado .= str_pad(utf8_decode('FECHA DE INICIO'), 20);
		$lcEncabezado .= str_pad(utf8_decode('FECHA FINAL'), 20);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(30, 5, substr(utf8_decode($reg['referencia']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(30, 5, substr(utf8_decode($reg['periodicidad']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(30, 5, substr(utf8_decode($reg['periodo']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(30, 5, substr(utf8_decode($reg['fechainicial']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(30, 5, substr(utf8_decode($reg['fechafinal']), 0, 60), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDePeriodosDePago.PDF', 'I'); 
	}
?>