<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('PARÁMETROS');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('PARÁMETRO'), 60);
		$lcEncabezado .= str_pad(utf8_decode('DETALLE'), 75);
		$lcEncabezado .= str_pad(utf8_decode('VALOR'), 15);
		$lcEncabezado .= str_pad(utf8_decode('FECHA'), 15);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(55, 5, substr(utf8_decode($reg['parametro']), 0, 40), 0, 0, 'L'); 
			$PDF->Cell(55, 5, substr(utf8_decode($reg['detalle']), 0, 40), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['valor']), 0, 5), 0, 0, 'R'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['fecha']), 0, 15), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeParametros.PDF', 'I'); 
	}
?>