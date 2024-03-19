<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class informeContabilizacionAcumulados extends Controllers
	{
		public function parametros()
		{
			set_time_limit(0);

            // SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			// $reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia 	= isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : $reg1['valor'];
			$Periodicidad 	= isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : $reg2['valor'];
			$IdComprobante 	= isset($_REQUEST['IdComprobante']) ? $_REQUEST['IdComprobante'] : 0;
			$Documento	 	= isset($_REQUEST['Documento']) ? $_REQUEST['Documento'] : '';
			$Cuenta	 		= isset($_REQUEST['Cuenta']) ? $_REQUEST['Cuenta'] : '';

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
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];
			}
			else
			{
				$regPeriodo = getRegistro('PERIODOS', $reg3['valor']);
				$Periodo = $regPeriodo['periodo'];
				$IdPeriodo = $regPeriodo['id'];
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];
			}

			if ($IdComprobante > 0) 
				$regTipoDoc = getRegistro('TIPODOC', $IdComprobante);
			else
				$regTipoDoc = false;

			// $CuentaNomina = getRegistro('PARAMETROS', 0, "Parametro = 'CuentaNomina' ")['valor'];
			// $CuentaNomina = str_pad($CuentaNomina, 12, '0', STR_PAD_RIGHT);

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Referencia' 	=> $Referencia, 
					'Periodicidad' 	=> $Periodicidad, 
					'Periodo' 		=> $Periodo, 
					'IdComprobante' => $IdComprobante, 
					'Documento'		=> $Documento, 
					'Cuenta'		=> $Cuenta
					),
				'mensajeError' => ''
			);

			if (empty($data['mensajeError'])) 
			{
				if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar')
				{
					$FechaInicial 	= $regPeriodo['fechainicial'];
					$FechaFinal 	= $regPeriodo['fechafinal'];

					$FechaInicialP 	= str_replace('-', '', $FechaInicial);
					$FechaFinalP 	= str_replace('-', '', $FechaFinal);

					$Mes = strtoupper(NombreMes(date('m', strtotime($FechaFinal))));
					$Ano = date('Y', strtotime($FechaFinal));

					$ExoneracionEmpresa = getRegistro('PARAMETROS', 0, "Parametro = 'ExoneracionEmpresa' ")['valor'];

					$query = <<<EOD
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							COMPROBANTES.TipoEmpleado = CENTROS.TipoEmpleado 
					EOD;

					if (! empty($Documento))
						$query .= <<<EOD
							AND EMPLEADOS.Documento = '$Documento' 
						EOD;

					if (! empty($Cuenta))
						$query .= <<<EOD
							AND (COMPROBANTES.CuentaDb LIKE '$Cuenta%' OR COMPROBANTES.CuentaCr LIKE '$Cuenta%') 
						EOD;

					$datosAcumulados = $this->model->cuentasSAP($query);

					if (count($datosAcumulados) > 0) 
					{
						$Secuencia = 0;
						$TotalDb = 0;
						$TotalCr = 0;

						$ArchivoCSV = './descargas/' . $_SESSION['Login']['Usuario'] . '_InformeContabilizacionAcumulados_' . date('YmdGis') . '.csv';

						$output = fopen($ArchivoCSV, 'w');

						fputcsv($output, array('TIPO DOC.', 'CONCEPTO', 'DETALLE', 'CUENTA DB.', 'CENTRO DB.', 'PROYECTO DB.', 'CUENTA CR.', 'CENTRO CR.', 'PROYECTO CR.', 'TIPO EMP.', 'BASE', 'PORCENTAJE', 'VALOR', 'TERCERO', 'NOMBRE TERCERO', 'COD.SAP TERCERO', 'DOCUMENTO', 'NOMBRE EMPLEADO', 'COD.SAP EMPLEADO'), ';');

						for ($i = 0; $i < count($datosAcumulados); $i++) 
						{ 
							$regAcumulados 		= $datosAcumulados[$i];

							if ($regAcumulados['Exonerable'] == 1 AND $regAcumulados['RegimenCesantias'] <> 'SALARIO INTEGRAL')
								continue;

							$TipoDocumento 		= $regAcumulados['TipoDocumento'];
							$Concepto 			= $regAcumulados['Concepto'];
							$Detalle 			= utf8_decode($regAcumulados['Detalle']);
							$CuentaDb 			= $regAcumulados['CuentaDb'];
							$CentroDb 			= $regAcumulados['CentroDb'];
							$ProyectoDb 		= $regAcumulados['ProyectoDb'];
							$CuentaCr 			= $regAcumulados['CuentaCr'];
							$CentroCr 			= $regAcumulados['CentroCr'];
							$ProyectoCr 		= $regAcumulados['ProyectoCr'];
							$TipoEmpleado 		= utf8_decode($regAcumulados['TipoEmpleado']);
							$Base 				= $regAcumulados['Base'];
							$Porcentaje 		= $regAcumulados['Porcentaje'];
							$Valor 				= $regAcumulados['Valor'];
							$Tercero			= $regAcumulados['Tercero'];
							$NombreTercero		= utf8_decode($regAcumulados['NombreTercero']);
							$CodigoSAPTercero	= $regAcumulados['CodigoSAPTercero'];
							$Documento			= $regAcumulados['Documento'];
							$NombreEmpleado		= utf8_decode($regAcumulados['NombreEmpleado']);
							$CodigoSAPEmpleado	= $regAcumulados['CodigoSAPEmpleado'];

							$datos = array('TipoDocumento' 	=> $TipoDocumento, 
										'Concepto' 			=> $Concepto, 
										'Detalle' 			=> $Detalle, 
										'CuentaDb' 			=> $CuentaDb, 
										'CentroDb' 			=> $CentroDb, 
										'ProyectoDb' 		=> $ProyectoDb, 
										'CuentaCr' 			=> $CuentaCr, 
										'CentroCr' 			=> $CentroCr, 
										'ProyectoCr' 		=> $ProyectoCr, 
										'TipoEmpleado' 		=> $TipoEmpleado, 
										'Base' 				=> $Base, 
										'Porcentaje' 		=> $Porcentaje, 
										'Valor' 			=> $Valor, 
										'Tercero'			=> $Tercero, 
										'NombreTercero'		=> $NombreTercero, 
										'CodigiSAPTercero'	=> $CodigoSAPTercero, 
										'Documento' 		=> $Documento, 
										'NombreEmpleado' 	=> $NombreEmpleado, 
										'CodigoSAPEmpleado' => $CodigoSAPEmpleado 
									);

							fputcsv($output, $datos, ';');
						}

						fclose($output);

						header('Content-Description: File Transfer');
						header('Content-Type: text/csv');
						header('Content-Disposition: attachment; filename=' . basename($ArchivoCSV));
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($ArchivoCSV));
						ob_clean();
						flush();
						readfile($ArchivoCSV);

						if (empty($data['mensajeError'])) 
						{
							header('Location: ' . SERVERURL . '/informeContabilizacionSAP/lista/1');
							exit();
						}
					}
				}

				if (isset($_REQUEST['Referencia']))
				{
					$FechaInicial 	= $regPeriodo['fechainicial'];
					$FechaFinal 	= $regPeriodo['fechafinal'];

					$FechaInicialP 	= str_replace('-', '', $FechaInicial);
					$FechaFinalP 	= str_replace('-', '', $FechaFinal);

					$Mes = strtoupper(NombreMes(date('m', strtotime($FechaFinal))));
					$Ano = date('Y', strtotime($FechaFinal));

					$ExoneracionEmpresa = getRegistro('PARAMETROS', 0, "Parametro = 'ExoneracionEmpresa' ")['valor'];

					$query = <<<EOD
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							COMPROBANTES.TipoEmpleado = CENTROS.TipoEmpleado 
					EOD;

					if (! empty($Documento))
						$query .= <<<EOD
							AND EMPLEADOS.Documento = '$Documento' 
						EOD;

					if (! empty($Cuenta))
						$query .= <<<EOD
							AND (COMPROBANTES.CuentaDb LIKE '$Cuenta%' OR COMPROBANTES.CuentaCr LIKE '$Cuenta%') 
						EOD;

					$datosAcumulados = $this->model->cuentasSAP($query);

					if ($datosAcumulados) 
					{
						for ($i = 0; $i < count($datosAcumulados); $i++) 
						{ 
							$regAcumulados 		= $datosAcumulados[$i];

							if ($regAcumulados['Exonerable'] == 1 AND $regAcumulados['RegimenCesantias'] <> 'SALARIO INTEGRAL')
								continue;

							$TipoDocumento 		= $regAcumulados['TipoDocumento'];
							$Concepto 			= $regAcumulados['Concepto'];
							$Detalle 			= $regAcumulados['Detalle'];
							$CuentaDb 			= $regAcumulados['CuentaDb'];
							$CentroDb 			= $regAcumulados['CentroDb'];
							$ProyectoDb 		= $regAcumulados['ProyectoDb'];
							$CuentaCr 			= $regAcumulados['CuentaCr'];
							$CentroCr 			= $regAcumulados['CentroCr'];
							$ProyectoCr 		= $regAcumulados['ProyectoCr'];
							$TipoEmpleado 		= $regAcumulados['TipoEmpleado'];
							$Base 				= $regAcumulados['Base'];
							$Porcentaje 		= $regAcumulados['Porcentaje'];
							$Valor 				= $regAcumulados['Valor'];
							$Tercero			= $regAcumulados['Tercero'];
							$NombreTercero		= $regAcumulados['NombreTercero'];
							$CodigoSAPTercero	= $regAcumulados['NombreTercero'];
							$Documento			= $regAcumulados['Documento'];
							$NombreEmpleado		= $regAcumulados['NombreEmpleado'];
							$CodigoSAPEmpleado	= $regAcumulados['CodigoSAPEmpleado'];

							$datos = array('TipoDocumento' 	=> $TipoDocumento, 
										'Concepto' 			=> $Concepto, 
										'Detalle' 			=> $Detalle, 
										'CuentaDb' 			=> $CuentaDb, 
										'CentroDb' 			=> $CentroDb, 
										'ProyectoDb' 		=> $ProyectoDb, 
										'CuentaCr' 			=> $CuentaCr, 
										'CentroCr' 			=> $CentroCr, 
										'ProyectoCr' 		=> $ProyectoCr, 
										'TipoEmpleado' 		=> $TipoEmpleado, 
										'BAse' 				=> $Base, 
										'Porcentaje' 		=> $Porcentaje, 
										'Valor' 			=> $Valor, 
										'Tercero'			=> $Tercero, 
										'NombreTercero'		=> $NombreTercero, 
										'CodigoSAPTercero'	=> $CodigoSAPTercero, 
										'Documento' 		=> $Documento, 
										'NombreEmpleado' 	=> $NombreEmpleado,  
										'CodigoSAPEmpleado'	=> $CodigoSAPEmpleado 
									);

							$this->model->guardarRegistro($datos);
						}

						if (empty($data['mensajeError'])) 
						{
							header('Location: ' . SERVERURL . '/informeContabilizacionAcumulados/lista/1');
							exit();
						}
					}
				}
			}

            $_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			// $_SESSION['ActualizarRegistro'] = SERVERURL . '/informeContabilizacionSAP/parametros';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/informeContabilizacionAcumulados/parametros';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			// $_SESSION['Lista'] = SERVERURL . '/informeContabilizacionSAP/lista/1';
			$_SESSION['Lista'] = '';

			if ($data) 
				$this->views->getView($this, 'parametros', $data);
		}

		public function lista($pagina)
		{
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
			{
				$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
				$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
				$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
				$Ciclo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
				$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];
	
				$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
				$Periodo = $regPeriodo['periodo'];
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];
	
				$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
				$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);
	
				$ArchivoSAP1 = 'descargas/' . $_SESSION['Login']['Usuario'] . '_ComprobanteSAP_H_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo . '_' . date('YmdGis') . '.txt';
				$ArchivoSAP2 = 'descargas/' . $_SESSION['Login']['Usuario'] . '_ComprobanteSAP_D_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo . '_' . date('YmdGis') . '.txt';

				$query = <<<EOD
					SELECT * 
						FROM DETALLESSAP 
						ORDER BY DETALLESSAP.LineNum
				EOD;

				$datos = $this->model->listar($query);

				if ($datos)
				{
					for ($i = 0; $i < count($datos); $i++)
					{
						$regDatos = $datos[$i];

						$Documento = $regDatos['ConsecID'];
						$FechaFinalP = $regDatos['DueDate'];

						if ($i == 0)
						{
							// ARCHIVO ENCABEZADO
							$datosEnc[] = array('ConsecID', 'RecordKey', 'DueDate', 'Memo', 'Reference', 'TaxDate', 'TransactionCode', 'Series', 'ReferenceDate', 'CodComp', 'Procesado');
							$datosEnc[] = array('ConsecID', 'RecordKey', 'DueDate', 'Memo', 'Reference', 'TaxDate', 'TransactionCode', 'Series', 'ReferenceDate', 'CodComp', 'Procesado');
							$datosEnc[] = array($Documento, $Documento, $FechaFinalP, 'LIQUIDACION TODOS ' . $FechaFinalP, '', $FechaFinalP, 'NOM', 0, $FechaFinalP, 1, 'NULL');

							// ARCHIVO DETALLE
							$datosDet[] = array('ConsecID', 'RecordKey', 'LineNum', 'AccountCode', 'ShortName', 'CostingCode', 'Projectcode', 'Debit', 'Credit', 'DueDate', 'LineMemo', 'Reference2', 'ReferenceDate1', 'ReferenceDate2', 'TaxDate', 'U_infoco01', 'U_codRet', 'U_BaseRet', 'U_TarifaRet', 'Procesado', 'CodCompania', 'OcrCode2');
							$datosDet[] = array('ConsecID', 'RecordKey', 'LineNum', 'AccountCode', 'ShortName', 'CostingCode', 'Projectcode', 'Debit', 'Credit', 'DueDate', 'LineMemo', 'Reference2', 'ReferenceDate1', 'ReferenceDate2', 'TaxDate', 'U_infoco01', 'U_codRet', 'U_BaseRet', 'U_TarifaRet', 'Procesado', 'CodCompania', 'OcrCode2');
						}

						$datosDet[] = array($regDatos['ConsecID'],
											$regDatos['RecordKey'], 
											$regDatos['LineNum'], 
											$regDatos['AccountCode'], 
											$regDatos['ShortName'], 
											$regDatos['CostingCode'], 
											$regDatos['Projectcode'], 
											$regDatos['Debit'], 
											$regDatos['Credit'], 
											$regDatos['DueDate'], 
											str_pad($regDatos['LineMemo'], 49), 
											$regDatos['Reference2'], 
											$regDatos['ReferenceDate1'], 
											$regDatos['ReferenceDate2'], 
											$regDatos['TaxDate'], 
											$regDatos['U_infoco01'], 
											$regDatos['U_codRet'], 
											$regDatos['U_BaseRet'], 
											$regDatos['U_TarifaRet'], 
											$regDatos['Procesado'], 
											$regDatos['CodCompania'], 
											$regDatos['OcrCode2']
										);
							
					}

					$fp1 = fopen($ArchivoSAP1, 'w');

					for ($i = 0; $i < count($datosEnc); $i++)
					{
						for ($j = 0; $j < count($datosEnc[$i]); $j++)
						{
							fwrite($fp1, $datosEnc[$i][$j]);

							if ($j < count($datosEnc[$i]) - 1)
								fwrite($fp1, "\t");
						}

						fwrite($fp1, PHP_EOL);
					}
					
					fclose($fp1);

					$fp2 = fopen($ArchivoSAP2, 'w');

					for ($i = 0; $i < count($datosDet); $i++)
					{
						for ($j = 0; $j < count($datosDet[$i]); $j++)
						{
							fwrite($fp2, $datosDet[$i][$j]);

							if ($j < count($datosDet[$i]) - 1)
								fwrite($fp2, "\t");
						}

						fwrite($fp2, PHP_EOL);
					}
					
					fclose($fp2);
				}

				header('Location: ' . SERVERURL . '/contabilizacionSAP/parametros');
				exit();
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = '';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = SERVERURL . '/contabilizacionSAP/exportar';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = '';
			
				$_SESSION['Paginar'] = TRUE;

				$_SESSION['CONTABILIZACIONSAP']['Pagina'] = $pagina;
				$_SESSION['PaginaActual'] = $_SESSION['CONTABILIZACIONSAP']['Pagina'];
				
				if	( isset($_REQUEST['Filtro']) )
				{
					$_SESSION['CONTABILIZACIONSAP']['Filtro'] = $_REQUEST['Filtro'];
					$_SESSION['CONTABILIZACIONSAP']['Pagina'] = 1;
					$pagina = 1;
				}

				if (! isset($_SESSION['CONTABILIZACIONSAP']['Filtro']))
				{
					$_SESSION['CONTABILIZACIONSAP']['Filtro'] = '';
				}

				$lcFiltro = $_SESSION['CONTABILIZACIONSAP']['Filtro'];

				if (isset($_REQUEST['Orden']))
				{
					$_SESSION['CONTABILIZACIONSAP']['Orden'] = $_REQUEST['Orden'];
					$_SESSION['CONTABILIZACIONSAP']['Pagina'] = 1;
					$pagina = 1;
				}
				else
					if (! isset($_SESSION['CONTABILIZACIONSAP']['Orden'])) 
						$_SESSION['CONTABILIZACIONSAP']['Orden'] = 'DETALLESSAP.LineNum';

				$query = '';

				if	( ! empty($lcFiltro) )
				{
					$aFiltro = explode(' ', $lcFiltro);

					for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
					{
						if (empty($query))
							$query .= 'WHERE ';
						else
							$query .= 'AND ';

						$query .= "(UPPER(REPLACE(DETALLESSAP.AccountCode, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(DETALLESSAP.CostingCode, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(DETALLESSAP.ProjectCode, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(DETALLESSAP.LineMemo, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(DETALLESSAP.U_InfoCo01, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%') ";
					}
				}
				
				$data['registros'] = $this->model->contarRegistros($query);
				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
				$query .= 'ORDER BY ' . $_SESSION['CONTABILIZACIONSAP']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listarCuentas($query);
				$this->views->getView($this, 'informe', $data);
			}
		}	
	}
?>