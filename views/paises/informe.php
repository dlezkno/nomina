<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('PAISES');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('NOMBRE ESPAÑOL'), 20);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE INGLÉS'), 50);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE FRANCÉS'), 50);
		$lcEncabezado .= str_pad(utf8_decode('ISO-2'), 50);
		$lcEncabezado .= str_pad(utf8_decode('ISO-3'), 50);
		$lcEncabezado .= str_pad(utf8_decode('PHONE CODE'), 50);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['nombre1']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nombre2']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['nombre3']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['iso2']), 0, 35), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['iso3']), 0, 35), 0, 0, 'L'); 
			$PDF->Cell(40, 5, substr(utf8_decode($reg['phonecode']), 0, 35), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDePaises.PDF', 'I'); 
	}
?>