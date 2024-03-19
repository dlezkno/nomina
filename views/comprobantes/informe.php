<?php 
	if (count($data['rows']) > 0) 
	{
		global $lcOrientacion;
		global $lcTitulo;
		global $lcSubTitulo;
		global $lcEncabezado;
		global $lcEncabezado2;
	
		$PDF = new PDF(); 
		$PDF->AliasNbPages();
	
		$lcTitulo = utf8_decode('COMPROBANTES DE CONTABILIZACIÓN DE NÓMINA');
		$lcOrientacion = 'L';
		$lcEncabezado = '';
		$lcEncabezado .= str_pad(utf8_decode('DOC.'), 10);
		$lcEncabezado .= str_pad(utf8_decode('CONC.'), 15);
		$lcEncabezado .= str_pad(utf8_decode('DESCRIPCIÓN'), 90);
		$lcEncabezado .= str_pad(utf8_decode('TIPO EMP.'), 23);
		$lcEncabezado .= str_pad(utf8_decode('IMPUTACIÓN'), 20);
		$lcEncabezado .= str_pad(utf8_decode('PORC.'), 14);
		$lcEncabezado .= str_pad(utf8_decode('CUENTA DB'), 15);
		$lcEncabezado .= str_pad(utf8_decode('X CENTRO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('CUENTA CR'), 15);
		$lcEncabezado .= str_pad(utf8_decode('X CENTRO'), 15);
		$lcEncabezado .= str_pad(utf8_decode('TIPO TERC.'), 15);

		$PDF->AddFont('Tahoma','','tahoma.php');
		$PDF->AddPage($lcOrientacion);
		$PDF->SetFont('Tahoma', '', 8);

		for ($i=0; $i < count($data['rows']); $i++)
		{
			$reg = $data['rows'][$i];

			$PDF->Cell(10, 5, substr(utf8_decode($reg['TipoDocumento']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 10), 0, 0, 'L'); 
			$PDF->Cell(70, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 
			$PDF->Cell(25, 5, substr(utf8_decode($reg['TipoEmpleado']), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(20, 5, substr(utf8_decode($reg['Imputacion']), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(15, 5, number_format($reg['Porcentaje'], 2), 0, 0, 'L'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['CuentaDb']), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(21, 5, ($reg['DetallaCentroDb'] ? 'SI' : 'NO'), 0, 0, 'C'); 
			$PDF->Cell(15, 5, substr(utf8_decode($reg['CuentaCr']), 0, 20), 0, 0, 'L'); 
			$PDF->Cell(21, 5, ($reg['DetallaCentroCr'] ? 'SI' : 'NO'), 0, 0, 'C'); 
			$PDF->Cell(50, 5, substr(utf8_decode($reg['TipoTercero']), 0, 60), 0, 0, 'L'); 
			$PDF->Ln(); 
		} 

		$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
		
		$PDF->Output('InformeDeComprobantesDeNomina.PDF', 'I'); 
	}
?>