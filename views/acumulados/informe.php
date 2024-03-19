<?php 
	global $lcOrientacion;
	global $lcTitulo;
	global $lcSubTitulo;
	global $lcEncabezado;
	global $lcEncabezado2;

	$PDF = new PDF(); 
	$PDF->AliasNbPages();

	$lcOrientacion = 'L';
	
	if (count($data['rows']) > 0) 
	{
		$lcTitulo = utf8_decode('ACUMULADOS DE EMPLEADOS');

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('EMPLEADO'), 17);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 65);
		$lcEncabezado .= str_pad(utf8_decode('CENTRO'), 65);
		$lcEncabezado .= str_pad(utf8_decode('FECHA INI.'), 20);
		$lcEncabezado .= str_pad(utf8_decode('FECHA FIN.'), 20);
		$lcEncabezado .= str_pad(utf8_decode('CONCEPTO'), 53);
		$lcEncabezado .= str_pad(utf8_decode('HOR/DIA'), 25);
		$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 20);
		$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 20);

		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage($lcOrientacion);
		$PDF->SetFont('Tahoma', '', 8);

		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$Mayor = $reg['Mayor'];
			$Auxiliar = $reg['Auxiliar'];

			$regMayor = getRegistro('MAYORES', 0, "MAYORES.Mayor = '$Mayor' ");
			$IdMayor = $regMayor['id'];
			$TipoLiquidacion = $regMayor['tipoliquidacion'];
			$TipoLiquidacion = getRegistro('PARAMETROS', $TipoLiquidacion)['detalle'];
			$regAuxiliar = getRegistro('AUXILIARES', 0, "AUXILIARES.IdMayor = $IdMayor AND AUXILIARES.Auxiliar = '$Auxiliar' "); 
			$Imputacion = $regAuxiliar['imputacion'];
			$Imputacion = getRegistro('PARAMETROS', $Imputacion)['detalle'];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 15), 0, 0, 'L'); 
			$PDF->Cell(60, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(50, 5, substr(utf8_decode($reg['NombreCentro']), 0, 40), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['FechaInicialPeriodo']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['FechaFinalPeriodo']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(45, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 40), 0, 0, 'L'); 
			if ($reg['Horas'] > 0)
				if ($TipoLiquidacion == 'HORAS')
					$PDF->Cell(15, 5, number_format($reg['Horas'], 0) . 'H', 0, 0, 'R'); 
				else
					$PDF->Cell(15, 5, number_format($reg['Horas'] / 8, 0) . 'D', 0, 0, 'R'); 
			else
				$PDF->Cell(15, 5, '', 0, 0, 'R'); 

			if	($Imputacion == 'PAGO')
				$PDF->Cell(20, 5, number_format($reg['Valor'], 0), 0, 0, 'R'); 
			else
				$PDF->Cell(20, 5, '', 0, 0, 'R'); 
			
			if	($Imputacion == 'DEDUCCIÓN')
				$PDF->Cell(25, 5, number_format($reg['Valor'], 0), 0, 0, 'R'); 
			else
				$PDF->Cell(20, 5, '', 0, 0, 'R'); 
			
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeAcumulados.PDF', 'I'); 
	}
?>