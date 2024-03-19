<?php 
	global $lcOrientacion;
	global $lcTitulo;
	global $lcSubTitulo;
	global $lcEncabezado;
	global $lcEncabezado2;

	$PDF = new PDF(); 
	$PDF->AliasNbPages();


	if (count($data['rows']) > 0) 
	{
		global $lcOrientacion;
		global $lcTitulo;
		global $lcSubTitulo;
		global $lcEncabezado;
		global $lcEncabezado2;
	
		$PDF = new PDF(); 
		$PDF->AliasNbPages();

		$lcOrientacion = 'P';
	
		$lcTitulo = utf8_decode('RETIROS DE EMPLEADOS');
		$lcSubTitulo = '';

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('EMPLEADO'), 18);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 70);
		$lcEncabezado .= str_pad(utf8_decode('FECHA RET.'), 25);
		$lcEncabezado .= str_pad(utf8_decode('FECHA LIQ.'), 25);
		$lcEncabezado .= str_pad(utf8_decode('MOTIVO RETIRO'), 20);

		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddPage($lcOrientacion);
		
		// $PDF->SetTextColor(255, 255, 255);
		// $PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		// $PDF->Ln(); 
		// $PDF->Cell(0, 5, $lcEncabezado2, 0, 0, 'L', TRUE);
		// $PDF->SetTextColor(0, 0, 0);
		// $PDF->Ln(); 
	
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(55, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(25, 5, substr(utf8_decode($reg['FechaRetiro']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(25, 5, substr(utf8_decode($reg['FechaLiquidacion']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, utf8_decode($reg['MotivoRetiro']), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeRetirosEmpleados.PDF', 'I'); 
	}
?>