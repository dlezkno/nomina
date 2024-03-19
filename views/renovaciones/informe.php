<?php 
	global $lcOrientacion;
	global $lcTitulo;
	global $lcSubTitulo;
	global $lcEncabezado;
	global $lcEncabezado2;
								
	$PDF = new PDF(); 
	$PDF->AliasNbPages();
	$lcOrientacion = 'L';

	$lcTitulo = utf8_decode('EMPLEADOS PARA RENOVACIÓN DE CONTRATO');

	$lcEncabezado = '';
	$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 25);
	$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 85);
	$lcEncabezado .= str_pad(utf8_decode('CARGO'), 75);
	$lcEncabezado .= str_pad(utf8_decode('CENTRO / PROYECTO'), 90);
	$lcEncabezado .= str_pad(utf8_decode('FECHA ING.'), 20);
	$lcEncabezado .= str_pad(utf8_decode('FECHA VCTO.'), 20);

	if (count($data['rows']) > 0) 
	{
		// $PDF->SetMargins(15, 10, 10); 
		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage($lcOrientacion);
		$PDF->SetFont('Arial', '', 8); 

		// $PDF->SetTextColor(255, 255, 255);
		// $PDF->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
		// $PDF->SetTextColor(0, 0, 0);
		// $PDF->Ln(); 

		$DocumentoGP = '';
	
		for ($i = 0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			if (! empty($reg['DocumentoGP2']) AND $reg['DocumentoGP2'] <> $DocumentoGP)
			{
				$PDF->SetFont('Arial', 'B', 8); 
				$PDF->Ln(); 
				$PDF->Cell(25, 5, 'GERENTE PROY.', 0, 0, 'L'); 
				$PDF->Cell(25, 5, substr(utf8_decode($reg['DocumentoGP2']), 0, 10), 0, 0, 'L'); 
				$PDF->Cell(60, 5, substr(utf8_decode(trim($reg['Apellido1GP2'])) . ' ' . utf8_decode(trim($reg['Apellido2GP2'])) . ' ' . utf8_decode(trim($reg['Nombre1GP2'])) . ' ' . utf8_decode(trim($reg['Nombre2GP2'])), 0, 60), 0, 0, 'L'); 
				$PDF->Ln(); 
				$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
				$PDF->SetFont('Arial', '', 8); 

				$DocumentoGP = $reg['DocumentoGP2'];
			}
			elseif (! empty($reg['DocumentoGP1']) AND $reg['DocumentoGP1'] <> $DocumentoGP)
			{
				$PDF->SetFont('Arial', 'B', 8); 
				$PDF->Ln(); 
				$PDF->Cell(25, 5, 'GERENTE PROY.', 0, 0, 'L'); 
				$PDF->Cell(25, 5, substr(utf8_decode($reg['DocumentoGP1']), 0, 10), 0, 0, 'L'); 
				$PDF->Cell(60, 5, substr(utf8_decode(trim($reg['Apellido1GP1'])) . ' ' . utf8_decode(trim($reg['Apellido2GP1'])) . ' ' . utf8_decode(trim($reg['Nombre1GP1'])) . ' ' . utf8_decode(trim($reg['Nombre2GP1'])), 0, 60), 0, 0, 'L'); 
				$PDF->Ln(); 
				$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
				$PDF->SetFont('Arial', '', 8); 

				$DocumentoGP = $reg['DocumentoGP1'];
			}

			$PDF->Cell(25, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(65, 5, substr(utf8_decode(trim($reg['Apellido1'])) . ' ' . utf8_decode(trim($reg['Apellido2'])) . ' ' . utf8_decode(trim($reg['Nombre1'])) . ' ' . utf8_decode(trim($reg['Nombre2'])), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreCargo']), 0, 30), 0, 0, 'L'); 

			if (empty($reg['Proyecto']))
			{
				$PDF->Cell(15, 5, $reg['Centro'], 0, 0, 'L'); 
				$PDF->Cell(60, 5, substr($reg['NombreCentro'], 0, 35), 0, 0, 'L'); 
			}
			else
			{
				$PDF->Cell(15, 5, $reg['Proyecto'], 0, 0, 'L'); 
				$PDF->Cell(60, 5, substr($reg['NombreProyecto'], 0, 35), 0, 0, 'L'); 
			}

			$PDF->Cell(25, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
			$PDF->Cell(25, 5, $reg['FechaVencimiento'], 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeEmpleadosParaRenovacionContrato.PDF', 'I'); 
	}
?>