<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class InformesNomina extends Controllers
	{
		public function informes()
		{
			set_time_limit(0);

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			// $reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia 	= isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : $reg1['valor'];
			$IdPeriodicidad = isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : $reg2['valor'];
			$Periodicidad 	= getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$cPeriodicidad 	= substr($Periodicidad, 0, 1);

			if (isset($_REQUEST['Periodo'])) 
			{
				$Periodo = $_REQUEST['Periodo'];

				$query = <<<EOD
					PERIODOS.Referencia = $Referencia AND 
					PERIODOS.Periodicidad = $IdPeriodicidad AND 
					PERIODOS.Periodo = $Periodo
				EOD;

				

				$regPeriodo = getRegistro('PERIODOS', 0, $query);
				$IdPeriodo	= $regPeriodo['id'];
			}
			else
			{
				$regPeriodo = getRegistro('PERIODOS', $reg3['valor']);
				$Periodo 	= $regPeriodo['periodo'];
				$IdPeriodo 	= $regPeriodo['id'];
			}

			if (isset($_REQUEST['Ciclo']))
				$Ciclo = $_REQUEST['Ciclo'];
			else
				$Ciclo = 1;

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Referencia' 	=> $Referencia, 
					'Periodicidad' 	=> $IdPeriodicidad, 
					'Periodo' 		=> $Periodo, 
					'Ciclo'			=> $Ciclo,
					'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : 0, 
					'Empleado' 		=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'IdCentro' 		=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'IdProyecto'	=> isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : 0
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
					$P_TipoEmpleados 	= $_REQUEST['TipoEmpleados'];
					$P_Empleado 		= $_REQUEST['Empleado'];
					$P_IdCentro 		= $_REQUEST['IdCentro'];
					$P_IdProyecto 		= $_REQUEST['IdProyecto'];
		
					$FechaInicial 		= $regPeriodo['fechainicial'];
					$FechaFinal 		= $regPeriodo['fechafinal'];

					$FechaInicialPeriodoAnterior = ComienzoMes(date('Y-m-d', strtotime($FechaInicial . ' -1 day')));
					$FechaFinalPeriodoAnterior = FinMes($FechaInicialPeriodoAnterior);

					if ($_REQUEST['Informe'] == 15)
						$query = <<<EOD
							WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND $ArchivoNomina.Ciclo = $Ciclo 
						EOD;
					else
					{
						if ($Ciclo == 0)
							$query = <<<EOD
								WHERE ACUMULADOS.IdPeriodo = $IdPeriodo 
							EOD;
						else
							$query = <<<EOD
								WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
									ACUMULADOS.Ciclo = $Ciclo 
							EOD;
					}

					if (! empty($P_TipEmpleados)) 
					{
						if ($_REQUEST['Informe'] == 15)
							$query .= <<<EOD
								AND $ArchivoNomina.TipoEmpleado = $P_TipoEmpleados 
							EOD;
						else
							$query .= <<<EOD
								AND ACUMULADOS.TipoEmpleado = $P_TipoEmpleados 
							EOD;
					}

					if (! empty($P_Empleado)) 
					{
						$query .= <<<EOD
							AND EMPLEADOS.Documento = '$P_Empleado' 
						EOD;
					}

					if (! empty($P_IdCentro)) 
					{
						if ($_REQUEST['Informe'] == 15)
							$query .= <<<EOD
								AND EMPLEADOS.IdCentro = $P_IdCentro 
							EOD;
						else
							$query .= <<<EOD
								AND EMPLEADOS.IdCentro = $P_IdCentro 
							EOD;
					}

					if (! empty($P_IdProyecto)) 
					{
						if ($_REQUEST['Informe'] == 15)
							$query .= <<<EOD
								AND EMPLEADOS.IdProyeto = $P_IdProyecto 
							EOD;
						else
							$query .= <<<EOD
								AND EMPLEADOS.IdProyecto = $P_IdProyecto 
							EOD;
					}

					switch ($_REQUEST['Informe'])
					{
						// COMPROBANTE DE PAGO
						case 1:
							$datos = $this->model->comprobantePago($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_ComprobantesDePago_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');

									fputcsv($output, array('EMPLEADO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FECHA INGRESO', 'CENTRO', 'NOMBRE CENTRO', 'NOMBRE CARGO', 'MAYOR', 'AUXILIAR', 'DESCRIPCION', 'IMPUTACION', 'TIPO LIQ', 'HORAS', 'VALOR', 'SALDO'), ';');
		
									$reg2 = array();

									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											if ($key == 'SueldoBasico' OR 
												$key == 'FechaVencimiento' OR 
												$key == 'NombreFormaDePago' OR
												$key == 'NombreBanco' OR 
												$key == 'CuentaBancaria' OR 
												$key == 'NombreTipoCuentaBancaria' OR 
												$key == 'NombreEPS' OR 
												$key == 'NombreFondoPension')
												continue;

											$reg2[$key] = utf8_decode($value);
										}

										fputcsv($output, $reg2, ';');
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
								
									$lcTitulo = utf8_decode('COMPROBANTE DE PAGO DE NÓMINA');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
							
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Tahoma', '', 8);

									$EmpleadoAnt = 0;

									$NombreFormaDePagoAnt 	= '';
									$NombreBancoAnt			= '';
									$NombreTipoCuentaAnt 	= '';
									$CuentaBancariaAnt		= '';

									$TotalPagos = 0;
									$TotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Documento'] <> $EmpleadoAnt) 
										{
											if (! empty($EmpleadoAnt)) 
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
												$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												if ($NombreFormaDePagoAnt == 'TRANSFERENCIA BANCARIA') 
													if ($NombreTipoCuentaAnt == 'CUENTA DE AHORROS')
														$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( AH ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
													else
														$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( CC ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
												else
													$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreFormaDePagoAnt), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(25, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

												$TotalPagos = 0;
												$TotalDeducciones = 0;

												$PDF->AddPage();
											}

											$PDF->Cell(25, 7, utf8_decode('DOCUMENTO: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(75, 7, number_format($reg['Documento'], 0), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Cell(35, 7, utf8_decode('SUELDO BÁSICO: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(60, 7, '$' . number_format($reg['SueldoBasico'], 0), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 
											$PDF->Cell(25, 7, utf8_decode('NOMBRE: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(75, 7, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 35), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Cell(35, 7, utf8_decode('FECHA DE INGRESO: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(60, 7, $reg['FechaIngreso'], 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 
											$PDF->Cell(25, 7, utf8_decode('CARGO: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(75, 7, substr(utf8_decode($reg['NombreCargo']), 0, 35), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Cell(35, 7, utf8_decode('E.P.S.: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(60, 7, substr(utf8_decode($reg['NombreEPS']), 0, 30), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 
											$PDF->Cell(25, 7, utf8_decode('CENTRO: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(75, 7, substr(utf8_decode($reg['Centro'] . ' - ' . $reg['NombreCentro']), 0, 35), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Cell(35, 7, utf8_decode('FONDO DE PENSIÓN: '), 0, 0, 'L'); 
											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(60, 7, substr(utf8_decode($reg['NombreFondoPension']), 0, 30), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 
											$PDF->Ln(); 

											
											$PDF->SetTextColor(255, 255, 255);
											$PDF->Cell(80, 5, utf8_decode('CONCEPTO'), 0, 0, 'L', TRUE); 
											$PDF->Cell(25, 5, utf8_decode('Ho/Di'), 0, 0, 'R', TRUE); 
											$PDF->Cell(25, 5, utf8_decode('PAGOS'), 0, 0, 'R', TRUE); 
											$PDF->Cell(35, 5, utf8_decode('DEDUCCIONES'), 0, 0, 'R', TRUE); 
											$PDF->Cell(25, 5, utf8_decode('SALDO'), 0, 0, 'R', TRUE); 
											$PDF->SetTextColor(0, 0, 0);
											$PDF->Ln(); 

											$EmpleadoAnt = $reg['Documento'];

											$NombreFormaDePagoAnt 	= $reg['NombreFormaDePago'];
											$NombreBancoAnt			= $reg['NombreBanco'];
											$NombreTipoCuentaAnt 	= $reg['NombreTipoCuentaBancaria'];
											$CuentaBancariaAnt		= $reg['CuentaBancaria'];
										}

										$PDF->Cell(80, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 

										if ($reg['Horas'] > 0)
											if ($reg['NombreTipoLiquidacion'] == 'HORAS')
												$PDF->Cell(25, 5, number_format($reg['Horas'], 0) . 'H', 0, 0, 'R'); 
											else
												$PDF->Cell(25, 5, number_format($reg['Horas'] / 8, 0) . 'D', 0, 0, 'R'); 
										else
											$PDF->Cell(25, 5, '', 0, 0, 'R'); 
										
										if ($reg['Imputacion'] == 'PAGO') 
										{
											$PDF->Cell(25, 5, number_format($reg['Valor'], 0), 0, 0, 'R'); 
											$TotalPagos += $reg['Valor'];
										}
										else
											$PDF->Cell(25, 5, '', 0, 0, 'R'); 
										
										if ($reg['Imputacion'] == 'DEDUCCIÓN') 
										{
											$PDF->Cell(35, 5, number_format($reg['Valor'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['Valor'];
										}
										else
											$PDF->Cell(35, 5, '', 0, 0, 'R'); 

										if ($reg['Saldo'] <> 0) 
											$PDF->Cell(25, 5, number_format($reg['Saldo'], 0), 0, 0, 'R'); 

										$PDF->Ln(); 
									} 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(25, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									if ($NombreFormaDePagoAnt == 'TRANSFERENCIA BANCARIA') 
										if ($NombreTipoCuentaAnt == 'CUENTA DE AHORROS')
											$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( AH ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
										else
											$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreBancoAnt . ' ( CC ' . $CuentaBancariaAnt . ')'), 0, 0, 'R'); 
									else
										$PDF->Cell(105, 5, utf8_decode('NETO A PAGAR EN ' . $NombreFormaDePagoAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(25, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
						
									$PDF->Output($_SESSION['Login']['Usuario'] . '_ComprobantesDePago_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR EMPLEADO
						case 2:
							$datos = $this->model->nominaPorEmpleado($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorEmpleado_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');

									fputcsv($output, array('EMPLEADO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'VR. PAGOS', 'VR. DEDUCCIONES'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR EMPLEADO');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 80);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 25);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 20);
									$lcEncabezado .= str_pad(utf8_decode('NETO'), 20);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$TotalPagos = 0;
									$TotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'R'); 
										$PDF->Cell(80, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 

										$PDF->Cell(25, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
										$PDF->Cell(35, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
										$PDF->Cell(25, 5, number_format($reg['ValorPagos'] - $reg['ValorDeducciones'], 0), 0, 0, 'R'); 
										$PDF->Ln(); 

										$TotalPagos += $reg['ValorPagos'];
										$TotalDeducciones += $reg['ValorDeducciones'];
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(25, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->Cell(25, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									// $PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorEmpleado_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR CONCEPTO
						case 3:
							$datos = $this->model->nominaPorConcepto($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorConcepto_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');

									fputcsv($output, array('MAYOR', 'AUXILIAR', 'DESCRIPCION', 'EMPLEADOS', 'VR. PAGOS', 'VR. DEDUCCIONES'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR CONCEPTO');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('CONCEPTO'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE CONCEPTO'), 75);
									$lcEncabezado .= str_pad(utf8_decode('EMPLEADOS'), 20);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 20);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 18);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$TotalPagos = 0;
									$TotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, substr(utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 10), 0, 0, 'L'); 
										$PDF->Cell(80, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 

										$PDF->Cell(25, 5, number_format($reg['Registros'], 0), 0, 0, 'R'); 
										if ($reg['ValorPagos'] > 0) 
											$PDF->Cell(25, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
										else
											$PDF->Cell(25, 5, '', 0, 0, 'R'); 
										if ($reg['ValorDeducciones'] > 0) 
											$PDF->Cell(35, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
										else
											$PDF->Cell(35, 5, '', 0, 0, 'R'); 
										$PDF->Ln(); 

										$TotalPagos += $reg['ValorPagos'];
										$TotalDeducciones += $reg['ValorDeducciones'];
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(50, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Cell(105, 5, utf8_decode('NETO TOTAL  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(50, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorConcepto_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR CENTRO DE COSTO
						case 4:
							$datos = $this->model->nominaPorCentro($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorCentro_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');

									fputcsv($output, array('CENTRO', 'NOMBRE CENTRO', 'EMPLEADOS', 'VR. PAGOS', 'VR. DEDUCCIONES'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR CENTRO DE COSTOS');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('CENTRO'), 20);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE CENTRO'), 110);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 20);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 20);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$TotalPagos = 0;
									$TotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, substr(utf8_decode($reg['Centro']), 0, 10), 0, 0, 'L'); 
										$PDF->Cell(80, 5, substr(utf8_decode($reg['NombreCentro']), 0, 60), 0, 0, 'L'); 

										if ($reg['ValorPagos'] > 0) 
											$PDF->Cell(50, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
										else
											$PDF->Cell(25, 5, '', 0, 0, 'R'); 
										if ($reg['ValorDeducciones'] > 0) 
											$PDF->Cell(35, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
										else
											$PDF->Cell(35, 5, '', 0, 0, 'R'); 
										$PDF->Ln(); 

										$TotalPagos += $reg['ValorPagos'];
										$TotalDeducciones += $reg['ValorDeducciones'];
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(105, 5, utf8_decode('TOTALES  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(50, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(35, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Cell(105, 5, utf8_decode('NETO TOTAL  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(50, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorCentro_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR FORMA DE PAGO
						case 5:
							$datos = $this->model->nominaPorFormaPago($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorFormaDePago_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');

									fputcsv($output, array('EMPLEADO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'FORMA DE PAGO', 'BANCO', 'TIPO CUENTA', 'CUENTA BANCARIA', 'VR. A PAGAR', 'FECHA LIQ.','FECHA CREAC.', 'CENTRO DE COS.', 'PROYECTO'), ';');
		
									for ($i = 0; $i < count($datos); $i++) 
									{ 
										$reg = $datos[$i];

										foreach ($reg as $key => $value) 
										{
											$reg[$key] = utf8_decode($value);

											if ($key == 'CuentaBancaria')
												$reg[$key] = "CTA $value";
											elseif ($key == 'ValorAPagar')
												$reg[$key] = round($value, 0);
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
								
									$lcTitulo = utf8_decode('NÓMINA POR FORMA DE PAGO');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 20, ' ', STR_PAD_LEFT);
									$lcEncabezado .= str_pad(utf8_decode('  NOMBRE EMPLEADO'), 85);
									$lcEncabezado .= str_pad(utf8_decode('BANCO'), 75);
									$lcEncabezado .= str_pad(utf8_decode('TIPO CUENTA'), 45);
									$lcEncabezado .= str_pad(utf8_decode('CUENTA'), 50);
									$lcEncabezado .= str_pad(utf8_decode('VR. PAGO'), 30);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage($lcOrientacion);
									$PDF->SetFont('Arial', '', 8);

									$TotalPagos = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'R'); 
										$PDF->Cell(70, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
										if ($reg['FormaDePago'] == 'EFECTIVO') 
										{
											$PDF->Cell(60, 5, 'EFECTIVO', 0, 0, 'L'); 
											$PDF->Cell(65, 5, '', 0, 0, 'L'); 
										}
										elseif($reg['FormaDePago'] == 'CHEQUE')
										{
											$PDF->Cell(60, 5, 'CHEQUE', 0, 0, 'L'); 
											$PDF->Cell(65, 5, '', 0, 0, 'L'); 
										}
										else
										{
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreBanco']), 0, 60), 0, 0, 'L'); 
											$PDF->Cell(40, 5, substr(utf8_decode($reg['TipoCuentaBancaria']), 0, 20), 0, 0, 'L'); 
											$PDF->Cell(25, 5, substr(utf8_decode($reg['CuentaBancaria']), 0, 25), 0, 0, 'L'); 
										}

										$PDF->Cell(30, 5, number_format($reg['ValorAPagar'], 0), 0, 0, 'R'); 
										$TotalPagos += $reg['ValorAPagar'];

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 
									$PDF->Cell(230, 5, utf8_decode('TOTAL PAGOS  '), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 285, $PDF->GetY()); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorFormaDePago_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR CENTRO - CONCEPTO
						case 6:
							$datos = $this->model->nominaPorCentroConcepto($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorCentroConcepto_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');

									fputcsv($output, array('CENTRO', 'NOMBRE CENTRO', 'MAYOR', 'AUXILIAR', 'DESCRIPCION', 'VR. PAGOS', 'VR. DEDUCCIONES'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR CENTROS - CONCEPTOS');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad('', 15);
									$lcEncabezado .= str_pad(utf8_decode('CONC.'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE CONCEPTO'), 105);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 15);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$CentroAnt = '';
									$NombreCentroAnt = '';
									$TotalPagos = 0;
									$TotalDeducciones = 0;
									$GranTotalPagos = 0;
									$GranTotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Centro'] <> $CentroAnt) 
										{
											if (! empty($CentroAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreCentroAnt), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalPagos = 0;
												$TotalDeducciones = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(15, 5, utf8_decode($reg['Centro']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreCentro']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$CentroAnt = $reg['Centro'];
											$NombreCentroAnt = $reg['NombreCentro'];
										}

										$PDF->Cell(15, 5, '', 0, 0, 'L'); 
										$PDF->Cell(15, 5, utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 0, 'L'); 
										$PDF->Cell(100, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 

										if ($reg['ValorPagos'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
											$TotalPagos += $reg['ValorPagos'];
											$GranTotalPagos += $reg['ValorPagos'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										if ($reg['ValorDeducciones'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['ValorDeducciones'];
											$GranTotalDeducciones += $reg['ValorDeducciones'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreCentroAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos - $GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorCentroConcepto_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR CONCEPTO - CENTRO
						case 7:
							$datos = $this->model->nominaPorConceptoCentro($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorConceptoCentro_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
			
									fputcsv($output, array('MAYOR', 'AUXILIAR', 'DESCRIPCION', 'CENTRO', 'NOMBRE CENTRO', 'VR. PAGOS', 'VR. DEDUCCIONES'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR CONCEPTOS - CENTROS');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad('', 15);
									$lcEncabezado .= str_pad(utf8_decode('C.C.'), 15);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE CENTRO'), 105);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 15);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$ConceptoAnt = '';
									$NombreConceptoAnt = '';
									$TotalPagos = 0;
									$TotalDeducciones = 0;
									$GranTotalPagos = 0;
									$GranTotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Mayor'] . $reg['Auxiliar'] <> $ConceptoAnt) 
										{
											if (! empty($ConceptoAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreConceptoAnt), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												// $PDF->Cell(15, 5, '', 0, 0, 'L'); 
												// $PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
												// $PDF->SetFont('Arial', 'B', 8); 
												// $PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
												// $PDF->SetFont('Arial', '', 8); 
												// $PDF->Ln(); 
												$PDF->Ln(); 

												$TotalPagos = 0;
												$TotalDeducciones = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(15, 5, utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$ConceptoAnt = $reg['Mayor'] . $reg['Auxiliar'];
											$NombreConceptoAnt = $reg['NombreConcepto'];
										}

										$PDF->Cell(15, 5, '', 0, 0, 'L'); 
										$PDF->Cell(15, 5, utf8_decode($reg['Centro']), 0, 0, 'L'); 
										$PDF->Cell(100, 5, substr(utf8_decode($reg['NombreCentro']), 0, 60), 0, 0, 'L'); 

										if ($reg['ValorPagos'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
											$TotalPagos += $reg['ValorPagos'];
											$GranTotalPagos += $reg['ValorPagos'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										if ($reg['ValorDeducciones'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['ValorDeducciones'];
											$GranTotalDeducciones += $reg['ValorDeducciones'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreConceptoAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									// $PDF->Cell(15, 5, '', 0, 0, 'L'); 
									// $PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									// $PDF->SetFont('Arial', 'B', 8); 
									// $PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									// $PDF->SetFont('Arial', '', 8); 
									// $PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos - $GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorConceptoCentro_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR EPS
						case 8:
							$datos = $this->model->nominaPorEPS($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorEPS_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('NIT. EPS', 'NOMBRE EPS', 'EMPLEADO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'I.B.C.', 'VALOR DEDUCCION'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR EPS');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad('', 15);
									$lcEncabezado .= str_pad(utf8_decode('EMPLEADO'), 20);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 105);
									$lcEncabezado .= str_pad(utf8_decode('IBC'), 20);
									$lcEncabezado .= str_pad(utf8_decode('VR. EPS'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$NitEPSAnt = '';
									$NombreEPSAnt = '';
									$TotalDeducciones = 0;
									$GranTotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['NitEPS'] <> $NitEPSAnt) 
										{
											if (! empty($NitEPSAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, substr(utf8_decode('TOTALES POR ' . $NombreEPSAnt), 0, 60), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(60, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalDeducciones = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(15, 5, utf8_decode($reg['NitEPS']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreEPS']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$NitEPSAnt = $reg['NitEPS'];
											$NombreEPSAnt = $reg['NombreEPS'];
										}

										$PDF->Cell(15, 5, '', 0, 0, 'L'); 
										$PDF->Cell(25, 5, utf8_decode($reg['Documento']), 0, 0, 'L'); 
										$PDF->Cell(90, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 

										$PDF->Cell(30, 5, number_format($reg['IBC'], 0), 0, 0, 'R'); 

										if ($reg['ValorEPS'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorEPS'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['ValorEPS'];
											$GranTotalDeducciones += $reg['ValorEPS'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, substr(utf8_decode('TOTALES POR ' . $NombreEPSAnt), 0, 60), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(60, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(60, 5, number_format($GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorEPS_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR FP
						case 9:
							$datos = $this->model->nominaPorFP($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorFP_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('NIT. FP', 'NOMBRE FP', 'EMPLEADO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'I.B.C.', 'VALOR DEDUCCION'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR FONDO DE PENSIÓN');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad('', 15);
									$lcEncabezado .= str_pad(utf8_decode('EMPLEADO'), 20);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 105);
									$lcEncabezado .= str_pad(utf8_decode('IBC'), 20);
									$lcEncabezado .= str_pad(utf8_decode('VR. FP'), 25);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$NitFPAnt = '';
									$NombreFPAnt = '';
									$TotalDeducciones = 0;
									$GranTotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['NitFP'] <> $NitFPAnt) 
										{
											if (! empty($NitFPAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, substr(utf8_decode('TOTALES POR ' . $NombreFPAnt), 0, 60), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(60, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												$PDF->Ln(); 

												$TotalDeducciones = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(15, 5, utf8_decode($reg['NitFP']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreFP']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$NitFPAnt = $reg['NitFP'];
											$NombreFPAnt = $reg['NombreFP'];
										}

										$PDF->Cell(15, 5, '', 0, 0, 'L'); 
										$PDF->Cell(25, 5, utf8_decode($reg['Documento']), 0, 0, 'L'); 
										$PDF->Cell(90, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 

										$PDF->Cell(30, 5, number_format($reg['IBC'], 0), 0, 0, 'R'); 

										if ($reg['ValorFP'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorFP'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['ValorFP'];
											$GranTotalDeducciones += $reg['ValorFP'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, substr(utf8_decode('TOTALES POR ' . $NombreFPAnt), 0, 60), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(60, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(60, 5, number_format($GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorFP_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR CONCEPTO - EMPLEADO
						case 10:
							$datos = $this->model->nominaPorConceptoEmpleado($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorConceptoEmpleado_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('MAYOR', 'AUXILIAR', 'DESCRIPCION', 'EMPLEADO', 'PRIMER APELLIDO', 'SEGUNDO APELLIDO', 'PRIMER NOMBRE', 'SEGUNDO NOMBRE', 'CENTRO', 'NOMBRE CENTRO', 'VR. PAGOS', 'VR. DEDUCCION'), ';');
		
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
								
									$lcTitulo = utf8_decode('NÓMINA POR CONCEPTO - EMPLEADO');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad('', 20);
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 10);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 100);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 10);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 20);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$ConceptoAnt = '';
									$NombreConceptoAnt = '';
									$TotalPagos = 0;
									$TotalDeducciones = 0;
									$GranTotalPagos = 0;
									$GranTotalDeducciones = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Mayor'] . $reg['Auxiliar'] <> $ConceptoAnt) 
										{
											if (! empty($ConceptoAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(15, 5, '', 0, 0, 'L'); 
												$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreConceptoAnt), 0, 0, 'R'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												// $PDF->Cell(15, 5, '', 0, 0, 'L'); 
												// $PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
												// $PDF->SetFont('Arial', 'B', 8); 
												// $PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
												// $PDF->SetFont('Arial', '', 8); 
												// $PDF->Ln(); 
												$PDF->Ln(); 

												$TotalPagos = 0;
												$TotalDeducciones = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(15, 5, utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$ConceptoAnt = $reg['Mayor'] . $reg['Auxiliar'];
											$NombreConceptoAnt = $reg['NombreConcepto'];
										}

										$PDF->Cell(15, 5, '', 0, 0, 'L'); 
										$PDF->Cell(25, 5, number_format($reg['Documento'], 0), 0, 0, 'R'); 
										$PDF->Cell(80, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L');
										if ($reg['ValorPagos'] <> 0) 
										{
											$PDF->Cell(40, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
											$TotalPagos += $reg['ValorPagos'];
											$GranTotalPagos += $reg['ValorPagos'];
										}
										else
											$PDF->Cell(40, 5, '', 0, 0, 'R'); 

										if ($reg['ValorDeducciones'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['ValorDeducciones'];
											$GranTotalDeducciones += $reg['ValorDeducciones'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('TOTALES POR ' . $NombreConceptoAnt), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									// $PDF->Cell(15, 5, '', 0, 0, 'L'); 
									// $PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									// $PDF->SetFont('Arial', 'B', 8); 
									// $PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									// $PDF->SetFont('Arial', '', 8); 
									// $PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, utf8_decode('GRAN TOTAL'), 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Cell(15, 5, '', 0, 0, 'L'); 
									$PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos - $GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorConceptoEmpleado_' . date('YmdGis') . '.PDF', 'I'); 
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// PLANILLA PILA VALIDACION Y DEFINITIVA
						case 11:
						case 12:
						case 13:
						case 14:
							$ArchivoNomina = 'ACUMULADOS';

							$query = <<<EOD
								WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo 
							EOD;
			
							if (! empty($P_IdCentro)) {
								$query .= <<<EOD
									AND $ArchivoNomina.IdCentro = $P_IdCentro 
								EOD;
							}
			
							if (! empty($P_Empleado)) {
								$query .= <<<EOD
									AND EMPLEADOS.Documento = '$P_Empleado' 
								EOD;
							}
			
							if (! empty($P_TipEmpleados)) {
								$query .= <<<EOD
									AND EMPLEADOS.TipoEmpleado = $P_TipoEmpleados 
								EOD;
							}

							$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

							$query .= <<<EOD
								AND $ArchivoNomina.ciclo < 99
							EOD;

							if ($_REQUEST['Informe'] == 11 OR $_REQUEST['Informe'] == 12)
								$empleados = $this->model->planillaPILAE($query, $ArchivoNomina);
							else
								$empleados = $this->model->planillaPILAK($query, $ArchivoNomina);

							if (count($empleados) > 0) {
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') {
									$NombreEmpresa 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'NombreEmpresa'")['detalle'];
									$NitEmpresa 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'NitEmpresa'")['detalle'];
									$DVEmpresa 					= substr($NitEmpresa, -1);
									$NitEmpresa 				= substr($NitEmpresa, 0, strpos($NitEmpresa, '-'));
									$ExoneracionEmpresa 		= (getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ExoneracionEmpresa'")['valor'] == 1 ? 'S' : 'N');

									// ARCHIVO TIPO 2
									$datosArchivo2 = '';

									// REGISTROS TIPO 2
									$Secuencia = 0;
									$TotalAfiliados = 0;
									$TotalIBC = 0;
									$TotalARL = 0;
									$TotalIncapacidades = 0;
									$ValorTotalIncapacidades = 0;

									if ($_REQUEST['Informe'] == 11 OR $_REQUEST['Informe'] == 12)
										$this->model->limpiarDataPILAE($IdPeriodo);

									for ($i = 0; $i < count($empleados); $i++) {
										$regEmp 		= $empleados[$i];

										$IdEmpleado 	= $regEmp['IdEmpleado'];
										$regEmpleado 	= getRegistro('EMPLEADOS', $IdEmpleado);

										$Apellido1 = cleanAccents($regEmpleado['apellido1']);
										$Apellido2 = cleanAccents($regEmpleado['apellido2']);
										$Nombre1 = cleanAccents($regEmpleado['nombre1']);
										$Nombre2 = cleanAccents($regEmpleado['nombre2']);

										$SubtipoCotizante = str_pad($regEmpleado['subtipocotizante'], 2, '0', STR_PAD_LEFT);
										$Ciudad = getRegistro('CIUDADES', $regEmpleado['idciudadtrabajo'])['ciudad'];
										$Centro = getRegistro('CENTROS', $regEmpleado['idcentro'])['centro'];

										$CodigoARL 		= getRegistro('TERCEROS', $regEmpleado['idarl'])['codigoarl'];
										$NitARL 		= getRegistro('TERCEROS', $regEmpleado['idarl'])['documento'];
										$NitARL 		= substr($NitARL, 0, strpos($NitARL, '-'));
										$NivelRiesgo 	= getRegistro('PARAMETROS', $regEmpleado['nivelriesgo'])['valor'];

										switch (getRegistro('PARAMETROS', $regEmpleado['tipoidentificacion'])['detalle'])
										{
											case 'CEDULA':
												$TipoDocumento = 'CC';
												break;
											case 'CEDULA EXTRANJERIA':
												$TipoDocumento = 'CE';
												break;
											case 'TARJETA DE IDENTIDAD':
												$TipoDocumento = 'TI';
												break;
											case 'PASAPORTE':
												$TipoDocumento = 'PA';
												break;
											case 'REGISTRO CIVIL':
												$TipoDocumento = 'RC';
												break;
											case 'PERMISO PERMANENCIA TEMPORAL':
											case 'PERMISO DE TRABAJO':
												$TipoDocumento = 'PT';
												break;
										}

										$TipoContrato = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
										$RegimenCesantias = getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['detalle'];

										switch ($TipoContrato) {
											case 'APRENDIZAJE - ETAPA LECTIVA':
												$TipoCotizante = '12';
												$IBC = $SueldoMinimo;
												break;
											case 'APRENDIZAJE - ETAPA PRÁCTICA':
											case 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD':
												$TipoCotizante = '19';
												$IBC = $SueldoMinimo;
												break;
											case 'PASANTÍA':
											default:
												$TipoCotizante = '01';
										}

										if (substr($regEmpleado['fechaingreso'], 0, 7) == ($Referencia . '-' . str_pad($Periodo, 2, '0', STR_PAD_LEFT))) {
											$EsIngreso = 'X';
											$FechaIngreso = $regEmpleado['fechaingreso'];
										} else {
											$EsIngreso = ' ';
											$FechaIngreso = str_pad(' ', 10);
										}

										if (substr($regEmpleado['fecharetiro'], 0, 7) == ($Referencia . '-' . str_pad($Periodo, 2, '0', STR_PAD_LEFT))) {
											$EsRetiro = 'X';
											$FechaRetiro = $regEmpleado['fecharetiro'];
										} else {
											$EsRetiro = ' ';
											$FechaRetiro = str_pad(' ', 10);
										}

										$queryAcumuladosBase = <<<EOD
											SELECT $ArchivoNomina.*, 
													PARAMETROS1.Detalle AS TipoRegistroAuxiliar, 
													PARAMETROS2.Detalle AS ClaseConcepto, 
													PARAMETROS3.Detalle AS Imputacion
												FROM $ArchivoNomina 
													INNER JOIN AUXILIARES 
														ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
													INNER JOIN MAYORES  
														ON AUXILIARES.IdMayor = MAYORES.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS1 
														ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS1.Id 
													INNER  JOIN PARAMETROS AS PARAMETROS2 
														ON MAYORES.ClaseConcepto = PARAMETROS2.Id 
													INNER  JOIN PARAMETROS AS PARAMETROS3 
														ON AUXILIARES.Imputacion = PARAMETROS3.Id 
												WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
													$ArchivoNomina.IdEmpleado = $IdEmpleado AND
													MAYORES.Mayor + AUXILIARES.Auxiliar IN (
														'01001', '10001', '02007', '02005', '03001','03008','03002', '99005', '02002', '02003',
														'02001', '02004', '16001', '17002', '10004', '01002', '10007', '01004', '01017',
														'10006', '17001', '06001', '01054', '10008', '10051', '01015', '01007'
													);
										EOD;

										$acumuladosDatosBase = $this->model->listar($queryAcumuladosBase);

										$TotalIBCParcial = 0;
										$IBCSalud = 0;
										$ValorSalud = 0;
										$IBCPension = 0;
										$IBCFondoSolidaridad = 0;
										$ValorPension = 0;
										$FondoSolidaridad = 0;
										$FondoSubsistencia = 0;
										$Dias = 0;

										if ($acumuladosDatosBase) {
											for ($j = 0; $j < count($acumuladosDatosBase); $j++) {
												$regNomina = $acumuladosDatosBase[$j];

												if	($regNomina['TipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO' OR 
													$regNomina['TipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
													$regNomina['TipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO (APRENDIZ SENA)')
												{													
													if ($regNomina['Imputacion'] == 'PAGO')
														if ($regEmpleado['horasmes'] == 120)
															$Dias += ($regNomina['horas'] / 4);
														else
															$Dias += ($regNomina['horas'] / 8);
													else
														if ($regEmpleado['horasmes'] == 120)
															$Dias -= ($regNomina['horas'] / 4);
														else
															$Dias -= ($regNomina['horas'] / 8);
												}

												if ($regNomina['ClaseConcepto'] == 'SALARIO') {
													if ($regNomina['Imputacion'] == 'PAGO') {
														$IBCSalud += $regNomina['valor'];
														$IBCPension += $regNomina['valor'];
													} else {
														$IBCSalud -= $regNomina['base'];
														$IBCPension -= $regNomina['base'];
													}
												}

												if	(($regNomina['TipoRegistroAuxiliar'] == 'ES LICENCIA NO REMUNERADA' OR $regNomina['TipoRegistroAuxiliar'] == 'ES SANCIÓN') AND 
													$regNomina['Imputacion'] == 'DEDUCCIÓN')
													$Dias -= ($regNomina['horas'] / 8);

												if ($regNomina['TipoRegistroAuxiliar'] == 'ES FONDO DE SOLIDARIDAD') 
													$IBCFondoSolidaridad = $regNomina['base'];
											}

											if ($regEmpleado['sueldobasico'] > $SueldoMinimo * 10 OR 
												$IBCSalud > $SueldoMinimo * 10 OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
												$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
												$TipoContrato == 'PASANTÍA'
											) 
												$ExoneradoSalud = 'N';
											else
												$ExoneradoSalud = $ExoneracionEmpresa;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 4) 
												$PorcentajeSolidaridad = 0.5;
											else
												$PorcentajeSolidaridad = 0;

											$PorcentajeSubsistencia = 0;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 4 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 16)
												$PorcentajeSubsistencia = 0.5;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 16 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 17)
												$PorcentajeSubsistencia = 0.7;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 17 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 18)
												$PorcentajeSubsistencia = 0.9;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 18 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 19)
												$PorcentajeSubsistencia = 1.1;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 19 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 20)
												$PorcentajeSubsistencia = 1.3;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 20)
												$PorcentajeSubsistencia = 1.5;

											if ($Dias > 0 AND $IBCSalud / $Dias * 30 < $SueldoMinimo)
												$IBCSalud = $SueldoMinimo / 30 * $Dias;

											if ($IBCSalud - intval($IBCSalud) > 0) 
												$IBCSalud = intval($IBCSalud + 1);

											if (intval("$IBCSalud") <> "$IBCSalud" OR "$IBCSalud" % 100 <> 0) 
												$IBCSalud = round($IBCSalud + 50, -2);

											if ($RegimenCesantias == 'SALARIO INTEGRAL') $IBCSalud *= 0.7;

											if ($IBCSalud > $SueldoMinimo * 25) $IBCSalud = $SueldoMinimo * 25;

											$IBCPension = $IBCSalud;

											if ($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
												$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
												$TipoContrato == 'PASANTÍA') 
												$IBCPension = 0;
										}

										$registro2 = NULL;

										// REGISTRO TIPO 2
										// REGISTROS ADICIONALES
										$query = <<<EOD
											SELECT $ArchivoNomina.id, 
													$ArchivoNomina.idconcepto, 
													$ArchivoNomina.Base, 
													$ArchivoNomina.Horas, 
													$ArchivoNomina.Valor, 
													$ArchivoNomina.FechaInicial, 
													$ArchivoNomina.FechaFinal, 
													PARAMETROS.Detalle AS TipoRegistroAuxiliar
												FROM $ArchivoNomina 
													INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
													INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
													INNER JOIN PARAMETROS ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
												WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
													$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
													$ArchivoNomina.FechaInicial IS NOT NULL AND
													MAYORES.Mayor + AUXILIARES.Auxiliar NOT IN ('05051'); -- INCAPACIDADES QUE NO APLICAN
										EOD;

										$incapacidades = $this->model->listar($query);

										if ($incapacidades)
										{
											if ($Dias == 0) 
												$TotalAfiliados++;

											for ($j = 0; $j < count($incapacidades); $j++)
											{
												$regIncapacidad = $incapacidades[$j];

												$registro2 = NULL;

												if ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES LICENCIA DE MATERNIDAD')
												{
													$IBC = $regIncapacidad['Valor'];
												}
												elseif ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD PROFESIONAL' OR 
													$regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD EN TIEMPO')
												{
													$conceptosIncapacidad = $regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD PROFESIONAL'
														? '01014' : '01008';
													$query = <<<EOD
														SELECT SUM(acu.valor) AS IBC
														FROM acumulados acu
														JOIN auxiliares aux on aux.id = acu.idconcepto
														JOIN mayores may on may.id = aux.idmayor
														WHERE acu.idperiodo = $IdPeriodo AND acu.idempleado = $IdEmpleado
															AND may.mayor + aux.auxiliar in (
																$conceptosIncapacidad
															);
													EOD;
													$regIBC = $this->model->leer($query);

													$IBC = $regIBC['IBC'];
												}
												elseif ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES VACACIONES EN TIEMPO') {
													$fecha = new DateTime ($regIncapacidad['FechaInicial']);
													$inicio = $fecha->modify ('first day of last month');
													$inicio = $inicio->format ('Y-m-d');

													$query = <<<EOD
														SELECT SUM(acu.valor) AS IBC
														FROM acumulados acu
														JOIN auxiliares aux on aux.id = acu.idconcepto
														JOIN mayores may on may.id = aux.idmayor
														WHERE acu.fechainicialperiodo = '$inicio' AND acu.idempleado = $IdEmpleado
															AND may.mayor + aux.auxiliar in (
																'01001', '01002', '01004', '01017', '01005', '01006', '01007', '01008', '01010', '01011', '01012',
																'01014', '01015', '01054', '02001', '02002', '02003', '02004', '02007', '02005', '02051', '03001',
																'03008','03002', '05004', '06001', '06051', '09001', '10001', '10004', '10006', '10007', '10008',
																'10051', '16001', '17001', '17002', '50001', '50002', '99005', '99012'
															);
													EOD;
													$regIBC = $this->model->leer($query);

													$IBC = $regIBC['IBC'];
												}
												else
												{
													// AQUI SE TOMA COMO IBC EL VALOR DE LA INCAPACIDAD NO LA BASE
													$IBC = $regIncapacidad['Valor'];

													if ($regEmpleado['horasmes'] == 120) 
														$Dias = round($regIncapacidad['Horas'] / 4, 0);
													else
														$Dias = round($regIncapacidad['Horas'] / 8, 0);

													if ($Dias == 0.5)
														$Dias = 1;

													if ($Dias == 30) 
														$IBC = $regEmpleado['sueldobasico'];

													if ($IBC < round($SueldoMinimo / 30 * $Dias, 0)) 
														$IBC = round($SueldoMinimo / 30 * $Dias, 0);
												}

												switch ($regIncapacidad['TipoRegistroAuxiliar'])
												{
													case 'ES SANCIÓN':
													case 'ES LICENCIA NO REMUNERADA':
														$FechaInicioLicenciaNoRemunerada = max($regIncapacidad['FechaInicial'], $FechaInicial);
														$FechaFinalLicenciaNoRemunerada = min($regIncapacidad['FechaFinal'], $FechaFinal);

														if ($regEmpleado['horasmes'] == 120) 
															$Dias = round($regIncapacidad['Horas'] / 4, 0);
														else
															$Dias = ($regIncapacidad['Horas'] / 8);

														if ($Dias == 0.5)
															$Dias = 1;

														$Secuencia++;

														$registro2 = '02';
														$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
														$registro2 .= $TipoDocumento;
														$registro2 .= str_pad($regEmpleado['documento'], 16);
														$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);
														// SUBTIPO COTIZANTE
														$registro2 .= $SubtipoCotizante;  			
														// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
														$registro2 .= ' ';  						
														// COLOMBIANO EN EL EXTERIOR
														$registro2 .= ' ';  						
														$registro2 .= $Ciudad;
														$registro2 .= str_pad($Apellido1, 20);
														$registro2 .= str_pad($Apellido2, 30);
														$registro2 .= str_pad($Nombre1, 20);
														$registro2 .= str_pad($Nombre2, 30);
														// INGRESO
														$registro2 .= ' ';  									
														// RETIRO
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRO FP  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRO FP  X
														$registro2 .= ' ';  									
														// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
														$registro2 .= ' ';  									
														// CORRECCIONES  A/C
														$registro2 .= ' ';  									
														// VARIACION TRANSITORIA  DE SALARIO
														$registro2 .= ' ';  									
														// SUSPENSION LICENCIA NO REMUNERADA
														$registro2 .= 'X';  									
														// INCAPACIDAD ENFERMEDAD GENERAL
														$registro2 .= ' ';  									
														// LICENCIA DE MATERNIDAD
														$registro2 .= ' ';  									
														// VACACIONES / LICENCIA REMUNERADA  X/L
														$registro2 .= ' ';  									
														// APORTE VOLUNTARIO
														$registro2 .= ' ';  									
														// VARIACION CENTRO DE TRABAJO
														$registro2 .= ' ';  									
														// DIAS INCAPACIDAD
														$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT);  		
				
														if ($regEmpleado['idfondopensiones'] > 0)
															$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
														else	
															$FP = str_pad(' ', 6);

														$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];

														if ($regEmpleado['idcajacompensacion'] > 0)
															$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
														else	
															$CCF = str_pad(' ', 6);
				
														$registro2 .= str_pad($FP, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($EPS, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($CCF, 6);

														$TarifaSalud = 0;
														$TarifaCCF = 0;
														if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBC >= $SueldoMinimo * 10)
														{
															$TarifaPension = 16;
															$TarifaSENA = 2;
															$TarifaICBF = 3;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
														elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
																$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
																$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
																$TipoContrato == 'PASANTÍA')
														{
															$TarifaPension = 0;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = 0;
															$DiasCCF = 0;
														}
														else
														{
															$TarifaPension = 16;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
								
														// DIAS PENSION
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS SALUD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS ARL
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS CCF
														$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
														$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);
				
														if ($RegimenCesantias == 'SALARIO INTEGRAL')
														{
															// SALARIO INTEGRAL
															$registro2 .= 'X';  								
														}
														else
														{
															$registro2 .= 'F';
														}
				
														$ibcPension = $IBC;
														$ValorPension = $IBC / 30 * $Dias * $TarifaPension / 100;
														if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
															$ValorPension = round($ValorPension + 50, -2);
			
														$ibcSalud = $IBC;
														$ValorSalud = $IBC / 30 * $Dias * $TarifaSalud / 100;
														if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
															$ValorSalud = round($ValorSalud + 50, -2);
			
														$ibcCCF = $IBC;
														$ValorCCF = $IBC / 30 * $Dias * $TarifaCCF / 100;
														if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
															$ValorCCF = round($ValorCCF + 50, -2);
			
														$ibcSENA = $IBC;
														$ValorSENA = $IBC / 30 * $Dias * $TarifaSENA / 100;
														if (intval("$ValorSENA") <> "$ValorSENA"  OR "$ValorSENA" % 100 <> 0) 
															$ValorSENA = round($ValorSENA + 50, -2);
			
														$ibcICBF = $IBC;
														$ValorICBF = $IBC / 30 * $Dias * $TarifaICBF / 100;
														if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
															$ValorICBF = round($ValorICBF + 50, -2);

														$ibcSolidaridad = $IBC;
														$FondoSolidaridad = $IBC / 30 * $Dias * $PorcentajeSolidaridad / 100;
														if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0)
															$FondoSolidaridad = round($FondoSolidaridad + 50, -2);

														$ibcSubsistencia = $IBC;
														$FondoSubsistencia = $IBC / 30 * $Dias * $PorcentajeSubsistencia / 100;
														if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0)
															$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
								
														// IBC PENSION
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														// IBC SALUD
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														// IBC ARL
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// IBC CCF
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  			// TARIFA PENSION
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// APORTE VOLUNTARIO PENSION EMPLEADO
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// APORTE VOLUNTARIO PENSION EMPLEADOR
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// FONDO SOLIDARIDAD
														$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
														// FONDO SUBSISTENCIA
														$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// VR NO RETENIDO POR APORTES VOLUNTARIOS
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA SALUD
														$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SALUD
														$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// VALOR UPC ADICIONAL
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// NUMERO INCAPACIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR INCAPACIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 						
														// NUMERO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR LICENCIA MATERNIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
				
														$TarifaARL = 0;
														$ValorARL = 0;
														$ibcARL = $IBC;
								
														// TARIFA ARL
														$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);				
														// CENTRO DE TRABAJO
														$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   										
														// VALOR ARL
														$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
														// TARIFA CCF
														$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  				
														// VALOR CCF
														$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  				
														// TARIFA SENA
														$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SENA
														$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ICBF
														$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR ICBF
														$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ESAP
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR ESAP
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA MEN
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR MEN
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   						
														$registro2 .= str_pad(' ', 2);											
														$registro2 .= str_pad(' ', 16);
														$registro2 .= $ExoneradoSalud;

														$registro2 .= str_pad($CodigoARL, 6);
														// CLASE RIESGO 1..5
														$registro2 .= $NivelRiesgo;												
														// ACTIVIDAD ALTO RIESGO
														$registro2 .= ' ';  													
														// FECHA INGRESO
														$registro2 .= str_pad(' ', 10);
														// FECHA RETIRO
														$registro2 .= str_pad(' ', 10);
														// FECHA AUMENTO
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA NO REMUNERADA
														$registro2 .= $FechaInicioLicenciaNoRemunerada;							
														// FECHA FINAL LICENCIA NO REMUNERADA
														$registro2 .= $FechaFinalLicenciaNoRemunerada;
														// FECHA INICIO INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA FINAL VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA INICIO INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														if ($TarifaSENA > 0 AND $TarifaICBF > 0)
														{
															// IBC OTROS PARAFISCALES NO CCF
															$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
														}
														else
														{
															// IBC OTROS PARAFISCALES NO CCF
															$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  									
														}
														$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);
														$registro2 .= str_pad(' ', 10);
														$registro2 .= $NivelRiesgo . '620901';

														$registro2 .= PHP_EOL;

														$datosArchivo2 .= $registro2;

														$TotalIBC += ($IBC / 30 * $Dias);

														break;

													case 'ES LICENCIA DE MATERNIDAD':
														$FechaInicioLicenciaMaternidad = max($regIncapacidad['FechaInicial'], $FechaInicial);
														$FechaFinalLicenciaMaternidad = min($regIncapacidad['FechaFinal'], $FechaFinal);

														if ($regEmpleado['horasmes'] == 120) 
															$Dias = round($regIncapacidad['Horas'] / 4, 0);
														else
															$Dias = ($regIncapacidad['Horas'] / 8);

														if ($Dias == 0.5)
															$Dias = 1;

														$Secuencia++;

														$registro2 = '02';
														$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
														$registro2 .= $TipoDocumento;
														$registro2 .= str_pad($regEmpleado['documento'], 16);
														$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);
														// SUBTIPO COTIZANTE
														$registro2 .= $SubtipoCotizante;  			
														// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
														$registro2 .= ' ';  						
														// COLOMBIANO EN EL EXTERIOR
														$registro2 .= ' ';  						
														// DEPARTAMENTO
														$registro2 .= '11';  						
														// MUNICIPIO
														$registro2 .= '001';  						
														$registro2 .= str_pad($Apellido1, 20);
														$registro2 .= str_pad($Apellido2, 30);
														$registro2 .= str_pad($Nombre1, 20);
														$registro2 .= str_pad($Nombre2, 30);
														// INGRESO
														$registro2 .= ' ';  									
														// RETIRO
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRO FP  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRO FP  X
														$registro2 .= ' ';  									
														// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
														$registro2 .= ' ';  									
														// CORRECCIONES  A/C
														$registro2 .= ' ';  									
														// VARIACION TRANSITORIA  DE SALARIO
														$registro2 .= ' ';  									
														// SUSPENSION LICENCIA NO REMUNERADA
														$registro2 .= ' ';  									
														// INCAPACIDAD ENFERMEDAD GENERAL
														$registro2 .= ' ';  									
														// LICENCIA DE MATERNIDADA
														$registro2 .= 'X';  									
														// VACACIONES / LICENCIA REMUNERADA  X/L
														$registro2 .= ' ';  									
														// APORTE VOLUNTARIO
														$registro2 .= ' ';  									
														// VARIACION CENTRO DE TRABAJO
														$registro2 .= ' ';  									
														// DIAS INCAPACIDAD
														$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT);  		
				
														if ($regEmpleado['idfondopensiones'] > 0)
															$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
														else	
															$FP = str_pad(' ', 6);

														$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];
				
														if ($regEmpleado['idcajacompensacion'] > 0)
															$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
														else	
															$CCF = str_pad(' ', 6);
				
														$registro2 .= str_pad($FP, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($EPS, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($CCF, 6);

														$TarifaCCF = 0;
														if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBC >= $SueldoMinimo * 10)
														{
															$TarifaPension = 16;
															$TarifaSalud = 12.5;
															$TarifaSENA = 2;
															$TarifaICBF = 3;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
														elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
																$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
																$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
																$TipoContrato == 'PASANTÍA')
														{
															$TarifaPension = 0;
															$TarifaSalud = 12.5;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = 0;
															$DiasCCF = 0;
														}
														else
														{
															$TarifaPension = 16;
															$TarifaSalud = 4;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
												
														// DIAS PENSION
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS SALUD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS ARL
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS CCF
														$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
														$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);
				
														// SALARIO INTEGRAL
														if ($RegimenCesantias == 'SALARIO INTEGRAL')
															$registro2 .= 'X';  								
														else
															$registro2 .= 'F';
				
														$ibcPension = $IBC;
														$ValorPension = $IBC / 30 * $Dias * $TarifaPension / 100;
														if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
															$ValorPension = round($ValorPension + 50, -2);
			
														$ibcSalud = $IBC;
														$ValorSalud = $IBC / 30 * $Dias * $TarifaSalud / 100;
														if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
															$ValorSalud = round($ValorSalud + 50, -2);
			
														$ibcCCF = $IBC;
														$ValorCCF = $IBC / 30 * $Dias * $TarifaCCF / 100;
														if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
															$ValorCCF = round($ValorCCF + 50, -2);
			
														$ibcSENA = $IBC;
														$ValorSENA = $IBC / 30 * $Dias * $TarifaSENA / 100;
														if (intval("$ValorSENA") <> "$ValorSENA"  OR "$ValorSENA" % 100 <> 0) 
															$ValorSENA = round($ValorSENA + 50, -2);
			
														$ibcICBF = $IBC;
														$ValorICBF = $IBC / 30 * $Dias * $TarifaICBF / 100;
														if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
															$ValorICBF = round($ValorICBF + 50, -2);

														$ibcSolidaridad = $IBC;
														$FondoSolidaridad = $IBC / 30 * $Dias * $PorcentajeSolidaridad / 100;
														if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0)
															$FondoSolidaridad = round($FondoSolidaridad + 50, -2);

														$ibcSubsistencia = $IBC;
														$FondoSubsistencia = $IBC / 30 * $Dias * $PorcentajeSubsistencia / 100;
														if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0)
															$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
							
														// IBC PENSION
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														// IBC SALUD
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														// IBC ARL
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// IBC CCF
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// TARIFA PENSION
														$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  			
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// APORTE VOLUNTARIO PENSION EMPLEADO
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// APORTE VOLUNTARIO PENSION EMPLEADOR
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// FONDO SOLIDARIDAD
														$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
														// FONDO SUBSISTENCIA
														$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// VR NO RETENIDO POR APORTES VOLUNTARIOS
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA SALUD
														$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SALUD
														$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// VALOR UPC ADICIONAL
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// NUMERO INCAPACIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR INCAPACIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 						
														// NUMERO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR LICENCIA MATERNIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
				
														$ibcARL = $IBC;
														$TarifaARL = 0;
														$ValorARL = 0;
				
														// TARIFA ARL
														$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);				
														// CENTRO DE TRABAJO
														$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   						
														// VALOR ARL
														$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
														// TARIFA CCF
														$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  				
														// VALOR CCF
														$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  				
														// TARIFA SENA
														$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SENA
														$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				// TARIFA ICBF
														// VALOR ICBF
														$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ESAP
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR ESAP
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA MEN
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR MEN
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   						
														$registro2 .= str_pad(' ', 2);											
														$registro2 .= str_pad(' ', 16);
														$registro2 .= $ExoneradoSalud;

														$registro2 .= str_pad($CodigoARL, 6);
														// CLASE RIESGO 1..5
														$registro2 .= $NivelRiesgo;												
														// ACTIVIDAD ALTO RIESGO
														$registro2 .= ' ';  													
														// FECHA INGRESO
														$registro2 .= str_pad(' ', 10);
														// FECHA RETIRO
														$registro2 .= str_pad(' ', 10);
														// FECHA AUMENTO
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA NO REMIUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA NO REMUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA MATERNIDAD
														$registro2 .= $FechaInicioLicenciaMaternidad;							
														// FECHA FINAL LICENCIA MATERNIDAD
														$registro2 .= $FechaFinalLicenciaMaternidad;							
														// FECHA INICIO VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA FINAL VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA INICIO INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// IBC OTROS PARAFISCALES NO CCF
														if ($TarifaSENA > 0 AND $TarifaICBF > 0)
															$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
														else
															$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  									
														$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);
														$registro2 .= str_pad(' ', 10);
														$registro2 .= $NivelRiesgo . '620901';
														$registro2 .= PHP_EOL;

														$datosArchivo2 .= $registro2;
														$TotalIBC += ($IBC / 30 * $Dias);

														break;

													case 'ES INCAPACIDAD PROFESIONAL':
														$FechaInicioIncapacidadLaboral = max($regIncapacidad['FechaInicial'], $FechaInicial);
														$FechaFinalIncapacidadLaboral = min($regIncapacidad['FechaFinal'], $FechaFinal);

														if ($regEmpleado['horasmes'] == 120) 
															$Dias = round($regIncapacidad['Horas'] / 4, 0);
														else
															$Dias = ($regIncapacidad['Horas'] / 8);

														if ($Dias == 0.5)
															$Dias = 1;

														$Secuencia++;

														$registro2 = '02';
														$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
														$registro2 .= $TipoDocumento;
														$registro2 .= str_pad($regEmpleado['documento'], 16);
														$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);
														// SUBTIPO COTIZANTE
														$registro2 .= $SubtipoCotizante;  			
														// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
														$registro2 .= ' ';  						
														// COLOMBIANO EN EL EXTERIOR
														$registro2 .= ' ';  						
														$registro2 .= $Ciudad;
														$registro2 .= str_pad($Apellido1, 20);
														$registro2 .= str_pad($Apellido2, 30);
														$registro2 .= str_pad($Nombre1, 20);
														$registro2 .= str_pad($Nombre2, 30);
														// INGRESO
														$registro2 .= ' ';  									
														// RETIRO
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRO FP  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRO FP  X
														$registro2 .= ' ';  									
														// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
														$registro2 .= ' ';  									
														// CORRECCIONES  A/C
														$registro2 .= ' ';  									
														// VARIACION TRANSITORIA  DE SALARIO
														$registro2 .= ' ';  									
														// SUSPENSION LICENCIA NO REMUNERADA
														$registro2 .= ' ';  									
														// INCAPACIDAD ENFERMEDAD GENERAL
														$registro2 .= ' ';  									
														// LICENCIA DE MATERNIDAD
														$registro2 .= ' ';  									
														// VACACIONES / LICENCIA REMUNERADA  X/L
														$registro2 .= ' ';  									
														// APORTE VOLUNTARIO
														$registro2 .= ' ';  									
														// VARIACION CENTRO DE TRABAJO
														$registro2 .= ' ';  									
														// DIAS INCAPACIDAD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
				
														if ($regEmpleado['idfondopensiones'] > 0)
															$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
														else	
															$FP = str_pad(' ', 6);

														$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];
				
														if ($regEmpleado['idcajacompensacion'] > 0)
															$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
														else	
															$CCF = str_pad(' ', 6);
				
														$registro2 .= str_pad($FP, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($EPS, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($CCF, 6);

														if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBC >= $SueldoMinimo * 10)
														{
															$TarifaPension = 16;
															$TarifaSalud = 12.5;
															$TarifaCCF = 4;
															$TarifaSENA = 2;
															$TarifaICBF = 3;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
														elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
																$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
																$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
																$TipoContrato == 'PASANTÍA')
														{
															$TarifaPension = 0;
															$TarifaSalud = 12.5;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$TarifaCCF = 0;
															$DiasPension = 0;
															$DiasCCF = 0;
														}
														else
														{
															$TarifaPension = 16;
															$TarifaSalud = 4;
															$TarifaCCF = 4;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
													
														// DIAS PENSION
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS SALUD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS ARL
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS CCF
														$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
														$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);
				
														// SALARIO INTEGRAL
														if ($RegimenCesantias == 'SALARIO INTEGRAL')
															$registro2 .= 'X';  								
														else
															$registro2 .= 'F';									
														// SALARIO FIJO
				
														$ibcPension = $IBC;
														$ValorPension = $IBC / 30 * $Dias * $TarifaPension / 100;
														if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
															$ValorPension = round($ValorPension + 50, -2);
			
														$ibcSalud = $IBC;
														$ValorSalud = $IBC / 30 * $Dias * $TarifaSalud / 100;
														if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
															$ValorSalud = round($ValorSalud + 50, -2);
			
														$ibcCCF = $IBC;
														$ValorCCF = $IBC / 30 * $Dias * $TarifaCCF / 100;
														if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
															$ValorCCF = round($ValorCCF + 50, -2);
			
														$ibcSENA = $IBC;
														$ValorSENA = $IBC / 30 * $Dias * $TarifaSENA / 100;
														if (intval("$ValorSENA") <> "$ValorSENA"  OR "$ValorSENA" % 100 <> 0) 
															$ValorSENA = round($ValorSENA + 50, -2);
			
														$ibcICBF = $IBC;
														$ValorICBF = $IBC / 30 * $Dias * $TarifaICBF / 100;
														if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
															$ValorICBF = round($ValorICBF + 50, -2);
						
														$ibcSolidaridad = $IBC;
														$FondoSolidaridad = $IBC / 30 * $Dias * $PorcentajeSolidaridad / 100;
														if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0)
															$FondoSolidaridad = round($FondoSolidaridad + 50, -2);

														$ibcSubsistencia = $IBC;
														$FondoSubsistencia = $IBC / 30 * $Dias * $PorcentajeSubsistencia / 100;
														if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0)
															$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
		
														// IBC PENSION
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														// IBC SALUD
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														// IBC ARL
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// IBC CCF
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// TARIFA PENSION
														$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  			
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// APORTE VOLUNTARIO PENSION EMPLEADO
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// APORTE VOLUNTARIO PENSION EMPLEADOR
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// FONDO SOLIDARIDAD
														$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
														// FONDO SUBSISTENCIA
														$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// VR NO RETENIDO POR APORTES VOLUNTARIOS
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA SALUD
														$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SALUD
														$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// VALOR UPC ADICIONAL
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// NUMERO INCAPACIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR INCAPACIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 						
														// NUMERO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR LICENCIA MATERNIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
				
														$ibcARL = $IBC;
														$TarifaARL = 0;
														$ValorARL = 0;
				
														// TARIFA ARL
														$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);				
														// CENTRO DE TRABAJO
														$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   												
														// VALOR ARL
														$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
														// TARIFA CCF
														$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  		
														// VALOR CCF
														$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// TARIFA SENA
														$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
														// VALOR SENA
														$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ICBF
														$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
														// VALOR ICBF
														$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ESAP
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR ESAP
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA MEN
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR MEN
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   						
														$registro2 .= str_pad(' ', 2);											
														$registro2 .= str_pad(' ', 16);
														$registro2 .= $ExoneradoSalud;

														$registro2 .= str_pad($CodigoARL, 6);
														// CLASE RIESGO 1..5
														$registro2 .= $NivelRiesgo;												
														// ACTIVIDAD ALTO RIESGO
														$registro2 .= ' ';  													
														// FECHA INGRESO
														$registro2 .= str_pad(' ', 10);
														// FECHA RETIRO
														$registro2 .= str_pad(' ', 10);
														// FECHA AUMENTO
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA NO REMIUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA NO REMUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA FINAL VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA INICIO INCAPACIDAD LABORAL
														$registro2 .= $FechaInicioIncapacidadLaboral;							
														// FECHA FINAL INCAPACIDAD LABORAL
														$registro2 .= $FechaFinalIncapacidadLaboral;							
														// IBC OTROS PARAFISCALES NO CCF
														if ($TarifaSENA > 0 AND $TarifaICBF > 0)
															$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
														else
															$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  									
														// HORAS LABORADAS
														$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(' ', 10);
														$registro2 .= $NivelRiesgo . '620901';
														$registro2 .= PHP_EOL;

														$datosArchivo2 .= $registro2;
														$TotalIBC += ($IBC / 30 * $Dias);

														$TotalIncapacidades++;
														$ValorTotalIncapacidades += $regIncapacidad['Valor'];

														break;

													case 'ES INCAPACIDAD EN TIEMPO':
													case 'ES INCAPACIDAD > 180 DÍAS':
													case 'ES AUXILIO POR INCAPACIDAD':
														$FechaInicioIncapacidad = max($regIncapacidad['FechaInicial'], $FechaInicial);
														$FechaFinalIncapacidad = min($regIncapacidad['FechaFinal'], $FechaFinal);

														if ($regEmpleado['horasmes'] == 120) 
															$Dias = round($regIncapacidad['Horas'] / 4, 0);
														else
															$Dias = ($regIncapacidad['Horas'] / 8);

														if ($Dias == 0.5)
															$Dias = 1;

														if ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES AUXILIO POR INCAPACIDAD')
															$FechaFinalIncapacidad = date('Y-m-d', strtotime($FechaInicioIncapacidad . ' +' . ($Dias - 1) . ' day'));

														if ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD EN TIEMPO')
															$FechaInicioIncapacidad = date('Y-m-d', strtotime($FechaFinalIncapacidad . ' -' . ($Dias - 1) . ' day'));

														$Secuencia++;

														$registro2 = '02';
														$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
														$registro2 .= $TipoDocumento;
														$registro2 .= str_pad($regEmpleado['documento'], 16);
														$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);
														// SUBTIPO COTIZANTE
														$registro2 .= $SubtipoCotizante;  			
														// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
														$registro2 .= ' ';  						
														// COLOMBIANO EN EL EXTERIOR
														$registro2 .= ' ';  						
														$registro2 .= $Ciudad;
														$registro2 .= str_pad($Apellido1, 20);
														$registro2 .= str_pad($Apellido2, 30);
														$registro2 .= str_pad($Nombre1, 20);
														$registro2 .= str_pad($Nombre2, 30);
														// INGRESO
														$registro2 .= ' ';  									
														// RETIRO
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRO FP  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRO FP  X
														$registro2 .= ' ';  									
														// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
														$registro2 .= ' ';  									
														// CORRECCIONES  A/C
														$registro2 .= ' ';  									
														// VARIACION TRANSITORIA  DE SALARIO
														$registro2 .= ' ';  									
														// SUSPENSION LICENCIA NO REMUNERADA
														$registro2 .= ' ';  									
														// INCAPACIDAD ENFERMEDAD GENERAL
														$registro2 .= 'X';  									
														// LICENCIA DE MATERNIDAD
														$registro2 .= ' ';  									
														// VACACIONES / LICENCIA REMUNERADA  X/L
														$registro2 .= ' ';  									
														// APORTE VOLUNTARIO
														$registro2 .= ' ';  									
														// VARIACION CENTRO DE TRABAJO
														$registro2 .= ' ';  									
														// DIAS INCAPACIDAD
														$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT);  	
				
														if ($regEmpleado['idfondopensiones'] > 0)
															$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
														else	
															$FP = str_pad(' ', 6);

														$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];
				
														if ($regEmpleado['idcajacompensacion'] > 0)
															$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
														else	
															$CCF = str_pad(' ', 6);
				
														$registro2 .= str_pad($FP, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($EPS, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($CCF, 6);

														$TarifaCCF = 0;
														if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBC >= $SueldoMinimo * 10)
														{
															$TarifaPension = 16;
															$TarifaSalud = 12.5;
															$TarifaSENA = 2;
															$TarifaICBF = 3;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
														elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
																$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
																$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
																$TipoContrato == 'PASANTÍA')
														{
															$TarifaPension = 0;
															$TarifaSalud = 12.5;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = 0;
															$DiasCCF = 0;
														}
														else
														{
															$TarifaPension = 16;
															$TarifaSalud = 4;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
													
														// DIAS PENSION
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS SALUD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS ARL
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS CCF
														$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
														$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);
				
														// SALARIO INTEGRAL
														if ($RegimenCesantias == 'SALARIO INTEGRAL')
															$registro2 .= 'X';  								
														else
															$registro2 .= 'F';
				
														$ibcPension = $IBC;
														$ValorPension = $IBC / 30 * $Dias * $TarifaPension / 100;
														if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
															$ValorPension = round($ValorPension + 50, -2);
			
														$ibcSalud = $IBC;
														$ValorSalud = $IBC / 30 * $Dias * $TarifaSalud / 100;
														if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
															$ValorSalud = round($ValorSalud + 50, -2);
			
														$ibcCCF = $IBC;
														$ValorCCF = $IBC / 30 * $Dias * $TarifaCCF / 100;
														if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
															$ValorCCF = round($ValorCCF + 50, -2);
			
														$ibcSENA = $IBC;
														$ValorSENA = $IBC / 30 * $Dias * $TarifaSENA / 100;
														if (intval("$ValorSENA") <> "$ValorSENA"  OR "$ValorSENA" % 100 <> 0) 
															$ValorSENA = round($ValorSENA + 50, -2);
			
														$ibcICBF = $IBC;
														$ValorICBF = $IBC / 30 * $Dias * $TarifaICBF / 100;
														if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
															$ValorICBF = round($ValorICBF + 50, -2);

														$ibcSolidaridad = $IBC;
														$FondoSolidaridad = $IBC / 30 * $Dias * $PorcentajeSolidaridad / 100;
														if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0)
															$FondoSolidaridad = round($FondoSolidaridad + 50, -2);

														$ibcSubsistencia = $IBC;
														$FondoSubsistencia = $IBC / 30 * $Dias * $PorcentajeSubsistencia / 100;
														if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0)
															$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
												
														// IBC PENSION
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														// IBC SALUD
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);					
														// IBC ARL
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// IBC CCF
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
														// TARIFA PENSION
														$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  	
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  		
														// APORTE VOLUNTARIO PENSION EMPLEADO
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// APORTE VOLUNTARIO PENSION EMPLEADOR
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
														// FONDO SOLIDARIDAD
														$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// FONDO SUBSISTENCIA
														$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// VR NO RETENIDO POR APORTES VOLUNTARIOS
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA SALUD
														$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SALUD
														$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// VALOR UPC ADICIONAL
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// NUMERO INCAPACIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR INCAPACIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 						
														// NUMERO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR LICENCIA MATERNIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
				
														$ibcARL = $IBC;
														$TarifaARL = 0;
														$ValorARL = 0;
				
														// TARIFA ARL
														$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);				
														// CENTRO DE TRABAJO
														$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   						
														// VALOR ARL
														$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
														// TARIFA CCF
														$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  		
														// VALOR CCF
														$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// TARIFA SENA
														$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
														// VALOR SENA
														$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ICBF
														$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
														// VALOR ICBF
														$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ESAP
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR ESAP
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA MEN
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR MEN
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   						
														$registro2 .= str_pad(' ', 2);											
														$registro2 .= str_pad(' ', 16);
														$registro2 .= $ExoneradoSalud;

														$registro2 .= str_pad($CodigoARL, 6);
														// CLASE RIESGO 1..5
														$registro2 .= $NivelRiesgo;												
														// ACTIVIDAD ALTO RIESGO
														$registro2 .= ' ';  													
														// FECHA INGRESO
														$registro2 .= str_pad(' ', 10);
														// FECHA RETIRO
														$registro2 .= str_pad(' ', 10);
														// FECHA AUMENTO
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA NO REMUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA NO REMUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO INCAPACIDAD ENFERMEDAD
														$registro2 .= $FechaInicioIncapacidad;									
														// FECHA FINAL INCAPACIDAD ENFERMEDAD
														$registro2 .= $FechaFinalIncapacidad;									
														// FECHA INICIO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL VACACIONES
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA FINAL VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA INICIO INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// IBC OTROS PARAFISCALES NO CCF
														if ($TarifaSENA > 0 AND $TarifaICBF > 0)
															$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
														else
															$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  									
														// HORAS LABORADAS
														$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(' ', 10);
														$registro2 .= $NivelRiesgo . '620901';
														$registro2 .= PHP_EOL;

														$datosArchivo2 .= $registro2;
														$TotalIBC += ($IBC / 30 * $Dias);

														$TotalIncapacidades++;
														$ValorTotalIncapacidades += $regIncapacidad['Valor'];

														break;

													case 'ES VACACIONES EN TIEMPO':
														$FechaInicioVacaciones = max($regIncapacidad['FechaInicial'], $FechaInicial);
														$FechaFinalVacaciones = min($regIncapacidad['FechaFinal'], $FechaFinal);

														if ($regEmpleado['horasmes'] == 120) 
															$Dias = round($regIncapacidad['Horas'] / 4, 0);
														else
															$Dias = ($regIncapacidad['Horas'] / 8);

														if ($Dias == 0.5)
															$Dias = 1;
														
														$Secuencia++;

														$registro2 = '02';
														$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
														$registro2 .= $TipoDocumento;
														$registro2 .= str_pad($regEmpleado['documento'], 16);
														$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);
														// SUBTIPO COTIZANTE
														$registro2 .= $SubtipoCotizante;  			
														// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
														$registro2 .= ' ';  						
														// COLOMBIANO EN EL EXTERIOR
														$registro2 .= ' ';  						
														$registro2 .= $Ciudad;
														$registro2 .= str_pad($Apellido1, 20);
														$registro2 .= str_pad($Apellido2, 30);
														$registro2 .= str_pad($Nombre1, 20);
														$registro2 .= str_pad($Nombre2, 30);
														// INGRESO
														$registro2 .= ' ';  									
														// RETIRO
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRO FP  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRO FP  X
														$registro2 .= ' ';  									
														// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
														$registro2 .= ' ';  									
														// CORRECCIONES  A/C
														$registro2 .= ' ';  									
														// VARIACION TRANSITORIA  DE SALARIO
														$registro2 .= ' ';  									
														// SUSPENSION LICENCIA NO REMUNERADA
														$registro2 .= ' ';  									
														// INCAPACIDAD ENFERMEDAD GENERAL
														$registro2 .= ' ';  									
														// LICENCIA DE MATERNIDAD
														$registro2 .= ' ';  									
														// VACACIONES / LICENCIA REMUNERADA  X/L
														$registro2 .= 'X';  									
														// APORTE VOLUNTARIO
														$registro2 .= ' ';  									
														// VARIACION CENTRO DE TRABAJO
														$registro2 .= ' ';  									
														// DIAS INCAPACIDAD
														$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT);  	
				
														if ($regEmpleado['idfondopensiones'] > 0)
															$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
														else	
															$FP = str_pad(' ', 6);

														$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];
				
														if ($regEmpleado['idcajacompensacion'] > 0)
															$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
														else	
															$CCF = str_pad(' ', 6);
				
														$registro2 .= str_pad($FP, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($EPS, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($CCF, 6);

														if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBC >= $SueldoMinimo * 10)
														{
															$TarifaPension = 16;
															$TarifaSalud = 12.5;
															$TarifaCCF = 4;
															$TarifaSENA = 2;
															$TarifaICBF = 3;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
														elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
																$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
																$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
																$TipoContrato == 'PASANTÍA')
														{
															$TarifaPension = 0;
															$TarifaSalud = 12.5;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$TarifaCCF = 0;
															$DiasPension = 0;
															$DiasCCF = 0;
														}
														else
														{
															$TarifaPension = 16;
															$TarifaSalud = 4;
															$TarifaCCF = 4;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
													
														// DIAS PENSION
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS SALUD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS ARL
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS CCF
														$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
														$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);
				
														// SALARIO INTEGRAL
														if ($RegimenCesantias == 'SALARIO INTEGRAL')
															$registro2 .= 'X';  								
														else
															$registro2 .= 'F';									
														// SALARIO FIJO
				
														$ibcPension = $IBC;
														$ValorPension = $IBC / 30 * $Dias * $TarifaPension / 100;
														if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
															$ValorPension = round($ValorPension + 50, -2);
			
														$ibcSalud = $IBC;
														$ValorSalud = $IBC / 30 * $Dias * $TarifaSalud / 100;
														if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
															$ValorSalud = round($ValorSalud + 50, -2);
			
														$ibcCCF = $regIncapacidad['Valor'];
														$ValorCCF = $regIncapacidad['Valor'] * $TarifaCCF / 100;
														if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
															$ValorCCF = round($ValorCCF + 50, -2);
			
														$ibcSENA = $regIncapacidad['Valor'];
														$ValorSENA = $regIncapacidad['Valor'] * $TarifaSENA / 100;
														if (intval("$ValorSENA") <> "$ValorSENA"  OR "$ValorSENA" % 100 <> 0) 
															$ValorSENA = round($ValorSENA + 50, -2);
			
														$ibcICBF = $regIncapacidad['Valor'];
														$ValorICBF = $regIncapacidad['Valor'] * $TarifaICBF / 100;
														if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
															$ValorICBF = round($ValorICBF + 50, -2);

														$ibcSolidaridad = $IBC;
														$FondoSolidaridad = $IBC / 30 * $Dias * $PorcentajeSolidaridad / 100;
														if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0)
															$FondoSolidaridad = round($FondoSolidaridad + 50, -2);

														$ibcSubsistencia = $IBC;
														$FondoSubsistencia = $IBC / 30 * $Dias * $PorcentajeSubsistencia / 100;
														if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0)
															$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
								
														// IBC PENSION
														$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);
														// IBC SALUD
														$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);
														// IBC ARL
														$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);
														// IBC CCF
														$registro2 .= str_pad(number_format($regIncapacidad['Valor'], 0, '.', ''), 9, '0', STR_PAD_LEFT);  						
														// TARIFA PENSION
														$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  		
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// APORTE VOLUNTARIO PENSION EMPLEADO
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// APORTE VOLUNTARIO PENSION EMPLEADOR
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// FONDO SOLIDARIDAD
														$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
														// FONDO SUBSISTENCIA
														$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// VR NO RETENIDO POR APORTES VOLUNTARIOS
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA SALUD
														$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SALUD
														$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// VALOR UPC ADICIONAL
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// NUMERO INCAPACIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR INCAPACIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 						
														// NUMERO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR LICENCIA MATERNIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
				
														$ibcARL = $IBC;
														$TarifaARL = 0;
														$ValorARL = 0;
				
														// TARIFA ARL
														$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);				
														// CENTRO DE TRABAJO
														$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   						
														// VALOR ARL
														$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
														// TARIFA CCF
														$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  				
														// VALOR CCF
														$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  				
														// TARIFA SENA
														$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR SENA
														$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ICBF
														$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				
														// VALOR ICBF
														$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ESAP
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR ESAP
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// TARIFA MEN
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  						
														// VALOR MEN
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   						
														$registro2 .= str_pad(' ', 2);											
														$registro2 .= str_pad(' ', 16);
														$registro2 .= $ExoneradoSalud;

														$registro2 .= str_pad($CodigoARL, 6);
														// CLASE RIESGO 1..5
														$registro2 .= $NivelRiesgo;												
														// ACTIVIDAD ALTO RIESGO
														$registro2 .= ' ';  													
														// FECHA INGRESO
														$registro2 .= str_pad(' ', 10);
														// FECHA RETIRO
														$registro2 .= str_pad(' ', 10);
														// FECHA AUMENTO
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA NO REMIUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA NO REMUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VACACIONES
														$registro2 .= $FechaInicioVacaciones;									
														// FECHA FINAL VACACIONES
														$registro2 .= $FechaFinalVacaciones;									
														// FECHA INICIO VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA FINAL VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA INICIO INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// IBC OTROS PARAFISCALES NO CCF
														if ($TarifaSENA > 0 AND $TarifaICBF > 0)
															$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
														else
															$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  									
														// HORAS LABORADAS
														$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(' ', 10);
														$registro2 .= $NivelRiesgo . '620901';
														$registro2 .= PHP_EOL;

														$datosArchivo2 .= $registro2;
														$TotalIBC += ($IBC / 30 * $Dias);

														break;

													case 'ES LICENCIA REMUNERADA':
													case 'ES PERMISO REMUNERADO':
														$FechaInicioVacaciones = max($regIncapacidad['FechaInicial'], $FechaInicial);
														$FechaFinalVacaciones = min($regIncapacidad['FechaFinal'], $FechaFinal);

														if ($regEmpleado['horasmes'] == 120) 
															$Dias = round($regIncapacidad['Horas'] / 4, 0);
														else
															$Dias = ($regIncapacidad['Horas'] / 8);

														if ($Dias == 0.5)
															$Dias = 1;
														
														$Secuencia++;

														$registro2 = '02';
														$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
														$registro2 .= $TipoDocumento;
														$registro2 .= str_pad($regEmpleado['documento'], 16);
														$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);
														// SUBTIPO COTIZANTE
														$registro2 .= $SubtipoCotizante;  			
														// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
														$registro2 .= ' ';  						
														// COLOMBIANO EN EL EXTERIOR
														$registro2 .= ' ';  						
														$registro2 .= $Ciudad;
														$registro2 .= str_pad($Apellido1, 20);
														$registro2 .= str_pad($Apellido2, 30);
														$registro2 .= str_pad($Nombre1, 20);
														$registro2 .= str_pad($Nombre2, 30);
														// INGRESO
														$registro2 .= ' ';  									
														// RETIRO
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRA EPS  X
														$registro2 .= ' ';  									
														// TRASLADO DESDE OTRO FP  X
														$registro2 .= ' ';  									
														// TRASLADO A OTRO FP  X
														$registro2 .= ' ';  									
														// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
														$registro2 .= ' ';  									
														// CORRECCIONES  A/C
														$registro2 .= ' ';  									
														// VARIACION TRANSITORIA  DE SALARIO
														$registro2 .= ' ';  									
														// SUSPENSION LICENCIA NO REMUNERADA
														$registro2 .= ' ';  									
														// INCAPACIDAD ENFERMEDAD GENERAL
														$registro2 .= ' ';  									
														// LICENCIA DE MATERNIDAD
														$registro2 .= ' ';  									
														// VACACIONES / LICENCIA REMUNERADA  X/L
														$registro2 .= 'L';  									
														// APORTE VOLUNTARIO
														$registro2 .= ' ';  									
														// VARIACION CENTRO DE TRABAJO
														$registro2 .= ' ';  									
														// DIAS INCAPACIDAD
														$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT);  	
				
														if ($regEmpleado['idfondopensiones'] > 0)
															$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
														else	
															$FP = str_pad(' ', 6);

														$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];
				
														if ($regEmpleado['idcajacompensacion'] > 0)
															$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
														else	
															$CCF = str_pad(' ', 6);
				
														$registro2 .= str_pad($FP, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($EPS, 6);
														// SI HAY TRASLADO
														$registro2 .= str_pad(' ', 6);  						
														$registro2 .= str_pad($CCF, 6);

														if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBC >= $SueldoMinimo * 10)
														{
															$TarifaPension = 16;
															$TarifaSalud = 12.5;
															$TarifaCCF = 4;
															$TarifaSENA = 2;
															$TarifaICBF = 3;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
														elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
																$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
																$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
																$TipoContrato == 'PASANTÍA')
														{
															$TarifaPension = 0;
															$TarifaSalud = 12.5;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$TarifaCCF = 0;
															$DiasPension = 0;
															$DiasCCF = 0;
														}
														else
														{
															$TarifaPension = 16;
															$TarifaSalud = 4;
															$TarifaCCF = 4;
															$TarifaSENA = 0;
															$TarifaICBF = 0;
															$DiasPension = $Dias;
															$DiasCCF = $Dias;
														}
													
														// DIAS PENSION
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS SALUD
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS ARL
														$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
														// DIAS CCF
														$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
														$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);
				
														// SALARIO INTEGRAL
														if ($RegimenCesantias == 'SALARIO INTEGRAL')
															$registro2 .= 'X';  								
														else
															$registro2 .= 'F';
				
														$ibcPension = $IBC;
														$ValorPension = $IBC * $TarifaPension / 100;
														if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
															$ValorPension = round($ValorPension + 50, -2);
			
														$ibcSalud = $IBC;
														$ValorSalud = $IBC * $TarifaSalud / 100;
														if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
															$ValorSalud = round($ValorSalud + 50, -2);
			
														$ibcCCF = $IBC;
														$ValorCCF = $IBC * $TarifaCCF / 100;
														if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
															$ValorCCF = round($ValorCCF + 50, -2);
			
														$ibcSENA = $IBC;
														$ValorSENA = $IBC * $TarifaSENA / 100;
														if (intval("$ValorSENA") <> "$ValorSENA"  OR "$ValorSENA" % 100 <> 0) 
															$ValorSENA = round($ValorSENA + 50, -2);
			
														$ibcICBF = $IBC;
														$ValorICBF = $IBC * $TarifaICBF / 100;
														if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
															$ValorICBF = round($ValorICBF + 50, -2);

														$ibcSolidaridad = $IBC;
														$FondoSolidaridad = $IBC * $PorcentajeSolidaridad / 100;
														if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0)
															$FondoSolidaridad = round($FondoSolidaridad + 50, -2);

														$ibcSubsistencia = $IBC;
														$FondoSubsistencia = $IBC * $PorcentajeSubsistencia / 100;
														if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0)
															$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
												
														// IBC PENSION
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														// IBC SALUD
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);						
														// IBC ARL
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  						
														// IBC CCF
														// $registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);  						
														$registro2 .= str_pad(number_format($IBC, 0, '.', ''), 9, '0', STR_PAD_LEFT);  						
														// TARIFA PENSION
														$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  			
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// APORTE VOLUNTARIO PENSION EMPLEADO
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// APORTE VOLUNTARIO PENSION EMPLEADOR
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
														// VALOR PENSION
														$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// FONDO SOLIDARIDAD
														$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
														// FONDO SUBSISTENCIA
														$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);		
														// VR NO RETENIDO POR APORTES VOLUNTARIOS
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);				// TARIFA SALUD
														// VALOR SALUD
														$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// VALOR UPC ADICIONAL
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
														// NUMERO INCAPACIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR INCAPACIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 						
														// NUMERO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 15);  										
														// VALOR LICENCIA MATERNIDAD
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  						
				
														$ibcARL = $IBC;
														$TarifaARL = 0;
														$ValorARL = 0;
				
														// TARIFA ARL
														$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);		
														// CENTRO DE TRABAJO
														$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   										
														// VALOR ARL
														$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
														// TARIFA CCF
														$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  		
														// VALOR CCF
														$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
														// TARIFA SENA
														$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
														// VALOR SENA
														$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ICBF
														$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
														// VALOR ICBF
														$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
														// TARIFA ESAP
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  												
														// VALOR ESAP
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  												
														// TARIFA MEN
														$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  												
														// VALOR MEN
														$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   												
														$registro2 .= str_pad(' ', 2);											
														$registro2 .= str_pad(' ', 16);
														$registro2 .= $ExoneradoSalud;

														$registro2 .= str_pad($CodigoARL, 6);
														// CLASE RIESGO 1..5
														$registro2 .= $NivelRiesgo;												
														// ACTIVIDAD ALTO RIESGO
														$registro2 .= ' ';  													
														// FECHA INGRESO
														$registro2 .= str_pad(' ', 10);
														// FECHA RETIRO
														$registro2 .= str_pad(' ', 10);
														// FECHA AUMENTO
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA NO REMIUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA NO REMUNERADA
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD ENFERMEDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL LICENCIA MATERNIDAD
														$registro2 .= str_pad(' ', 10);											
														// FECHA INICIO VACACIONES
														$registro2 .= $FechaInicioVacaciones;									
														// FECHA FINAL VACACIONES
														$registro2 .= $FechaFinalVacaciones;									
														// FECHA INICIO VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA FINAL VCT
														$registro2 .= str_pad(' ', 10);  										
														// FECHA INICIO INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// FECHA FINAL INCAPACIDAD LABORAL
														$registro2 .= str_pad(' ', 10);											
														// IBC OTROS PARAFISCALES NO CCF
														if ($TarifaSENA > 0 AND $TarifaICBF > 0)
															$registro2 .= str_pad(number_format($IBC / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
														else
															$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  								
														// HORAS LABORADAS
														$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);					
														$registro2 .= str_pad(' ', 10);
														$registro2 .= $NivelRiesgo . '620901';
														$registro2 .= PHP_EOL;

														$datosArchivo2 .= $registro2;
														$TotalIBC += ($IBC / 30 * $Dias);

														break;

												}

												if (!empty($registro2) AND ($_REQUEST['Informe'] == 11 OR $_REQUEST['Informe'] == 12)) {
													$this->model->guardarPILAE(array(
														$IdPeriodo,
														$Ciclo,
														$IdEmpleado,
														$ArchivoNomina,
														$regIncapacidad['id'],
														$regIncapacidad['idconcepto'],
														$Dias,
														$ibcPension,
														$ibcSalud,
														$ibcARL,
														$ibcCCF,
														$ibcSENA,
														$ibcICBF,
														$ibcSolidaridad,
														$ibcSubsistencia,
														$TarifaPension,
														$TarifaSalud,
														$TarifaARL,
														$TarifaCCF,
														$TarifaSENA,
														$TarifaICBF,
														$PorcentajeSolidaridad,
														$PorcentajeSubsistencia,
														$ValorPension,
														$ValorSalud,
														$ValorARL,
														$ValorCCF,
														$ValorSENA,
														$ValorICBF,
														$FondoSolidaridad,
														$FondoSubsistencia,
														$registro2
													));
												}

												$TotalIBCParcial += ($IBC / 30 * $Dias);
											}
										}

										$IBCSalud = 0;
										$ValorSalud = 0;
										$IBCPension = 0;
										$IBCFondoSolidaridad = 0;
										$ValorPension = 0;
										$FondoSolidaridad = 0;
										$FondoSubsistencia = 0;
										$Dias = 0;
										$idsAchivo = '';
										$idsConcepto = '';

										if ($acumuladosDatosBase) {
											for ($j = 0; $j < count($acumuladosDatosBase); $j++) {
												$regNomina = $acumuladosDatosBase[$j];

												if	($regNomina['TipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO' OR 
													$regNomina['TipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
													$regNomina['TipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO (APRENDIZ SENA)')
												{													
													if ($regNomina['Imputacion'] == 'PAGO')
														if ($regEmpleado['horasmes'] == 120)
															$Dias += ($regNomina['horas'] / 4);
														else
															$Dias += ($regNomina['horas'] / 8);
													else
														if ($regEmpleado['horasmes'] == 120)
															$Dias -= ($regNomina['horas'] / 4);
														else
															$Dias -= ($regNomina['horas'] / 8);
												}

												if ($regNomina['ClaseConcepto'] == 'SALARIO') {
													if ($idsAchivo=='') $idsAchivo = $regNomina['id'];
													else $idsAchivo .= ','.$regNomina['id'];
	
													if ($idsConcepto=='') $idsConcepto = $regNomina['idconcepto'];
													else $idsConcepto .= ','.$regNomina['idconcepto'];

													if ($regNomina['Imputacion'] == 'PAGO') {
														$IBCSalud += $regNomina['valor'];
														$IBCPension += $regNomina['valor'];
													} else {
														$IBCSalud -= $regNomina['base'];
														$IBCPension -= $regNomina['base'];
													}
												}

												if	(($regNomina['TipoRegistroAuxiliar'] == 'ES LICENCIA NO REMUNERADA' OR $regNomina['TipoRegistroAuxiliar'] == 'ES SANCIÓN') AND 
													$regNomina['Imputacion'] == 'DEDUCCIÓN')
													$Dias -= ($regNomina['horas'] / 8);

												if ($regNomina['TipoRegistroAuxiliar'] == 'ES FONDO DE SOLIDARIDAD') 
													$IBCFondoSolidaridad = $regNomina['base'];
											}

											if ($regEmpleado['sueldobasico'] > $SueldoMinimo * 10 OR 
												$IBCSalud > $SueldoMinimo * 10 OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
												$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
												$TipoContrato == 'PASANTÍA'
											) 
												$ExoneradoSalud = 'N';
											else
												$ExoneradoSalud = $ExoneracionEmpresa;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 4) 
												$PorcentajeSolidaridad = 0.5;
											else
												$PorcentajeSolidaridad = 0;

											$PorcentajeSubsistencia = 0;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 4 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 16)
												$PorcentajeSubsistencia = 0.5;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 16 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 17)
												$PorcentajeSubsistencia = 0.7;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 17 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 18)
												$PorcentajeSubsistencia = 0.9;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 18 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 19)
												$PorcentajeSubsistencia = 1.1;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 19 AND 
												$IBCFondoSolidaridad < $SueldoMinimo * 20)
												$PorcentajeSubsistencia = 1.3;

											if ($IBCFondoSolidaridad >= $SueldoMinimo * 20)
												$PorcentajeSubsistencia = 1.5;

											if ($Dias > 0 AND $IBCSalud / $Dias * 30 < $SueldoMinimo)
												$IBCSalud = $SueldoMinimo / 30 * $Dias;

											if ($IBCSalud - intval($IBCSalud) > 0) 
												$IBCSalud = intval($IBCSalud + 1);

											if (intval("$IBCSalud") <> "$IBCSalud" OR "$IBCSalud" % 100 <> 0) 
												$IBCSalud = round($IBCSalud + 50, -2);

											if ($RegimenCesantias == 'SALARIO INTEGRAL') $IBCSalud *= 0.7;

											if ($IBCSalud > $SueldoMinimo * 25) $IBCSalud = $SueldoMinimo * 25;

											$IBCPension = $IBCSalud;

											if ($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
												$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
												$TipoContrato == 'PASANTÍA') 
												$IBCPension = 0;
										}

										if ($IBCSalud + $TotalIBCParcial <= $SueldoMinimo * 25) $TotalIBCParcial = 0;

										if ($Dias > 0) {
											$Secuencia++;

											$registro2 = '02';
											$registro2 .= str_pad($Secuencia, 5, '0', STR_PAD_LEFT);
											$registro2 .= $TipoDocumento;
											$registro2 .= str_pad($regEmpleado['documento'], 16);
											$registro2 .= str_pad($TipoCotizante, 2, '0', STR_PAD_LEFT);

											// SUBTIPO COTIZANTE
											$registro2 .= $SubtipoCotizante;  			
											// EXTRANJERO NO OBLIOGADO A COTIZAR PENSION
											$registro2 .= ' ';  						
											// COLOMBIANO EN EL EXTERIOR
											$registro2 .= ' ';  						

											$registro2 .= $Ciudad;

											$registro2 .= str_pad($Apellido1, 20);
											$registro2 .= str_pad($Apellido2, 30);
											$registro2 .= str_pad($Nombre1, 20);
											$registro2 .= str_pad($Nombre2, 30);

											// INGRESO
											$registro2 .= $EsIngreso;  					
											// RETIRO
											$registro2 .= $EsRetiro;  					

											// TRASLADO DESDE OTRA EPS  X
											$registro2 .= ' ';  						
											// TRASLADO A OTRA EPS  X
											$registro2 .= ' ';  						
											// TRASLADO DESDE OTRO FP  X
											$registro2 .= ' ';  						
											// TRASLADO A OTRO FP  X
											$registro2 .= ' ';  						

											$query = <<<EOD
												SELECT * 
													FROM AUMENTOSSALARIALES 
													WHERE AUMENTOSSALARIALES.FechaAumento >= '$FechaInicial' AND 
														AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
														AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado;
											EOD; 

											$aumentos = $this->model->listar($query);

											if ($aumentos)
											{
												$EsAumento = 'X';
												$FechaAumento = $aumentos[0]['fechaaumento'];
											}
											else
											{
												$EsAumento = ' ';
												$FechaAumento = str_pad(' ', 10);
											}

											// VARIACION PERMANENTE DE SALARIO (AUMENTOS)
											$registro2 .= $EsAumento;  						
											// CORRECCIONES  A/C
											$registro2 .= ' ';  							
											if ($IBCSalud <> $regEmpleado['sueldobasico'] AND 
												$TipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
												$TipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
												$TipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
												$TipoContrato <> 'PASANTÍA') 
											{
												// VARIACION TRANSITORIA  DE SALARIO
												$registro2 .= 'X';  							
											}
											else
											{
												// VARIACION TRANSITORIA  DE SALARIO
												$registro2 .= ' ';  							
											}

											// REGISTRO BASE
											// SUSPENSION LICENCIA NO REMUNERADA
											$registro2 .= ' ';  							
											// INCAPACIDAD ENFERMEDAD GENERAL
											$registro2 .= ' ';  							
											// LICENCIA DE MATERNIDADA
											$registro2 .= ' ';  							
											// VACACIONES / LICENCIA REMUNERADA  X/L
											$registro2 .= ' ';  							
											// APORTE VOLUNTARIO
											$registro2 .= ' ';  							
											// VARIACION CENTRO DE TRABAJO
											$registro2 .= ' ';  							
											// DIAS INCAPACIDAD
											$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT); 

											if ($regEmpleado['idfondopensiones'] > 0)
												$FP = getRegistro('TERCEROS', $regEmpleado['idfondopensiones'])['codigofp'];
											else	
												$FP = str_pad(' ', 6);

											if ($regEmpleado['ideps'] > 0)
												$EPS = getRegistro('TERCEROS', $regEmpleado['ideps'])['codigoeps'];
											else
												$EPS = str_pad(' ', 6);

											if ($regEmpleado['idcajacompensacion'] > 0)
												$CCF = getRegistro('TERCEROS', $regEmpleado['idcajacompensacion'])['codigoccf'];
											else
												$CCF = str_pad(' ', 6);

											$registro2 .= str_pad($FP, 6);
											// SI HAY TRASLADO
											$registro2 .= str_pad(' ', 6);  						
											$registro2 .= str_pad($EPS, 6);
											// SI HAY TRASLADO
											$registro2 .= str_pad(' ', 6);  						
											$registro2 .= str_pad($CCF, 6);

											if ($regEmpleado['sueldobasico'] >= $SueldoMinimo * 10 OR $IBCSalud >= $SueldoMinimo * 10)
											{
												$TarifaPension = 16;
												$TarifaSalud = 12.5;
												$TarifaCCF = 4;
												$TarifaSENA = 2;
												$TarifaICBF = 3;
												$DiasPension = $Dias;
												$DiasCCF = $Dias;
											}
											elseif($TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
												$TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
												$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
												$TipoContrato == 'PASANTÍA')
											{
												$TarifaPension = 0;
												$TarifaSalud = 12.5;
												$TarifaSENA = 0;
												$TarifaICBF = 0;
												$TarifaCCF = 0;
												$DiasPension = 0;
												$DiasCCF = 0;
											}
											else
											{
												$TarifaPension = 16;
												$TarifaSalud = 4;
												$TarifaCCF = 4;
												$TarifaSENA = 0;
												$TarifaICBF = 0;
												$DiasPension = $Dias;
												$DiasCCF = $Dias;
											}

											if ($regEmpleado['idfondopensiones'] == 0)
											{
												$IBCPension = 0;
												$TarifaPension = 0;
												$DiasPension = 0;
												$PorcentajeSolidaridad = 0;
												$PorcentajeSubsistencia = 0;
											}

											if ($TarifaPension == 0) 
											{
												// DIAS PENSION
												$registro2 .= str_pad(0, 2, '0', STR_PAD_LEFT);  		
											}
											else
											{
												// DIAS PENSION
												$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  	
											}
											// DIAS SALUD
											$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  		
											// DIAS ARL
											$registro2 .= str_pad($Dias, 2, '0', STR_PAD_LEFT);  		
											// DIAS CCF
											$registro2 .= str_pad($DiasCCF, 2, '0', STR_PAD_LEFT);  	
											$registro2 .= str_pad(number_format($regEmpleado['sueldobasico'], 0, '.', ''), 9, '0', STR_PAD_LEFT);

											if ($RegimenCesantias == 'SALARIO INTEGRAL')
											{
												// SALARIO INTEGRAL
												$registro2 .= 'X';  									
											}
											else
											{
												$registro2 .= 'F';
											}

											$ValorPension = ($IBCPension - $TotalIBCParcial) * $TarifaPension / 100;
											if (intval("$ValorPension") <> "$ValorPension" OR "$ValorPension" % 100 <> 0) 
												$ValorPension = round($ValorPension + 50, -2);

											$ValorSalud = ($IBCSalud - $TotalIBCParcial) * $TarifaSalud / 100;
											if (intval("$ValorSalud") <> "$ValorSalud" OR "$ValorSalud" % 100 <> 0) 
												$ValorSalud = round($ValorSalud + 50, -2);

											$ValorCCF = $IBCSalud * $TarifaCCF / 100;
											if (intval("$ValorCCF") <> "$ValorCCF" OR "$ValorCCF" % 100 <> 0) 
												$ValorCCF = round($ValorCCF + 50, -2);

											$ValorSENA = $IBCSalud * $TarifaSENA / 100;
											if (intval("$ValorSENA") <> "$ValorSENA" OR "$ValorSENA" % 100 <> 0) 
												$ValorSENA = round($ValorSENA + 50, -2);

											$ValorICBF = $IBCSalud * $TarifaICBF / 100;
											if (intval("$ValorICBF") <> "$ValorICBF" OR "$ValorICBF" % 100 <> 0) 
												$ValorICBF = round($ValorICBF + 50, -2);

											$FondoSolidaridad = $IBCPension * $PorcentajeSolidaridad / 100;
											if (intval("$FondoSolidaridad") <> "$FondoSolidaridad" OR "$FondoSolidaridad" % 100 <> 0) 
												$FondoSolidaridad = round($FondoSolidaridad + 50, -2);
											
											$FondoSubsistencia = $IBCPension * $PorcentajeSubsistencia / 100;
											if (intval("$FondoSubsistencia") <> "$FondoSubsistencia" OR "$FondoSubsistencia" % 100 <> 0) 
												$FondoSubsistencia = round($FondoSubsistencia + 50, -2);
	
											$registro2 .= str_pad(number_format(($IBCPension - $TotalIBCParcial), 0, '.', ''), 9, '0', STR_PAD_LEFT);
											$registro2 .= str_pad(number_format(($IBCSalud - $TotalIBCParcial), 0, '.', ''), 9, '0', STR_PAD_LEFT);
											// IBC ARL
											$registro2 .= str_pad(number_format(($IBCSalud - $TotalIBCParcial), 0, '.', ''), 9, '0', STR_PAD_LEFT);  				

											if ($TarifaCCF == 0) 
											{
												// IBC CCF
												$registro2 .= str_pad(number_format(0, 0, '.', ''), 9, '0', STR_PAD_LEFT);  					
											}
											else
											{
												// IBC CCF
												$registro2 .= str_pad(number_format($IBCSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
											}

											// TARIFA PENSION
											$registro2 .= str_pad(number_format($TarifaPension / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  		
											// VALOR PENSION
											$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
											// APORTE VOLUNTARIO PENSION EMPLEADO
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
											// APORTE VOLUNTARIO PENSION EMPLEADOR
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
											// VALOR PENSION
											$registro2 .= str_pad(number_format($ValorPension, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
											// FONDO SOLIDARIDAD
											$registro2 .= str_pad(number_format($FondoSolidaridad, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
											// FONDO SUBSISTENCIA
											$registro2 .= str_pad(number_format($FondoSubsistencia, 0, '.', ''), 9, '0', STR_PAD_LEFT);			
											// VR NO RETENIDO POR APORTES VOLUNTARIOS
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
											// TARIFA SALUD
											$registro2 .= str_pad(number_format($TarifaSalud / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);			
											// VALOR SALUD
											$registro2 .= str_pad(number_format($ValorSalud, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
											// VALOR UPC ADICIONAL
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													
											// NUMERO INCAPACIDAD
											$registro2 .= str_pad(' ', 15);  																	
											// VALOR INCAPACIDAD
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT); 													
											// NUMERO LICENCIA MATERNIDAD
											$registro2 .= str_pad(' ', 15);  																	
											// VALOR LICENCIA MATERNIDAD
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  													

											$TarifaARL = getRegistro('PARAMETROS', $regEmpleado['nivelriesgo'])['valor2'];
											$ValorARL = round(($IBCSalud - $TotalIBCParcial) * $TarifaARL / 100, 2);
											if (intval("$ValorARL") <> "$ValorARL" OR "$ValorARL" % 100 <> 0) 
												$ValorARL = round($ValorARL + 50, -2);

											// TARIFA ARL
											$registro2 .= str_pad(number_format($TarifaARL / 100, 7, '.', ''), 9, '0', STR_PAD_LEFT);			

											// CENTRO DE TRABAJO
											$registro2 .= str_pad($Centro, 9, '0', STR_PAD_LEFT);   										
											// VALOR ARL
											$registro2 .= str_pad(number_format($ValorARL, 0, '.', ''), 9, '0', STR_PAD_LEFT); 				
											// TARIFA CCF
											$registro2 .= str_pad(number_format($TarifaCCF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);  		
											// VALOR CCF
											$registro2 .= str_pad(number_format($ValorCCF, 0, '.', ''), 9, '0', STR_PAD_LEFT);  			
											// TARIFA SENA
											$registro2 .= str_pad(number_format($TarifaSENA / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
											// VALOR SENA
											$registro2 .= str_pad(number_format($ValorSENA, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
											// TARIFA ICBF
											$registro2 .= str_pad(number_format($TarifaICBF / 100, 5, '.', ''), 7, '0', STR_PAD_LEFT);		
											// VALOR ICBF
											$registro2 .= str_pad(number_format($ValorICBF, 0, '.', ''), 9, '0', STR_PAD_LEFT);				
											// TARIFA ESAP
											$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  												
											// VALOR ESAP
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  												
											// TARIFA MEN
											$registro2 .= str_pad(0, 7, '0', STR_PAD_LEFT);  												
											// VALOR MEN
											$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);   												
											$registro2 .= str_pad(' ', 2);											
											$registro2 .= str_pad(' ', 16);
											$registro2 .= $ExoneradoSalud;
											$registro2 .= str_pad($CodigoARL, 6);
											// CLASE RIESGO 1..5
											$registro2 .= $NivelRiesgo;												
											// ACTIVIDAD ALTO RIESGO
											$registro2 .= ' ';  													
											// FECHA INGRESO
											$registro2 .= $FechaIngreso;											
											// FECHA RETIRO
											$registro2 .= $FechaRetiro;												
											// FECHA AUMENTO
											$registro2 .= $FechaAumento;											
											// FECHA INICIO LICENCIA NO REMUNERADA
											$registro2 .= str_pad(' ', 10);											
											// FECHA FINAL LICENCIA NO REMUNERADA
											$registro2 .= str_pad(' ', 10);											
											// FECHA INICIO INCAPACIDAD ENFERMEDAD
											$registro2 .= str_pad(' ', 10);											
											// FECHA FINAL INCAPACIDAD ENFERMEDAD
											$registro2 .= str_pad(' ', 10);											
											// FECHA INICIO LICENCIA MATERNIDAD
											$registro2 .= str_pad(' ', 10);											
											// FECHA FINAL LICENCIA MATERNIDAD
											$registro2 .= str_pad(' ', 10);											
											// FECHA INICIO VACACIONES
											$registro2 .= str_pad(' ', 10);											
											// FECHA FINAL VACACIONES
											$registro2 .= str_pad(' ', 10);											
											// FECHA INICIO VCT
											$registro2 .= str_pad(' ', 10);  										
											// FECHA FINAL VCT
											$registro2 .= str_pad(' ', 10);  										
											// FECHA INICIO INCAPACIDAD LABORAL
											$registro2 .= str_pad(' ', 10);											
											// FECHA FINAL INCAPACIDAD LABORAL
											$registro2 .= str_pad(' ', 10);	

											if ($TarifaSENA > 0 AND $TarifaICBF > 0)
											{
												// IBC OTROS PARAFISCALES NO CCF
												$registro2 .= str_pad(number_format($IBCSalud / 30 * $Dias, 0, '.', ''), 9, '0', STR_PAD_LEFT);	
											}
											else
											{
												// IBC OTROS PARAFISCALES NO CCF
												$registro2 .= str_pad(0, 9, '0', STR_PAD_LEFT);  									
											}

											$registro2 .= str_pad($Dias * 8, 3, '0', STR_PAD_LEFT);

											$registro2 .= str_pad(' ', 10);
											$registro2 .= '1620901';
											$registro2 .= PHP_EOL;

											$datosArchivo2 .= $registro2;

											$TotalARL += $ValorARL;
											$TotalIBC += ($IBCSalud / 30 * $Dias);

											$TotalAfiliados++;
										}

										if (!empty($registro2) AND ($_REQUEST['Informe'] == 11 OR $_REQUEST['Informe'] == 12)) {
											$this->model->guardarPILAE(array(
												$IdPeriodo,
												$Ciclo,
												$IdEmpleado,
												$ArchivoNomina,
												$idsAchivo,
												$idsConcepto,
												$Dias,
												($IBCPension - $TotalIBCParcial),
												($IBCSalud - $TotalIBCParcial),
												($IBCSalud - $TotalIBCParcial), // ibcARL
												$IBCSalud, // ibcCCF
												$IBCSalud, // ibcSENA
												$IBCSalud, // ibcICBF
												$IBCPension, // ibcSolidaridad
												$IBCPension, // ibcSubsistencia
												$TarifaPension,
												$TarifaSalud,
												$TarifaARL,
												$TarifaCCF,
												$TarifaSENA,
												$TarifaICBF,
												$PorcentajeSolidaridad,
												$PorcentajeSubsistencia,
												$ValorPension,
												$ValorSalud,
												$ValorARL,
												$ValorCCF,
												$ValorSENA,
												$ValorICBF,
												$FondoSolidaridad,
												$FondoSubsistencia,
												$registro2
											));
										}
									}

									// REGISTRO TIPO 1
									$registro1 = '01';
									// MODALIDAD PLANILLA - ELECTRONICA
									$registro1 .= '1';  																
									$registro1 .= str_pad(1, 4, '0', STR_PAD_LEFT);
									$registro1 .= str_pad($NombreEmpresa, 200);
									$registro1 .= 'NI';
									$registro1 .= str_pad($NitEmpresa, 16);
									$registro1 .= $DVEmpresa;

									// TIPO DE PLANILLA
									if ($_REQUEST['Informe'] == 11 OR $_REQUEST['Informe'] == 12)
										$registro1 .= 'E';
									else
										$registro1 .= 'K';

									$registro1 .= str_pad(' ', 20);
									// FORMA PRESENTACION (UNICA)
									$registro1 .= 'U';   																
									// SUCURSAL
									$registro1 .= str_pad(' ', 10);   													
									// NOMBRE SUCURSAL
									$registro1 .= str_pad(' ', 40);   													
									$registro1 .= str_pad($CodigoARL, 6);
									// PERIODO NO SALUD
									$registro1 .= $Referencia . '-' . str_pad($Periodo, 2, '0', STR_PAD_LEFT);  		
									
									// PERIODO NO SALUD
									if ($Periodo == 12) 
										$registro1 .= ($Referencia) . '-' . str_pad(1, 2, '0', STR_PAD_LEFT);  		
									else
										$registro1 .= $Referencia . '-' . str_pad($Periodo + 1, 2, '0', STR_PAD_LEFT);  
									
									// NUMERO DE PLANILLA
									$registro1 .= str_pad('0', 10);   													
									// FECHA DE PAGO
									$registro1 .= date('Y-m-d');   														
									// NUMERO TOTAL DE EMPLEADOS
									$registro1 .= str_pad($TotalAfiliados, 5, '0', STR_PAD_LEFT);   					
									// TOTAL IBC EMPLEADOS
									$registro1 .= str_pad(number_format($TotalIBC, 0, '.', ''), 12, '0', STR_PAD_LEFT); 
									// TIPO APORTANTE
									$registro1 .= '01';  																
									$registro1 .= '00';
									$registro1 .= PHP_EOL;

									if ($_REQUEST['Informe'] == 11 OR $_REQUEST['Informe'] == 12)
										$Archivo2 = 'descargas/' . $_SESSION['Login']['Usuario'] . '_PlanillaPILA2_E_' . date('YmdGis') . '.txt';
									else
										$Archivo2 = 'descargas/' . $_SESSION['Login']['Usuario'] . '_PlanillaPILA2_K_' . date('YmdGis') . '.txt';
									
									$fp2 = fopen($Archivo2, 'w');
									
									fwrite($fp2, $registro1);
									fwrite($fp2, $datosArchivo2);

									fclose($fp2);

									$type = 'applicatio/force-download';

									header('Content-Description: File Transfer');
									// header('Content-Type: ' . mime_content_type($Archivo2));
									header("Content-Type: $type");
									header('Content-Disposition: attachment; filename=' . basename($Archivo2));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate');
									header('Pragma: public');
									header('Content-Length: ' . filesize($Archivo2));
									ob_clean();
									flush();
									readfile($Archivo2);
					
									header('Location: ' . SERVERURL . '/informesNomina/informes');
									exit();
								}
							}
							else
							{
								$data['mensajeError'] = 'No hay datos disponibles.';
							}

							break;

						// NOMINA POR EMPLEADO CONCEPTO
						case 15:
						case 16:
							if ($_REQUEST['Informe'] == 15)
								$datos = $this->model->prenominaPorEmpleadoConcepto($query, $ArchivoNomina);
							else
								$datos = $this->model->nominaPorEmpleadoConcepto($query);

							if (count($datos) > 0) 
							{
								if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
								{
									$Archivo = COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NominaPorEmpleadoConcepto_' . date('YmdGis') . '.csv';

									$output = fopen($Archivo, 'w');
		
									fputcsv($output, array('EMPLEADO', 'NOMBRE EMPLEADO', 'CONCEPTO', 'DESCRIPCION', 'CENTRO', 'NOMBRE CENTRO', 'VR. PAGOS', 'VR. DEDUCCION'), ';');
		
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
								
									if ($_REQUEST['Informe'] == 15)
										$lcTitulo = utf8_decode('PRENÓMINA POR EMPLEADO - CONCEPTO');
									else
										$lcTitulo = utf8_decode('NÓMINA POR EMPLEADO - CONCEPTO');
									$lcSubTitulo = utf8_decode('PERÍODO LIQUIDADO: ' . $FechaInicial . ' - ' . $FechaFinal);
									$lcEncabezado = '';
									$lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 20);
									$lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 110);
									$lcEncabezado .= str_pad(utf8_decode('PAGOS'), 20);
									$lcEncabezado .= str_pad(utf8_decode('DEDUCCIONES'), 20);
									$lcEncabezado .= str_pad(utf8_decode('NETO A PAGAR'), 20);

									// $PDF->SetMargins(15, 10, 10); 
									$PDF->AddFont('Tahoma','','tahoma.php');
									$PDF->AddPage();
									$PDF->SetFont('Arial', '', 8);

									$DocumentoAnt = '';
									$NombreEmpleadoAnt = '';
									$TotalPagos = 0;
									$TotalDeducciones = 0;
									$GranTotalPagos = 0;
									$GranTotalDeducciones = 0;
									$TotalEmpleados = 0;
							
									for ($i = 0; $i < count($datos); $i++)
									{
										$reg = $datos[$i];

										if ($reg['Documento'] <> $DocumentoAnt) 
										{
											if (! empty($DocumentoAnt))
											{
												$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
												$PDF->Cell(95, 5, utf8_decode('TOTALES POR ' . $NombreEmpleadoAnt), 0, 0, 'L'); 
												$PDF->SetFont('Arial', 'B', 8); 
												$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
												$PDF->SetFont('Arial', '', 8); 
												$PDF->Ln(); 
												// $PDF->Cell(15, 5, '', 0, 0, 'L'); 
												// $PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
												// $PDF->SetFont('Arial', 'B', 8); 
												// $PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
												// $PDF->SetFont('Arial', '', 8); 
												// $PDF->Ln(); 
												$PDF->Ln(); 

												$TotalPagos = 0;
												$TotalDeducciones = 0;
											}

											$PDF->SetFont('Arial', 'B', 8); 
											$PDF->Cell(25, 5, utf8_decode($reg['Documento']), 0, 0, 'L'); 
											$PDF->Cell(60, 5, substr(utf8_decode($reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2']), 0, 60), 0, 0, 'L'); 
											$PDF->SetFont('Arial', '', 8); 
											$PDF->Ln(); 

											$DocumentoAnt = $reg['Documento'];
											$NombreEmpleadoAnt = $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'];
											$TotalEmpleados++;
										}

										$PDF->Cell(25, 5, utf8_decode($reg['Mayor'] . $reg['Auxiliar']), 0, 0, 'L'); 
										$PDF->Cell(60, 5, substr(utf8_decode($reg['NombreConcepto']), 0, 60), 0, 0, 'L'); 
										if ($reg['ValorPagos'] <> 0) 
										{
											$PDF->Cell(40, 5, number_format($reg['ValorPagos'], 0), 0, 0, 'R'); 
											$TotalPagos += $reg['ValorPagos'];
											$GranTotalPagos += $reg['ValorPagos'];
										}
										else
											$PDF->Cell(40, 5, '', 0, 0, 'R'); 

										if ($reg['ValorDeducciones'] <> 0) 
										{
											$PDF->Cell(30, 5, number_format($reg['ValorDeducciones'], 0), 0, 0, 'R'); 
											$TotalDeducciones += $reg['ValorDeducciones'];
											$GranTotalDeducciones += $reg['ValorDeducciones'];
										}
										else
											$PDF->Cell(30, 5, '', 0, 0, 'R'); 

										$PDF->Ln(); 
									}
							
									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(95, 5, utf8_decode('TOTALES POR ' . $NombreEmpleadoAnt), 0, 0, 'L'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($TotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 
									// $PDF->Cell(15, 5, '', 0, 0, 'L'); 
									// $PDF->Cell(115, 5, 'NETO ', 0, 0, 'R'); 
									// $PDF->SetFont('Arial', 'B', 8); 
									// $PDF->Cell(30, 5, number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
									// $PDF->SetFont('Arial', '', 8); 
									// $PDF->Ln(); 
									$PDF->Ln(); 

									$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
									$PDF->Cell(95, 5, utf8_decode('GRAN TOTAL (' . $TotalEmpleados .  ')'), 0, 0, 'L'); 
									$PDF->SetFont('Arial', 'B', 8); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->Cell(30, 5, number_format($GranTotalPagos - $GranTotalDeducciones, 0), 0, 0, 'R'); 
									$PDF->SetFont('Arial', '', 8); 
									$PDF->Ln(); 

									$PDF->Output($_SESSION['Login']['Usuario'] . '_NominaPorEmpleadoConcepto_' . date('YmdGis') . '.PDF', 'I'); 
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
			$_SESSION['Exportar'] = SERVERURL . '/informesNomina/informes';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = SERVERURL . '/informesNomina/informes';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';

			if ($data) 
				$this->views->getView($this, 'informes', $data);
		}

		function CalcularValorIBC($IdEmpleado, $FechaInicialPeriodo, $FechaFinalPeriodo, $SueldoMinimo)
		{
			$query = <<<EOD
				SELECT ACUMULADOS.IdEmpleado, 
						EMPLEADOS.SueldoBasico, 
						SUM(IIF(PARAMETROS3.Detalle = 'ES SUELDO BÁSICO' OR 
								PARAMETROS3.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
								PARAMETROS3.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
								PARAMETROS3.Detalle = 'ES SANCIÓN' OR 
								PARAMETROS3.Detalle = 'ES LICENCIA NO REMUNERADA' OR 
								PARAMETROS3.Detalle = 'ES LICENCIA DE MATERNIDAD' OR 
								PARAMETROS3.Detalle = 'ES INCAPACIDAD EN TIEMPO', 
							IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1), 0)) AS Horas,
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id
						INNER JOIN PARAMETROS AS PARAMETROS1
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2
							ON MAYORES.ClaseConcepto = PARAMETROS2.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS3 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS3.Id 
					WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
						ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
						ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' AND 
						PARAMETROS2.Detalle = 'SALARIO' 
					GROUP BY ACUMULADOS.IdEmpleado, EMPLEADOS.SueldoBasico;
			EOD;

			$ibc = $this->model->listar($query);
								
			if ($ibc) 
			{
				$HorasIBC = $ibc[0]['Horas'];
				$ValorIBC = $ibc[0]['Valor'];
				$hoursMonht = getHoursMonth();
				if ($ValorIBC == 0)
					$ValorIBC = $ibc[0]['SueldoBasico'];

				if ($HorasIBC == 0)
				{ 
					$HorasIBC = $hoursMonht;
				}
	
				$ValorIBC = round($ValorIBC / $HorasIBC * $hoursMonht, 0);
	
				if ($ValorIBC < $SueldoMinimo)
					$ValorIBC = $SueldoMinimo;
			}
			else
				$ValorIBC = 0;

			return $ValorIBC;
		}
	}
?>
