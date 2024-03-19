<?php 
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcTitulo = utf8_decode('EMPLEADOS');
	$lcEncabezado = '';

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage();
		$PDF->SetFont('Tahoma', '', 7);

		$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 30);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 70);
		$lcEncabezado .= str_pad(utf8_decode('CARGO'), 40);
		$lcEncabezado .= str_pad(utf8_decode('E-MAIL'), 60);
		$lcEncabezado .= str_pad(utf8_decode('CELULAR'), 50);

		$PDF->SetTextColor(255, 255, 255);
		$PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		$PDF->SetTextColor(0, 0, 0);
		$PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(25, 5, substr(utf8_decode($reg['documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(60, 5, substr(utf8_decode(trim($reg['apellido1'])) . ' ' . utf8_decode(trim($reg['apellido2'])) . ' ' . utf8_decode(trim($reg['nombre1'])) . ' ' . utf8_decode(trim($reg['nombre2'])), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(30, 5, substr(utf8_decode($reg['NombreCargo']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(55, 5, substr(utf8_decode($reg['email']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['celular']), 0, 60), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
		
		$PDF->Output('InformeDeEmpleados.PDF', 'I'); 
	}
?>