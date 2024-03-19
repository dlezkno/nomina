<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class InformesEmpleados extends Controllers
	{
		public function informes()
		{
			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'IdCentro' => isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'Empleado' => isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : 0
					),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'ELIMINAR' AND file_exists($_REQUEST['Archivo']))
			{
				unlink($_REQUEST['Archivo']); 
				$this->views->getView($this, 'informes', $data);
			}
			else
			{
				if (isset($_REQUEST['Informe']))
				{
					if ($_REQUEST['Informe'] == 13) 
					{
						$query = <<<EOD
							WHERE PARAMETROS1.Detalle = 'RETIRADO' 
						EOD;
					}
					else
					{
						$query = <<<EOD
							WHERE PARAMETROS1.Detalle = 'ACTIVO' 
						EOD;
					}

					if (! empty($_REQUEST['IdCentro'])) 
					{
						$IdCentro = $_REQUEST['IdCentro'];

						$query .= <<<EOD
							AND EMPLEADOS.IdCentro = $IdCentro  
						EOD;
					}

					if (! empty($_REQUEST['Empleado'])) 
					{
						$Empleado = $_REQUEST['Empleado'];

						$query .= <<<EOD
							AND EMPLEADOS.Documento = '$Empleado' 
						EOD;
					}

					if (! empty($_REQUEST['TipoEmpleados'])) 
					{
						$TipoEmpleados = $_REQUEST['TipoEmpleados'];

						$query .= <<<EOD
							AND EMPLEADOS.TipoEmpleado = $TipoEmpleados 
						EOD;
					}

					switch ($_REQUEST['Informe'])
					{
						// EMPLEADOS GENERAL 1
						case 1:
						// EMPLEADOS RETIRADOS
						case 13:
							$isRetired = $_REQUEST['Informe']==13 ? true : false;
							$datos = $this->model->empleadosGeneral1($query, $isRetired);

							if (count($datos) > 0)  {
								$Archivo = $_REQUEST['Informe']==1 ? 'General1' : 'Retirados';
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar')  {
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_Empleados' . $Archivo . '_' . date('YmdGis') . '.csv';
									generateCSV($Archivo, $datos);
								}
								else {
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();

									$lcTitulo = utf8_decode($isRetired ? 'EMPLEADOS (GENERAL)' : 'EMPLEADOS RETIRADOS');
									$lcOrientacion = 'L';
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('Tipo de Identificación'), 25);
									$lcEncabezado .= str_pad(utf8_decode('No. Identificación'), 25);
									$lcEncabezado .= str_pad(utf8_decode('APELLIDOS Y NOMBRES'), 65);
									$lcEncabezado .= str_pad(utf8_decode($isRetired ? 'Retiro' : 'Ingreso'), 22);
									$lcEncabezado .= str_pad(utf8_decode('Cargo'), 95);
									$lcEncabezado .= str_pad(utf8_decode('Salario'), 20);
									$lcEncabezado .= str_pad(utf8_decode('Centro de Costo'), 20);

									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage($lcOrientacion);
									$PDF->SetFont('Tahoma', '', 8);

									for ($i = 0; $i < count($datos); $i++) {
										$reg = $datos[$i];

										$PDF->Cell(25, 5, $reg['Tipo de Identificación'], 0, 0, 'L');
										$PDF->Cell(25, 5, number_format($reg['No. Identificación'], 0), 0, 0, 'L');
										$PDF->Cell(60, 5, substr(utf8_decode($reg['APELLIDOS Y NOMBRES']), 0, 60), 0, 0, 'L');
										$PDF->Cell(18, 5, $reg[$isRetired ? 'Fecha de retiro' : 'Fecha de ingreso'], 0, 0, 'L');
										$PDF->Cell(70, 5, substr(utf8_decode($reg['Cargo']), 0, 38), 0, 0, 'L');
										$PDF->Cell(15, 5, '$' . number_format($reg['Salario básico'], 0), 0, 0, 'R');
										$PDF->Cell(40, 5, substr(utf8_decode($reg['Centro de Costo']), 0, 35), 0, 0, 'L');

										$PDF->Ln(); 
									} 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
									$PDF->Cell(85, 5, utf8_decode('TOTAL EMPLEADOS '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(18, 5, number_format(count($datos), 0), 0, 0, 'L'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
						
									$PDF->Output('Empleados'.$Archivo.'.PDF', 'I'); 
								}
							}
							else {
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS GENERAL 2
						case 2:
							$datos = $this->model->empleadosGeneral2($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosGeneral2_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'DIRECCION', 'CIUDAD', 'TELEFONO', 'CELULAR', 'EMAIL'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS (GENERAL)');
									$lcOrientacion = 'L';
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 45);
									$lcEncabezado .= str_pad(utf8_decode('DIRECCION'), 45);
									$lcEncabezado .= str_pad(utf8_decode('CIUDAD'), 20);
									$lcEncabezado .= str_pad(utf8_decode('TELEFONO'), 17);
									$lcEncabezado .= str_pad(utf8_decode('CELULAR'), 20);
									$lcEncabezado .= str_pad(utf8_decode('E-MAIL'), 20);

									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage($lcOrientacion);
									$PDF->SetFont('Tahoma', '', 8);

									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(60, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
										$PDF->Cell(50, 5, substr(utf8_decode($reg['Direccion']), 0, 50), 0, 0, 'L'); 
										$PDF->Cell(25, 5, substr(utf8_decode($reg['NombreCiudad']), 0, 50), 0, 0, 'L'); 
										$PDF->Cell(25, 5, substr(utf8_decode($reg['Telefono']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, substr(utf8_decode($reg['Celular']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(40, 5, substr(utf8_decode($reg['Email']), 0, 40), 0, 0, 'L'); 

										$PDF->Ln(); 
									} 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
									$PDF->Cell(85, 5, utf8_decode('TOTAL EMPLEADOS '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(18, 5, number_format(count($datos), 0), 0, 0, 'L'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
						
									$PDF->Output('EmpleadosGeneral2.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR CENTRO
						case 3:
							$datos = $this->model->empleadosPorCentro($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorCentro_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('CENTRO', 'NOMBRE CENTRO', 'PROYECTO', 'NOMBRE PROYECTO', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR CENTRO DE COSTOS');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$CentroAnt = '';
									$ProyectoAnt = '';
									$NombreCentroAnt = '';
									$NombreProyectoAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Centro'] . $reg['Proyecto'] <> $CentroAnt . $ProyectoAnt) 
										{
											if (! empty($CentroAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode("TOTALES POR $NombreCentroAnt - $NombreProyectoAnt"), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(25, 5, utf8_decode($reg['Centro']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreCentro']), 0, 60), 0, 0, 'L'); 
											$PDF->Ln(); 
											
											if (! empty($reg['Proyecto']))
											{
												$PDF->Cell(25, 5, utf8_decode($reg['Proyecto']), 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreProyecto']), 0, 60), 0, 0, 'L'); 
												$PDF->Ln(); 
											}
											
											$PDF->SetFont('Arial', '', 8); 

											$CentroAnt 			= $reg['Centro'];
											$NombreCentroAnt 	= $reg['NombreCentro'];
											$ProyectoAnt 		= $reg['Proyecto'];
											$NombreProyectoAnt 	= $reg['NombreProyecto'];
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode("TOTALES POR $NombreCentroAnt - $NombreProyectoAnt"), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorCentro.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR CARGO
						case 4:
							$datos = $this->model->empleadosPorCargo($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorCargo_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('CARGO', 'NOMBRE CARGO', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR CARGO');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CENTRO DE COSTOS'), 45);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$CargoAnt = '';
									$NombreCargoAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Cargo'] <> $CargoAnt) 
										{
											if (! empty($CargoAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreCargoAnt), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(25, 5, utf8_decode($reg['Cargo']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreCargo']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$CargoAnt = $reg['Cargo'];
											$NombreCargoAnt = $reg['NombreCargo'];
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCentro']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreCargoAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorCargo.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR CATEGORIA
						case 5:
							$datos = $this->model->empleadosPorCategoria($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorCategoria_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('CATEGORIA', 'NOMBRE CATEGORIA', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR CATEGORÍA');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CENTRO DE COSTOS'), 45);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$CategoriaAnt = ' ';
									$NombreCategoriaAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Categoria'] <> $CategoriaAnt) 
										{
											if ($CategoriaAnt <> ' ')
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreCategoriaAnt), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											if (empty($reg['Categoria'])) 
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, '', 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode('SIN CATEGORÍA'), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$CategoriaAnt = $reg['Categoria'];
												$NombreCategoriaAnt = 'SIN CATEGORÍA';
											}
											else
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, utf8_decode($reg['Categoria']), 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreCategoria']), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$CategoriaAnt = $reg['Categoria'];
												$NombreCategoriaAnt = $reg['NombreCategoria'];
											}
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCentro']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreCategoriaAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorCategoria.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR ANTIGUEDAD
						case 6:
							$datos = $this->model->empleadosPorAntiguedad($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorAntiguedad_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'DIRECCION', 'CIUDAD', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO', 'ANTIGUEDAD'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										$reg['Antiguedad'] = Antiguedad($reg['FechaIngreso']);

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR ANTIGÜEDAD');
									$lcOrientacion = 'L';
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 45);
									$lcEncabezado .= str_pad(utf8_decode('DIRECCIÓN'), 68);
									$lcEncabezado .= str_pad(utf8_decode('CIUDAD'), 25);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 25);
									$lcEncabezado .= str_pad(utf8_decode('ANTIGÜEDAD'), 30);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS.'), 13);

									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage($lcOrientacion);
									$PDF->SetFont('Tahoma', '', 8);

									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(60, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
										$PDF->Cell(70, 5, substr(utf8_decode($reg['Direccion']), 0, 38), 0, 0, 'L'); 
										$PDF->Cell(30, 5, substr(utf8_decode($reg['NombreCiudad']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(30, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(45, 5, Antiguedad($reg['FechaIngreso']), 0, 0, 'L'); 
										$PDF->Cell(15, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$PDF->Ln(); 
									} 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
									$PDF->Cell(85, 5, utf8_decode('TOTAL EMPLEADOS '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(18, 5, number_format(count($datos), 0), 0, 0, 'L'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
						
									$PDF->Output('EmpleadosPorAntiguedad.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR EPS
						case 7:
							$datos = $this->model->empleadosPorEPS($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorEPS_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('NIT EPS', 'NOMBRE EPS', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR E.P.S.');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$EPSAnt = ' ';
									$NombreEPSAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['DocumentoEPS'] <> $EPSAnt) 
										{
											if ($EPSAnt <> ' ')
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . substr($NombreEPSAnt, 0, 40)), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											if (empty($reg['DocumentoEPS'])) 
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, '', 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode('SIN E.P.S. ASIGNADA'), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$EPSAnt = $reg['DocumentoEPS'];
												$NombreEPSAnt = 'SIN E.P.S. ASIGNADA';
											}
											else
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, utf8_decode($reg['DocumentoEPS']), 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreEPS']), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$EPSAnt = $reg['DocumentoEPS'];
												$NombreEPSAnt = $reg['NombreEPS'];
											}
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreEPSAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorEPS.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR FONDO CESANTIAS
						case 8:
							$datos = $this->model->empleadosPorFC($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorFondoCesantias_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('NIT FDO. CES.', 'NOMBRE FDO. CES.', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR FONDO DE CESANTÍAS');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$FCAnt = ' ';
									$NombreFCAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['DocumentoFC'] <> $FCAnt) 
										{
											if ($FCAnt <> ' ')
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . substr($NombreFCAnt, 0, 40)), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											if (empty($reg['DocumentoFC'])) 
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, '', 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode('SIN FONDO DE CESANTÍAS ASIGNADO'), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$FCAnt = $reg['DocumentoFC'];
												$NombreFCAnt = 'SIN FONDO DE CESANTÍAS ASIGNADO';
											}
											else
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, utf8_decode($reg['DocumentoFC']), 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreFC']), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$FCAnt = $reg['DocumentoFC'];
												$NombreFCAnt = $reg['NombreFC'];
											}
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreFCAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorFondoCesantias.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR FONDO DE PENSIONES
						case 9:
							$datos = $this->model->empleadosPorFP($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorFondoPensiones_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('NIT FDO. PEN.', 'NOMBRE FDO. PEN.', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR FONDO DE PENSIONES');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$FPAnt = ' ';
									$NombreFPAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['DocumentoFP'] <> $FPAnt) 
										{
											if ($FPAnt <> ' ')
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . substr($NombreFPAnt, 0, 40)), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											if (empty($reg['DocumentoFP'])) 
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, '', 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode('SIN FONDO DE PENSIONES ASIGNADO'), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$FPAnt = $reg['DocumentoFP'];
												$NombreFPAnt = 'SIN FONDO DE PENSIONES ASIGNADO';
											}
											else
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, utf8_decode($reg['DocumentoFP']), 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreFP']), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$FPAnt = $reg['DocumentoFP'];
												$NombreFPAnt = $reg['NombreFP'];
											}
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreFPAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorFondoPensiones.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS POR ARP
						case 10:
							$datos = $this->model->empleadosPorARP($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosPorARL_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('NIT ARP', 'NOMBRE ARP', 'DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS POR A.R:P.');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 13);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$ARPAnt = ' ';
									$NombreARPAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['DocumentoARP'] <> $ARPAnt) 
										{
											if ($ARPAnt <> ' ')
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . substr($NombreARPAnt, 0, 40)), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalEmpleados = 0;
												$TotalSueldo = 0;
											}

											if (empty($reg['DocumentoARP'])) 
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, '', 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode('SIN A.R.P. ASIGNADA'), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$ARPAnt = $reg['DocumentoARP'];
												$NombreARPAnt = 'SIN A.R.P. ASIGNADA';
											}
											else
											{
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, utf8_decode($reg['DocumentoARP']), 0, 0, 'L'); 
												$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreARP']), 0, 60), 0, 0, 'L'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$ARPAnt = $reg['DocumentoARP'];
												$NombreARPAnt = $reg['NombreARP'];
											}
										}

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];
										$GranTotalEmpleados++;
										$GranTotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreARPAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosPorARP.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS EN PERIODO DE PRUEBA
						case 11:
							$datos = $this->model->empleadosEnPeriodoPrueba($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosEnPeriodoPrueba_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'FECHA PRUEBA', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
									$lcOrientacion = 'L';
								
									$lcTitulo = utf8_decode('EMPLEADOS EN PERÍODO DE PRUEBA');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 12);
									$lcEncabezado .= str_pad(utf8_decode('EN PRUEBA'), 22);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage($lcOrientacion);
									$PDF->SetFont('Arial', '', 8);

									$DocumentoAnt = ' ';
									$NombreEmpleadoAnt = '';
									$TotalEmpleados = 0;
									$TotalSueldo = 0;
									$GranTotalEmpleados = 0;
									$GranTotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(30, 5, $reg['FechaPeriodoPrueba'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosEnPeriodoPrueba.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// EMPLEADOS NUEVOS
						case 12:
							$datos = $this->model->empleadosNuevos($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EmpleadosNuevos_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CARGO', 'NOMBRE CARGO', 'CENTRO', 'NOMBRE CENTRO', 'SUELDO BASICO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('EMPLEADOS NUEVOS');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('INGRESO'), 12);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 58);
									$lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$TotalEmpleados = 0;
									$TotalSueldo = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(18, 5, $reg['FechaIngreso'], 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'R'); 

										$TotalEmpleados++;
										$TotalSueldo += $reg['SueldoBasico'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalEmpleados, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalSueldo, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output('EmpleadosNuevos.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;
						// CUMPLEAÑOS EMPLEADOS
						case 14:
							$datos = $this->model->cumpleanosEmpleados($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_CumpleanosEmpleados_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'CARGO', 'NOMBRE CARGO', 'FECHA NACIMIENTO', 'EDAD'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										$reg['Edad'] = round(Dias365(date('Y-m-d'), $reg['FechaNacimiento']) / 365, 0);

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('CUMPLEAÑOS DE EMPLEADOS');
									$lcEncabezado = '';
									$lcEncabezado .= str_pad('', 12);
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 56);
									$lcEncabezado .= str_pad(utf8_decode('FECHA NAC.'), 12);
									$lcEncabezado .= str_pad(utf8_decode('EDAD'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									// $PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$MesAnt = '';
									$DiaAnt = 0;

									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if (date('m', strtotime($reg['FechaNacimiento'])) <> $MesAnt) 
										{
											$PDF->AddPage();
											$PDF->SetFont('Arial', 'B', 12); 
											$PDF->Cell(25, 5, NombreMes(date('m', strtotime($reg['FechaNacimiento']))), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 
											$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

											$MesAnt = date('m', strtotime($reg['FechaNacimiento']));
										}

										if (date('d', strtotime($reg['FechaNacimiento'])) <> $DiaAnt)
										{ 
											$PDF->Ln(); 
											$DiaAnt = date('d', strtotime($reg['FechaNacimiento']));
										}

										$PDF->SetFont('Arial', 'B', 8); 
										$PDF->Cell(10, 5, date('d', strtotime($reg['FechaNacimiento'])), 0, 0, 'L'); 
										$PDF->SetFont('Arial', '', 8); 
										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(25, 5, $reg['FechaNacimiento'], 0, 0, 'L'); 
										$PDF->Cell(18, 5, round(Dias365(date('Y-m-d'), $reg['FechaNacimiento']) / 365, 0), 0, 0, 'L'); 

										$PDF->Ln(); 
									}
							
									$PDF->Output('CumpleanosEmpleados.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// LISTA ENTREGA DOCUMENTOS
						case 16:
							$datos = $this->model->entregaDocumentos($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_EntregaDocumentos_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('DOCUMENTO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'CENTRO', 'NOMBRE CENTRO', 'CARGO', 'NOMBRE CARGO', 'TIPO EMPLEADO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg, ';');
									}
		
									fclose($output);

									header('Content-Description: File Transfer');
									header('Content-Type: text/csv');
									header('Content-Disposition: attachment; filename=' . basename($Archivo));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo));
									ob_clean();
									flush();
									readfile($Archivo);
								}
								else
								{
									global $lcOrientacion;
									global $lcTitulo;
									global $lcSubTitulo;
									global $lcEncabezado;
									global $lcEncabezado2;
								
									$PDF = new PDF(); 
									$PDF->AliasNbPages();
								
									$lcTitulo = utf8_decode('ENTREGA DE DOCUMENTOS');
									$lcSubTitulo = '__________________________________________________________________';
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 50);
									$lcEncabezado .= str_pad(utf8_decode('CARGO'), 56);
									$lcEncabezado .= str_pad(utf8_decode('FIRMA'), 20);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$MesAnt = '';
									$DiaAnt = 0;

									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Ln(); 

										$PDF->Cell(25, 5, number_format($reg['documento'], 0), 0, 0, 'L'); 
										$PDF->Cell(65, 5, substr(utf8_decode($reg['apellido1'] . ' ' . $reg['apellido2'] . ' ' . $reg['nombre1'] . ' ' . $reg['nombre2']), 0, 30), 0, 0, 'L'); 
										$PDF->Cell(57, 5, substr(utf8_decode($reg['NombreCargo']), 0, 25), 0, 0, 'L'); 
										$PDF->Cell(60, 5, '___________________________', 0, 0, 'L'); 

										$PDF->Ln(); 
									}
							
									$PDF->Output('EntregaDocumentos.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;
					}

					$this->views->getView($this, 'informes', $data);
				}
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/informesEmpleados/informes';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = SERVERURL . '/informesEmpleados/informes';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';

			if ($data) 
				$this->views->getView($this, 'informes', $data);
		}
	}
?>