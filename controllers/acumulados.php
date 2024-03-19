<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Acumulados extends Controllers
	{
		public function parametros()
		{
			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'IdCentro' 				=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'IdProyecto' 			=> isset($_REQUEST['IdProyecto']) ? $_REQUEST['IdProyecto'] : 0,
					'Empleado' 				=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'Concepto' 				=> isset($_REQUEST['Concepto']) ? $_REQUEST['Concepto'] : '',
					'FechaInicialPeriodo' 	=> isset($_REQUEST['FechaInicialPeriodo']) ? $_REQUEST['FechaInicialPeriodo'] : '',
					'FechaFinalPeriodo' 	=> isset($_REQUEST['FechaFinalPeriodo']) ? $_REQUEST['FechaFinalPeriodo'] : '',
					'InformeResumido' 		=> isset($_REQUEST['InformeResumido']) ? TRUE : FALSE
					),
				'mensajeError' => ''
			);

			if	(isset($_REQUEST['IdCentro']))
			{
				if	($_REQUEST['IdCentro'] > 0)
					$IdCentro = $_REQUEST['IdCentro'];
				else
					$IdCentro = '';

				if	($_REQUEST['IdProyecto'] > 0)
					$IdProyecto = $_REQUEST['IdProyecto'];
				else
					$IdProyecto = '';

				if	(! empty($_REQUEST['Empleado']))
					$Empleado = $_REQUEST['Empleado'];
				else
					$Empleado = '';

				if	(! empty($_REQUEST['Concepto']))
					$Concepto = $_REQUEST['Concepto'];
				else
					$Concepto = '';

				$FechaInicialPeriodo = $_REQUEST['FechaInicialPeriodo'];
				$FechaFinalPeriodo = $_REQUEST['FechaFinalPeriodo'];

				if	(empty($data['mensajeError']))
				{
					if	(! empty($FechaFinalperiodo) AND $FechaInicialPeriodo > $FechaFinalPeriodo)
					{
						$FechaInicialPeriodo = $FechaFinalPeriodo;
						$FechaFinalPeriodo = $_REQUEST['FechaInicialPeriodo'];
					}

					$query = '';

					if	(! empty($IdCentro))
						$query = 'WHERE ACUMULADOS.IdCentro = ' . $IdCentro . ' ';

					if	(! empty($IdProyecto))
					{
						if	(! empty($query))
							$query .= "AND EMPLEADOS.IdProyecto = $IdProyecto ";
						else
							$query = "WHERE EMPLEADOS.IdProyecto = $IdProyecto ";
					}

					if	(! empty($Empleado))
					{
						if	(! empty($query))
							$query .= "AND EMPLEADOS.Documento = '$Empleado' ";
						else
							$query = "WHERE EMPLEADOS.Documento = '$Empleado' ";
					}

					if	(! empty($Concepto))
					{
						$Mayor = left($Concepto, 2);
						$Auxiliar = right($Concepto, 3);

						if	(! empty($query))
							$query .= "AND MAYORES.Mayor = '$Mayor' AND AUXILIARES.Auxiliar = '$Auxiliar' ";
						else
							$query = "WHERE MAYORES.Mayor = '$Mayor' AND AUXILIARES.Auxiliar = '$Auxiliar' ";
					}

					if	(! empty($FechaInicialPeriodo))
					{
						if	(! empty($query))
							$query .= "AND ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' ";
						else
							$query .= "WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' ";
					}

					if	(! empty($FechaFinalPeriodo))
					{
						if	(! empty($query))
							$query .= "AND ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' ";
						else
							$query .= "WHERE ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo' ";
					}

					$_SESSION['ACUMULADOS']['Filtro'] = $query;
					$_SESSION['ACUMULADOS']['InformeResumido'] = $data['reg']['InformeResumido'];

					if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
					{
						$Resumen = $_SESSION['ACUMULADOS']['InformeResumido'];

						if	($Resumen)
							$query .= ' GROUP BY EMPLEADOS.Id, EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Centro, CENTROS.Nombre, PROYECTOS.Centro, PROYECTOS.Nombre, MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, PARAMETROS1.Detalle, PARAMETROS2.Detalle, TERCEROS.NOmbre ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, MIN(ACUMULADOS.FechaInicialPeriodo) ';
						else
							$query .= ' ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, ACUMULADOS.FechaInicialPeriodo ';

						$data['rows'] = $this->model->listarAcumulados($query, $Resumen);

						$Archivo = 'descargas/' . $_SESSION['Login']['Usuario'] . '_Acumulados_' . date('YmdGis') . '.csv';

						$output = fopen($Archivo, 'w');

						fputcsv($output, array('DOCUMENTO', 'NOMBRE EMPLEADO', 'CENTRO', 'NOMBRE CENTRO', 'PROYECTO', 'NOMBRE PROYECTO', 'CONCEPTO', 'DESCRIPCION', 'HORAS', 'VALOR DB.', 'VALOR CR.', 'FECHA INI.', 'FECHA FIN.', 'TERCERO'), ';');

						for ($i = 0; $i < count($data['rows']); $i++) 
						{ 
							$reg = $data['rows'][$i];

							foreach ($reg as $key => $value) 
							{
								if ($key == 'FechaInicialPeriodo' OR 
									$key == 'FechaFinalPeriodo' OR 
									$key == 'Imputacion' OR 
									$key == 'Horas' OR
									$key == 'Valor')
									continue;

								$reg[$key] = utf8_decode($value);
							}

							$regDatos = array($reg['Documento'], $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], $reg['Centro'], $reg['NombreCentro'], $reg['Proyecto'], $reg['NombreProyecto'], $reg['Mayor'] . $reg['Auxiliar'], $reg['NombreConcepto'], number_format($reg['Horas'], 2, '.', ''), ($reg['Imputacion'] == 'PAGO' ? number_format($reg['Valor'], 2, '.', '') : 0), ($reg['Imputacion'] == 'DEDUCCIÓN' ? number_format($reg['Valor'], 2, '.', '') : 0), $reg['FechaInicialPeriodo'], $reg['FechaFinalPeriodo'], $reg['NombreTercero']);

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
						exit();
					}
					else
					{
						header('Location: ' . SERVERURL . '/acumulados/list/1');
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/acumulados/parametros';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
					$_SESSION['Importar'] = SERVERURL . '/acumulados/importar';
				else
					$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = SERVERURL . '/acumulados/exportar';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = '';

				$_SESSION['ACUMULADOS']['Filtro'] = '';

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function list($pagina)
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
			$_SESSION['Informe'] = SERVERURL . '/acumulados/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/acumulados/parametros';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['ACUMULADOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['ACUMULADOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['ACUMULADOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['ACUMULADOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['ACUMULADOS']['Filtro']))
			{
				$_SESSION['ACUMULADOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['ACUMULADOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['ACUMULADOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['ACUMULADOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['ACUMULADOS']['Orden'])) 
					$_SESSION['ACUMULADOS']['Orden'] = 'EMPLEADOS.Id,EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,MAYORES.Mayor,AUXILIARES.Auxiliar';
			
			$query = $lcFiltro;
			$lcFiltro = '';
			$Resumen = $_SESSION['ACUMULADOS']['InformeResumido'];

			$data['registros'] = $this->model->contarRegistros($query, $Resumen);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;

			if	($Resumen)
				$query .= ' GROUP BY EMPLEADOS.Id, EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Centro, CENTROS.Nombre, PROYECTOS.Centro, PROYECTOS.Nombre, MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, PARAMETROS1.Detalle, PARAMETROS2.Detalle, TERCEROS.Nombre ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, MIN(ACUMULADOS.FechaInicialPeriodo) ';
			else
				$query .= ' ORDER BY EMPLEADOS.Id, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, ACUMULADOS.FechaInicialPeriodo ';

			$query .= 'OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarAcumulados($query, $Resumen);
			$this->views->getView($this, 'acumulados', $data);
		}	

		public function informe()
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
			$_SESSION['Informe'] = 00;
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/acumulados/list/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['ACUMULADOS']['Filtro'];
			$query = $lcFiltro;
			$lcFiltro = '';
			$Resumen = $_SESSION['ACUMULADOS']['InformeResumido'];
			
			if	($Resumen)
				$query .= ' GROUP BY EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Centro, CENTROS.Nombre, MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, PARAMETROS1.Detalle, PARAMETROS2.Detalle ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, MIN(ACUMULADOS.FechaInicialPeriodo) ';
			else
				$query .= ' ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, ACUMULADOS.FechaInicialPeriodo ';

			$data['rows'] = $this->model->listarAcumulados($query, $Resumen);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_REQUEST['FechaInicialPeriodo'])) 
			{
				$FechaInicialPeriodo = $_REQUEST['FechaInicialPeriodo'];
				$FechaFinalPeriodo = $_REQUEST['FechaFinalPeriodo'];
			}

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['Archivo']['name']) )
				{
					$data['mensajeError'] .= "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 0);
		
					$archivo = $_FILES['Archivo']['name'];
					var_dump($_FILES);

					if ( copy($_FILES['Archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							$oExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $oExcel->getSheet(0);
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('E' . $i)->getCalculatedValue()) )
								{
                                    $Empleado 		= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
                                    $NombreEmpleado = trim($oHoja->getCell('F' . $i)->getCalculatedValue());

                                    $regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '$Empleado'");

                                    if ($regEmpleado)
                                    {
                                        $IdEmpleado = $regEmpleado['id'];
                                        $IdCentro = $regEmpleado['idcentro'];

										if ($IdCentro > 0) 
										{
											$regCentro = getRegistro('CENTROS', $IdCentro);
											$TipoEmpleado = $regCentro['tipoempleado'];
										}
										else
											$TipoEmpleado = 0;
                                    }
                                    else
                                    {
										$data['mensajeError'] .= 'Empleado no existe <strong>' . $Empleado . '</strong> ' . $NombreEmpleado . '<br>';
										continue;
                                    }

									$ConceptoDesigner 		= trim($oHoja->getCell('K' . $i)->getCalculatedValue());
									$NombreConceptoDesigner = trim($oHoja->getCell('L' . $i)->getCalculatedValue());

									$query = <<<EOD
										SELECT EQUIV_CONCEPTOS.ConceptoComware  
											FROM EQUIV_CONCEPTOS 
											WHERE EQUIV_CONCEPTOS.ConceptoDesigner = '$ConceptoDesigner';
									EOD;

									$regEquiv = $this->model->leer($query);

									if ($regEquiv) 
									{
										$Mayor 		= substr($regEquiv['ConceptoComware'], 0, 2);
										$Auxiliar 	= substr($regEquiv['ConceptoComware'], 2, 3);

										$regMayor = getRegistro('MAYORES', 0, "MAYORES.Mayor = '$Mayor'");

										if (! $regMayor)
										{
											$data['mensajeError'] .= 'Concepto mayor no existe <strong>' . $Mayor . $Auxiliar . '</strong><br>';
											continue;
										}

										$IdMayor = $regMayor['id'];
										$TipoLiquidacion = getRegistro('PARAMETROS', $regMayor['tipoliquidacion'])['detalle'];

										$regAuxiliar = getRegistro('AUXILIARES', 0, "AUXILIARES.IdMayor = $IdMayor AND AUXILIARES.Auxiliar = '$Auxiliar'");

										if (! $regAuxiliar)
										{
											$data['mensajeError'] .= 'Concepto auxiliar no existe <strong>' . $Mayor . $Auxiliar . '</strong><br>';
											continue;
										}

										$IdConcepto = $regAuxiliar['id'];

										$Horas = $oHoja->getCell('J' . $i)->getCalculatedValue();

										if (empty($Horas))
											$Horas = 0;

										if ($TipoLiquidacion == 'DÍAS')
											$Horas = $Horas * 8;

										if (! empty($oHoja->getCell('N' . $i)->getCalculatedValue()))
											$Valor = $oHoja->getCell('N' . $i)->getCalculatedValue();
										else
											$Valor = $oHoja->getCell('O' . $i)->getCalculatedValue();

										$Tercero = trim($oHoja->getCell('M' . $i)->getCalculatedValue());

										if (! empty($Tercero))
										{
											// SE BUSCA EL TERCERO
											$IdTercero = getId('TERCEROS', "TERCEROS.Documento = '$Tercero'");

											if ($IdTercero == 0)
											{
												$data['mensajeError'] .= 'Tercero no existe <strong>' . $Tercero . '</strong><br>';
												continue;
											}
										}
										else
											$IdTercero = 0;

										// SE BUSCA EL ACUMULADO
										$IdAcumulado = getId('ACUMULADOS', "ACUMULADOS.IdPeriodo = 20 AND ACUMULADOS.Ciclo = 2 AND ACUMULADOS.IdEmpleado = $IdEmpleado AND ACUMULADOS.IdConcepto = $IdConcepto AND ACUMULADOS.FechaInicialPeriodo = '$FechaInicialPeriodo' AND ACUMULADOS.IdTercero = $IdTercero");

										if ($IdAcumulado == 0)
										{
											$query = <<<EOD
												INSERT INTO ACUMULADOS 
													(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, IdCentro, TipoEmpleado, FechaInicialPeriodo, FechaFinalPeriodo, IdTercero)
													VALUES (
														20, 2, 
														$IdEmpleado,
														$IdConcepto, 
														$Horas, 
														$Valor, 
														0, 
														$IdCentro,
														$TipoEmpleado, 
														'$FechaInicialPeriodo', 
														'$FechaFinalPeriodo', 
														$IdTercero);
											EOD;
										}
										else
										{
											$query = <<<EOD
												UPDATE ACUMULADOS
													SET
														Horas = ACUMULADOS.Horas + $Horas, 
														Valor = ACUMULADOS.Valor + $Valor
													WHERE ACUMULADOS.Id = $IdAcumulado;
											EOD;
										}

										$ok = $this->model->query($query);
									}
									else
										$data['mensajeError'] .= 'Concepto Designer  no existe <strong>' . $ConceptoDesigner . '</strong> ' . $NombreConceptoDesigner . '<br>';
								}
							}

							if (empty($data['mensajeError']))
							{
								header('Location: ' . SERVERURL . '/acumulados/parametros');
								exit;
							}
						}
					}
				}
			}

			if (isset($_FILES) AND count($_FILES) == 0 OR ! empty($data['mensajeError'])) 
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = '';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] =  '';
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/acumulados/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				// $_SESSION['Lista'] = SERVERURL. '/acumulados/lista/' . $_SESSION['ACUMULADOS']['Pagina'];
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/acumulados/parametros';
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function acumularNomina()
		{
			set_time_limit(0);

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$Periodicidad 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$Referencia 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodo 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];

			$regPeriodo 		= getRegistro('PERIODOS', $IdPeriodo);
			$Periodo 			= $regPeriodo['periodo'];

			$regPeriodicidad 	= getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad 		= substr($regPeriodicidad['detalle'], 0, 1);

			$data['Periodo'] 		= $Periodo;
			$data['Ciclo'] 			= $Ciclo;
			$data['FechaInicial'] 	= $regPeriodo['fechainicial'];
			$data['FechaFinal'] 	= $regPeriodo['fechafinal'];
			$data['mensajeError'] 	= '';

			$IdIcetex = getId('CENTROS', "CENTROS.centro = 'S1376'");
			$queryValidacionIcetex = '';
			$queryValidacionIcetex2 = '';

			if (!$IdIcetex) $data['mensajeError'] .= label('Para poder usar el Ciclo 20 o 21 debe existir el centro de costo ICETEX con codigo de proyecto "S1376"') . '<br>';

			if ($Ciclo == 20 AND $IdIcetex) { // PRENOMINA SIN ICETEX
				$queryValidacionIcetex = <<<EOD
					AND EMPLEADOS.IdProyecto <> $IdIcetex 
				EOD;
				$queryValidacionIcetex2 = <<<EOD
					WHERE EMPLEADOS.IdProyecto <> $IdIcetex 
				EOD;
			}

			if ($Ciclo == 21 AND $IdIcetex) { // PRENOMINA SOLO ICETEX
				$queryValidacionIcetex = <<<EOD
					AND EMPLEADOS.IdProyecto = $IdIcetex 
				EOD;
				$queryValidacionIcetex2 = <<<EOD
					WHERE EMPLEADOS.IdProyecto = $IdIcetex 
				EOD;
			}

			// SE BUSCA EL CONCEPTO DE VACACIONES EN TIEMPO
			$query = <<<EOD
				SELECT AUXILIARES.Id, 
						AUXILIARES.FactorConversion, 
						MAYORES.TipoRetencion 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ES VACACIONES EN TIEMPO';
			EOD;

			$reg = $this->model->leer($query);

			if ($reg)
				$IdConceptoVT = $reg['Id'];
			else
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en tiempo') . '<br>';
		
			// SE BUSCA EL CONCEPTO DE VACACIONES EN DINERO
			$query = <<<EOD
				SELECT AUXILIARES.Id, 
						AUXILIARES.FactorConversion, 
						MAYORES.TipoRetencion 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ES VACACIONES EN DINERO';
			EOD;

			$reg = $this->model->leer($query);

			if ($reg)
				$IdConceptoVD = $reg['Id'];
			else
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en dinero') . '<br>';

			$IdARL 	= getID('TERCEROS', "TERCEROS.EsARL = 1");
			if ($IdARL == 0)
				$data['mensajeError'] .= label('No hay definido un tercero para ARL') . '<br>';

			$IdICBF = getID('TERCEROS', "TERCEROS.EsICBF = 1");
			if ($IdICBF == 0)
				$data['mensajeError'] .= label('No hay definido un tercero para ICBF') . '<br>';

			$IdSENA = getID('TERCEROS', "TERCEROS.EsSENA = 1");
			if ($IdSENA == 0)
				$data['mensajeError'] .= label('No hay definido un tercero para SENA') . '<br>';

			if (empty($data['mensajeError']))
			{
				if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Acumular') 
				{
					$query = <<<EOD
							PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
							PERIODOSACUMULADOS.Ciclo = $Ciclo; 
					EOD;

					$regPA = getRegistro('PERIODOSACUMULADOS', 0, $query);

					// if ($regPA['acumulado'] == 1)
					// 	$data['mensajeError'] .= label('Período - Ciclo ya está liquidado y acumulado') . '<br>';

					if (empty($data['mensajeError'])) 
					{
						$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

						// SE TRANSFIERE LA NOMINA A ACUMULADOS
						$query = <<<EOD
							INSERT INTO ACUMULADOS 
								(IdPeriodo, Ciclo, FechaInicialPeriodo, FechaFinalPeriodo, IdEmpleado, IdConcepto, Base, Porcentaje, Horas, Valor, Saldo, Liquida, Afecta, ClaseCr, IdCentro, TipoEmpleado, IdCredito, Fecha, FechaInicial, FechaFinal, IdTercero)
								SELECT $ArchivoNomina.IdPeriodo, 
										$ArchivoNomina.Ciclo, 
										PERIODOS.FechaInicial AS FechaInicialPeriodo, 
										PERIODOS.FechaFinal AS FechaFinalPeriodo, 
										$ArchivoNomina.IdEmpleado, 
										$ArchivoNomina.IdConcepto, 
										$ArchivoNomina.Base, 
										$ArchivoNomina.Porcentaje, 
										$ArchivoNomina.Horas, 
										$ArchivoNomina.Valor,
										$ArchivoNomina.Saldo, 
										$ArchivoNomina.Liquida, 
										$ArchivoNomina.Afecta, 
										$ArchivoNomina.Clase_Cr, 
										$ArchivoNomina.IdCentro, 
										$ArchivoNomina.TipoEmpleado, 
										$ArchivoNomina.IdCredito, 
										$ArchivoNomina.Fecha, 
										$ArchivoNomina.FechaInicial, 
										$ArchivoNomina.FechaFinal, 
										$ArchivoNomina.IdTercero  
									FROM $ArchivoNomina 
										INNER JOIN PERIODOS 
											ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
									WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
										$ArchivoNomina.Ciclo = $Ciclo 
									ORDER BY $ArchivoNomina.IdEmpleado, $ArchivoNomina.IdConcepto;
						EOD;

						$ok = $this->model->actualizarRegistros($query);

						// SE GENERAN LOS PARAFISCALES
						$query = <<<EOD
							SELECT AUXILIARES.Id 
								FROM AUXILIARES 
									INNER JOIN PARAMETROS 
										ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
								WHERE PARAMETROS.Detalle = 'ES APORTE DE SALUD';
						EOD;

						$reg = $this->model->leer($query);

						if ($reg)
							$IdConceptoSalud = $ref['Id'];
						else
							$IdConceptoSalud = 0;

						if ($IdConceptoSalud > 0)
						{
							$query = <<<EOD
								SELECT ACUMULADOS.*, 
										EMPLEADOS.IdCajaCompensacion, 
										PARAMETROS.Valor2 AS PorcentajeRiesgo, 
										CENTROS.TipoEmpleado 
									FROM ACUMULADOS 
										INNER JOIN EMPLEADOS 
											ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
										INNER JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN PARAMETROS 
											ON EMPLEADOS.NivelRiesgo = PARAMETROS.Id 
									WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
										ACUMULADOS.Ciclo = $Ciclo AND 
										ACUMULADOS.IdConcepto = $IdConceptoSalud;
							EOD;

							$acumulados = $this->model->listar($query);

							if ($acumulados)
							{
								for ($i = 0; $i < count($acumulados); $i++)
								{
									$regAcumulado = $acumulados[$i];

									$TipoEmpleado = $regAcumulado['TipoEmpleado'];

									$query = <<<EOD
										SELECT * 
											FROM COMPROBANTES 
												INNER JOIN TIPODOC 
													ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
											WHERE TIPODOC.TipoDocumento = 'PARAF' AND 
												COMPROBANTES.TipoEmpleado = $TipoEmpleado;
									EOD; 

									$comprobantes = $this->model->listar($query);

									if ($comprobantes)
									{
										for ($j = 0; $j < count($comprobantes); $j++)
										{
											$regComprobante = $comprobantes[$j];

											$IdEmpleado 			= $regAcumulado['idempleado'];
											$IdConcepto 			= $regComprobante['idconcepto'];
											$Base 					= $regAcumulado['base'];
											
											if ($regComprobante['porcentaje'] == 0)
												$Porcentaje 		= $regAcumulado['PorcentajeRiesgo'];
											else
												$Porcentaje 		= $regComprobate['porcentaje'];
											
											$Valor 					= round($regAcumulado['base'] * $Porcentaje / 100, 0);
											$IdCentro				= $regAcumulado['idcentro'];
											$FechaInicialPeriodo 	= $regAcumulado['fechainicialperiodo'];
											$FechaFinalPeriodo 		= $regAcumulado['fechafinalperiodo'];

											switch($regComprobante['detalle'])
											{
												case 'ARL':
													$IdTercero = $IdARL;
													break;
												case 'CAJA DE COMPENSACION':
													$IdTercero = $regAcumulado['IdCajaCompensacion'];
													break;
												case 'ICBF':
													$IdTercero = $IdICBF;
													break;
												case 'SENA':
													$IdTercero = $IdSENA;
													break;
												default:
													$IdTercero = 0;
													break;
											}

											$query = <<<EOD
												INSERT INTO ACUMULADOS 
													(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Base, Porcentaje, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado,  FechainicialPeriodo, FechaFinalPeriodo, IdTercero, PagoDispersado)
													VALUES (
														$IdPeriodo, 
														$Ciclo, 
														$IdEmpleado, 
														$IdConcepto, 
														$Base, 
														$Porcentaje, 
														0, 
														$Valor, 
														0, 
														'A', 
														0, 
														$IdCentro, 
														$TipoEmpleado, 
														'$FechaInicialPeriodo', 
														'$FechaFinalPeriodo', 
														$IdTercero, 
														1);
											EOD;

											$ok = $this->model->query($query);
										}
									}
								}
							}
						}

						// SE ACTUALIZAN LAS VACACIONES
						$FechaFinalPeriodo = $regPeriodo['fechafinal'];

						$query = <<<EOD
							SELECT VACACIONES.Id, 
									VACACIONES.IdEmpleado 
								FROM VACACIONES 
								JOIN EMPLEADOS ON EMPLEADOS.Id = VACACIONES.IdEmpleado
								WHERE VACACIONES.Procesado = 0 AND 
									VACACIONES.FechaInicio <= '$FechaFinalPeriodo'
									$queryValidacionIcetex; 
						EOD;

						$vacaciones = $this->model->listarRegistros($query);

						if ($vacaciones) 
						{
							for ($i = 0; $i < count($vacaciones); $i++) 
							{ 
								$regVacaciones = $vacaciones[$i];
								$IdVacaciones = $regVacaciones['Id'];
								$IdEmpleado = $regVacaciones['IdEmpleado'];

								// SE ACUMULAN LAS VACACIONES EN TIEMPO LIQUIDADAS
								$query = <<<EOD
									SELECT $ArchivoNomina.Horas 
										FROM $ArchivoNomina 
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											$ArchivoNomina.IdConcepto = $IdConceptoVT;
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$DiasCausados = $reg['Horas'] / 8;

									$query = <<<EOD
										UPDATE VACACIONES 
											SET
												DiasProcesados = $DiasCausados, 
												Procesado = 1, 
												IdPeriodo = $IdPeriodo, 
												Ciclo = $Ciclo  
											WHERE VACACIONES.Id = $IdVacaciones AND 
												VACACIONES.DiasEnTiempo > 0 AND 
												VACACIONES.Procesado = 0;
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}

								// SE ACUMULAN LAS VACACIONES EN DINERO LIQUIDADAS
								$query = <<<EOD
									SELECT $ArchivoNomina.Horas 
										FROM $ArchivoNomina 
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											$ArchivoNomina.IdConcepto = $IdConceptoVD;
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$DiasCausados = $reg['Horas'] / 8;

									$query = <<<EOD
										UPDATE VACACIONES 
											SET
												DiasProcesados = $DiasCausados, 
												Procesado = 1 
											WHERE VACACIONES.Id = $IdVacaciones AND 
												VACACIONES.DiasEnDinero > 0 AND 
												VACACIONES.Procesado = 0;
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						// SE ACTUALIZAN LAS INCAPACIDADES
						$query = <<<EOD
							SELECT INCAPACIDADES.IdEmpleado, 
									INCAPACIDADES.IdConcepto, 
									AUXILIARES.FactorConversion, 
									INCAPACIDADES.FechaInicio, 
									INCAPACIDADES.DiasIncapacidad, 
									INCAPACIDADES.DiasCausados, 
									INCAPACIDADES.PorcentajeAuxilio
								FROM INCAPACIDADES 
									JOIN EMPLEADOS ON EMPLEADOS.Id = INCAPACIDADES.IdEmpleado
									INNER JOIN AUXILIARES ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
								WHERE INCAPACIDADES.DiasIncapacidad > INCAPACIDADES.DiasCausados
									$queryValidacionIcetex; 
						EOD;

						$incapacidades = $this->model->listarRegistros($query);

						if ($incapacidades) 
						{
							for ($i = 0; $i < count($incapacidades); $i++) 
							{ 
								$regIncapacidad = $incapacidades[$i];
								$IdEmpleado = $regIncapacidad['IdEmpleado'];
								$IdConcepto = $regIncapacidad['IdConcepto'];

								$query = <<<EOD
									SELECT SUM($ArchivoNomina.Horas) Horas
										FROM $ArchivoNomina 
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											$ArchivoNomina.IdConcepto IN ($IdConcepto, 361);
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$DiasCausados = $reg['Horas'] / 8;

									$query = <<<EOD
										UPDATE INCAPACIDADES 
											SET
												DiasCausados = INCAPACIDADES.DiasCausados + $DiasCausados
											WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado AND
												INCAPACIDADES.IdConcepto = $IdConcepto;
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						// SE ACTUALIZAN LOS AUMENTOS SALARIALES
						if ($Ciclo==1 OR $Ciclo==20 OR $Ciclo == 21) {
							$FechaFinal = $regPeriodo['fechafinal'];
	
							$query = <<<EOD
								SELECT AUMENTOSSALARIALES.* 
									FROM AUMENTOSSALARIALES 
									JOIN EMPLEADOS ON EMPLEADOS.Id = AUMENTOSSALARIALES.IdEmpleado
									WHERE AUMENTOSSALARIALES.Procesado = 0 AND 
										AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal'
										$queryValidacionIcetex;
							EOD;
	
							$datos = $this->model->listar($query);
	
							if ($datos)
							{
								for ($i = 0; $i < count($datos); $i++)
								{
									$IdEmpleado 				= $datos[$i]['idempleado'];
									$SueldoBasicoAnterior 		= $datos[$i]['sueldobasicoanterior'];
									$SubsidioTransporteAnterior = $datos[$i]['subsidiotransporteanterior'];
									$SueldoBasico 				= $datos[$i]['sueldobasico'];
									$SubsidioTransporte			= $datos[$i]['subsidiotransporte'];
									$FechaAumento 				= $datos[$i]['fechaaumento'];
	
									$Campo = 'SueldoBasico';
									$ValorAnterior = $SueldoBasicoAnterior;
									$ValorActual = $SueldoBasico;
									$logEmpleado = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
	
									$ok = $this->model->guardarLogEmpleado($logEmpleado);
	
									$Campo = 'SubsidioTransporte';
	
									if ($SubsidioTransporteAnterior > 0) 
									{
										$reg = getRegistro('PARAMETROS', $SubsidioTransporteAnterior);
										$ValorAnterior = $reg['detalle'];
									}
									else
										$ValorAnterior = '';
				
									if ($SubsidioTransporte > 0)
									{
										$reg = getRegistro('PARAMETROS', $SubsidioTransporte);
										$ValorActual = $reg['detalle'];
									}
									else
										$ValorActual = '';
				
									$logEmpleado = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);
	
									$ok = $this->model->guardarLogEmpleado($logEmpleado);
	
									$query = <<<EOD
										UPDATE EMPLEADOS 
											SET 
												SueldoBasico 		= $SueldoBasico, 
												SubsidioTransporte 	= $SubsidioTransporte, 
												FechaAumento 		= '$FechaAumento' 
											WHERE EMPLEADOS.Id 		= $IdEmpleado;
									EOD;
	
									$ok = $this->model->query($query);
								}
	
								$query = <<<EOD
									UPDATE AUMENTOSSALARIALES
										SET	Procesado = 1, 
											IdPeriodo = $IdPeriodo, 
											Ciclo = $Ciclo 
										FROM AUMENTOSSALARIALES 
										JOIN EMPLEADOS ON EMPLEADOS.Id = AUMENTOSSALARIALES.IdEmpleado
										WHERE AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
											AUMENTOSSALARIALES.Procesado = 0
											$queryValidacionIcetex;
								EOD;
	
								$ok = $this->model->query($query);
							}
						}

						// SE LEEN LAS SANCIONES Y LICENCIAS
						$query = <<<EOD
							SELECT $ArchivoNomina.IdEmpleado, 
									$ArchivoNomina.Horas, 
									PARAMETROS.Detalle AS Imputacion, 
									MAYORES.AcumulaSanciones, 
									MAYORES.AcumulaLicencias 
								FROM $ArchivoNomina
									JOIN EMPLEADOS ON EMPLEADOS.Id = $ArchivoNomina.IdEmpleado
									INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
									INNER JOIN PARAMETROS ON AUXILIARES.Imputacion = PARAMETROS.Id 
								WHERE (MAYORES.AcumulaSanciones = 1 OR 
									MAYORES.AcumulaLicencias = 1) $queryValidacionIcetex 
								ORDER BY $ArchivoNomina.IdEmpleado;
						EOD;

						$nomina = $this->model->listarRegistros($query);

						if ($nomina) 
						{
							for ($i = 0; $i < count($nomina); $i++) 
							{ 
								$regNomina = $nomina[$i];

								$IdEmpleado = $regNomina['IdEmpleado'];
								$Dias = intdiv($regNomina['Horas'], 8);

								if ($regNomina['AcumulaSanciones'] == 1) 
								{
									if ($regNomina['Imputacion'] == 'PAGO') 
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasSancion = DiasSancion - $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}
									else
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasSancion = DiasSancion + $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}

									$ok = $this->model->actualizarRegistros($query);
								}

								if ($regNomina['AcumulaLicencias']) 
								{
									if ($regNomina['Imputacion'] == 'PAGO') 
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasLicencia = DiasLicencia - $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}
									else
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasLicencia = DiasLicencia + $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						// SE LEEN LOS DESCUENTOS DE PRESTAMOS
						$query = <<<EOD
							SELECT $ArchivoNomina.IdEmpleado, 
									$ArchivoNomina.IdCredito, 
									$ArchivoNomina.Valor, 
									PARAMETROS.Detalle AS Imputacion
								FROM $ArchivoNomina
									JOIN EMPLEADOS ON EMPLEADOS.Id = $ArchivoNomina.IdEmpleado
									INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
									INNER JOIN PARAMETROS ON AUXILIARES.Imputacion = PARAMETROS.Id 
								WHERE $ArchivoNomina.IdCredito > 0 $queryValidacionIcetex 
								ORDER BY $ArchivoNomina.IdEmpleado;
						EOD;

						$nomina = $this->model->listarRegistros($query);

						if ($nomina) 
						{
							for ($i = 0; $i < count($nomina); $i++) 
							{ 
								$regNomina = $nomina[$i];

								$IdEmpleado = $regNomina['IdEmpleado'];
								$IdCredito = $regNomina['IdCredito'];
								$Valor = $regNomina['Valor'];

								$query = <<<EOD
									UPDATE PRESTAMOS 
										SET SaldoPrestamo = SaldoPrestamo - $Valor, 
											SaldoCuotas = SaldoCuotas - 1 
										WHERE PRESTAMOS.Id = $IdCredito;
								EOD;

								$ok = $this->model->actualizarRegistros($query);
							}
						}

						// SE INACTIVAN LAS NOVEDADES PROGRAMABLES DE UNA SOLA VEZ O DE FECHA DETERMINADA
						$Estado = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'EstadoNovedad' AND PARAMETROS.Detalle = 'INACTIVA'")['valor'];
						$Aplica = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'AplicaNovedad' AND PARAMETROS.Detalle = 'UNA VEZ'")['valor'];
						$FechaLimite = $regPeriodo['fechafinal'];

						$query = <<<EOD
							UPDATE NOVEDADESPROGRAMABLES 
								SET Estado = $Estado, 
									IdPeriodoCierre = $IdPeriodo, 
									CicloCierre = $Ciclo 
								FROM NOVEDADESPROGRAMABLES 
								JOIN EMPLEADOS ON EMPLEADOS.Id = NOVEDADESPROGRAMABLES.IdEmpleado
								WHERE (NOVEDADESPROGRAMABLES.Aplica = $Aplica OR 
									(NOVEDADESPROGRAMABLES.FechaLimite IS NOT NULL AND
									NOVEDADESPROGRAMABLES.FechaLimite <= '$FechaLimite')) 
									$queryValidacionIcetex;
						EOD;

						$ok = $this->model->actualizarRegistros($query);

						// SE ACUMULAN LAS EXENCIONES POR EMPLEADO
						$query = <<<EOD
							UPDATE EMPLEADOS
								SET ExencionAfcFvpAnual = EMPLEADOS.ExencionAfcFvpAnual + EMPLEADOS.ExencionAfcFvpMes, 
									ExencionAnual25 	= EMPLEADOS.ExencionAnual25 	+ EMPLEADOS.ExencionMes25, 
									ExencionAnual 		= EMPLEADOS.ExencionAnual 		+ EMPLEADOS.ExencionMes, 
									ExencionAfcFvpRev	= EMPLEADOS.ExencionAfcFvpMes, 
									ExencionMes25Rev	= EMPLEADOS.ExencionMes25, 
									ExencionMesRev		= EMPLEADOS.ExencionMes, 
									ExencionAfcFvpMes 	= 0, 
									ExencionMes25 		= 0, 
									ExencionMes 		= 0 
							$queryValidacionIcetex2;
						EOD;

						$ok = $this->model->query($query);

						// SE GUARDA LOG DEL VALOR DE LOS TOPES AL MOMENTO
						$query = <<<EOD
						INSERT INTO nomina.log_topes
							(
								idperiodo, ciclo, idempleado, ExencionAfcFvpAnual, ExencionAnual25,
								ExencionAnual, ExencionAfcFvpRev, ExencionMes25Rev, ExencionMesRev,
								ExencionAfcFvpMes, ExencionMes25, ExencionMes
							)
						SELECT
							$IdPeriodo AS idperiodo,
							$Ciclo AS ciclo,
							emp.id AS idempleado,
							emp.ExencionAfcFvpAnual AS ExencionAfcFvpAnual,
							emp.ExencionAnual25 AS ExencionAnual25,
							emp.ExencionAnual AS ExencionAnual,
							emp.ExencionAfcFvpRev AS ExencionAfcFvpRev,
							emp.ExencionMes25Rev AS ExencionMes25Rev,
							emp.ExencionMesRev AS ExencionMesRev,
							emp.ExencionAfcFvpMes AS ExencionAfcFvpMes,
							emp.ExencionMes25 AS ExencionMes25,
							emp.ExencionMes AS ExencionMes
						FROM empleados emp
						WHERE (
								SELECT COUNT(DISTINCT acu.idempleado) FROM acumulados acu
								WHERE acu.idperiodo=$IdPeriodo AND acu.ciclo=$Ciclo AND emp.id=acu.idempleado
							) > 0 AND (
								SELECT COUNT(*) FROM nomina.log_topes ltp
								WHERE ltp.idperiodo=$IdPeriodo AND ltp.ciclo=$Ciclo AND emp.id=ltp.idempleado
							) = 0;
						EOD;

						$ok = $this->model->query($query);

						// SE MARCA EL PERIODO COMO ACUMULADO
						$query = <<<EOD
							UPDATE PERIODOSACUMULADOS 
								SET Acumulado = 1 
								WHERE PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
									PERIODOSACUMULADOS.Ciclo = $Ciclo; 
						EOD;

						$ok = $this->model->actualizarRegistros($query);

						header('Location: ' . SERVERURL . '/dashboard/dashboard');
						exit;
					}
				}
				elseif (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Reversar')
				{
					$query = <<<EOD
							PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
							PERIODOSACUMULADOS.Ciclo = $Ciclo; 
					EOD;

					$regPA = getRegistro('PERIODOSACUMULADOS', 0, $query);

					if ($regPA['acumulado'] == 0)
						$data['mensajeError'] .= label('Período - Ciclo no se encuentra liquidado y acumulado') . '<br>';

					if (empty($data['mensajeError'])) 
					{
						$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

						// SE REVERSAN LAS EXENCIONES
						// SE ACUMULAN LAS EXENCIONES POR EMPLEADO
						$query = <<<EOD
							UPDATE EMPLEADOS
								SET ExencionAfcFvpAnual = EMPLEADOS.ExencionAfcFvpAnual - EMPLEADOS.ExencionAfcFvpRev, 
									ExencionAnual25 	= EMPLEADOS.ExencionAnual25 	- EMPLEADOS.ExencionMes25Rev, 
									ExencionAnual 		= EMPLEADOS.ExencionAnual 		- EMPLEADOS.ExencionMesRev, 
									ExencionAfcFvpMes 	= EMPLEADOS.ExencionAfcFvpRev, 
									ExencionMes25 		= EMPLEADOS.ExencionMes25Rev, 
									ExencionMes 		= EMPLEADOS.ExencionMesRev,
									ExencionAfcFvpRev	= 0, 
									ExencionMes25Rev	= 0, 
									ExencionMesRev		= 0 
							$queryValidacionIcetex2; 
						EOD;

						$ok = $this->model->query($query);

						// SE REVERSAN LOS LOGS DE LOS TOPES
						$query = <<<EOD
							DELETE FROM nomina.log_topes WHERE idperiodo=$IdPeriodo AND ciclo=$Ciclo;
						EOD;

						$ok = $this->model->query($query);

						// SE REACTIVAN LAS NOVEDADES PROGRAMABLES DE UNA SOLA VEZ O DE FECHA DETERMINADA
						$Estado = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'EstadoNovedad' AND PARAMETROS.Detalle = 'ACTIVA'")['valor'];
						$Aplica = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'AplicaNovedad' AND PARAMETROS.Detalle = 'UNA VEZ'")['valor'];
						$FechaLimite = $regPeriodo['fechafinal'];

						$query = <<<EOD
							UPDATE NOVEDADESPROGRAMABLES 
								SET Estado = $Estado 
								FROM NOVEDADESPROGRAMABLES 
								JOIN EMPLEADOS ON EMPLEADOS.Id = NOVEDADESPROGRAMABLES.IdEmpleado
								WHERE NOVEDADESPROGRAMABLES.IdPeriodoCierre = $IdPeriodo AND 
									NOVEDADESPROGRAMABLES.CicloCierre = $Ciclo AND 
									(NOVEDADESPROGRAMABLES.Aplica = $Aplica OR 
									(NOVEDADESPROGRAMABLES.FechaLimite IS NOT NULL AND 
									NOVEDADESPROGRAMABLES.FechaLimite <= '$FechaLimite'))
									$queryValidacionIcetex;
						EOD;

						$ok = $this->model->actualizarRegistros($query);

						// SE REVERSAN LOS DESCUENTOS DE PRESTAMOS
						$query = <<<EOD
							SELECT $ArchivoNomina.IdEmpleado, 
									$ArchivoNomina.IdCredito, 
									$ArchivoNomina.Valor, 
									PARAMETROS.Detalle AS Imputacion
								FROM $ArchivoNomina
									JOIN EMPLEADOS ON EMPLEADOS.Id = $ArchivoNomina.IdEmpleado
									INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
									INNER JOIN PARAMETROS ON AUXILIARES.Imputacion = PARAMETROS.Id 
								WHERE $ArchivoNomina.IdCredito > 0 $queryValidacionIcetex 
								ORDER BY $ArchivoNomina.IdEmpleado;
						EOD;

						$nomina = $this->model->listarRegistros($query);

						if ($nomina) 
						{
							for ($i = 0; $i < count($nomina); $i++) 
							{ 
								$regNomina = $nomina[$i];

								$IdEmpleado = $regNomina['IdEmpleado'];
								$IdCredito = $regNomina['IdCredito'];
								$Valor = $regNomina['Valor'];

								$query = <<<EOD
									UPDATE PRESTAMOS 
										SET SaldoPrestamo = SaldoPrestamo + $Valor, 
											SaldoCuotas = SaldoCuotas + 1 
										WHERE PRESTAMOS.Id = $IdCredito;
								EOD;

								$ok = $this->model->actualizarRegistros($query);
							}
						}

						// SE REVERSAN LAS SANCIONES Y LICENCIAS
						$query = <<<EOD
							SELECT $ArchivoNomina.IdEmpleado, 
									$ArchivoNomina.Horas, 
									PARAMETROS.Detalle AS Imputacion, 
									MAYORES.AcumulaSanciones, 
									MAYORES.AcumulaLicencias 
								FROM $ArchivoNomina
									JOIN EMPLEADOS ON EMPLEADOS.Id = $ArchivoNomina.IdEmpleado
									INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
									INNER JOIN PARAMETROS ON AUXILIARES.Imputacion = PARAMETROS.Id 
								WHERE (MAYORES.AcumulaSanciones = 1 OR 
									MAYORES.AcumulaLicencias = 1) $queryValidacionIcetex 
								ORDER BY $ArchivoNomina.IdEmpleado;
						EOD;

						$nomina = $this->model->listarRegistros($query);

						if ($nomina) 
						{
							for ($i = 0; $i < count($nomina); $i++) 
							{ 
								$regNomina = $nomina[$i];

								$IdEmpleado = $regNomina['IdEmpleado'];
								$Dias = intdiv($regNomina['Horas'], 8);

								if ($regNomina['AcumulaSanciones'] == 1) 
								{
									if ($regNomina['Imputacion'] == 'PAGO') 
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasSancion = DiasSancion + $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}
									else
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasSancion = DiasSancion - $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}

									$ok = $this->model->actualizarRegistros($query);
								}

								if ($regNomina['AcumulaLicencias']) 
								{
									if ($regNomina['Imputacion'] == 'PAGO') 
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasLicencia = DiasLicencia + $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}
									else
									{
										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET DiasLicencia = DiasLicencia - $Dias 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;
									}

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						// SE REVERSAN LOS AUMENTOS SALARIALES
						$FechaInicial = $regPeriodo['fechainicial'];
						$FechaFinal = $regPeriodo['fechafinal'];

						$query = <<<EOD
							UPDATE AUMENTOSSALARIALES
								SET	Procesado = 0, 
									IdPeriodo = 0, 
									Ciclo = 0  
								WHERE AUMENTOSSALARIALES.IdPeriodo = $IdPeriodo AND 
									AUMENTOSSALARIALES.Ciclo = $Ciclo AND 
									AUMENTOSSALARIALES.Procesado = 1;
						EOD;

						$ok = $this->model->query($query);

						// SE REVERSAN LAS INCAPACIDADES
						$query = <<<EOD
							SELECT INCAPACIDADES.IdEmpleado, 
									INCAPACIDADES.IdConcepto, 
									AUXILIARES.FactorConversion, 
									INCAPACIDADES.FechaInicio, 
									INCAPACIDADES.DiasIncapacidad, 
									INCAPACIDADES.DiasCausados, 
									INCAPACIDADES.PorcentajeAuxilio
								FROM INCAPACIDADES 
									INNER JOIN AUXILIARES ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id;
						EOD;

						$incapacidades = $this->model->listarRegistros($query);

						if ($incapacidades) 
						{
							for ($i = 0; $i < count($incapacidades); $i++) 
							{ 
								$regIncapacidad = $incapacidades[$i];
								$IdEmpleado = $regIncapacidad['IdEmpleado'];
								$IdConcepto = $regIncapacidad['IdConcepto'];

								$query = <<<EOD
									SELECT SUM($ArchivoNomina.Horas) Horas
										FROM $ArchivoNomina 
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											$ArchivoNomina.IdConcepto IN ($IdConcepto, 361);
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$DiasCausados = $reg['Horas'] / 8;

									$query = <<<EOD
										UPDATE INCAPACIDADES 
											SET
												DiasCausados = INCAPACIDADES.DiasCausados - $DiasCausados
											WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado AND
												INCAPACIDADES.IdConcepto = $IdConcepto;
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						// SE REVERSAN LAS VACACIONES
						$FechaFinalPeriodo = $regPeriodo['fechafinal'];

						$query = <<<EOD
							SELECT VACACIONES.Id, 
									VACACIONES.IdEmpleado 
								FROM VACACIONES 
								JOIN EMPLEADOS ON EMPLEADOS.Id = VACACIONES.IdEmpleado
								WHERE VACACIONES.Procesado = 1 AND 
									VACACIONES.IdPeriodo = $IdPeriodo AND 
									VACACIONES.Ciclo = $Ciclo $queryValidacionIcetex; 
						EOD;

						$vacaciones = $this->model->listarRegistros($query);

						if ($vacaciones) 
						{
							for ($i = 0; $i < count($vacaciones); $i++) 
							{ 
								$regVacaciones = $vacaciones[$i];
								$IdVacaciones = $regVacaciones['Id'];
								$IdEmpleado = $regVacaciones['IdEmpleado'];

								// SE REVERSAN LAS VACACIONES EN TIEMPO LIQUIDADAS
								$query = <<<EOD
									SELECT $ArchivoNomina.Horas 
										FROM $ArchivoNomina 
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											$ArchivoNomina.IdConcepto = $IdConceptoVT;
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$query = <<<EOD
										UPDATE VACACIONES 
											SET
												DiasProcesados = 0, 
												Procesado = 0, 
												IdPeriodo = 0, 
												Ciclo = 0
											WHERE VACACIONES.Id = $IdVacaciones AND 
												VACACIONES.DiasEnTiempo > 0 AND 
												VACACIONES.Procesado = 1;
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}

								// SE REVERSAN LAS VACACIONES EN DINERO LIQUIDADAS
								$query = <<<EOD
									SELECT $ArchivoNomina.Horas 
										FROM $ArchivoNomina 
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											$ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											$ArchivoNomina.IdConcepto = $IdConceptoVD;
								EOD;

								$reg = $this->model->leer($query);

								if ($reg) 
								{
									$DiasCausados = $reg['Horas'] / 8;

									$query = <<<EOD
										UPDATE VACACIONES 
											SET
												DiasProcesados = 0, 
												Procesado = 0, 
												IdPeriodo = 0, 
												Ciclo = 0 
											WHERE VACACIONES.Id = $IdVacaciones AND 
												VACACIONES.DiasEnDinero > 0 AND 
												VACACIONES.Procesado = 1;
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						// SE MARCA EL PERIODO COMO ACUMULADO
						$query = <<<EOD
							UPDATE PERIODOSACUMULADOS 
								SET Acumulado = 0 
								WHERE PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
									PERIODOSACUMULADOS.Ciclo = $Ciclo; 
						EOD;

						$ok = $this->model->actualizarRegistros($query);

						// SE ELIMINAN LOS ACUMULADOS
						$query = <<<EOD
							DELETE FROM ACUMULADOS
								WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
									ACUMULADOS.Ciclo = $Ciclo;
						EOD;

						$ok = $this->model->actualizarRegistros($query);

						header('Location: ' . SERVERURL . '/dashboard/dashboard');
						exit;
					}

					header('Location: ' . SERVERURL . '/dashboard/dashboard');
					exit;
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
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';

			$_SESSION['ACUMULADOS']['Filtro'] = '';

			$this->views->getView($this, 'acumularNomina', $data);
		}

		public function calculoRetFte()
		{
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'");
			$ValorUVT = $reg1['valor'];

			if (empty($data['mensajeError'])) 
			{
				$AnoActual = date('Y') - 1;
				//$AnoActual = date('Y');
				$AnoInicial = $AnoActual - 1;

				$FechaInicial = date($AnoInicial . '-12-01');
				$FechaFinal = date($AnoActual . '-11-30');

				// $query = <<<EOD
				// 	SELECT MIN(ACUMULADOS.FechaInicialPeriodo) AS FechaInicial, 
				// 			MAX(ACUMULADOS.FechaFinalPeriodo) AS FechaFinal, 
				// 			ACUMULADOS.IdEmpleado, 
				// 			EMPLEADOS.Apellido1, 
				// 			EMPLEADOS.Apellido2, 
				// 			EMPLEADOS.Nombre1, 
				// 			EMPLEADOS.Nombre2, 
				// 			EMPLEADOS.CuotaVivienda, 
				// 			EMPLEADOS.SaludYEducacion, 
				// 			EMPLEADOS.DeduccionDependientes, 
				// 			EMPLEADOS.PorcentajeRetencion, 
				// 			SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS IngresoBruto, 
				// 			SUM(IIF(PARAMETROS1.Detalle = 'DEDUCCIÓN' AND PARAMETROS3.Detalle <> 'RENTAS DEDUCIBLES', ACUMULADOS.Valor, 0)) AS ValorDeducciones, 
				// 			SUM(IIF(PARAMETROS3.Detalle = 'RENTAS DEDUCIBLES', ACUMULADOS.Valor, 0)) AS RentasExentas 
				// 		FROM ACUMULADOS 
				// 			INNER JOIN EMPLEADOS 
				// 				ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
				// 			INNER JOIN AUXILIARES 
				// 				ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
				// 			INNER JOIN MAYORES 
				// 				ON AUXILIARES.IdMayor = MAYORES.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS1 
				// 				ON AUXILIARES.Imputacion = PARAMETROS1.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS2 
				// 				ON MAYORES.TipoRetencion = PARAMETROS2.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS3
				// 				ON MAYORES.ClaseConcepto = PARAMETROS3.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS4 
				// 				ON EMPLEADOS.MetodoRetencion = PARAMETROS4.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS5 
				// 				ON EMPLEADOS.Estado = PARAMETROS5.Id 
				// 		WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
				// 			ACUMULADOS.FechaFinalPeriodo <= '$FechaFinal' AND 
				// 			PARAMETROS2.Detalle = 'SALARIOS' AND 
				// 			PARAMETROS4.Detalle = 'PORCENTAJE FIJO' AND 
				// 			PARAMETROS5.Detalle = 'ACTIVO' 
				// 		GROUP BY ACUMULADOS.IdEmpleado, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.PorcentajeRetencion;
				// EOD;

				$query = <<<EOD
					SELECT MIN(ACUMULADOS.FechaInicialPeriodo) AS FechaInicial, 
							MAX(ACUMULADOS.FechaFinalPeriodo) AS FechaFinal, 
							ACUMULADOS.IdEmpleado, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							EMPLEADOS.FechaIngreso, 
							EMPLEADOS.CuotaVivienda, 
							EMPLEADOS.SaludYEducacion, 
							EMPLEADOS.DeduccionDependientes, 
							EMPLEADOS.ExencionAfcFvpAnual, 
							EMPLEADOS.ExencionAnual25, 
							EMPLEADOS.ExencionAnual, 
							EMPLEADOS.PorcentajeRetencion, 
							SUM(IIF(PARAMETROS3.Detalle = 'SALARIOS', IIF(PARAMETROS4.Detalle = 'PAGO', ACUMULADOS.Valor, 0), 0)) AS IngresoBruto, 
							SUM(IIF(PARAMETROS3.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS4.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor), 0)) AS PrimaLegal, 
							SUM(IIF(PARAMETROS3.Detalle = 'CESANTIAS', IIF(PARAMETROS4.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor), 0)) AS Cesantias, 
							SUM(IIF(PARAMETROS3.Detalle = 'SALUD / PENSION', IIF(PARAMETROS4.Detalle = 'PAGO', -ACUMULADOS.Valor, ACUMULADOS.Valor), 0)) AS SaludPension, 
							SUM(IIF(PARAMETROS3.Detalle = 'AFC / FVP', IIF(PARAMETROS4.Detalle = 'PAGO', -ACUMULADOS.Valor, ACUMULADOS.Valor), 0)) AS AfcFvp
						FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1
								ON EMPLEADOS.Estado = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2
								ON EMPLEADOS.MetodoRetencion = PARAMETROS2.Id 
							INNER JOIN PARAMETROS AS PARAMETROS3
								ON MAYORES.TipoRetencion = PARAMETROS3.Id 
							INNER JOIN PARAMETROS AS PARAMETROS4
								ON AUXILIARES.Imputacion = PARAMETROS4.Id 
						WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
							ACUMULADOS.FechaFinalPeriodo <= '$FechaFinal' AND 
							PARAMETROS1.Detalle = 'ACTIVO' AND 
							PARAMETROS2.Detalle = 'PORCENTAJE FIJO' 
						GROUP BY ACUMULADOS.IdEmpleado, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, EMPLEADOS.FechaIngreso, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.ExencionAfcFvpAnual, EMPLEADOS.ExencionAnual25, EMPLEADOS.ExencionAnual, EMPLEADOS.PorcentajeRetencion 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
				EOD;

				$acumulados = $this->model->listarRegistros($query);

				if ($acumulados) 
				{
					echo '<table style="width:100%">';
						echo '<tr>';
							echo '<th>NOMBRE EMPLEADO</th>';
							echo '<th>ING. BRUTO</th>';
							echo '<th>SALUD/PENSIÓN</th>';
							echo '<th>AFC/FVP</th>';
							echo '<th>VR. VIVIENDA</th>';
							echo '<th>VR. SALUD</th>';
							echo '<th>VR. DEPENDIENTES</th>';
							echo '<th>DEDUCCIONES</th>';
							echo '<th>RENTA EXENTA 25%</th>';
							echo '<th>ING. NETO</th>';
							echo '<th>RENTA EXENTA 40%</th>';
							echo '<th>ING. NETO</th>';
							echo '<th>DÍAS</th>';
							echo '<th>ING. NETO MES</th>';
							echo '<th>ING. NETO UVT</th>';
							echo '<th>PORCENTAJE NUEVO</th>';
							echo '<th>PROC. ACTUAL</th>';
						echo '</tr>';

					for ($i = 0; $i < count($acumulados); $i++) 
					{ 
						$regAcumulado = $acumulados[$i];

						$FechaInicial = max($regAcumulado['FechaInicial'], $regAcumulado['FechaIngreso']);
						$FechaFinal = $regAcumulado['FechaFinal'];

						if (right($FechaFinal, 2) == '31')
							$Dias = Dias360($FechaFinal, $FechaInicial) - 1;
						else
							$Dias = Dias360($FechaFinal, $FechaInicial);

						$IngresoBruto = $regAcumulado['IngresoBruto'] + $regAcumulado['PrimaLegal'];

						$SaludPension = $regAcumulado['SaludPension'];
						$AfcFvp = $regAcumulado['AfcFvp'];

						if ($AfcFvp > $IngresoBruto * .3)
							$AfcFvp = round($IngresoBruto * .3, 0);

						$CuotaVivienda = $regAcumulado['CuotaVivienda'] * 12;

						if ($CuotaVivienda > $ValorUVT * 1200)
							$CuotaVivienda = $ValorUVT * 1200;

						$SaludYEducacion = $regAcumulado['SaludYEducacion']; // ALIVIOS A SALUD

						if ($SaludYEducacion * 12 > $ValorUVT * 192)
							$SaludYEducacion = $ValorUVT * 192;
						else
							$SaludYEducacion *= 12;

						$DeduccionDependientes = $regAcumulado['DeduccionDependientes'];

						if ($DeduccionDependientes) 
							$DeduccionDependientes = min($ValorUVT * 384, round($IngresoBruto * 0.1, 0));
						else
							$DeduccionDependientes = 0;

						$ValorDeducciones = $SaludPension + $AfcFvp + $CuotaVivienda + $SaludYEducacion + $DeduccionDependientes;
						$RentaExenta25 = round(min(($IngresoBruto - $ValorDeducciones) * .25, $ValorUVT * 790), 0);
						$IngresoNeto1 = $IngresoBruto - $ValorDeducciones - $RentaExenta25;

						$Limite40 = min(round($IngresoNeto1 * .4, 0), $ValorUVT * 1340);
						$Limite40 = $ValorUVT * 1340;

						$RentaExenta40 = $AfcFvp + $CuotaVivienda + $SaludYEducacion + $DeduccionDependientes + $RentaExenta25;

						if ($RentaExenta40 > $Limite40)
							$RentaExenta40 = $Limite40 - $RentaExenta40;
						else
							$RentaExenta40 = 0;

						$IngresoNeto2 = $IngresoNeto1 - $RentaExenta40;

						// VALOR MENSUAL
						if ($Dias >= 360)
							$IngresoNetoMes = round($IngresoNeto2 / 13, 0);
						else
							$IngresoNetoMes = round($IngresoNeto2 / $Dias * 30, 0);

						$IngresoNetoUVT = $IngresoNetoMes / $ValorUVT;

						if ($IngresoNetoUVT <= 95)
							$Porcentaje = 0;
						elseif ($IngresoNetoUVT <= 150) 
							$Porcentaje = round(($IngresoNetoUVT - 95) * .19 / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 360)
							$Porcentaje = round((($IngresoNetoUVT - 150) * .28 + 10) / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 640)
							$Porcentaje = round((($IngresoNetoUVT - 360) * .33 + 69) / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 945)
							$Porcentaje = round((($IngresoNetoUVT - 640) * .35 + 162) / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 2300)
							$Porcentaje = round((($IngresoNetoUVT - 945) * .37 + 268) / $IngresoNetoUVT * 100, 2);
						else
							$Porcentaje = round((($IngresoNetoUVT - 2300) * .39 + 770) / $IngresoNetoUVT * 100, 2);

						if ($Porcentaje <> $regAcumulado['PorcentajeRetencion'] OR TRUE)
						{
							echo '<tr>';
								echo '<td style="border: 1px solid black">';
									echo $regAcumulado['Apellido1'] . ' ' . $regAcumulado['Apellido2'] . ' ' . $regAcumulado['Nombre1'] . ' ' . $regAcumulado['Nombre2'];
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoBruto, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($SaludPension, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($AfcFvp, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($CuotaVivienda, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($SaludYEducacion, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($DeduccionDependientes, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($ValorDeducciones, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($RentaExenta25, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNeto1, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($RentaExenta40, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNeto2, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($Dias, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNetoMes, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNetoUVT, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo $Porcentaje;
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo $regAcumulado['PorcentajeRetencion'];
								echo '</td>';
							echo '</tr>';
						}
					}
					
					echo '</table>';
				}
			}
			
			// header('Location: ' . SERVERURL . '/dashboard/dashboard');
			exit();
		}

		public function calculoRetFte2()
		{
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'");
			$ValorUVT = $reg1['valor'];

			if (empty($data['mensajeError'])) 
			{
				// $AnoActual = date('Y') - 1;
				$AnoActual = date('Y');
				$AnoInicial = $AnoActual - 1;

				$FechaInicial = date($AnoInicial . '-06-01');
				$FechaFinal = date($AnoActual . '-05-31');

				// $query = <<<EOD
				// 	SELECT MIN(ACUMULADOS.FechaInicialPeriodo) AS FechaInicial, 
				// 			MAX(ACUMULADOS.FechaFinalPeriodo) AS FechaFinal, 
				// 			ACUMULADOS.IdEmpleado, 
				// 			EMPLEADOS.Apellido1, 
				// 			EMPLEADOS.Apellido2, 
				// 			EMPLEADOS.Nombre1, 
				// 			EMPLEADOS.Nombre2, 
				// 			EMPLEADOS.CuotaVivienda, 
				// 			EMPLEADOS.SaludYEducacion, 
				// 			EMPLEADOS.DeduccionDependientes, 
				// 			EMPLEADOS.PorcentajeRetencion, 
				// 			SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS IngresoBruto, 
				// 			SUM(IIF(PARAMETROS1.Detalle = 'DEDUCCIÓN' AND PARAMETROS3.Detalle <> 'RENTAS DEDUCIBLES', ACUMULADOS.Valor, 0)) AS ValorDeducciones, 
				// 			SUM(IIF(PARAMETROS3.Detalle = 'RENTAS DEDUCIBLES', ACUMULADOS.Valor, 0)) AS RentasExentas 
				// 		FROM ACUMULADOS 
				// 			INNER JOIN EMPLEADOS 
				// 				ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
				// 			INNER JOIN AUXILIARES 
				// 				ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
				// 			INNER JOIN MAYORES 
				// 				ON AUXILIARES.IdMayor = MAYORES.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS1 
				// 				ON AUXILIARES.Imputacion = PARAMETROS1.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS2 
				// 				ON MAYORES.TipoRetencion = PARAMETROS2.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS3
				// 				ON MAYORES.ClaseConcepto = PARAMETROS3.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS4 
				// 				ON EMPLEADOS.MetodoRetencion = PARAMETROS4.Id 
				// 			INNER JOIN PARAMETROS AS PARAMETROS5 
				// 				ON EMPLEADOS.Estado = PARAMETROS5.Id 
				// 		WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
				// 			ACUMULADOS.FechaFinalPeriodo <= '$FechaFinal' AND 
				// 			PARAMETROS2.Detalle = 'SALARIOS' AND 
				// 			PARAMETROS4.Detalle = 'PORCENTAJE FIJO' AND 
				// 			PARAMETROS5.Detalle = 'ACTIVO' 
				// 		GROUP BY ACUMULADOS.IdEmpleado, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.PorcentajeRetencion;
				// EOD;

				$query = <<<EOD
					SELECT MIN(ACUMULADOS.FechaInicialPeriodo) AS FechaInicial, 
							MAX(ACUMULADOS.FechaFinalPeriodo) AS FechaFinal, 
							ACUMULADOS.IdEmpleado, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							EMPLEADOS.FechaIngreso, 
							EMPLEADOS.CuotaVivienda, 
							EMPLEADOS.SaludYEducacion, 
							EMPLEADOS.DeduccionDependientes, 
							EMPLEADOS.ExencionAfcFvpAnual, 
							EMPLEADOS.ExencionAnual25, 
							EMPLEADOS.ExencionAnual, 
							EMPLEADOS.PorcentajeRetencion, 
							SUM(IIF(PARAMETROS3.Detalle = 'SALARIOS', IIF(PARAMETROS4.Detalle = 'PAGO', ACUMULADOS.Valor, 0), 0)) AS IngresoBruto, 
							SUM(IIF(PARAMETROS3.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS4.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor), 0)) AS PrimaLegal, 
							SUM(IIF(PARAMETROS3.Detalle = 'CESANTIAS', IIF(PARAMETROS4.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor), 0)) AS Cesantias, 
							SUM(IIF(PARAMETROS3.Detalle = 'SALUD / PENSION', IIF(PARAMETROS4.Detalle = 'PAGO', -ACUMULADOS.Valor, ACUMULADOS.Valor), 0)) AS SaludPension, 
							SUM(IIF(PARAMETROS3.Detalle = 'AFC / FVP', IIF(PARAMETROS4.Detalle = 'PAGO', -ACUMULADOS.Valor, ACUMULADOS.Valor), 0)) AS AfcFvp
						FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1
								ON EMPLEADOS.Estado = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2
								ON EMPLEADOS.MetodoRetencion = PARAMETROS2.Id 
							INNER JOIN PARAMETROS AS PARAMETROS3
								ON MAYORES.TipoRetencion = PARAMETROS3.Id 
							INNER JOIN PARAMETROS AS PARAMETROS4
								ON AUXILIARES.Imputacion = PARAMETROS4.Id 
						WHERE ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
							ACUMULADOS.FechaFinalPeriodo <= '$FechaFinal' AND 
							PARAMETROS1.Detalle = 'ACTIVO' AND 
							PARAMETROS2.Detalle = 'PORCENTAJE FIJO' 
						GROUP BY ACUMULADOS.IdEmpleado, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, EMPLEADOS.FechaIngreso, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.ExencionAfcFvpAnual, EMPLEADOS.ExencionAnual25, EMPLEADOS.ExencionAnual, EMPLEADOS.PorcentajeRetencion 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
				EOD;

				$acumulados = $this->model->listarRegistros($query);

				if ($acumulados) 
				{
					echo '<table style="width:100%">';
						echo '<tr>';
							echo '<th>NOMBRE EMPLEADO</th>';
							echo '<th>ING. BRUTO</th>';
							echo '<th>SALUD/PENSIÓN</th>';
							echo '<th>AFC/FVP</th>';
							echo '<th>VR. VIVIENDA</th>';
							echo '<th>VR. SALUD</th>';
							echo '<th>VR. DEPENDIENTES</th>';
							echo '<th>DEDUCCIONES</th>';
							echo '<th>RENTA EXENTA 25%</th>';
							echo '<th>ING. NETO</th>';
							echo '<th>RENTA EXENTA 40%</th>';
							echo '<th>ING. NETO</th>';
							echo '<th>DÍAS</th>';
							echo '<th>ING. NETO MES</th>';
							echo '<th>ING. NETO UVT</th>';
							echo '<th>PORCENTAJE NUEVO</th>';
							echo '<th>PROC. ACTUAL</th>';
						echo '</tr>';

					for ($i = 0; $i < count($acumulados); $i++) 
					{ 
						$regAcumulado = $acumulados[$i];

						$FechaInicial = max($regAcumulado['FechaInicial'], $regAcumulado['FechaIngreso']);
						$FechaFinal = $regAcumulado['FechaFinal'];

						if (right($FechaFinal, 2) == '31')
							$Dias = Dias360($FechaFinal, $FechaInicial) - 1;
						else
							$Dias = Dias360($FechaFinal, $FechaInicial);

						$IngresoBruto = $regAcumulado['IngresoBruto'] + $regAcumulado['PrimaLegal'];

						$SaludPension = $regAcumulado['SaludPension'];
						$AfcFvp = $regAcumulado['AfcFvp'];

						if ($AfcFvp > $IngresoBruto * .3)
							$AfcFvp = round($IngresoBruto * .3, 0);

						$CuotaVivienda = $regAcumulado['CuotaVivienda'] * 12;

						if ($CuotaVivienda > $ValorUVT * 1200)
							$CuotaVivienda = $ValorUVT * 1200;

						$SaludYEducacion = $regAcumulado['SaludYEducacion']; // ALIVIOS A SALUD

						if ($SaludYEducacion * 12 > $ValorUVT * 192)
							$SaludYEducacion = $ValorUVT * 192;
						else
							$SaludYEducacion *= 12;

						$DeduccionDependientes = $regAcumulado['DeduccionDependientes'];

						if ($DeduccionDependientes) 
							$DeduccionDependientes = min($ValorUVT * 384, round($IngresoBruto * 0.1, 0));
						else
							$DeduccionDependientes = 0;

						$ValorDeducciones = $SaludPension + $AfcFvp + $CuotaVivienda + $SaludYEducacion + $DeduccionDependientes;
						$RentaExenta25 = round(min(($IngresoBruto - $ValorDeducciones) * .25, $ValorUVT * 790), 0);
						$IngresoNeto1 = $IngresoBruto - $ValorDeducciones - $RentaExenta25;

						$Limite40 = min(round($IngresoNeto1 * .4, 0), $ValorUVT * 1340);
						$Limite40 = $ValorUVT * 1340;

						$RentaExenta40 = $AfcFvp + $CuotaVivienda + $SaludYEducacion + $DeduccionDependientes + $RentaExenta25;

						if ($RentaExenta40 > $Limite40)
							$RentaExenta40 = $Limite40 - $RentaExenta40;
						else
							$RentaExenta40 = 0;

						$IngresoNeto2 = $IngresoNeto1 - $RentaExenta40;

						// VALOR MENSUAL
						if ($Dias >= 360)
							$IngresoNetoMes = round($IngresoNeto2 / 13, 0);
						else
							$IngresoNetoMes = round($IngresoNeto2 / $Dias * 30, 0);

						$IngresoNetoUVT = $IngresoNetoMes / $ValorUVT;

						if ($IngresoNetoUVT <= 95)
							$Porcentaje = 0;
						elseif ($IngresoNetoUVT <= 150) 
							$Porcentaje = round(($IngresoNetoUVT - 95) * .19 / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 360)
							$Porcentaje = round((($IngresoNetoUVT - 150) * .28 + 10) / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 640)
							$Porcentaje = round((($IngresoNetoUVT - 360) * .33 + 69) / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 945)
							$Porcentaje = round((($IngresoNetoUVT - 640) * .35 + 162) / $IngresoNetoUVT * 100, 2);
						elseif ($IngresoNetoUVT <= 2300)
							$Porcentaje = round((($IngresoNetoUVT - 945) * .37 + 268) / $IngresoNetoUVT * 100, 2);
						else
							$Porcentaje = round((($IngresoNetoUVT - 2300) * .39 + 770) / $IngresoNetoUVT * 100, 2);

						if ($Porcentaje <> $regAcumulado['PorcentajeRetencion'] OR TRUE)
						{
							echo '<tr>';
								echo '<td style="border: 1px solid black">';
									echo $regAcumulado['Apellido1'] . ' ' . $regAcumulado['Apellido2'] . ' ' . $regAcumulado['Nombre1'] . ' ' . $regAcumulado['Nombre2'];
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoBruto, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($SaludPension, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($AfcFvp, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($CuotaVivienda, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($SaludYEducacion, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($DeduccionDependientes, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($ValorDeducciones, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($RentaExenta25, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNeto1, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($RentaExenta40, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNeto2, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($Dias, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNetoMes, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo number_format($IngresoNetoUVT, 0);
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo $Porcentaje;
								echo '</td>';
								echo '<td style="border:1px solid black;text-align:right">';
									echo $regAcumulado['PorcentajeRetencion'];
								echo '</td>';
							echo '</tr>';
						}
					}
					
					echo '</table>';
				}
			}
			
			// header('Location: ' . SERVERURL . '/dashboard/dashboard');
			exit();
		}
	}
?>