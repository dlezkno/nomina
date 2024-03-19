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
	
		$lcTitulo = utf8_decode('DEDUCCIONES RTE. FTE.');
		$lcSubTitulo = '';

		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('EMPLEADO'), 18);
		$lcEncabezado .= str_pad(utf8_decode('NOMBRE'), 75);
		$lcEncabezado .= str_pad(utf8_decode('CUOTA VIVIENDA'), 40);
		$lcEncabezado .= str_pad(utf8_decode('SALUD'), 15);
		$lcEncabezado .= str_pad(utf8_decode('ALIMENTACIÓN'), 25);
		$lcEncabezado .= str_pad(utf8_decode('DEPENDIENTES'), 25);

		$PDF->AddPage($lcOrientacion);
		
		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(20, 5, substr(utf8_decode($reg['Documento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(55, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(25, 5, number_format($reg['CuotaVivienda'], 0), 0, 0, 'R'); 
			$PDF->Cell(25, 5, number_format($reg['SaludYEducacion'], 0), 0, 0, 'R'); 
			$PDF->Cell(30, 5, number_format($reg['Alimentacion'], 0), 0, 0, 'R'); 
			$PDF->Cell(25, 5, $reg['DeduccionDependientes'] ? 'SI' : '', 0, 0, 'C'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeduccionesRetFte.PDF', 'I'); 
	}
?>