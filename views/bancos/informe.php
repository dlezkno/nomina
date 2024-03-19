<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('ENTIDADES BANCARIAS');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('BANCO'), 30);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 120);
		$lcEncabezado .= str_pad(utf8_decode('NIT.'), 50);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(25, 5, substr(utf8_decode($reg['banco']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(100, 5, substr(utf8_decode($reg['nombre']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nit']), 0, 60), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeEntidadesBancarias.PDF', 'I'); 
	}
?>