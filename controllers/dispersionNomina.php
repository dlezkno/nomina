<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class dispersionNomina extends Controllers
	{
		public function parametros($Ciclo = 0, $IdBanco = 0)
		{
			set_time_limit(0);

            // SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia 	= isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : $reg1['valor'];
			$Periodicidad 	= isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : $reg2['valor'];
			$Periodo 		= isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : $reg3['valor'];
			$Ciclo 			= isset($_REQUEST['Ciclo']) ? $_REQUEST['Ciclo'] : $reg4['valor'];
			$IdBanco 		= isset($_REQUEST['IdBanco']) ? $_REQUEST['IdBanco'] : 0;

			if (isset($_REQUEST['Periodo'])) 
			{
				$Periodo = $_REQUEST['Periodo'];

				$query = <<<EOD
					PERIODOS.Referencia = $Referencia AND 
					PERIODOS.Periodicidad = $Periodicidad AND 
					PERIODOS.Periodo = $Periodo
				EOD;

				$regPeriodo = getRegistro('PERIODOS', 0, $query);
				$IdPeriodo = $regPeriodo['id'];
			}
			else
			{
				$regPeriodo = getRegistro('PERIODOS', $reg3['valor']);
				$Periodo = $regPeriodo['periodo'];
				$IdPeriodo = $regPeriodo['id'];
			}

			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			if ($IdBanco > 0) 
				$regBanco = getRegistro('BANCOS', $IdBanco);
			else
				$regBanco = false;

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Referencia' => $Referencia, 
					'Periodicidad' => $Periodicidad, 
					'Periodo' => $Periodo, 
					'Ciclo' => $Ciclo, 
					'IdBanco' => $IdBanco 
					),
				'mensajeError' => ''
			);

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Documento2, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.IdBanco, 
						EMPLEADOS.CuentaBancaria, 
						EMPLEADOS.TipoCuentaBancaria  
					FROM EMPLEADOS 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.FormaDepago = PARAMETROS2.Id 
					WHERE PARAMETROS1.Detalle = 'ACTIVO' AND 
						PARAMETROS2.Detalle = 'TRANSFERENCIA BANCARIA' AND 
						(EMPLEADOS.IdBanco = 0 OR 
						EMPLEADOS.CuentaBancaria = '' OR 
						EMPLEADOS.TipoCuentaBancaria = 0); 
			EOD;

			$empleados = $this->model->listar($query);

			if ($empleados) 
			{
				for ($i = 0; $i < count($empleados); $i++) 
				{ 
					$regEmpleado = $empleados[$i];

					if ($regEmpleado['IdBanco'] == 0) 
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido un BANCO.<br>';

					if ($regEmpleado['CuentaBancaria'] == '') 
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definida una CUENTA BANCARIA.<br>';

					if ($regEmpleado['TipoCuentaBancaria'] == 0) 
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido un TIPO DE CUENTA BANCARIA.<br>';
				}
			}

			// if (empty($data['mensajeError'])) 
			// {
				if (isset($_REQUEST['Referencia']))
				{
					switch ($regBanco['banco'])
					{
						case '51':  // DAVIVIENDA
							$query = <<<EOD
								WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' AND 
									ACUMULADOS.Ciclo = $Ciclo AND 
									ACUMULADOS.PagoDispersado = 0 AND 
									BANCOS.Nombre IN (SELECT PARAMETROS.Detalle FROM PARAMETROS WHERE PARAMETROS.Parametro = 'PagosDavivienda') 
							EOD;

							break;

						case '07':  // BANCOLOMBIA
							$query = <<<EOD
								WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' AND 
									ACUMULADOS.Ciclo = $Ciclo AND 
									ACUMULADOS.PagoDispersado = 0 AND 
									BANCOS.Nombre IN (SELECT PARAMETROS.Detalle FROM PARAMETROS WHERE PARAMETROS.Parametro = 'PagosBancolombia') 
							EOD;

							break;

						case '13':  // BBVA
							$query = <<<EOD
								WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' AND 
									ACUMULADOS.Ciclo = $Ciclo AND 
									ACUMULADOS.PagoDispersado = 0 AND 
									BANCOS.Nombre IN (SELECT PARAMETROS.Detalle FROM PARAMETROS WHERE PARAMETROS.Parametro = 'PagosBBVA') 
							EOD;

							break;

						case '01':  // BOGOTA
							$query = <<<EOD
								WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' AND 
									ACUMULADOS.Ciclo = $Ciclo AND 
									ACUMULADOS.PagoDispersado = 0 AND 
									BANCOS.Nombre IN (SELECT PARAMETROS.Detalle FROM PARAMETROS WHERE PARAMETROS.Parametro = 'PagosBogota') 
							EOD;

							break;

						default:
							$query = <<<EOD
								WHERE FALSE 
							EOD;

							break;
					}

					$datosNomina = $this->model->dispersionNomina($query);

					if (count($datosNomina) > 0) 
					{
						switch ($regBanco['banco'])
						{
							case '51':  // DAVIVIENDA
								$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_DispersionBancariaDavivienda_' . date('YmdGis') . '.csv';
								break;
							case '07':  // BANCOLOMBIA
								$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_DispersionBancariaBancolombia_' . date('YmdGis') . '.csv';
								break;
							case '13':  // BBVA
								$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_DispersionBancariaBBVA_' . date('YmdGis') . '.csv';
								break;
							case '01':  // BOGOTA
								$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_DispersionBancariaBogota_' . date('YmdGis') . '.csv';
								break;
						}

						$output = fopen($Archivo, 'w');

						fputcsv($output, array('DOCUMENTO', 'NOMBRE EMPLEADO', 'BANCO', 'TIPO CUENTA', 'CUENTA', 'VALOR'), ';');

						for ($i = 0; $i < count($datosNomina); $i++) 
						{ 
							$reg = $datosNomina[$i];

							foreach ($reg as $key => $value) 
							{
								if ($key == 'Valor')
									continue;

								$reg[$key] = utf8_decode($value);
							}

							if (empty($reg['Documento2']))
								$regDatos = array($reg['Documento'], $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], $reg['NombreBanco'], $reg['TipoCuentaBancaria'], $reg['CuentaBancaria'], number_format($reg['ValorPago'], 0, '.', ''));
							else
								$regDatos = array($reg['Documento2'], $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], $reg['NombreBanco'], $reg['TipoCuentaBancaria'], $reg['CuentaBancaria'], number_format($reg['ValorPago'], 0, '.', ''));

							fputcsv($output, $regDatos, ';');
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

						switch ($regBanco['banco'])
						{
							case '51':  // DAVIVIENDA
								$archivo = '';
								$ValorTotal = 0;
								$Traslados = 0;

								for ($i = 0; $i < count($datosNomina); $i++) 
								{ 
									$regNomina = $datosNomina[$i];

									if ($regNomina['ValorPago'] == 0)
										continue;

									$registro = 'TR';

									if (empty($regNomina['Documento2']))
										$registro .= str_pad($regNomina['Documento'], 16, '0', STR_PAD_LEFT);
									else
										$registro .= str_pad($regNomina['Documento2'], 16, '0', STR_PAD_LEFT);

									$registro .= '0000000000000000';
									$registro .= str_pad(trim($regNomina['CuentaBancaria']), 16, '0', STR_PAD_LEFT);

									switch($regNomina['TipoCuentaBancaria'])
									{
										case 'CUENTA DE AHORROS':
											if ($regNomina['Banco'] == '97')
												$registro .= 'DP';
											else
												$registro .= 'CA';
											break;
										case 'CUENTA CORRIENTE':
											$registro .= 'CC';
											break;
										case 'DAVIPLATA':
											$registro .= 'DP';
											break;
									}

									if ($regNomina['Banco'] == '97')
										$registro .= '000051';
									else
										$registro .= str_pad($regNomina['Banco'], 6, '0', STR_PAD_LEFT);

									$registro .= str_pad(number_format($regNomina['ValorPago'] * 100, 0, '', ''), 18, '0', STR_PAD_LEFT);
									$registro .= '000000';

									switch ($regNomina['TipoIdentificacion'])
									{
										case 'NIT':
											$registro .= '03';
											break;
										case 'CEDULA':
											$registro .= '01';
											break;
										case 'NIT EXTRANJERIA':
											$registro .= '11';
											break;
										case 'CEDULA EXTRANJERIA':
											$registro .= '02';
											break;
										case 'REGISTRO CIVIL':
											$registro .= '13';
											break;
										case 'TARJETA DE IDENTIDAD':
											$registro .= '04';
											break;
										case 'PASAPORTE':
											$registro .= '05';
											break;
										case 'RIF VENEZUELA':
											$registro .= '10';
											break;
									}

									$registro .= '199990000000000000000000000000000000000000000';
									$registro .= '000000000000000000';
									$registro .= '0000000000000000';
									$registro .= '0000000';
									$registro .= PHP_EOL;

									$archivo .= $registro;

									$ValorTotal += $regNomina['ValorPago'];
									$Traslados++;
								}

								$NitEmpresa = str_replace('.', '', getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'NitEmpresa' ")['detalle']);
								$NitEmpresa .= str_replace('.', '', getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'DigitoVerificacionEmpresa' ")['detalle']);
								$Cuenta = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CuentaDavivienda' ")['detalle'];
								$TipoCuenta = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'TipoCuentaDavivienda' ")['detalle'];

								$registro = 'RC';
								$registro .= str_pad($NitEmpresa, 16, '0', STR_PAD_LEFT);
								$registro .= 'NOMI';
								$registro .= 'NOMI';
								$registro .= str_pad($Cuenta, 16, '0', STR_PAD_LEFT);
								$registro .= $TipoCuenta;
								$registro .= '000051';
								$registro .= str_pad(number_format($ValorTotal * 100, 0, '', ''), 18, '0', STR_PAD_LEFT);
								$registro .= str_pad(number_format($Traslados, 0, '', ''), 6, '0', STR_PAD_LEFT);
								$registro .= date('Ymd');
								$registro .= date('his');
								$registro .= '0000';
								$registro .= '9999';
								$registro .= '0000000000000000';
								$registro .= '03';
								$registro .= '0000000000000000';
								$registro .= '0000000000000000000000000000000000000000';
								$registro .= PHP_EOL;

								$archivo = $registro . $archivo;

								$fichero = 'descargas/DispersionNominaDavivienda' . date('Ymd-His') . '.txt';

								$ok = file_put_contents($fichero, $archivo);

								// SE MARCAN LOS ACUMULADOS COMO YA DISPERSADOS UNA VEZ GENERADO EL ARCHIVO
								if ($ok)
									$this->model->actualizarPagoDispersionNomina($query);

								break;

							case '07':  // BANCOLOMBIA
								$archivo = '';
								$ValorTotal = 0;
								$Traslados = 0;

								for ($i = 0; $i < count($datosNomina); $i++) 
								{ 
									$regNomina = $datosNomina[$i];

									if ($regNomina['ValorPago'] == 0)
										continue;

									$NombreEmpleado = $regNomina['Apellido1'] . ' ' . $regNomina['Apellido2'] . ' ' . $regNomina['Nombre1'] . ' ' . $regNomina['Nombre2'];
									$NombreEmpleado = str_ireplace(array('Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', 'Ñ', 'ñ'), array('A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', 'N', 'n'), $NombreEmpleado);

									$registro = '6';

									if (empty($regNomina['Documento2']))
										$registro .= str_pad($regNomina['Documento'], 15, '0', STR_PAD_LEFT);
									else
										$registro .= str_pad($regNomina['Documento2'], 15, '0', STR_PAD_LEFT);

									$registro .= str_pad(left(trim($NombreEmpleado), 18), 18);
									if ($regNomina['Banco'] == '07')
										$registro .= '005600078';   // BANCOLOMBIA
									else
										$registro .= '000001507';   // NEQUI

									$registro .= str_pad(trim($regNomina['CuentaBancaria']), 17, '0', STR_PAD_LEFT);
									$registro .= 'S';
									
									switch($regNomina['TipoCuentaBancaria'])
									{
										case 'CUENTA DE AHORROS':
											$registro .= '37';
											break;
										case 'CUENTA CORRIENTE':
											$registro .= '27';
											break;
									}
									
									$registro .= str_pad(number_format($regNomina['ValorPago'], 0, '', ''), 10, '0', STR_PAD_LEFT);
									$registro .= 'NOMINA   ';
									$registro .= '             ';
									$registro .= PHP_EOL;

									$archivo .= $registro;

									$ValorTotal += $regNomina['ValorPago'];
									$Traslados++;
								}

								$NitEmpresa = str_replace('.', '', getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'NitEmpresa' ")['detalle']);
								$Cuenta = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CuentaBancolombia' ")['detalle'];
								$TipoCuenta = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'TipoCuentaBancolombia' ")['detalle'];

								$registro = '1';
								$registro .= str_pad($NitEmpresa, 10, '0', STR_PAD_LEFT);
								$registro .= 'COMWARE S.A.    ';
								$registro .= '225';
								$registro .= str_pad($Referencia . '-' . $Periodo, 10);
								$registro .= date('ymd');
								$registro .= 'A';
								$registro .= date('ymd');
								$registro .= str_pad($Traslados, 6, '0', STR_PAD_LEFT);
								$registro .= '000000000000';
								$registro .= str_pad(number_format($ValorTotal, 0, '', ''), 12, '0', STR_PAD_LEFT);
								$registro .= str_pad($Cuenta, 11, '0', STR_PAD_LEFT);
								switch($TipoCuenta)
								{
									case 'CUENTA DE AHORROS':
										$registro .= 'S';
										break;
									case 'CUENTA CORRIENTE':
										$registro .= 'D';
										break;
								}
								$registro .= PHP_EOL;

								$archivo = $registro . $archivo;

								$fichero = 'descargas/DispersionNominaBancolombia' . date('Ymd-His') . '.txt';

								$ok = file_put_contents($fichero, $archivo);

								// SE MARCAN LOS ACUMULADOS COMO YA DISPERSADOS UNA VEZ GENERADO EL ARCHIVO
								if ($ok)
									$this->model->actualizarPagoDispersionNomina($query);

								break;

							case '13':  // BBVA
								$archivo = '';
								$ValorTotal = 0;
								$Traslados = 0;

								for ($i = 0; $i < count($datosNomina); $i++) 
								{ 
									$regNomina = $datosNomina[$i];

									if ($regNomina['ValorPago'] == 0)
										continue;

									$NombreEmpleado = $regNomina['Apellido1'] . ' ' . $regNomina['Apellido2'] . ' ' . $regNomina['Nombre1'] . ' ' . $regNomina['Nombre2'];
									$NombreEmpleado = str_ireplace(array('Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', 'Ñ', 'ñ'), array('A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', 'N', 'n'), $NombreEmpleado);
									$CuentaBancaria = $regNomina['CuentaBancaria'];

									if (strlen($CuentaBancaria) < 16)
									{
										if (strlen($CuentaBancaria) >= 14)
											$CuentaBancaria = str_pad($CuentaBancaria, 16, '0', STR_PAD_LEFT);
										else
										{
											$Cuenta = substr($CuentaBancaria, -6);
											$CuentaBancaria = str_replace($Cuenta, '', $CuentaBancaria);
											$CuentaBancaria = str_pad($CuentaBancaria, 4, '0', STR_PAD_LEFT) . '00';

											if ($regNomina['TipoCuentaBancaria'] == 'CUENTA DE AHORROS')
												$CuentaBancaria .= '0200';
											else
												$CuentaBancaria .= '0100';

											$CuentaBancaria .= $Cuenta;
										}
									}

									// $Direccion = str_ireplace(array('Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', 'Ñ', 'ñ'), array('A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', 'N', 'n'), $regNomina['Direccion']);
									$Direccion = 'COMWARE S.A.';

									$Email = trim($regNomina['Email']);

									$registro = '';

									switch ($regNomina['TipoIdentificacion'])
									{
										case 'NIT':
											$registro .= '03';
											break;
										case 'CEDULA':
											$registro .= '01';
											break;
										case 'NIT EXTRANJERIA':
											$registro .= '06';
											break;
										case 'CEDULA DE EXTRANJERIA':
											$registro .= '02';
											break;
										case 'TARJETA DE IDENTIDAD':
											$registro .= '04';
											break;
										case 'PASAPORTE':
											$registro .= '05';
											break;
										case 'RIF VENEZUELA':
											$registro .= '02';
											break;
										default:
											$registro .= '02';
											break;
									}

									if (empty($regNomina['Documento2']))
										$registro .= str_pad($regNomina['Documento'] . '0', 16, '0', STR_PAD_LEFT);
									else
										$registro .= str_pad($regNomina['Documento2'] . '0', 16, '0', STR_PAD_LEFT);

									$registro .= '1';
									$registro .= str_pad($regNomina['Banco'], 4, '0', STR_PAD_LEFT);

									if ($regNomina['Banco'] == '13')
									{
										$registro .= $CuentaBancaria;
										$registro .= '00';
										$registro .= '0000000000000000';
									}
									else
									{	
										$registro .= '0000000000000000';

										switch($regNomina['TipoCuentaBancaria'])
										{
											case 'CUENTA DE AHORROS':
												$registro .= '02';
												break;
											case 'CUENTA CORRIENTE':
												$registro .= '01';
												break;
											default:
												$registro .= '00';
										}
										
										$registro .= str_pad(trim($regNomina['CuentaBancaria']), 16, '0', STR_PAD_LEFT);
									}
									
									$registro .= str_pad(number_format($regNomina['ValorPago'], 0, '', ''), 14, '0', STR_PAD_LEFT);
									$registro .= '00';
									$registro .= '00000000';
									$registro .= '0000';
									$registro .= str_pad(left(trim($NombreEmpleado), 36), 36);
									$registro .= str_pad(left(trim($Direccion), 36), 36);
									$registro .= str_pad(left(trim($Direccion), 36), 36);
									$registro .= str_pad(left(trim($Email), 48), 48);
									$registro .= str_pad(left('NOMINA ' . NombreMes($Periodo) . '-' . $Referencia, 40), 40);
									$registro .= PHP_EOL;

									$archivo .= $registro;

									$ValorTotal += $regNomina['ValorPago'];
									$Traslados++;
								}

								$fichero = 'descargas/DispersionNominaBBVA' . date('Ymd-His') . '.txt';

								$ok = file_put_contents($fichero, $archivo);

								// SE MARCAN LOS ACUMULADOS COMO YA DISPERSADOS UNA VEZ GENERADO EL ARCHIVO
								if ($ok)
									$this->model->actualizarPagoDispersionNomina($query);

								break;
						}

						header('Location: ' . SERVERURL . '/dispersionNomina/descarga');
						exit();
					}
					else
					{
						$data['mensajeError'] .= 'No hay datos disponible.<br>';
					}
				}
			// }

            $_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/dispersionNomina/parametros';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/dispersionNomina/descarga';

			if ($data) 
				$this->views->getView($this, 'parametros', $data);
		}

		public function descarga()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/dispersionNomina/parametros';
		
			$_SESSION['Paginar'] = FALSE;

			if (! isset($_SESSION['DISPERSIONNOMINA']['Filtro']))
			{
				$_SESSION['DISPERSIONNOMINA']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['DISPERSIONNOMINA']['Filtro'];

			$this->views->getView($this, 'descarga');
		}
	}
?>