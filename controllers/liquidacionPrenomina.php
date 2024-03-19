<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class LiquidacionPrenomina extends Controllers
	{
		public function liquidar($IdCentro = 0, $Empleado = '', $TipoEmpleados = 0, $Ciclo = 0)
		{
			set_time_limit(0);

			if (! empty($Empleado))
				$RetornaLiquidacion = TRUE;
			else
				$RetornaLiquidacion = FALSE;

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");
			$reg5 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'");
			$reg6 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'TipoPrestamo' AND PARAMETROS.Detalle = 'PRÉSTAMO EMPRESA'");

			$Referencia = $reg1['valor'];
			$IdPeriodicidad = $reg2['valor'];

			$Periodicidad 		= getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$cPeriodicidad 		= substr($Periodicidad, 0, 1);
			$IdPeriodo 			= $reg3['valor'];

			if ($Ciclo == 0)
				$Ciclo 			= $reg4['valor'];

			$regPeriodo 		= getRegistro('PERIODOS', $IdPeriodo);

			$periodosAcumulados = getTabla('PERIODOSACUMULADOS', "PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo", 'Id');
			$PeriodoAcumulado 	= FALSE;
			$SoloNovedades 		= FALSE;

			if ($periodosAcumulados)
			{
				for ($i = 0; $i < count($periodosAcumulados); $i++)
				{
					if ($periodosAcumulados[$i]['ciclo'] == $Ciclo)
						$SoloNovedades = ($periodosAcumulados[$i]['solonovedades'] == 1 ? TRUE : FALSE);

					if ($periodosAcumulados[$i]['ciclo'] == 1 AND $periodosAcumulados[$i]['acumulado'] == 1)
						$PeriodoAcumulado = TRUE;
				}
			}

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Periodo' 		=> ($regPeriodo ? $regPeriodo['periodo'] : 0), 
					'Ciclo' 		=> $Ciclo, 
					'FechaInicial' 	=> ($regPeriodo ? $regPeriodo['fechainicial'] : ''), 
					'FechaFinal' 	=> ($regPeriodo ? $regPeriodo['fechafinal'] : ''),
					'IdCentro' 		=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : $IdCentro,
					'Empleado' 		=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : $Empleado,
					'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : $TipoEmpleados
					),
				'mensajeError' => ($regPeriodo ? '' : label('Perído definido no existe') . '<br>')
			);

			$IdIcetex = getId('CENTROS', "CENTROS.centro = 'S1376'");
			$queryValidacionIcetex = '';

			if (!$IdIcetex) $data['mensajeError'] .= label('Para poder usar el Ciclo 20 o 21 debe existir el centro de costo ICETEX con codigo de proyecto "S1376"') . '<br>';

			if ($Ciclo == 20 AND $IdIcetex) // PRENOMINA SIN ICETEX
				$queryValidacionIcetex = <<<EOD
					AND EMPLEADOS.IdProyecto <> $IdIcetex 
				EOD;

			if ($Ciclo == 21 AND $IdIcetex) // PRENOMINA SOLO ICETEX
				$queryValidacionIcetex = <<<EOD
					AND EMPLEADOS.IdProyecto = $IdIcetex 
				EOD;

			if (empty($data['mensajeError'])) 
			{
				$Periodo = $regPeriodo['periodo'];
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];
				$FechaInicialPeriodoAnterior = ComienzoMes(date('Y-m-d', strtotime($FechaInicialPeriodo . ' -1 day')));
				$FechaFinalPeriodoAnterior = FinMes($FechaInicialPeriodoAnterior);

				$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

				$ValorUVT = $reg5['valor'];
				$TipoPrestamoEmpresa = $reg6['id'];

				$query = <<<EOD
						PERIODOSACUMULADOS.IdPeriodo  = $IdPeriodo AND 
						PERIODOSACUMULADOS.Ciclo = $Ciclo; 
				EOD;

				$regPA = getRegistro('PERIODOSACUMULADOS', 0, $query);

				if ($regPA AND $regPA['acumulado'] == 1)
					$data['mensajeError'] .= label('Período - Ciclo ya está liquidado y acumulado') . '<br>';
			}

			if (empty($data['mensajeError']))
			{
				if (isset($_REQUEST['IdCentro']) OR ! empty($Empleado))
				{
					$P_IdCentro = isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : $IdCentro;
					$P_Empleado = isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : $Empleado;
					$P_TipoEmpleados = isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : $TipoEmpleados;

					$data['mensajeError'] = '';

					$ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte' AND PARAMETROS.Detalle = 'VALOR'")['valor'];

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

					$reg = $this->model->leerRegistro($query);

					if ($reg)
					{
						$IdConceptoVT = $reg['Id'];
						$FactorConversionVT = $reg['FactorConversion'];
						$TipoRetencionVT = $reg['TipoRetencion'];
					}
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
	
					$reg = $this->model->leerRegistro($query);
	
					if ($reg)
					{
						$IdConceptoVD = $reg['Id'];
						$FactorConversionVD = $reg['FactorConversion'];
						$TipoRetencionVD = $reg['TipoRetencion'];
					}
					else
						$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en dinero') . '<br>';

					// SE BUSCA EL CONCEPTO DE PRESTAMO AUTOMATICO
					$query = <<<EOD
						SELECT AUXILIARES.Id, 
								MAYORES.TipoRetencion 
							FROM AUXILIARES 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
							WHERE PARAMETROS.Detalle = 'ES PRÉSTAMO AUTOMÁTICO';
					EOD;

					$reg = $this->model->leerRegistro($query);

					if ($reg)
					{
						$IdConceptoPrestamo = $reg['Id'];
						$TipoRetencionPrestamo = $reg['TipoRetencion'];
					}
					else
					{
						$IdConceptoPrestamo = 0;
						$TipoRetencionPrestamo = 0;
					}

					if (empty($IdConceptoPrestamo))
						$data['mensajeError'] .= label('No hay definido un concepto de préstamo automático') . '<br>';
			
					// SE BUSCA EL CONCEPTO DE CUOTA DE PRESTAMO AUTOMATICO
					$query = <<<EOD
						SELECT AUXILIARES.Id,
								MAYORES.TipoRetencion 
							FROM AUXILIARES 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
							WHERE PARAMETROS.Detalle = 'ES CUOTA PRÉSTAMO AUTOMÁTICO';
					EOD;
							
					$reg = $this->model->leerRegistro($query);

					if ($reg)
					{
						$IdConceptoCuotaPrestamo = $reg['Id'];
						$TipoRetencionCuotaPrestamo = $reg['TipoRetencion'];
					}
					else
					{
						$IdConceptoCuotaPrestamo = 0;
						$TipoRetencionCuotaPrestamo = 0;
					}

					if (empty($IdConceptoCuotaPrestamo))
						$data['mensajeError'] .= label('No hay definido un concepto de Cuota de Préstamo Automático') . '<br>';

					// BUSCAR FONDO DE SOLIDARIDAD
					$query = <<<EOD
						SELECT AUXILIARES.*, 
								MAYORES.TipoRetencion, 
								PARAMETROS.Detalle AS NombreTipoRegistroAuxiliar 
							FROM AUXILIARES 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
							WHERE PARAMETROS.Detalle = 'ES FONDO DE SOLIDARIDAD';
					EOD;
							
					$regFSP = $this->model->leerRegistro($query);

					if (empty($regFSP)) 
						$data['mensajeError'] .= label('No hay definido un concepto de Fondo de solidaridad pensional') . '<br>';

					// BUSCAR FONDO DE SUBSISTENCIA
					$query = <<<EOD
						SELECT AUXILIARES.*, 
								MAYORES.TipoRetencion, 
								PARAMETROS.Detalle AS NombreTipoRegistroAuxiliar 
							FROM AUXILIARES 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
							WHERE PARAMETROS.Detalle = 'ES FONDO DE SUBSISTENCIA';
					EOD;
							
					$regFS = $this->model->leerRegistro($query);

					if (empty($regFS)) 
						$data['mensajeError'] .= label('No hay definido un concepto de Fondo de subsistencia') . '<br>';

					// SE  BUSCA EL CONCEPTO DE RET.FTE.
					$query = <<<EOD
						SELECT AUXILIARES.*, 
								MAYORES.TipoRetencion 
							FROM AUXILIARES 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
							WHERE PARAMETROS.Detalle = 'ES RETENCIÓN SALARIOS'; 
					EOD;

					$regRF = $this->model->leerRegistro($query);

					if (empty($regRF)) 
						$data['mensajeError'] .= label('No hay definido un concepto de Retención Fuente') . '<br>';

					if (empty($data['mensajeError'])) 
					{
						// SE BORRAN LOS CREDITOS AUTOMÁTICOS
						$query = <<<EOD
							DELETE PRESTAMOS
								FROM PRESTAMOS
									INNER JOIN EMPLEADOS
										ON PRESTAMOS.IdEmpleado = EMPLEADOS.Id
									INNER JOIN CENTROS 
										ON EMPLEADOS.IdCentro = CENTROS.Id
								WHERE PRESTAMOS.IdPeriodo = $IdPeriodo 
						EOD;

						if (! empty($P_IdCentro))
							$query .= <<<EOD
								AND EMPLEADOS.IdCentro = $P_IdCentro 
							EOD;

						if (! empty($P_Empleado))
							$query .= <<<EOD
								AND EMPLEADOS.Documento = '$P_Empleado' 
							EOD;

						if (! empty($P_TipoEmpleados))
							$query .= <<<EOD
								AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
							EOD;

						$query .= $queryValidacionIcetex;

						$this->model->actualizarRegistros($query);

						// SE BORRAN LOS REGISTROS QUE NO SEAN NOVEDADES NI VACACIONES
						$query = <<<EOD
							DELETE $ArchivoNomina 
								FROM $ArchivoNomina 
									INNER JOIN EMPLEADOS 
										ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
									INNER JOIN CENTROS  
										ON EMPLEADOS.IdCentro = CENTROS.Id 
								WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
									$ArchivoNomina.Ciclo = $Ciclo AND 
									$ArchivoNomina.Liquida <> 'N' AND 
									$ArchivoNomina.Liquida <> 'T' AND 
									$ArchivoNomina.Liquida <> 'V' 
						EOD;

						if (! empty($P_IdCentro))
							$query .= <<<EOD
								AND EMPLEADOS.IdCentro = $P_IdCentro 
							EOD;

						if (! empty($P_Empleado))
							$query .= <<<EOD
								AND EMPLEADOS.Documento = '$P_Empleado' 
							EOD;

						if (! empty($P_TipoEmpleados))
							$query .= <<<EOD
								AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
							EOD;

						$query .= $queryValidacionIcetex;

						$this->model->actualizarRegistros($query);

						// PROCESOS AUTOMATICOS
						if ($SoloNovedades == FALSE) 
						{
							$FechaInicial = $FechaInicialPeriodo;
							$FechaFinal = $FechaFinalPeriodo;

							switch ($Periodicidad) 
							{
								case 'QUNCENAL':
									$FactorHoras = 0.5;
									break;
								
								case 'MENSUAL':
									$FactorHoras = 1;
									break;
									
								case 'SEMANAL':
									$FactorHoras = 1 / 30 * 7;
									break;
									
								case 'DECADAL':
									$FactorHoras = 1 / 3;
									break;
										
								case 'CATORCENAL':
									$FactorHoras = 1 / 30 * 14;
									break;
							}

							// SE LEEN LOS EMPLEADOS CON LOS CONCEPTOS AUTOMATICOS
							$query = <<<EOD
								SELECT MAYORES.Id AS IdMayor, 
										MAYORES.TipoLiquidacion, 
										PARAMETROS1.Detalle AS NombreTipoLiquidacion, 
										MAYORES.TipoRetencion, 
										AUXILIARES.Id AS IdConcepto, 
										AUXILIARES.HoraFija, 
										AUXILIARES.ValorFijo, 
										AUXILIARES.FactorConversion, 
										AUXILIARES.TipoRegistroAuxiliar, 
										PARAMETROS2.Detalle AS NombreTipoRegistroAuxiliar, 
										EMPLEADOS.Id AS IdEmpleado, 
										EMPLEADOS.FechaIngreso, 
										EMPLEADOS.FechaVencimiento, 
										EMPLEADOS.FechaRetiro, 
										EMPLEADOS.SueldoBasico, 
										EMPLEADOS.FactorPrestacional, 
										EMPLEADOS.HorasMes, 
										EMPLEADOS.DiasAno, 
										EMPLEADOS.ModalidadTrabajo, 
										PARAMETROS3.Detalle AS NombreModalidadTrabajo, 
										EMPLEADOS.SubsidioTransporte, 
										PARAMETROS4.Detalle AS NombreSubsidioTransporte, 
										EMPLEADOS.RegimenCesantias, 
										PARAMETROS5.Detalle AS NombreRegimenCesantias, 
										EMPLEADOS.IdCentro, 
										EMPLEADOS.IdEPS, 
										EMPLEADOS.IdFondoCesantias, 
										EMPLEADOS.IdFondoPensiones, 
										EMPLEADOS.IdCajaCompensacion, 
										EMPLEADOS.IdARL, 
										CENTROS.TipoEmpleado, 
										EMPLEADOS.TipoContrato, 
										PARAMETROS6.Detalle AS NombreTipoContrato 
									FROM EMPLEADOS 
										RIGHT JOIN AUXILIARES 
											ON AUXILIARES.Id > 0 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1 
											ON MAYORES.TipoLiquidacion = PARAMETROS1.Id 
										INNER JOIN PARAMETROS AS PARAMETROS2 
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id 
										INNER JOIN PARAMETROS AS PARAMETROS3 
											ON EMPLEADOS.ModalidadTrabajo = PARAMETROS3.Id 
										INNER JOIN PARAMETROS AS PARAMETROS4 
											ON EMPLEADOS.SubsidioTransporte = PARAMETROS4.Id 
										LEFT JOIN PARAMETROS AS PARAMETROS5 
											ON EMPLEADOS.RegimenCesantias = PARAMETROS5.Id 
										INNER JOIN PARAMETROS AS PARAMETROS6 
											ON EMPLEADOS.TipoContrato = PARAMETROS6.Id 
										INNER JOIN PARAMETROS AS PARAMETROS7 
											ON EMPLEADOS.Estado = PARAMETROS7.Id 
										INNER JOIN PARAMETROS AS PARAMETROS8
											ON AUXILIARES.ModoLiquidacion = PARAMETROS8.Id 
										INNER JOIN PARAMETROS AS PARAMETROS9 
											ON AUXILIARES.TipoAuxiliar = PARAMETROS9.Id 
									WHERE EMPLEADOS.PeriodicidadPago = $IdPeriodicidad $queryValidacionIcetex AND 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									EMPLEADOS.IdCentro = $P_IdCentro AND 
								EOD;

							if (! empty($P_Empleado))
								$query .= <<<EOD
									EMPLEADOS.Documento = '$P_Empleado' AND 
								EOD;

							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									CENTROS.TipoEmpleado = $P_TipoEmpleados AND 
								EOD;

							if ($Ciclo < 98)
							{
								$query .= <<<EOD
										PARAMETROS7.Detalle = 'ACTIVO' AND 
										EMPLEADOS.FechaIngreso <= '$FechaFinal' AND 
										PARAMETROS8.Detalle = 'AUTOMÁTICO' AND 
										PARAMETROS9.Detalle = 'CONTABLE' AND 
										AUXILIARES.TipoEmpleado = $P_TipoEmpleados  
									ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Id, AUXILIARES.Id ;
								EOD;
							}
							else
							{
								$query .= <<<EOD
										PARAMETROS7.Detalle = 'RETIRADO' AND 
										EMPLEADOS.FechaIngreso <= '$FechaFinal' AND 
										PARAMETROS8.Detalle = 'AUTOMÁTICO' AND 
										PARAMETROS9.Detalle = 'CONTABLE' AND 
										AUXILIARES.TipoEmpleado = $P_TipoEmpleados  
									ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Id, AUXILIARES.Id ;
								EOD;
							}

							$empleados = $this->model->listarRegistros($query);

							$HorasVacaciones = 0;
							$DiasIncapacidad = 0;
							$DiasIncapacidadAcumulados = 0;
							$HorasIncapacidad = 0;

							$IdEmpleadoAnt = 0;

							for ($i = 0; $i < count($empleados) ; $i++) 
							{ 
								$regEmpleado = $empleados[$i];
								$IdEmpleado = $regEmpleado['IdEmpleado'];

								if ($IdEmpleado <> $IdEmpleadoAnt)
								{
									$HorasVacaciones = 0;
									$DiasIncapacidad = 0;
									$DiasIncapacidadAcumulados = 0;
									$HorasIncapacidad = 0;

									$IdEmpleadoAnt = $IdEmpleado;
								}

								$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

								// SE REVISA SI ESTE EMPLEADO YA FUE LIQUIDADO EN EL MISMO PERIODO
								if ($PeriodoAcumulado)
								{
									$query = <<<EOD
										SELECT COUNT(*) AS Registros 
											FROM ACUMULADOS 
												INNER JOIN AUXILIARES 
													ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
												INNER JOIN MAYORES 
													ON AUXILIARES.IdMayor = MAYORES.Id 
												INNER JOIN PARAMETROS 
													ON MAYORES.ClaseConcepto = PARAMETROS.Id 
											WHERE ACUMULADOS.Ciclo = $Ciclo AND 
												ACUMULADOS.IdEmpleado = $IdEmpleado AND 
												ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
												ACUMULADOS.FechaFinalPeriodo= '$FechaFinalPeriodo' AND 
												PARAMETROS.Detalle = 'SALARIO'; 
									EOD; 

									$regProcesado = $this->model->leerRegistro($query);

									if ($regProcesado['Registros'] > 0 AND ($Ciclo == 1 OR $Ciclo == 20 OR $Ciclo == 21))
										$LiquidaAutomatico = FALSE;
									else
										$LiquidaAutomatico = TRUE;
								}
								else
									$LiquidaAutomatico = TRUE;

								if ($Ciclo <> 1 AND $Ciclo <> 20 AND $Ciclo <> 21 AND $Ciclo <> 98)
									$LiquidaAutomatico = FALSE;

								if ($LiquidaAutomatico) 
								{
									$Horas = 0;
									$ValorNovedad = 0;

									if ($regEmpleado['FechaRetiro'] >= $regPeriodo['fechainicial'] AND $regEmpleado['FechaRetiro'] <= $regPeriodo['fechafinal']) 
										$FechaFinal = $regEmpleado['FechaRetiro'];
									else
									{
										if (substr($regPeriodo['fechafinal'], 8, 2) == 31) 
											$FechaFinal = date('Y-m-d', strtotime($regPeriodo['fechafinal'] . ' - 1 days')); 
										else
											$FechaFinal = $regPeriodo['fechafinal'];
									}

									if ($regEmpleado['FechaIngreso'] >= $regPeriodo['fechainicial'] AND 
										$regEmpleado['FechaIngreso'] <= $regPeriodo['fechafinal'])
										$FechaInicial = $regEmpleado['FechaIngreso'];
									else
										$FechaInicial = $regPeriodo['fechainicial'];

									if (($Periodicidad == 'QUINCENAL' OR $Periodicidad == 'MENSUAL') AND $regEmpleado['DiasAno'] == 365)
										$Horas = ((strtotime($FechaFinal) - strtotime($FechaInicial)) / 86400 + 1) * 8;
									else
									{
										if ($FechaInicial == $regPeriodo['fechainicial'] AND $FechaFinal == $regPeriodo['fechafinal'])
										{
											if ($regEmpleado['HoraFija'] == 0)
												$Horas = $regEmpleado['HorasMes'] * $FactorHoras;
											else
												$Horas = $regEmpleado['HoraFija'];
										}
										else
											$Horas =((strtotime($FechaFinal) - strtotime($FechaInicial)) / 86400 + 1) * 8;
									}

									if ($regEmpleado['HorasMes'] == 120) 
										$Horas /= 2;

									// SUELDO BASICO
									if ($regEmpleado['NombreTipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO' AND 
										$regEmpleado['NombreModalidadTrabajo'] == 'SUELDO BÁSICO' AND
										$regEmpleado['NombreRegimenCesantias'] <> 'SALARIO INTEGRAL' AND 
										$regEmpleado['NombreTipoContrato'] <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND
										$regEmpleado['NombreTipoContrato'] <> 'APRENDIZAJE - ETAPA LECTIVA' AND
										$regEmpleado['NombreTipoContrato'] <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND
										$regEmpleado['NombreTipoContrato'] <> 'PASANTÍA	') 
									{
										// SE APLICAN LAS VACACIONES
										$query = <<<EOD
											SELECT VACACIONES.IdEmpleado, 
													VACACIONES.SalarioBase, 
													VACACIONES.DiasALiquidar,
													VACACIONES.DiasEnTiempo, 
													VACACIONES.DiasFestivos, 
													VACACIONES.Dias31, 
													VACACIONES.ValorLiquidado, 
													VACACIONES.DiasEnDinero, 
													VACACIONES.ValorEnDinero, 
													VACACIONES.FechaInicio, 
													VACACIONES.FechaIngreso,
													VACACIONES.ValorFestivos,
													VACACIONES.ValorDia31
												FROM VACACIONES 
												WHERE VACACIONES.IdEmpleado = $IdEmpleado AND 
													VACACIONES.FechaInicio >= '$FechaInicialPeriodo' AND
													VACACIONES.FechaInicio <= '$FechaFinalPeriodo'; 
										EOD;

										$vacaciones = $this->model->listarRegistros($query);

										if ($vacaciones) 
										{
											$HorasVacaciones = 0;
											for ($j = 0; $j < count($vacaciones); $j++) 
											{ 
												$regVacaciones = $vacaciones[$j];

												if ($regVacaciones['DiasEnTiempo'] > 0 ) 
												{
													$FechaInicioVacaciones = $regVacaciones['FechaInicio'];

													if ($FechaInicioVacaciones < $FechaInicial) 
														$FechaInicioVacaciones = $FechaInicial;

													$FechaFinalVacaciones = date('Y-m-d', strtotime($regVacaciones['FechaIngreso'] . ' - 1 day'));

													if ($FechaFinalVacaciones > $FechaFinalPeriodo) 
														$FechaFinalVacaciones = $FechaFinalPeriodo;

													if ($FechaInicioVacaciones <= $FechaFinal AND
														$FechaFinalVacaciones >= $FechaInicial) 
													{
														$dias31 = isset($regVacaciones['dias31']) ? $regVacaciones['dias31'] : $regVacaciones['Dias31'];
														$currentHorasVacaciones = ($regVacaciones['DiasALiquidar']+$regVacaciones['DiasFestivos']+$dias31) * 8;

														if ($currentHorasVacaciones > $regEmpleado['HorasMes']) 
															$currentHorasVacaciones = $regEmpleado['HorasMes'];

														$SalarioBase = $regVacaciones['SalarioBase'];
														$TipoRetencion = $regEmpleado['TipoRetencion'];
														$IdCentro = $regEmpleado['IdCentro'];
														$TipoEmpleado = $regEmpleado['TipoEmpleado'];

														$ValorNovedad = $regVacaciones['ValorLiquidado']+$regVacaciones['ValorFestivos']+$regVacaciones['ValorDia31'];

														$Horas -= $currentHorasVacaciones;
														$HorasVacaciones += $currentHorasVacaciones;

														$FechaInicio = $regVacaciones['FechaInicio'];
														$FechaIngreso = date('Y-m-d', strtotime($regVacaciones['FechaIngreso'] . ' - 1 day'));

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVT, $SalarioBase, 0, $currentHorasVacaciones, $ValorNovedad, 0, $FechaInicio, $FechaIngreso, 'A', $TipoRetencionVT, $IdCentro, $TipoEmpleado, 0, 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}
													else
														continue;
												}
												elseif($regVacaciones['DiasEnDinero'] > 0)
												{
													$HorasVacacionesDinero = $regVacaciones['DiasEnDinero'] * 8;
													$SalarioBase = $regVacaciones['SalarioBase'];
													$TipoRetencion = $regEmpleado['TipoRetencion'];
													$IdCentro = $regEmpleado['IdCentro'];
													$TipoEmpleado = $regEmpleado['TipoEmpleado'];

													$ValorNovedad = $regVacaciones['ValorEnDinero'];

													$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVD, $SalarioBase, 0, $HorasVacacionesDinero, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencionVD, $IdCentro, $TipoEmpleado, 0, 0);
													$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
												}
												else
													continue;
											}
										}
										else
										{
											$HorasVacaciones = 0;
											
											// SE BUSCAN CONCEPTOS DE VACACIONES PARA AJUSTAR LOS DIAS TRABAJADOS
											$query = <<<EOD
												SELECT $ArchivoNomina.* 
													FROM $ArchivoNomina 
														INNER JOIN AUXILIARES 
															ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
														INNER JOIN PARAMETROS 
															ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
													WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
														PARAMETROS.Detalle = 'ES VACACIONES EN TIEMPO';
											EOD;

											$vacaciones = $this->model->listarRegistros($query);

											if ($vacaciones) 
											{
												for ($j = 0; $j < count($vacaciones); $j++) 
												{ 
													$regVacaciones = $vacaciones[$j];

													$Horas -= $regVacaciones['Horas'];
												}
											}
										}

										// SE APLICAN LAS INCAPACIDADES
										$query = <<<EOD
											SELECT INCAPACIDADES.IdEmpleado, 
													INCAPACIDADES.IdConcepto, 
													AUXILIARES.FactorConversion, 
													PARAMETROS4.Detalle AS Imputacion, 
													INCAPACIDADES.FechaInicio, 
													INCAPACIDADES.DiasIncapacidad, 
													INCAPACIDADES.DiasCausados, 
													INCAPACIDADES.PorcentajeAuxilio, 
													INCAPACIDADES.BaseLiquidacion, 
													INCAPACIDADES.EsProrroga, 
													PARAMETROS1.Detalle AS TipoRegistroAuxiliar, 
													PARAMETROS2.Detalle AS CuasaAusentismo, 
													PARAMETROS3.Detalle AS NombreBaseLiquidacion 
												FROM INCAPACIDADES 
													INNER JOIN AUXILIARES 
														ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
													INNER JOIN MAYORES 
														ON AUXILIARES.IdMayor = MAYORES.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS1 
														ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS1.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS2 
														ON INCAPACIDADES.CausaAusentismo = PARAMETROS2.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS3 
														ON INCAPACIDADES.BaseLiquidacion = PARAMETROS3.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS4 
														ON AUXILIARES.Imputacion = PARAMETROS4.Id 
												WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado AND 
													INCAPACIDADES.DiasIncapacidad > INCAPACIDADES.DiasCausados 
												ORDER BY INCAPACIDADES.FechaInicio; 
										EOD;

										$incapacidades = $this->model->listarRegistros($query);
										$incapacidadesAcumuladas = [];

										// INCAPACIDADES - AUSENCIAS
										if ($incapacidades) 
										{
											for ($j = 0; $j < count($incapacidades); $j++) 
											{ 
												$regIncapacidad = $incapacidades[$j];
												$IdConcepto = $regIncapacidad['IdConcepto'];

												$FechaInicioIncapacidad = $regIncapacidad['FechaInicio'];

												// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
												$query = <<<EOD
													SELECT AUMENTOSSALARIALES.FechaAumento, 
															AUMENTOSSALARIALES.SueldoBasico, 
															AUMENTOSSALARIALES.SueldoBasicoAnterior 
														FROM AUMENTOSSALARIALES 
														WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
															AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
															AUMENTOSSALARIALES.Procesado = 0;
												EOD;

												$regAumento = $this->model->leerRegistro($query);

												if ($regAumento) 
												{
													if ($regIncapacidad['FechaInicio'] >= $regAumento['FechaAumento'])
														$SueldoBasico = $regAumento['SueldoBasico'];
													else
														$SueldoBasico = $regEmpleado['SueldoBasico'];
												}
												else
													$SueldoBasico = $regEmpleado['SueldoBasico'];

												$FechaFinalIncapacidad = date('Y-m-d', strtotime($regIncapacidad['FechaInicio'] . ' + ' . ($regIncapacidad['DiasIncapacidad'] - 1) . ' days'));
												array_push($incapacidadesAcumuladas, $regIncapacidad);

												if ($FechaFinalIncapacidad > $FechaFinal) 
													$FechaFinalIncapacidad = $FechaFinal;

												if ($FechaInicioIncapacidad <= $FechaFinal AND
													$FechaFinalIncapacidad >= $FechaInicial) 
												{
													$DiasIncapacidad = $regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados'];

													// ESTO ES PARA INCAPACIDADES QUE ENTRAN Y SALEN, NO SE HAGA DOBLE COBRO DE DIAS
													$esIncapacidadAnteriorPagada = FALSE;
													foreach ($incapacidadesAcumuladas as $currentIncapacidad) {
														$esIncapacidadAnteriorPagada = (
															$this->compareObjects($currentIncapacidad, $regIncapacidad, 'Imputacion') AND
															$currentIncapacidad['Imputacion'] == 'PAGO');
														if ($esIncapacidadAnteriorPagada) {
															unset($currentIncapacidad);
															break;
														}
													}

													if ($DiasIncapacidadAcumulados + $DiasIncapacidad > 30 AND $regIncapacidad['Imputacion'] == 'PAGO')
													{
														$DiasIncapacidad = 30 - $DiasIncapacidadAcumulados;
														$DiasIncapacidadAcumulados = 30;
													}
													elseif (!$esIncapacidadAnteriorPagada)
														$DiasIncapacidadAcumulados += $DiasIncapacidad;

													// VALIDAR LOS DIAS RESTANTES DEL MES SI LA INCAPACIDAD SOBREPASA EL PERIODO
													if ($regEmpleado['HorasMes'] == 120)
														$HorasIncapacidad = $DiasIncapacidad * 4;
													else
														$HorasIncapacidad = $DiasIncapacidad * 8;
	
													if ($HorasIncapacidad > $regEmpleado['HorasMes']) 
														$HorasIncapacidad = $regEmpleado['HorasMes'];

													$TipoRetencion = $regEmpleado['TipoRetencion'];
													$IdCentro = $regEmpleado['IdCentro'];
													$TipoEmpleado = $regEmpleado['TipoEmpleado'];

													if ($regIncapacidad['NombreBaseLiquidacion'] == 'IBC MES ANTERIOR')
													{
														// CALCULAMOS IBC ANTERIOR
														if ($regIncapacidad['TipoRegistroAuxiliar'] <> 'ES INCAPACIDAD EN TIEMPO' AND 
															$regIncapacidad['TipoRegistroAuxiliar'] <> 'ES INCAPACIDAD PROFESIONAL') 
															$ValorIBCAnterior = $regAumento ? $regAumento['SueldoBasico'] : $regEmpleado['SueldoBasico'];
														else
														{
															$ValorIBCAnterior = $this->CalcularValorIBC($IdEmpleado, $FechaInicialPeriodoAnterior, $FechaFinalPeriodoAnterior, $SueldoMinimo, "INCAPACIDAD");

															if ($ValorIBCAnterior == 0)
																$ValorIBCAnterior = $SueldoBasico;
														}
													}
													else
													{
														$ValorIBCAnterior = $SueldoBasico;
													}

													if ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD EN TIEMPO' OR $regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD PROFESIONAL')
													{
														if ($regIncapacidad['DiasIncapacidad'] <= 2)
														{
															if ($regEmpleado['HorasMes'] == 120)
																$HorasAuxilioIncapacidad = $regIncapacidad['DiasIncapacidad'] * 2;
															else
																$HorasAuxilioIncapacidad = $regIncapacidad['DiasIncapacidad'] * 8;

															$FactorConversion = $regIncapacidad['FactorConversion'];
															$ValorNovedad = 0;
														}
														elseif ($regIncapacidad['DiasIncapacidad'] <= 90)
														{
															if ($regIncapacidad['DiasCausados'] == 0)
															{
																if ($regIncapacidad['EsProrroga'] == 1)
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
																else
																{
																	$HorasAuxilioIncapacidad = 16;
																	$HorasIncapacidad -= 16;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
															}
															else
															{
																$HorasAuxilioIncapacidad = 0;
																$FactorConversion = $regIncapacidad['FactorConversion'];
															}

															$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $FactorConversion, 0);

															if ($ValorNovedad < round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0)) 
																$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0);
														}
														else   // INCAPACIDAD > 180 DIAS
														{
															if ($regIncapacidad['DiasCausados'] + ($HorasIncapacidad / 8) > 90) 
															{
																if ($regIncapacidad['DiasCausados'] == 0)
																{
																	if ($regIncapacidad['EsProrroga'] == 1)
																	{
																		$HorasAuxilioIncapacidad = 0;
																		$FactorConversion = 0.5;
																	}
																	else
																	{
																		$HorasAuxilioIncapacidad = 16;
																		$HorasIncapacidad -= 16;
																		$FactorConversion = $regIncapacidad['FactorConversion'];
																	}
																}
																else
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
		
																// PRIMEROS 90 DIAS
																$HorasIncapacidad1 = (90 - $regIncapacidad['DiasCausados']) * 8 - $HorasAuxilioIncapacidad;
																$FactorConversion1 = $regIncapacidad['FactorConversion'];
																// DIAS POSTERIORES AL DIA 90
																$HorasIncapacidad2 = ($regIncapacidad['DiasCausados'] + ($HorasIncapacidad / 8) - 90) * 8 - $HorasAuxilioIncapacidad;
																$HorasIncapacidad2 = max($HorasIncapacidad2, 0);
																$FactorConversion2 = 0.5;

																$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad1 * $FactorConversion1, 0);
																$ValorNovedad += round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad2 * $FactorConversion2, 0);
															}
															else
															{
																if ($regIncapacidad['DiasCausados'] == 0)
																{
																	if ($regIncapacidad['EsProrroga'] == 1)
																	{
																		$HorasAuxilioIncapacidad = 0;
																		$FactorConversion = 0.5;
																	}
																	else
																	{
																		$HorasAuxilioIncapacidad = 16;
																		$HorasIncapacidad -= 16;
																		$FactorConversion = $regIncapacidad['FactorConversion'];
																	}
																}
																else
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}

																$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * ($HorasIncapacidad) * $FactorConversion, 0);
															}
														}
													}
													else
													{
														if ($ValorIBCAnterior < $SueldoMinimo)
															$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad * $regIncapacidad['FactorConversion'], 0);
														else
															$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $regIncapacidad['FactorConversion'], 0);

														$HorasAuxilioIncapacidad = 0;
													}

													if ($ValorNovedad > 0)
													{
														if ($regIncapacidad['Imputacion'] == 'PAGO')
															$Horas -= $HorasIncapacidad;

														$idTercero = 0; // ID PARA CUANDO DETALLA POR EMPLEADO
														if (in_array($IdConcepto, array(289, 360, 362)))
															$idTercero = $regEmpleado['IdEPS'];

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $ValorIBCAnterior, 0, $HorasIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, $idTercero, 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}

													if ($regIncapacidad['DiasIncapacidad'] <= 2)
														$HorasIncapacidad = 0;

													// GENERAR AUXILIO DE INCAPACIDAD SI EXISTE.
													if ($HorasAuxilioIncapacidad > 0)
													{
														if ($regIncapacidad['PorcentajeAuxilio'] > 0) 
														{
															$query = <<<EOD
																SELECT AUXILIARES.Id, 
																		MAYORES.TipoRetencion 
																	FROM AUXILIARES 
																		INNER JOIN MAYORES 
																			ON AUXILIARES.IdMayor = MAYORES.Id
																		INNER JOIN PARAMETROS 
																			ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
																	WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
																		PARAMETROS.Detalle = 'ES AUXILIO POR INCAPACIDAD';
															EOD;
															
															$auxilio = $this->model->listarRegistros($query);

															if ($auxilio) 
															{
																for ($k = 0; $k < count($auxilio); $k++) 
																{ 
																	$regAuxilio = $auxilio[$k];

																	$IdConcepto = $regAuxilio['Id'];
																	$TipoRetencion = $regAuxilio['TipoRetencion'];

																	$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * ($HorasIncapacidad + $HorasAuxilioIncapacidad), 0) - $ValorNovedad;

																	$Horas -= $HorasAuxilioIncapacidad;

																	if ($ValorNovedad > 0) 
																	{
																		$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $regEmpleado['SueldoBasico'], 0,$HorasIncapacidad + $HorasAuxilioIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
																		$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
																	}
																}
															}
														}
														else
														{
															// SE GENERA COMO AUXILIO INCAPACIDAD LOS 2 PRIMEROS DIAS SIN EL PORCENTAJE DE AUXILIO SOLO EL 66%
															$query = <<<EOD
																SELECT AUXILIARES.Id, 
																		MAYORES.TipoRetencion 
																	FROM AUXILIARES 
																		INNER JOIN MAYORES 
																			ON AUXILIARES.IdMayor = MAYORES.Id
																		INNER JOIN PARAMETROS 
																			ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
																	WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
																		PARAMETROS.Detalle = 'ES AUXILIO POR INCAPACIDAD';
															EOD;
															
															$auxilio = $this->model->listarRegistros($query);

															if ($auxilio) 
															{
																for ($k = 0; $k < count($auxilio); $k++) 
																{ 
																	$regAuxilio = $auxilio[$k];

																	$IdConcepto = $regAuxilio['Id'];
																	$TipoRetencion = $regAuxilio['TipoRetencion'];

																	if ($regEmpleado['HorasMes'] == 120)
																	{
																		if ($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * 4 * .6667 * 30  < $SueldoMinimo)
																		{
																			$BaseIncapacidad = $SueldoMinimo;
																			$ValorNovedad = round($SueldoMinimo / 120 * $HorasAuxilioIncapacidad, 0);
																		}														else
																		{
																			$BaseIncapacidad = $regEmpleado['SueldoBasico'];
																			$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAuxilioIncapacidad * 0.6667, 0);
																		}
																	}
																	else
																	{
																		if ($regEmpleado['SueldoBasico'] / 240 * 8 * .6667 * 30  < $SueldoMinimo)
																		{
																			$BaseIncapacidad = $SueldoMinimo;
																			$ValorNovedad = round($SueldoMinimo / 240 * $HorasAuxilioIncapacidad, 0);
																		}
																		else
																		{
																			$BaseIncapacidad = $regEmpleado['SueldoBasico'];
																			$ValorNovedad = round($regEmpleado['SueldoBasico'] / 240 * $HorasAuxilioIncapacidad * 0.6667, 0);
																		}
																	}

																	$Horas -= $HorasAuxilioIncapacidad;

																	if ($ValorNovedad > 0) 
																	{
																		$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $BaseIncapacidad, 0, $HorasAuxilioIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
																		$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
																	}
																}
															}
														}
													}
												}
												else
													continue;
											}
										}
										else
										{
											$DiasIncapacidad = 0;
											$HorasIncapacidad = 0;
										}

										// SE APLICAN LOS AUMENTOS
										$query = <<<EOD
											SELECT AUMENTOSSALARIALES.FechaAumento, 
													AUMENTOSSALARIALES.SueldoBasico, 
													AUMENTOSSALARIALES.SueldoBasicoAnterior 
												FROM AUMENTOSSALARIALES 
												WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
													AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
													AUMENTOSSALARIALES.Procesado = 0;
										EOD;

										$regAumento = $this->model->leerRegistro($query);

										if ($regAumento) 
										{
											if ($regAumento['FechaAumento'] < $FechaInicial)
											{
												if (substr(FinMes($regAumento['FechaAumento']), 8, 2) == '31')
													$HorasAumento = (dias360($FechaInicial, $regAumento['FechaAumento']) - 1) * 8;
												else
													$HorasAumento = (dias360($FechaInicial, $regAumento['FechaAumento']) - 1) * 8;
											
												$ValorAumento = round($regAumento['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
												$ValorAumento -= round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
												$HorasAumento = 0;
											}
											elseif ($regAumento['FechaAumento'] > $FechaInicial)
											{
												if (substr($regAumento['FechaAumento'], 8, 2) == '31')
													$HorasAumento = (dias360($regAumento['FechaAumento'], $FechaInicial) - 1) * 8;
												else
													$HorasAumento = (dias360($regAumento['FechaAumento'], $FechaInicial) - 1) * 8;

												$ValorAumento = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
											}
											else
											{
												$HorasAumento = getHoursMonth();
												$ValorAumento = round($regAumento['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
											}
										}
										else
											$ValorAumento = 0;

										if ($regEmpleado['NombreTipoLiquidacion'] == 'HORAS' OR
											$regEmpleado['NombreTipoLiquidacion'] == 'DÍAS')
										{
											if ($ValorAumento <> 0) 
											{
												$ValorNovedad = round($regAumento['SueldoBasico'] / $regEmpleado['HorasMes'] * ($Horas - $HorasAumento) * $regEmpleado['FactorConversion'], 0);
												
												$ValorNovedad += $ValorAumento;
											}
											else
												$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $Horas * $regEmpleado['FactorConversion'], 0);
										}
										else
										{
											$Horas = 0;
											$ValorNovedad = round($regEmpleado['ValorFijo'] * $regEmpleado['FactorConversion'], 0);

											if ($HorasAumento > 0) 
												$ValorNovedad += $ValorAumento;
										}
									}
									// SUELDO BASICO SALARIO INTEGRAL
									elseif ($regEmpleado['NombreTipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' AND 
										$regEmpleado['NombreModalidadTrabajo'] == 'SUELDO BÁSICO' AND 
										$regEmpleado['NombreRegimenCesantias'] == 'SALARIO INTEGRAL')
									{
										// SE APLICAN LAS VACACIONES
										$query = <<<EOD
											SELECT VACACIONES.IdEmpleado, 
													VACACIONES.SalarioBase, 
													VACACIONES.DiasALiquidar,
													VACACIONES.DiasEnTiempo, 
													VACACIONES.DiasFestivos, 
													VACACIONES.Dias31, 
													VACACIONES.ValorLiquidado, 
													VACACIONES.DiasEnDinero, 
													VACACIONES.ValorEnDinero, 
													VACACIONES.FechaInicio, 
													VACACIONES.FechaIngreso,
													VACACIONES.ValorFestivos,
													VACACIONES.ValorDia31
												FROM VACACIONES 
												WHERE VACACIONES.IdEmpleado = $IdEmpleado AND 
													VACACIONES.FechaInicio >= '$FechaInicialPeriodo' AND
													VACACIONES.FechaInicio <= '$FechaFinalPeriodo'; 
										EOD;

										$vacaciones = $this->model->listarRegistros($query);

										if ($vacaciones) 
										{
											$HorasVacaciones=0;
											for ($j = 0; $j < count($vacaciones); $j++) 
											{ 
												$regVacaciones = $vacaciones[$j];

												if ($regVacaciones['DiasEnTiempo'] > 0 ) 
												{
													$FechaInicioVacaciones = $regVacaciones['FechaInicio'];

													if ($FechaInicioVacaciones < $FechaInicial) 
														$FechaInicioVacaciones = $FechaInicial;

													$FechaFinalVacaciones = date('Y-m-d', strtotime($regVacaciones['FechaIngreso'] . ' - 1 day'));

													if ($FechaFinalVacaciones > $FechaFinalPeriodo) 
														$FechaFinalVacaciones = $FechaFinalPeriodo;

													if ($FechaInicioVacaciones <= $FechaFinal AND
														$FechaFinalVacaciones >= $FechaInicial) 
													{
														$dias31 = isset($regVacaciones['dias31']) ? $regVacaciones['dias31'] : $regVacaciones['Dias31'];
														$currentHorasVacaciones = ($regVacaciones['DiasALiquidar']+$regVacaciones['DiasFestivos']+$dias31) * 8;

														if ($currentHorasVacaciones > $regEmpleado['HorasMes']) 
															$currentHorasVacaciones = $regEmpleado['HorasMes'];

														$SalarioBase = $regVacaciones['SalarioBase'];
														$TipoRetencion = $regEmpleado['TipoRetencion'];
														$IdCentro = $regEmpleado['IdCentro'];
														$TipoEmpleado = $regEmpleado['TipoEmpleado'];

														$ValorNovedad = $regVacaciones['ValorLiquidado']+$regVacaciones['ValorFestivos']+$regVacaciones['ValorDia31'];

														$Horas -= $currentHorasVacaciones;
														$HorasVacaciones += $currentHorasVacaciones;

														$FechaInicio = $regVacaciones['FechaInicio'];
														$FechaIngreso = date('Y-m-d', strtotime($regVacaciones['FechaIngreso'] . ' - 1 day'));

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVT, $SalarioBase, 0, $currentHorasVacaciones, $ValorNovedad, 0, $FechaInicio, $FechaIngreso, 'A', $TipoRetencionVT, $IdCentro, $TipoEmpleado, 0, 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}
													else
														continue;
												}
												elseif($regVacaciones['DiasEnDinero'] > 0)
												{
													$HorasVacacionesDinero = $regVacaciones['DiasEnDinero'] * 8;
													$SalarioBase = $regVacaciones['SalarioBase'];
													$TipoRetencion = $regEmpleado['TipoRetencion'];
													$IdCentro = $regEmpleado['IdCentro'];
													$TipoEmpleado = $regEmpleado['TipoEmpleado'];

													$ValorNovedad = $regVacaciones['ValorEnDinero'];

													$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVD, $SalarioBase, 0, $HorasVacacionesDinero, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencionVD, $IdCentro, $TipoEmpleado, 0, 0);
													$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
												}
												else
													continue;
											}
										}
										else
										{
											$HorasVacaciones = 0;
											
											// SE BUSCAN CONCEPTOS DE VACACIONES PARA AJUSTAR LOS DIAS TRABAJADOS
											$query = <<<EOD
												SELECT $ArchivoNomina.* 
													FROM $ArchivoNomina 
														INNER JOIN AUXILIARES 
															ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
														INNER JOIN PARAMETROS 
															ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
													WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
														PARAMETROS.Detalle = 'ES VACACIONES EN TIEMPO';
											EOD;

											$vacaciones = $this->model->listarRegistros($query);

											if ($vacaciones) 
											{
												for ($j = 0; $j < count($vacaciones); $j++) 
												{ 
													$regVacaciones = $vacaciones[$j];

													$Horas -= $regVacaciones['Horas'];
												}
											}
										}

										// SE APLICAN LAS INCAPACIDADES
										$query = <<<EOD
											SELECT INCAPACIDADES.IdEmpleado, 
													INCAPACIDADES.IdConcepto, 
													AUXILIARES.FactorConversion, 
													PARAMETROS4.Detalle AS Imputacion, 
													INCAPACIDADES.FechaInicio, 
													INCAPACIDADES.DiasIncapacidad, 
													INCAPACIDADES.DiasCausados, 
													INCAPACIDADES.PorcentajeAuxilio, 
													INCAPACIDADES.BaseLiquidacion, 
													INCAPACIDADES.EsProrroga, 
													PARAMETROS1.Detalle AS TipoRegistroAuxiliar, 
													PARAMETROS2.Detalle AS CuasaAusentismo, 
													PARAMETROS3.Detalle AS NombreBaseLiquidacion 
												FROM INCAPACIDADES 
													INNER JOIN AUXILIARES 
														ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
													INNER JOIN MAYORES 
														ON AUXILIARES.IdMayor = MAYORES.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS1 
														ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS1.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS2 
														ON INCAPACIDADES.CausaAusentismo = PARAMETROS2.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS3 
														ON INCAPACIDADES.BaseLiquidacion = PARAMETROS3.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS4 
														ON AUXILIARES.Imputacion = PARAMETROS4.Id 
												WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado AND 
													INCAPACIDADES.DiasIncapacidad > INCAPACIDADES.DiasCausados 
												ORDER BY INCAPACIDADES.FechaInicio; 
										EOD;

										$incapacidades = $this->model->listarRegistros($query);

										if ($incapacidades) 
										{
											for ($j = 0; $j < count($incapacidades); $j++) 
											{ 
												$regIncapacidad = $incapacidades[$j];
												$IdConcepto = $regIncapacidad['IdConcepto'];

												$FechaInicioIncapacidad = $regIncapacidad['FechaInicio'];

												// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
												$query = <<<EOD
													SELECT AUMENTOSSALARIALES.FechaAumento, 
															AUMENTOSSALARIALES.SueldoBasico, 
															AUMENTOSSALARIALES.SueldoBasicoAnterior 
														FROM AUMENTOSSALARIALES 
														WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
															AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
															AUMENTOSSALARIALES.Procesado = 0;
												EOD;

												$regAumento = $this->model->leerRegistro($query);

												if ($regAumento) 
												{
													if ($regIncapacidad['FechaInicio'] >= $regAumento['FechaAumento'])
														$SueldoBasico = $regAumento['SueldoBasico'];
													else
														$SueldoBasico = $regEmpleado['SueldoBasico'];
												}
												else
													$SueldoBasico = $regEmpleado['SueldoBasico'];

												// if ($FechaInicioIncapacidad < $FechaInicial) 
												// 	$FechaInicioIncapacidad = $FechaInicial;

												$DiasIncapacidad = $regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados'];

												// if ($regIncapacidad['Imputacion'] == 'PAGO')
													if ($DiasIncapacidadAcumulados + $DiasIncapacidad > 30)
													{
														$DiasIncapacidad = 30 - $DiasIncapacidadAcumulados;
														$DiasIncapacidadAcumulados = 30;
													}
													else
														$DiasIncapacidadAcumulados += $DiasIncapacidad;
												

												$FechaFinalIncapacidad = date('Y-m-d', strtotime($regIncapacidad['FechaInicio'] . ' + ' . ($regIncapacidad['DiasIncapacidad'] - 1) . ' days'));

												if ($FechaFinalIncapacidad > $FechaFinal) 
													$FechaFinalIncapacidad = $FechaFinal;

												if ($FechaInicioIncapacidad <= $FechaFinal AND
													$FechaFinalIncapacidad >= $FechaInicial) 
												{
													// VALIDAR LOS DIAS RESTANTES DEL MES SI LA INCAPACIDAD SOBREPASA EL PERIODO
													if ($regEmpleado['HorasMes'] == 120)
														$HorasIncapacidad = $DiasIncapacidad * 4;
													else
														$HorasIncapacidad = $DiasIncapacidad * 8;
	
													if ($HorasIncapacidad > $regEmpleado['HorasMes']) 
														$HorasIncapacidad = $regEmpleado['HorasMes'];

													$TipoRetencion = $regEmpleado['TipoRetencion'];
													$IdCentro = $regEmpleado['IdCentro'];
													$TipoEmpleado = $regEmpleado['TipoEmpleado'];

													if ($regIncapacidad['NombreBaseLiquidacion'] == 'IBC MES ANTERIOR')
													{
														// CALCULAMOS IBC ANTERIOR
														if ($regIncapacidad['TipoRegistroAuxiliar'] <> 'ES INCAPACIDAD EN TIEMPO' AND 
															$regIncapacidad['TipoRegistroAuxiliar'] <> 'ES INCAPACIDAD PROFESIONAL') 
															$ValorIBCAnterior = $regEmpleado['SueldoBasico'];
														else
														{
															$ValorIBCAnterior = $this->CalcularValorIBC($IdEmpleado, $FechaInicialPeriodoAnterior, $FechaFinalPeriodoAnterior, $SueldoMinimo);

															if ($ValorIBCAnterior == 0)
																$ValorIBCAnterior = $SueldoBasico;
														}
													}
													else
													{
														$ValorIBCAnterior = $SueldoBasico;
													}

													// if ($regEmpleado['HorasMes'] == 120) 
													// 	$HorasIncapacidad /= 2;

													if ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD EN TIEMPO' OR $regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD PROFESIONAL')
													{
														if ($regIncapacidad['DiasIncapacidad'] <= 2)
														{
															if ($regEmpleado['HorasMes'] == 120)
																$HorasAuxilioIncapacidad = $regIncapacidad['DiasIncapacidad'] * 4;
															else
																$HorasAuxilioIncapacidad = $regIncapacidad['DiasIncapacidad'] * 8;

															$FactorConversion = $regIncapacidad['FactorConversion'];
															$ValorNovedad = 0;
															// $ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $FactorConversion, 0);

															// if ($ValorNovedad < round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0)) 
															// 	$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0);
														}
														elseif ($regIncapacidad['DiasIncapacidad'] <= 90)
														{
															if ($regIncapacidad['DiasCausados'] == 0)
															{
																if ($regIncapacidad['EsProrroga'] == 1)
																{
																	$HorasAuxilioIncapacidad = 0;
																	// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
																else
																{
																	$HorasAuxilioIncapacidad = 16;
																	$HorasIncapacidad -= 16;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
															}
															else
															{
																$HorasAuxilioIncapacidad = 0;
																// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																$FactorConversion = $regIncapacidad['FactorConversion'];
															}

															$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $FactorConversion, 0);

															if ($ValorNovedad < round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0)) 
																$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0);
														}
														else   // INCAPACIDAD > 180 DIAS
														{
															if ($regIncapacidad['DiasCausados'] + ($HorasIncapacidad / 8) > 90) 
															{
																if ($regIncapacidad['DiasCausados'] == 0)
																{
																	if ($regIncapacidad['EsProrroga'] == 1)
																	{
																		$HorasAuxilioIncapacidad = 0;
																		// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																		$FactorConversion = 0.5;
																	}
																	else
																	{
																		$HorasAuxilioIncapacidad = 16;
																		$HorasIncapacidad -= 16;
																		$FactorConversion = $regIncapacidad['FactorConversion'];
																	}
																}
																else
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
		
																// PRIMEROS 90 DIAS
																$HorasIncapacidad1 = (90 - $regIncapacidad['DiasCausados']) * 8 - $HorasAuxilioIncapacidad;
																$FactorConversion1 = $regIncapacidad['FactorConversion'];
																// DIAS POSTERIORES AL DIA 90
																$HorasIncapacidad2 = ($regIncapacidad['DiasCausados'] + ($HorasIncapacidad / 8) - 90) * 8 - $HorasAuxilioIncapacidad;
																$HorasIncapacidad2 = max($HorasIncapacidad2, 0);
																$FactorConversion2 = 0.5;

																$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad1 * $FactorConversion1, 0);
																$ValorNovedad += round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad2 * $FactorConversion2, 0);
															}
															else
															{
																if ($regIncapacidad['DiasCausados'] == 0)
																{
																	if ($regIncapacidad['EsProrroga'] == 1)
																	{
																		$HorasAuxilioIncapacidad = 0;
																		// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																		$FactorConversion = 0.5;
																	}
																	else
																	{
																		$HorasAuxilioIncapacidad = 16;
																		$HorasIncapacidad -= 16;
																		$FactorConversion = $regIncapacidad['FactorConversion'];
																	}
																}
																else
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}

																$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * ($HorasIncapacidad) * $FactorConversion, 0);
															}
														}
													}
													else
													{
														if ($ValorIBCAnterior < $SueldoMinimo)
															$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad * $regIncapacidad['FactorConversion'], 0);
														else
															$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $regIncapacidad['FactorConversion'], 0);

														$HorasAuxilioIncapacidad = 0;
														// $Horas = $HorasIncapacidad;
													}


													if ($ValorNovedad > 0)
													{
														if ($regIncapacidad['Imputacion'] == 'PAGO')
															$Horas -= $HorasIncapacidad;

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $ValorIBCAnterior, 0, $HorasIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, $regEmpleado['IdEPS'], 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}

													if ($regIncapacidad['DiasIncapacidad'] <= 2)
														$HorasIncapacidad = 0;

													// GENERAR AUXILIO DE INCAPACIDAD SI EXISTE.
													if ($HorasAuxilioIncapacidad > 0)
													{
														if ($regIncapacidad['PorcentajeAuxilio'] > 0) 
														{
															$query = <<<EOD
																SELECT AUXILIARES.Id, 
																		MAYORES.TipoRetencion 
																	FROM AUXILIARES 
																		INNER JOIN MAYORES 
																			ON AUXILIARES.IdMayor = MAYORES.Id
																		INNER JOIN PARAMETROS 
																			ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
																	WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
																		PARAMETROS.Detalle = 'ES AUXILIO POR INCAPACIDAD';
															EOD;
															
															$auxilio = $this->model->listarRegistros($query);

															if ($auxilio) 
															{
																for ($k = 0; $k < count($auxilio); $k++) 
																{ 
																	$regAuxilio = $auxilio[$k];

																	$IdConcepto = $regAuxilio['Id'];
																	$TipoRetencion = $regAuxilio['TipoRetencion'];

																	$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * ($HorasIncapacidad + $HorasAuxilioIncapacidad), 0) - $ValorNovedad;

																	$Horas -= $HorasAuxilioIncapacidad;

																	if ($ValorNovedad > 0) 
																	{
																		$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $regEmpleado['SueldoBasico'], 0,$HorasIncapacidad + $HorasAuxilioIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
																		$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
																	}
																}
															}
														}
														else
														{
															// SE GENERA COMO AUXILIO INCAPACIDAD LOS 2 PRIMEROS DIAS SIN EL PORCENTAJE DE AUXILIO SOLO EL 66%
															$query = <<<EOD
																SELECT AUXILIARES.Id, 
																		MAYORES.TipoRetencion 
																	FROM AUXILIARES 
																		INNER JOIN MAYORES 
																			ON AUXILIARES.IdMayor = MAYORES.Id
																		INNER JOIN PARAMETROS 
																			ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
																	WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
																		PARAMETROS.Detalle = 'ES AUXILIO POR INCAPACIDAD';
															EOD;
															
															$auxilio = $this->model->listarRegistros($query);

															if ($auxilio) 
															{
																for ($k = 0; $k < count($auxilio); $k++) 
																{ 
																	$regAuxilio = $auxilio[$k];

																	$IdConcepto = $regAuxilio['Id'];
																	$TipoRetencion = $regAuxilio['TipoRetencion'];

																	if ($regEmpleado['HorasMes'] == 120)
																	{
																		if ($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * 4 * .6667 * 30  < $SueldoMinimo)
																		{
																			$BaseIncapacidad = $SueldoMinimo;
																			$ValorNovedad = round($SueldoMinimo / 120 * $HorasAuxilioIncapacidad, 0);
																		}														else
																		{
																			$BaseIncapacidad = $regEmpleado['SueldoBasico'];
																			$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAuxilioIncapacidad * 0.6667, 0);
																		}
																	}
																	else
																	{
																		if ($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * 8 * .6667 * 30  < $SueldoMinimo)
																		{
																			$BaseIncapacidad = $SueldoMinimo;
																			$hoursMonht = getHoursMonth();
																			$ValorNovedad = round($SueldoMinimo / $hoursMonht * $HorasAuxilioIncapacidad, 0);
																		}
																		else
																		{
																			$BaseIncapacidad = $regEmpleado['SueldoBasico'];
																			$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAuxilioIncapacidad * 0.6667, 0);
																		}
																	}

																	$Horas -= $HorasAuxilioIncapacidad;

																	if ($ValorNovedad > 0) 
																	{
																		$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $BaseIncapacidad, 0, $HorasAuxilioIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
																		$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
																	}
																}
															}
														}
													}
												}
												else
													continue;
											}
										}
										else
										{
											$DiasIncapacidad = 0;
											$HorasIncapacidad = 0;
										}

										// SE APLICAN LOS AUMENTOS
										$query = <<<EOD
											SELECT AUMENTOSSALARIALES.FechaAumento, 
													AUMENTOSSALARIALES.SueldoBasico, 
													AUMENTOSSALARIALES.SueldoBasicoAnterior 
												FROM AUMENTOSSALARIALES 
												WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
													AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
													AUMENTOSSALARIALES.Procesado = 0;
										EOD;

										$regAumento = $this->model->leerRegistro($query);

										if ($regAumento) 
										{
											if ($regAumento['FechaAumento'] < $FechaInicial)
											{
												if (substr(FinMes($regAumento['FechaAumento']), 8, 2) == '31')
													$HorasAumento = (dias360($FechaInicial, $regAumento['FechaAumento']) - 1) * 8;
												else
													$HorasAumento = (dias360($FechaInicial, $regAumento['FechaAumento']) - 1) * 8;
											
												$ValorAumento = round($regAumento['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
												$ValorAumento -= round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
												$HorasAumento = 0;
											}
											elseif ($regAumento['FechaAumento'] > $FechaInicial)
											{
												if (substr($regAumento['FechaAumento'], 8, 2) == '31')
													$HorasAumento = (dias360($regAumento['FechaAumento'], $FechaInicial) - 1) * 8;
												else
													$HorasAumento = (dias360($regAumento['FechaAumento'], $FechaInicial) - 1) * 8;

												$ValorAumento = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
											}
											else{
											
												$HorasAumento = getHoursMonth();
												$ValorAumento = round($regAumento['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAumento * $regEmpleado['FactorConversion'], 0);
											}
										}
										else
											$ValorAumento = 0;

										if ($regEmpleado['NombreTipoLiquidacion'] == 'HORAS' OR
											$regEmpleado['NombreTipoLiquidacion'] == 'DÍAS')
										{
											if ($ValorAumento <> 0) 
											{
												$ValorNovedad = round($regAumento['SueldoBasico'] / $regEmpleado['HorasMes'] * ($Horas - $HorasAumento) * $regEmpleado['FactorConversion'], 0);
												
												$ValorNovedad += $ValorAumento;
											}
											else
												$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $Horas * $regEmpleado['FactorConversion'], 0);
										}
										else
										{
											$Horas = 0;
											$ValorNovedad = round($regEmpleado['ValorFijo'] * $regEmpleado['FactorConversion'], 0);

											if ($HorasAumento > 0) 
												$ValorNovedad += $ValorAumento;
										}
									}
									// SUELDO BASICO APRENDIZ SENA
									elseif ($regEmpleado['NombreTipoRegistroAuxiliar'] == 'ES SUELDO BÁSICO (APRENDIZ SENA)' AND 
										$regEmpleado['NombreModalidadTrabajo'] == 'SUELDO BÁSICO' AND 
										($regEmpleado['NombreTipoContrato'] == 'APRENDIZAJE - ETAPA PRÁCTICA' OR
										$regEmpleado['NombreTipoContrato'] == 'APRENDIZAJE - ETAPA LECTIVA' OR
										$regEmpleado['NombreTipoContrato'] == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR
										$regEmpleado['NombreTipoContrato'] == 'PASANTÍA	')) 
									{
										// SE APLICAN LAS INCAPACIDADES
										$query = <<<EOD
											SELECT INCAPACIDADES.IdEmpleado, 
													INCAPACIDADES.IdConcepto, 
													AUXILIARES.FactorConversion, 
													PARAMETROS4.Detalle AS Imputacion, 
													INCAPACIDADES.FechaInicio, 
													INCAPACIDADES.DiasIncapacidad, 
													INCAPACIDADES.DiasCausados, 
													INCAPACIDADES.PorcentajeAuxilio, 
													INCAPACIDADES.BaseLiquidacion, 
													INCAPACIDADES.EsProrroga, 
													PARAMETROS1.Detalle AS TipoRegistroAuxiliar, 
													PARAMETROS2.Detalle AS CuasaAusentismo, 
													PARAMETROS3.Detalle AS NombreBaseLiquidacion 
												FROM INCAPACIDADES 
													INNER JOIN AUXILIARES 
														ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
													INNER JOIN MAYORES 
														ON AUXILIARES.IdMayor = MAYORES.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS1 
														ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS1.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS2 
														ON INCAPACIDADES.CausaAusentismo = PARAMETROS2.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS3 
														ON INCAPACIDADES.BaseLiquidacion = PARAMETROS3.Id 
													LEFT JOIN PARAMETROS AS PARAMETROS4 
														ON AUXILIARES.Imputacion = PARAMETROS4.Id 
												WHERE INCAPACIDADES.IdEmpleado = $IdEmpleado AND 
													INCAPACIDADES.DiasIncapacidad > INCAPACIDADES.DiasCausados 
												ORDER BY INCAPACIDADES.FechaInicio; 
										EOD;

										$incapacidades = $this->model->listarRegistros($query);

										if ($incapacidades) 
										{
											for ($j = 0; $j < count($incapacidades); $j++) 
											{ 
												$regIncapacidad = $incapacidades[$j];
												$IdConcepto = $regIncapacidad['IdConcepto'];

												$FechaInicioIncapacidad = $regIncapacidad['FechaInicio'];

												// SE REVISAN SI HAY AUMENTOS SALARIALES Y SI LA INCAPACIDAD ESTA COBIJADA POR EL AUMENTO
												$query = <<<EOD
													SELECT AUMENTOSSALARIALES.FechaAumento, 
															AUMENTOSSALARIALES.SueldoBasico, 
															AUMENTOSSALARIALES.SueldoBasicoAnterior 
														FROM AUMENTOSSALARIALES 
														WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
															AUMENTOSSALARIALES.FechaAumento <= '$FechaFinal' AND 
															AUMENTOSSALARIALES.Procesado = 0;
												EOD;

												$regAumento = $this->model->leerRegistro($query);

												if ($regAumento) 
												{
													if ($regIncapacidad['FechaInicio'] >= $regAumento['FechaAumento'])
														$SueldoBasico = $regAumento['SueldoBasico'];
													else
														$SueldoBasico = $regEmpleado['SueldoBasico'];
												}
												else
													$SueldoBasico = $regEmpleado['SueldoBasico'];


												$DiasIncapacidad = $regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados'];

												if ($DiasIncapacidadAcumulados + $DiasIncapacidad > 30)
												{
													$DiasIncapacidad = 30 - $DiasIncapacidadAcumulados;
													$DiasIncapacidadAcumulados = 30;
												}
												else
													$DiasIncapacidadAcumulados += $DiasIncapacidad;
												

												$FechaFinalIncapacidad = date('Y-m-d', strtotime($regIncapacidad['FechaInicio'] . ' + ' . ($regIncapacidad['DiasIncapacidad'] - 1) . ' days'));

												if ($FechaFinalIncapacidad > $FechaFinal) 
													$FechaFinalIncapacidad = $FechaFinal;

												if ($FechaInicioIncapacidad <= $FechaFinal AND
													$FechaFinalIncapacidad >= $FechaInicial) 
												{
													// VALIDAR LOS DIAS RESTANTES DEL MES SI LA INCAPACIDAD SOBREPASA EL PERIODO
													if ($regEmpleado['HorasMes'] == 120)
														$HorasIncapacidad = $DiasIncapacidad * 4;
													else
														$HorasIncapacidad = $DiasIncapacidad * 8;
	
													if ($HorasIncapacidad > $regEmpleado['HorasMes']) 
														$HorasIncapacidad = $regEmpleado['HorasMes'];

													$TipoRetencion = $regEmpleado['TipoRetencion'];
													$IdCentro = $regEmpleado['IdCentro'];
													$TipoEmpleado = $regEmpleado['TipoEmpleado'];

													if ($regIncapacidad['NombreBaseLiquidacion'] == 'IBC MES ANTERIOR')
													{
														// CALCULAMOS IBC ANTERIOR
														if ($regIncapacidad['TipoRegistroAuxiliar'] <> 'ES INCAPACIDAD EN TIEMPO' AND 
															$regIncapacidad['TipoRegistroAuxiliar'] <> 'ES INCAPACIDAD PROFESIONAL') 
															$ValorIBCAnterior = $regEmpleado['SueldoBasico'];
														else
														{
															$ValorIBCAnterior = $this->CalcularValorIBC($IdEmpleado, $FechaInicialPeriodoAnterior, $FechaFinalPeriodoAnterior, $SueldoMinimo);

															if ($ValorIBCAnterior == 0)
																$ValorIBCAnterior = $SueldoBasico;
														}
													}
													else
													{
														$ValorIBCAnterior = $SueldoBasico;
													}

													if ($regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD EN TIEMPO' OR $regIncapacidad['TipoRegistroAuxiliar'] == 'ES INCAPACIDAD PROFESIONAL')
													{
														if ($regIncapacidad['DiasIncapacidad'] <= 2)
														{
															if ($regEmpleado['HorasMes'] == 120)
																$HorasAuxilioIncapacidad = $regIncapacidad['DiasIncapacidad'] * 4;
															else
																$HorasAuxilioIncapacidad = $regIncapacidad['DiasIncapacidad'] * 8;

															$FactorConversion = $regIncapacidad['FactorConversion'];
															$ValorNovedad = 0;
														}
														elseif ($regIncapacidad['DiasIncapacidad'] <= 90)
														{
															if ($regIncapacidad['DiasCausados'] == 0)
															{
																if ($regIncapacidad['EsProrroga'] == 1)
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
																else
																{
																	$HorasAuxilioIncapacidad = 16;
																	$HorasIncapacidad -= 16;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
															}
															else
															{
																$HorasAuxilioIncapacidad = 0;
																// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																$FactorConversion = $regIncapacidad['FactorConversion'];
															}

															$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $FactorConversion, 0);

															if ($ValorNovedad < round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0)) 
																$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad, 0);
														}
														else   // INCAPACIDAD > 180 DIAS
														{
															if ($regIncapacidad['DiasCausados'] + ($HorasIncapacidad / 8) > 90) 
															{
																if ($regIncapacidad['DiasCausados'] == 0)
																{
																	if ($regIncapacidad['EsProrroga'] == 1)
																	{
																		$HorasAuxilioIncapacidad = 0;
																		// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																		$FactorConversion = 0.5;
																	}
																	else
																	{
																		$HorasAuxilioIncapacidad = 16;
																		$HorasIncapacidad -= 16;
																		$FactorConversion = $regIncapacidad['FactorConversion'];
																	}
																}
																else
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}
		
																// PRIMEROS 90 DIAS
																$HorasIncapacidad1 = (90 - $regIncapacidad['DiasCausados']) * 8 - $HorasAuxilioIncapacidad;
																$FactorConversion1 = $regIncapacidad['FactorConversion'];
																// DIAS POSTERIORES AL DIA 90
																$HorasIncapacidad2 = ($regIncapacidad['DiasCausados'] + ($HorasIncapacidad / 8) - 90) * 8 - $HorasAuxilioIncapacidad;
																$HorasIncapacidad2 = max($HorasIncapacidad2, 0);
																$FactorConversion2 = 0.5;

																$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad1 * $FactorConversion1, 0);
																$ValorNovedad += round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad2 * $FactorConversion2, 0);
															}
															else
															{
																if ($regIncapacidad['DiasCausados'] == 0)
																{
																	if ($regIncapacidad['EsProrroga'] == 1)
																	{
																		$HorasAuxilioIncapacidad = 0;
																		// $HorasIncapacidad = ($regIncapacidad['DiasIncapacidad'] - $regIncapacidad['DiasCausados']) * 8;
																		$FactorConversion = 0.5;
																	}
																	else
																	{
																		$HorasAuxilioIncapacidad = 16;
																		$HorasIncapacidad -= 16;
																		$FactorConversion = $regIncapacidad['FactorConversion'];
																	}
																}
																else
																{
																	$HorasAuxilioIncapacidad = 0;
																	$FactorConversion = $regIncapacidad['FactorConversion'];
																}

																$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * ($HorasIncapacidad) * $FactorConversion, 0);
															}
														}
													}
													else
													{
														if ($ValorIBCAnterior < $SueldoMinimo)
															$ValorNovedad = round($SueldoMinimo / $regEmpleado['HorasMes'] * $HorasIncapacidad * $regIncapacidad['FactorConversion'], 0);
														else
															$ValorNovedad = round($ValorIBCAnterior / $regEmpleado['HorasMes'] * $HorasIncapacidad * $regIncapacidad['FactorConversion'], 0);

														$HorasAuxilioIncapacidad = 0;
														// $Horas = $HorasIncapacidad;
													}


													if ($ValorNovedad > 0)
													{
														if ($regIncapacidad['Imputacion'] == 'PAGO')
															$Horas -= $HorasIncapacidad;

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $ValorIBCAnterior, 0, $HorasIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, $regEmpleado['IdEPS'], 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}

													if ($regIncapacidad['DiasIncapacidad'] <= 2)
														$HorasIncapacidad = 0;

													// GENERAR AUXILIO DE INCAPACIDAD SI EXISTE.
													if ($HorasAuxilioIncapacidad > 0)
													{
														if ($regIncapacidad['PorcentajeAuxilio'] > 0) 
														{
															$query = <<<EOD
																SELECT AUXILIARES.Id, 
																		MAYORES.TipoRetencion 
																	FROM AUXILIARES 
																		INNER JOIN MAYORES 
																			ON AUXILIARES.IdMayor = MAYORES.Id
																		INNER JOIN PARAMETROS 
																			ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
																	WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
																		PARAMETROS.Detalle = 'ES AUXILIO POR INCAPACIDAD';
															EOD;
															
															$auxilio = $this->model->listarRegistros($query);

															if ($auxilio) 
															{
																for ($k = 0; $k < count($auxilio); $k++) 
																{ 
																	$regAuxilio = $auxilio[$k];

																	$IdConcepto = $regAuxilio['Id'];
																	$TipoRetencion = $regAuxilio['TipoRetencion'];

																	$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * ($HorasIncapacidad + $HorasAuxilioIncapacidad), 0) - $ValorNovedad;

																	$Horas -= $HorasAuxilioIncapacidad;

																	if ($ValorNovedad > 0) 
																	{
																		$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $regEmpleado['SueldoBasico'], 0,$HorasIncapacidad + $HorasAuxilioIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
																		$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
																	}
																}
															}
														}
														else
														{
															// SE GENERA COMO AUXILIO INCAPACIDAD LOS 2 PRIMEROS DIAS SIN EL PORCENTAJE DE AUXILIO SOLO EL 66%
															$query = <<<EOD
																SELECT AUXILIARES.Id, 
																		MAYORES.TipoRetencion 
																	FROM AUXILIARES 
																		INNER JOIN MAYORES 
																			ON AUXILIARES.IdMayor = MAYORES.Id
																		INNER JOIN PARAMETROS 
																			ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
																	WHERE PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND
																		PARAMETROS.Detalle = 'ES AUXILIO POR INCAPACIDAD';
															EOD;
															
															$auxilio = $this->model->listarRegistros($query);

															if ($auxilio) 
															{
																for ($k = 0; $k < count($auxilio); $k++) 
																{ 
																	$regAuxilio = $auxilio[$k];

																	$IdConcepto = $regAuxilio['Id'];
																	$TipoRetencion = $regAuxilio['TipoRetencion'];

																	if ($regEmpleado['HorasMes'] == 120)
																	{
																		if ($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * 4 * .6667 * 30  < $SueldoMinimo)
																		{
																			$BaseIncapacidad = $SueldoMinimo;
																			$ValorNovedad = round($SueldoMinimo / 120 * $HorasAuxilioIncapacidad, 0);
																		}														else
																		{
																			$BaseIncapacidad = $regEmpleado['SueldoBasico'];
																			$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAuxilioIncapacidad * 0.6667, 0);
																		}
																	}
																	else
																	{
																		if ($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * 8 * .6667 * 30  < $SueldoMinimo)
																		{
																			$hoursMonht = getHoursMonth();
																			$BaseIncapacidad = $SueldoMinimo;
																			$ValorNovedad = round($SueldoMinimo / $hoursMonht * $HorasAuxilioIncapacidad, 0);
																		}
																		else
																		{
																			$BaseIncapacidad = $regEmpleado['SueldoBasico'];
																			$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $HorasAuxilioIncapacidad * 0.6667, 0);
																		}
																	}

																	$Horas -= $HorasAuxilioIncapacidad;

																	if ($ValorNovedad > 0) 
																	{
																		$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $BaseIncapacidad, 0, $HorasAuxilioIncapacidad, $ValorNovedad, 0, $FechaInicioIncapacidad, $FechaFinalIncapacidad, 'I', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
																		$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
																	}
																}
															}
														}
													}
												}
												else
													continue;
											}
										}
										else
										{
											$DiasIncapacidad = 0;
											$HorasIncapacidad = 0;
										}

										if ($regEmpleado['NombreTipoLiquidacion'] == 'HORAS' OR
											$regEmpleado['NombreTipoLiquidacion'] == 'DÍAS')
											$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $Horas * $regEmpleado['FactorConversion'], 0);
										else
										{
											$Horas = 0;
											$ValorNovedad = round($regEmpleado['ValorFijo'] * $regEmpleado['FactorConversion'], 0);
										}
									}
									// SUBSIDIO DE TRANSPORTE
									elseif ($regEmpleado['NombreTipoRegistroAuxiliar'] == 'ES SUBSIDIO DE TRANSPORTE' AND 
										($regEmpleado['NombreModalidadTrabajo'] == 'SUELDO BÁSICO' OR
										$regEmpleado['NombreModalidadTrabajo'] == 'DESTAJO CON HORARIO') AND
										($regEmpleado['NombreTipoContrato'] <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND
										$regEmpleado['NombreTipoContrato'] <> 'APRENDIZAJE - ETAPA LECTIVA' AND
										$regEmpleado['NombreTipoContrato'] <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND
										$regEmpleado['NombreTipoContrato'] <> 'PASANTÍA	'))
									{
										switch ($regEmpleado['NombreSubsidioTransporte'])
										{
											case 'SUBSIDIO COMPLETO':
												// SE CALCULAN LOS PAGOS SALARIALES
												$query = <<<EOD
													SELECT EMPLEADOS.Id AS IdEmpleado, 
															SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
																PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
																PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
																PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
																PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
																PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
																PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
																PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
																PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
																PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
																PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
																PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO' OR  
																PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
																IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Horas, $ArchivoNomina.Horas * -1), 0)) AS Horas,
															SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
																PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
																PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
																PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
																PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
																PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
																PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
																PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
																PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
																PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
																PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
																PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO' OR  
																PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
																IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Valor, $ArchivoNomina.Valor * -1), 0)) AS Valor,
															SUM(IIF(PARAMETROS2.Detalle = 'NO SALARIO', 
																IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Valor, 0), 0)) AS ValorNoSalarial,
															SUM(IIF(PARAMETROS5.Detalle = 'ES FACTOR PRESTACIONAL', $ArchivoNomina.Valor, 0)) AS ValorPrestacional
														FROM $ArchivoNomina
															INNER JOIN EMPLEADOS 
																ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id
															INNER JOIN AUXILIARES 
																ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
															INNER JOIN MAYORES 
																ON AUXILIARES.IdMayor = MAYORES.Id 
															INNER JOIN CENTROS 
																ON EMPLEADOS.IdCentro = CENTROS.Id 
															INNER JOIN PARAMETROS AS PARAMETROS1
																ON AUXILIARES.Imputacion = PARAMETROS1.Id 
															INNER JOIN PARAMETROS AS PARAMETROS2
																ON MAYORES.ClaseConcepto = PARAMETROS2.Id 
															INNER JOIN PARAMETROS AS PARAMETROS3
																ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
															INNER JOIN PARAMETROS AS PARAMETROS4
																ON EMPLEADOS.Estado = PARAMETROS4.Id 
															LEFT JOIN PARAMETROS AS PARAMETROS5 
																ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS5.Id 
														WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
															$ArchivoNomina.Ciclo = $Ciclo AND 
															$ArchivoNomina.IdEmpleado = $IdEmpleado 
												EOD;

												$query .= <<<EOD
													GROUP BY EMPLEADOS.Id;
												EOD;

												$seguridad = $this->model->listarRegistros($query);

												for ($j = 0; $j < count($seguridad); $j++) 
												{ 
													$regSeguridad = $seguridad[$j];

													if ($regSeguridad['Valor'] <= $SueldoMinimo * 2)
													{
														$Horas -= $HorasVacaciones;
														$Horas -= $DiasIncapacidadAcumulados * 8;
														$ValorNovedad = round($ValorSubsidioTransporte / $regEmpleado['HorasMes'] * $Horas * $regEmpleado['FactorConversion'], 0);
													}
												}

												break;

											case 'MEDIO SUBSIDIO':
												$Horas -= $HorasVacaciones;
												$Horas -= $DiasIncapacidadAcumulados * 8;
												$ValorNovedad = round($ValorSubsidioTransporte / $regEmpleado['HorasMes'] * $Horas * $regEmpleado['FactorConversion'] / 2, 0);
												break;
										}
									}
									else
									{
										if ($regEmpleado['NombreTipoRegistroAuxiliar'] <> 'ES SUELDO BÁSICO' AND 
											$regEmpleado['NombreTipoRegistroAuxiliar'] <> 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' AND 
											$regEmpleado['NombreTipoRegistroAuxiliar'] <> 'ES SUELDO BÁSICO (APRENDIZ SENA)' AND 
											$regEmpleado['NombreTipoRegistroAuxiliar'] <> 'ES FACTOR PRESTACIONAL' AND 
											$regEmpleado['NombreTipoRegistroAuxiliar'] <> 'ES SUBSIDIO DE TRANSPORTE' AND 
											($regEmpleado['NombreModalidadTrabajo'] == 'SUELDO BÁSICO' OR 
											$regEmpleado['NombreModalidadTrabajo'] == 'DESTAJO CON HORARIO'))
										{
											if ($regEmpleado['NombreTipoLiquidacion'] == 'HORAS' OR
												$regEmpleado['NombreTipoLiquidacion'] == 'DÍAS'){
												$ValorNovedad = round($regEmpleado['SueldoBasico'] / $regEmpleado['HorasMes'] * $Horas * $regEmpleado['FactorConversion'], 0);
											}else
											{
												$Horas = 0;
												$ValorNovedad = round($regEmpleado['ValorFijo'] * $regEmpleado['FactorConversion'], 0);
											}
										}
									}

									if ($ValorNovedad > 0) 
									{
										$IdPeriodo = $regPeriodo['id'];
										$IdConcepto = $regEmpleado['IdConcepto'];

										$TipoRetencion = $regEmpleado['TipoRetencion'];
										$IdCentro = $regEmpleado['IdCentro'];
										$TipoEmpleado = $regEmpleado['TipoEmpleado'];

										if ($regEmpleado['HorasMes'] == 120)
											$Horas *= 2;

										$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, 0, 0, $Horas, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
										$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
									}
								}
							}
						}

						// NOVEDADES PROGRAMABLES
						if ($SoloNovedades == FALSE AND $Ciclo < 98) 
						{
							$FechaInicial = $FechaInicialPeriodo;
							$FechaFinal = $FechaFinalPeriodo;

							$query = <<<EOD
								SELECT NOVEDADESPROGRAMABLES.Fecha, 
										NOVEDADESPROGRAMABLES.IdEmpleado AS IdEmpleadoNP, 
										NOVEDADESPROGRAMABLES.IdCentro AS IdCentroNP, 
										NOVEDADESPROGRAMABLES.TipoEmpleado AS TipoEmpleadoNP, 
										NOVEDADESPROGRAMABLES.IdCargo AS IdCargoNP, 
										EMPLEADOS.Id AS IdEmpleado, 
										EMPLEADOS.Documento, 
										EMPLEADOS.IdCentro, 
										CENTROS.TipoEmpleado, 
										EMPLEADOS.IdCargo, 
										EMPLEADOS.SueldoBasico, 
										EMPLEADOS.DiasAno, 
										EMPLEADOS.HorasMes, 
										EMPLEADOS.FechaIngreso, 
										EMPLEADOS.FechaRetiro, 
										NOVEDADESPROGRAMABLES.IdConcepto, 
										AUXILIARES.HoraFija, 
										AUXILIARES.FactorConversion, 
										PARAMETROS6.Detalle AS NombreTipoRegistroAuxiliar, 
										MAYORES.TipoRetencion, 
										MAYORES.ControlaSaldos, 
										NOVEDADESPROGRAMABLES.Horas, 
										NOVEDADESPROGRAMABLES.Valor, 
										NOVEDADESPROGRAMABLES.SalarioLimite, 
										NOVEDADESPROGRAMABLES.FechaLimite, 
										NOVEDADESPROGRAMABLES.ModoLiquidacion, 
										PARAMETROS1.Detalle AS NombreModoLiquidacion, 
										NOVEDADESPROGRAMABLES.IdTercero, 
										NOVEDADESPROGRAMABLES.Id 
									FROM NOVEDADESPROGRAMABLES 
										INNER JOIN EMPLEADOS 
											ON NOVEDADESPROGRAMABLES.IdEmpleado = EMPLEADOS.Id 
										INNER JOIN AUXILIARES 
											ON NOVEDADESPROGRAMABLES.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1  
											ON NOVEDADESPROGRAMABLES.ModoLiquidacion = PARAMETROS1.Id 
										INNER JOIN PARAMETROS AS PARAMETROS2  
											ON NOVEDADESPROGRAMABLES.Estado = PARAMETROS2.Id 
										INNER JOIN PARAMETROS AS PARAMETROS3   
											ON EMPLEADOS.Estado = PARAMETROS3.Id 
										INNER JOIN PARAMETROS AS PARAMETROS4    
											ON EMPLEADOS.TipoContrato = PARAMETROS4.Id 
										INNER JOIN PARAMETROS AS PARAMETROS5    
											ON AUXILIARES.TipoAuxiliar = PARAMETROS5.Id 
										LEFT JOIN PARAMETROS AS PARAMETROS6    
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS6.Id 
									WHERE PARAMETROS2.Detalle = 'ACTIVA' AND 
										(PARAMETROS3.Detalle = 'ACTIVO' OR 
										(PARAMETROS3.Detalle = 'RETIRADO' AND 
										EMPLEADOS.FechaRetiro >= '$FechaInicial')) AND 
										EMPLEADOS.PeriodicidadPago = $IdPeriodicidad $queryValidacionIcetex AND 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									EMPLEADOS.IdCentro = $P_IdCentro AND 
								EOD;
					
							if (! empty($P_Empleado))
								$query .= <<<EOD
									EMPLEADOS.Documento = '$P_Empleado' AND 
								EOD;
					
							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									CENTROS.TipoEmpleado = $P_TipoEmpleados AND 
								EOD;

							$query .= <<<EOD
										EMPLEADOS.FechaIngreso <= '$FechaFinal' AND 
										PARAMETROS5.Detalle = 'CONTABLE' AND 
										AUXILIARES.TipoEmpleado = $P_TipoEmpleados AND 
										1 = IIF(NOVEDADESPROGRAMABLES.FechaLimite IS NULL, 1, 
											IIF(NOVEDADESPROGRAMABLES.FechaLimite >= '$FechaFinal', 1, 0)) 
								UNION 
								( SELECT NOVEDADESPROGRAMABLES.Fecha, 
										NOVEDADESPROGRAMABLES.IdEmpleado AS IdEmpleadoNP, 
										NOVEDADESPROGRAMABLES.IdCentro AS IdCentroNP, 
										NOVEDADESPROGRAMABLES.TipoEmpleado AS TipoEmpleadoNP, 
										NOVEDADESPROGRAMABLES.IdCargo AS IdCargoNP, 
										EMPLEADOS.Id AS IdEmpleado, 
										EMPLEADOS.Documento, 
										EMPLEADOS.IdCentro, 
										CENTROS.TipoEmpleado, 
										EMPLEADOS.IdCargo, 
										EMPLEADOS.SueldoBasico, 
										EMPLEADOS.DiasAno, 
										EMPLEADOS.HorasMes, 
										EMPLEADOS.FechaIngreso, 
										EMPLEADOS.FechaRetiro, 
										NOVEDADESPROGRAMABLES.IdConcepto, 
										AUXILIARES.HoraFija, 
										AUXILIARES.FactorConversion, 
										PARAMETROS6.Detalle AS NombreTipoRegistroAuxiliar, 
										MAYORES.TipoRetencion, 
										MAYORES.ControlaSaldos, 
										NOVEDADESPROGRAMABLES.Horas, 
										NOVEDADESPROGRAMABLES.Valor, 
										NOVEDADESPROGRAMABLES.SalarioLimite, 
										NOVEDADESPROGRAMABLES.FechaLimite, 
										NOVEDADESPROGRAMABLES.ModoLiquidacion, 
										PARAMETROS1.Detalle AS NombreModoLiquidacion, 
										NOVEDADESPROGRAMABLES.IdTercero, 
										NOVEDADESPROGRAMABLES.Id 
									FROM NOVEDADESPROGRAMABLES 
										FULL JOIN EMPLEADOS 
											ON NOVEDADESPROGRAMABLES.IdEmpleado = 0 
										INNER JOIN AUXILIARES 
											ON NOVEDADESPROGRAMABLES.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1  
											ON NOVEDADESPROGRAMABLES.ModoLiquidacion = PARAMETROS1.Id 
										INNER JOIN PARAMETROS AS PARAMETROS2 
											ON NOVEDADESPROGRAMABLES.Estado = PARAMETROS2.Id 
										INNER JOIN PARAMETROS AS PARAMETROS3   
											ON EMPLEADOS.Estado = PARAMETROS3.Id 
										INNER JOIN PARAMETROS AS PARAMETROS4    
											ON EMPLEADOS.TipoContrato = PARAMETROS4.Id 
										INNER JOIN PARAMETROS AS PARAMETROS5    
											ON AUXILIARES.TipoAuxiliar = PARAMETROS5.Id 
										LEFT JOIN PARAMETROS AS PARAMETROS6 
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS6.Id 
									WHERE PARAMETROS2.Detalle = 'ACTIVA' AND 
										(PARAMETROS3.Detalle = 'ACTIVO' OR 
										(PARAMETROS3.Detalle = 'RETIRADO' AND 
										EMPLEADOS.FechaRetiro >= '$FechaInicial')) AND 
										EMPLEADOS.PeriodicidadPago = $IdPeriodicidad $queryValidacionIcetex AND 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									EMPLEADOS.IdCentro = $P_IdCentro AND 
								EOD;
					
							if (! empty($P_Empleado))
								$query .= <<<EOD
									EMPLEADOS.Documento = '$P_Empleado' AND 
								EOD;
					
							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									CENTROS.TipoEmpleado = $P_TipoEmpleados AND 
								EOD;

							$query .= <<<EOD
										EMPLEADOS.FechaIngreso <= '$FechaFinal' AND 
										PARAMETROS5.Detalle = 'CONTABLE' AND 
										AUXILIARES.TipoEmpleado = $P_TipoEmpleados AND 
										1 = IIF(NOVEDADESPROGRAMABLES.FechaLimite IS NULL, 1, 
											IIF(NOVEDADESPROGRAMABLES.FechaLimite >= '$FechaFinal', 1, 0)) )
								ORDER BY IdEmpleado, IdConcepto;
							EOD;

							$novedades = $this->model->listarRegistros($query);

							for ($i = 0; $i < count($novedades) ; $i++) 
							{ 
								$regNovedad = $novedades[$i];
								$IdEmpleado = $regNovedad['IdEmpleado'];

								if ($regNovedad['IdEmpleadoNP'] > 0 AND $regNovedad['IdEmpleadoNP'] <> $regNovedad['IdEmpleado']) 
									continue;
								else
								{
									if ($regNovedad['IdCentroNP'] > 0 AND $regNovedad['IdCentroNP'] <> $regNovedad['IdCentro'])
										continue;

									if ($regNovedad['TipoEmpleadoNP'] > 0 AND $regNovedad['TipoEmpleadoNP'] <> $regNovedad['TipoEmpleado'])
										continue;

									if ($regNovedad['IdCargoNP'] > 0 AND $regNovedad['IdCargoNP'] <> $regNovedad['IdCargo'])
										continue;
								}

								// SE REVISA SI ESTE EMPLEADO YA FUE LIQUIDADO EN EL MISMO PERIODO
								$query = <<<EOD
									SELECT COUNT(*) AS Registros 
										FROM ACUMULADOS 
										WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND ACUMULADOS.ciclo = $Ciclo AND
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
											ACUMULADOS.FechaFinalPeriodo= '$FechaFinalPeriodo'; 
								EOD; 

								$regProcesado = $this->model->leerRegistro($query);

								if ($regProcesado['Registros'] > 0)
									$LiquidaAutomatico = FALSE;
								else
									$LiquidaAutomatico = TRUE;

								if ($LiquidaAutomatico) 
								{
									$Horas = 0;
									$ValorNovedad = 0;

									if ($regNovedad['FechaRetiro'] >= $regPeriodo['fechainicial'] AND 
										$regNovedad['FechaRetiro'] <= $regPeriodo['fechafinal'])
										$FechaFinal = $regNovedad['FechaRetiro'];
									else
									{
										if (substr($regPeriodo['fechafinal'], 8, 2) == 31) 
											$FechaFinal = date('Y-m-d', strtotime($regPeriodo['fechafinal'] . ' - 1 days')); 
										else
											$FechaFinal = $regPeriodo['fechafinal'];
									}

									if ($regNovedad['FechaIngreso'] >= $regPeriodo['fechainicial'] AND 
										$regNovedad['FechaIngreso'] <= $regPeriodo['fechafinal'])
										$FechaInicial = $regNovedad['FechaIngreso'];
									else
										$FechaInicial = $regPeriodo['fechainicial'];

									if ($regNovedad['Fecha'] >= $regPeriodo['fechainicial'] AND 
										$regNovedad['Fecha'] <= $regPeriodo['fechafinal'])
										$FechaInicial = $regNovedad['Fecha'];
									else
										$FechaInicial = $regPeriodo['fechainicial'];

									if (($Periodicidad == 'QUINCENAL' OR $Periodicidad == 'MENSUAL') AND $regNovedad['DiasAno'] == 365)
										$Horas = ((strtotime($FechaFinal) - strtotime($FechaInicial)) / 86400 + 1) * 8;
									else
									{
										if ($FechaInicial == $regPeriodo['fechainicial'] AND $FechaFinal == $regPeriodo['fechafinal'])
										{
											if ($regNovedad['HoraFija'] == 0)
												$Horas = $regNovedad['HorasMes'] * $FactorHoras;
											else
												$Horas = $regNovedad['HoraFija'];
										}
										else
											$Horas =((strtotime($FechaFinal) - strtotime($FechaInicial)) / 86400 + 1) * 8;
									}
									
									if ($regNovedad['NombreModoLiquidacion'] == 'PROPORCIONAL AL TIEMPO TRABAJADO') 
									{
										// NOTA: Este proceso permite liquidar el valor de la novedad solo por los // días efectivamente trabajados
										$query = <<<EOD
											SELECT $ArchivoNomina.FechaInicial, 
													$ArchivoNomina.FechaFinal 
												FROM $ArchivoNomina 
													INNER JOIN AUXILIARES 
														ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
													INNER JOIN PARAMETROS 
														ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
												WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
													(PARAMETROS.Detalle = 'ES PERMISO REMUNERADO' OR 
													PARAMETROS.Detalle = 'ES LICENCIA REMUNERADA' OR 
													PARAMETROS.Detalle = 'ES PERÍODO DE VACACIONES' OR 
													PARAMETROS.Detalle = 'ES PERÍODO DE MATERNIDAD' OR 
													PARAMETROS.Detalle = 'ES PERÍODO DE LUTO' OR 
													PARAMETROS.Detalle = 'ES SANCIÓN' OR 
													PARAMETROS.Detalle = 'ES INCAPACIDAD EN TIEMPO') AND  
													(($ArchivoNomina.FechaInicial >= '$FechaInicial' AND 
													$ArchivoNomina.FechaInicial <= '$FechaFinal') OR
													($ArchivoNomina.FechaFinal >= '$FechaInicial' AND 
													$ArchivoNomina.FechaFinal <= '$FechaFinal'));
										EOD;

										$diasDcto = $this->model->listarRegistros($query);

										if ($diasDcto) 
										{
											for ($j = 0; $j < count($diasDcto) ; $j++) 
											{ 
												$regDiasDcto = $diasDcto[$j];

												if ($regDiasDcto['FechaInicial'] < $regPeriodo['fechainicial']) 
													$FechaInicial = $regPeriodo['fechainicial'];
												else
													$FechaInicial = $regDiasDcto['FechaInicial'];

												if ($regDiasDcto['FechaFinal'] > $regPeriodo['fechafinal']) 
													if (substr($regPeriodo['fechafinal'], 8, 2) == 31) 
														$FechaFinal = date('Y-m-d', strtotime($regPeriodo['fechafinal'] . ' - 1 days')); 
													else
														$FechaFinal = $regPeriodo['fechafinal'];
												else
													$FechaFinal = $regDiasDcto['FechaFinal'];

												$Horas = ((strtotime($FechaFinal) - strtotime($FechaInicial)) / 86400 + 1) * 8;
												$ValorNovedad = round($regNovedad['Valor'] / $regNovedad['HorasMes'] * $Horas * $regNovedad['FactorConversion'], 0);
											}
										}
										else
											$ValorNovedad = round($regNovedad['Valor'] / $regNovedad['HorasMes'] * $Horas * $regNovedad['FactorConversion'], 0);
									}
									else
									{
										// $Horas = $regNovedad['Horas'];
										// $ValorNovedad = round($regNovedad['Valor'] * $regNovedad['FactorConversion'], 0);
										$Horas =((strtotime($FechaFinal) - strtotime($FechaInicial)) / 86400 + 1) * 8;
										$ValorNovedad = round($regNovedad['Valor'] / $regNovedad['HorasMes'] * $Horas * $regNovedad['FactorConversion'], 0);
									}

									if ($ValorNovedad > 0) 
									{
										$NovedadValida = TRUE;

										$IdConcepto = $regNovedad['IdConcepto'];
										$TipoRetencion = $regNovedad['TipoRetencion'];
										$IdCentro = $regNovedad['IdCentro'];
										$TipoEmpleado = $regNovedad['TipoEmpleado'];

										if ($regNovedad['ControlaSaldos']) 
										{
											$query = <<<EOD
												SELECT AUXILIARES.Id, 
														SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Saldo
													FROM ACUMULADOS 
														INNER JOIN AUXILIARES 
															ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
														INNER JOIN PARAMETROS 
															ON AUXILIARES.Imputacion = PARAMETROS.Id 
													WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
														AUXILIARES.Id = $IdConcepto 
													GROUP BY AUXILIARES.Id ;
											EOD;

											$regSaldo = $this->model->leerRegistro($query);

											if ($regSaldo) 
												$Saldo = $regSaldo['Saldo'] + $ValorNovedad;
											else
												$Saldo = $ValorNovedad;
										}
										else
											$Saldo = 0;

										if ($regNovedad['SalarioLimite'] == 0 OR 
											($regNovedad['SalarioLimite'] > 0 AND $regNovedad['SueldoBasico'] <= $regNovedad['SalarioLimite']))
										{
											$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, 0, 0, 0, $ValorNovedad, $Saldo, NULL, NULL, 'P', $TipoRetencion, $IdCentro, $TipoEmpleado, $regNovedad['IdTercero'], 0);
											$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);

											if ($regNovedad['NombreTipoRegistroAuxiliar'] == 'ES AUXILIO MEDICINA PREPAGADA') 
											{
												$query = <<<EOD
													SELECT AUXILIARES.Id 
														FROM AUXILIARES 
															INNER JOIN PARAMETROS 
																ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
														WHERE PARAMETROS.Detalle = 'ES DESCUENTO AUXILIO MEDICINA PREPAGADA';
												EOD;

												$reembolsos = $this->model->listarRegistros($query);

												if ($reembolsos) 
												{
													for ($j = 0; $j < count($reembolsos); $j++) 
													{ 
														$regReembolso = $reembolsos[$j];
														$IdConcepto = $regReembolso['Id'];

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, 0, 0, 0, $ValorNovedad, $Saldo, NULL, NULL, 'P', $TipoRetencion, $IdCentro, $TipoEmpleado, $regNovedad['IdTercero'], 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}
												}
											}
										}
									}
								}
							}
						}

						// PRÉSTAMOS A EMPLEADOS
						if ($SoloNovedades == FALSE AND $Ciclo < 98) 
						{
							$FechaInicial = $FechaInicialPeriodo;
							$FechaFinal = $FechaFinalPeriodo;
															
							$query = <<<EOD
								SELECT PRESTAMOS.IdEmpleado, 
										EMPLEADOS.IdCentro, 
										CENTROS.TipoEmpleado, 
										EMPLEADOS.FechaRetiro, 
										PRESTAMOS.IdConcepto, 
										MAYORES.TipoRetencion, 
										PRESTAMOS.ValorCuota, 
										PRESTAMOS.SaldoPrestamo, 
										PRESTAMOS.IdTercero, 
										PARAMETROS4.Detalle AS NombreTipoPrestamo,  
										PRESTAMOS.Id 
									FROM PRESTAMOS 
										INNER JOIN EMPLEADOS 
											ON PRESTAMOS.IdEmpleado = EMPLEADOS.Id 
										INNER JOIN AUXILIARES 
											ON PRESTAMOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1 
											ON PRESTAMOS.Estado = PARAMETROS1.Id 
										INNER JOIN PARAMETROS AS PARAMETROS2 
											ON EMPLEADOS.Estado = PARAMETROS2.Id 
										INNER JOIN PARAMETROS AS PARAMETROS3 
											ON CENTROS.TipoEmpleado = PARAMETROS3.Id 
										INNER JOIN PARAMETROS AS PARAMETROS4 
											ON PRESTAMOS.TipoPrestamo = PARAMETROS4.Id 
									WHERE PRESTAMOS.SaldoPrestamo > 0 AND 
										PARAMETROS1.Detalle = 'ACTIVO' $queryValidacionIcetex AND 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									EMPLEADOS.IdCentro = $P_IdCentro AND 
								EOD;
					
							if (! empty($P_Empleado))
								$query .= <<<EOD
									EMPLEADOS.Documento = '$P_Empleado' AND 
								EOD;
					
							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									CENTROS.TipoEmpleado = $P_TipoEmpleados AND 
								EOD;

							$query .= <<<EOD
										((PARAMETROS2.Detalle = 'ACTIVO' AND 
										EMPLEADOS.FechaIngreso <= '$FechaFinal') OR 
										(PARAMETROS2.Detalle = 'RETIRADO' AND 
										EMPLEADOS.FechaRetiro >= '$FechaInicial')) 
									ORDER BY PRESTAMOS.IdEmpleado, PRESTAMOS.IdConcepto;
							EOD;

							$prestamos = $this->model->listarRegistros($query);

							if ($prestamos) 
							{
								for ($i = 0; $i < count($prestamos); $i++) 
								{
									$regPrestamo = $prestamos[$i];
									$IdPrestamo = $regPrestamo['Id'];
									$IdEmpleado = $regPrestamo['IdEmpleado'];

									// SE REVISA SI ESTE EMPLEADO YA FUE LIQUIDADO EN EL MISMO PERIODO
									$query = <<<EOD
										SELECT COUNT(*) AS Registros 
											FROM ACUMULADOS 
											WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
												ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPeriodo' AND 
												ACUMULADOS.FechaFinalPeriodo= '$FechaFinalPeriodo'; 
									EOD; 

									$regProcesado = $this->model->leerRegistro($query);

									if ($regProcesado['Registros'] > 0)
										$LiquidaAutomatico = FALSE;
									else
										$LiquidaAutomatico = TRUE;

									if ($LiquidaAutomatico) 
									{
										$IdConcepto = $regPrestamo['IdConcepto'];
										$TipoRetencion = $regPrestamo['TipoRetencion'];
										$IdCentro = $regPrestamo['IdCentro'];
										$TipoEmpleado = $regPrestamo['TipoEmpleado'];
										$IdTercero = $regPrestamo['IdTercero'];
										$SaldoPrestamo = $regPrestamo['SaldoPrestamo'];
										$ValorCuota = $regPrestamo['ValorCuota'];

										if ($regPrestamo['FechaRetiro'] >= $regPeriodo['fechainicial'] AND 
											$regPrestamo['FechaRetiro'] <= $regPeriodo['fechafinal'] AND 
											($regPrestamo['NombreTipoPrestamo'] == 'PRÉSTAMO EMPRESA' OR
											$regPrestamo['NombreTipoPrestamo'] == 'PRÉSTAMO FONDO EMPLEADOS'))
										{
											$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, 0, 0, 0, $SaldoPrestamo, 0, NULL, NULL, 'C', $TipoRetencion, $IdCentro, $TipoEmpleado, $IdTercero, 0, $IdPrestamo);
											$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
										}
										else
										{
											$ValorNovedad = min($ValorCuota, $SaldoPrestamo);
											$Saldo = $SaldoPrestamo - $ValorNovedad;

											$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, 0, 0, 0, $ValorNovedad, 0, NULL, NULL, 'C', $TipoRetencion, $IdCentro, $TipoEmpleado, $IdTercero, $IdPrestamo);
											$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
										}
									}
								}
							}
						}

						$FechaInicial = $FechaInicialPeriodo;
						$FechaFinal = $FechaFinalPeriodo;

						// SEGURIDAD SOCIAL
						if (TRUE) 
						{
							$FechaInicial = $FechaInicialPeriodo;
							$FechaFinal = $FechaFinalPeriodo;

							$query = <<<EOD
								SELECT AUXILIARES.*, 
										MAYORES.TipoRetencion, 
										PARAMETROS.Detalle AS NombreTipoRegistroAuxiliar 
									FROM AUXILIARES 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS 
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
									WHERE PARAMETROS.Detalle = 'ES APORTE DE SALUD' OR 
										PARAMETROS.Detalle = 'ES APORTE DE PENSIÓN' OR 
										PARAMETROS.Detalle = 'ES DEVOLUCIÓN SALUD' OR 
										PARAMETROS.Detalle = 'ES FONDO DE SOLIDARIDAD' OR 
										PARAMETROS.Detalle = 'ES FONDO DE SUBSISTENCIA';
							EOD;

							$conceptos = $this->model->listarRegistros($query);

							if ($conceptos) 
							{
								$query = <<<EOD
									SELECT EMPLEADOS.Id AS IdEmpleado, 
											SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
												PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
												PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
												PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
												PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
												PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO' OR  
												PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
												IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Horas, $ArchivoNomina.Horas * -1), 0)) AS Horas,
											SUM(IIF(PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO',  
												IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Horas, $ArchivoNomina.Horas * -1), 0)) AS HorasVacaciones,
											SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
												PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
												PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
												PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
												PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
												PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO' OR  
												PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
												IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Valor, $ArchivoNomina.Valor * -1), 0)) AS Valor,
											SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
												PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
												PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
												PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
												PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
												PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
												PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
												PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
												IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Valor, $ArchivoNomina.Valor * -1), 0)) AS ValorSS,
											SUM(IIF(PARAMETROS2.Detalle = 'NO SALARIO', 
												IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Valor, 0), 0)) AS ValorNoSalarial,
											SUM(IIF(PARAMETROS5.Detalle = 'ES FACTOR PRESTACIONAL', $ArchivoNomina.Valor, 0)) AS ValorPrestacional
										FROM $ArchivoNomina
											INNER JOIN EMPLEADOS 
												ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id
											INNER JOIN AUXILIARES 
												ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN CENTROS 
												ON EMPLEADOS.IdCentro = CENTROS.Id 
											INNER JOIN PARAMETROS AS PARAMETROS1
												ON AUXILIARES.Imputacion = PARAMETROS1.Id 
											INNER JOIN PARAMETROS AS PARAMETROS2
												ON MAYORES.ClaseConcepto = PARAMETROS2.Id 
											INNER JOIN PARAMETROS AS PARAMETROS3
												ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
											INNER JOIN PARAMETROS AS PARAMETROS4
												ON EMPLEADOS.Estado = PARAMETROS4.Id 
											LEFT JOIN PARAMETROS AS PARAMETROS5 
												ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS5.Id 
								EOD;

								if ($Ciclo < 99)
								{
									$query .= <<<EOD
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND
											PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA LECTIVA' AND
											PARAMETROS3.Detalle <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND
											PARAMETROS3.Detalle <> 'PASANTÍA' AND 
											(PARAMETROS4.Detalle = 'ACTIVO' OR 
											(PARAMETROS4.Detalle = 'RETIRADO' AND 
											EMPLEADOS.FechaRetiro >= '$FechaInicial'))  
									EOD;
								}
								else
								{
									$query .= <<<EOD
										WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
											$ArchivoNomina.Ciclo = $Ciclo AND 
											PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND
											PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA LECTIVA' AND
											PARAMETROS3.Detalle <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND
											PARAMETROS3.Detalle <> 'PASANTÍA' AND 
											PARAMETROS4.Detalle = 'RETIRADO'
									EOD;
								}

								if (! empty($P_IdCentro))
									$query .= <<<EOD
										AND EMPLEADOS.IdCentro = $P_IdCentro 
									EOD;
						
								if (! empty($P_Empleado))
									$query .= <<<EOD
										AND EMPLEADOS.Documento = '$P_Empleado' 
									EOD;
						
								if (! empty($P_TipoEmpleados))
									$query .= <<<EOD
										AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
									EOD;

								$query .= <<<EOD
									AND MAYORES.mayor + AUXILIARES.auxiliar not in ('53003', '53004', '54002')
									$queryValidacionIcetex
									GROUP BY EMPLEADOS.Id;
								EOD;

								$seguridad = $this->model->listarRegistros($query);

								for ($i = 0; $i < count($seguridad); $i++) 
								{ 
									$regSeguridad = $seguridad[$i];

									$IdEmpleado = $regSeguridad['IdEmpleado'];

									// SE LEE EN ACUMULADOS LAS BASES PARA SEGURIDAD SOCIAL
									$query = <<<EOD
										SELECT EMPLEADOS.Id AS IdEmpleado, 
												SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
													PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
													PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
													PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
													PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
													PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
													PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
													PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
													PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
													PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
													PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
													PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO' OR  
													PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
													IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1), 0)) AS Horas,
												SUM(IIF(PARAMETROS2.Detalle = 'SALARIO' OR 
													PARAMETROS5.Detalle = 'ES SUELDO BÁSICO' OR 
													PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)' OR 
													PARAMETROS5.Detalle = 'ES SUELDO BÁSICO (APRENDIZ SENA)' OR 
													PARAMETROS5.Detalle = 'ES SANCIÓN' OR 
													PARAMETROS5.Detalle = 'ES LICENCIA NO REMUNERADA' OR  
													PARAMETROS5.Detalle = 'ES LICENCIA DE MATERNIDAD' OR  
													PARAMETROS5.Detalle = 'ES INCAPACIDAD PROFESIONAL' OR  
													PARAMETROS5.Detalle = 'ES INCAPACIDAD EN TIEMPO' OR  
													PARAMETROS5.Detalle = 'ES INCAPACIDAD > 180 DÍAS' OR  
													PARAMETROS5.Detalle = 'ES DEVOL. INCAPACIDAD > 180 DÍAS' OR  
													PARAMETROS5.Detalle = 'ES VACACIONES EN TIEMPO' OR  
													PARAMETROS5.Detalle = 'ES LICENCIA REMUNERADA',  
													IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1), 0)) AS Valor,
												SUM(IIF(PARAMETROS2.Detalle = 'NO SALARIO', 
													IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, 0), 0)) AS ValorNoSalarial,
												SUM(IIF(PARAMETROS5.Detalle = 'ES FACTOR PRESTACIONAL', ACUMULADOS.Valor, 0)) AS ValorPrestacional
											FROM ACUMULADOS
												INNER JOIN EMPLEADOS 
													ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id
												INNER JOIN AUXILIARES 
													ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
												INNER JOIN MAYORES 
													ON AUXILIARES.IdMayor = MAYORES.Id 
												INNER JOIN CENTROS 
													ON EMPLEADOS.IdCentro = CENTROS.Id 
												INNER JOIN PARAMETROS AS PARAMETROS1
													ON AUXILIARES.Imputacion = PARAMETROS1.Id 
												INNER JOIN PARAMETROS AS PARAMETROS2
													ON MAYORES.ClaseConcepto = PARAMETROS2.Id 
												INNER JOIN PARAMETROS AS PARAMETROS3
													ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
												INNER JOIN PARAMETROS AS PARAMETROS4
													ON EMPLEADOS.Estado = PARAMETROS4.Id 
												LEFT JOIN PARAMETROS AS PARAMETROS5 
													ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS5.Id 
											WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND ACUMULADOS.ciclo=$Ciclo AND 
												PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND
												PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA LECTIVA' AND
												PARAMETROS3.Detalle <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND
												PARAMETROS3.Detalle <> 'PASANTÍA' AND 
												(PARAMETROS4.Detalle = 'ACTIVO' OR 
												(PARAMETROS4.Detalle = 'RETIRADO' AND 
												EMPLEADOS.FechaRetiro >= '$FechaInicial')) AND 
												EMPLEADOS.Id = $IdEmpleado
												AND MAYORES.mayor + AUXILIARES.auxiliar not in ('53003', '53004', '54002')
											GROUP BY EMPLEADOS.Id;
									EOD;

									$acumSeguridad = $this->model->leer($query);

									if ($acumSeguridad AND $Ciclo < 99)
									{
										$regSeguridad['Horas'] += (is_null($acumSeguridad['Horas']) ? 0 : $acumSeguridad['Horas']);
										$regSeguridad['Valor'] += (is_null($acumSeguridad['Valor']) ? 0 : $acumSeguridad['Valor']);
										$regSeguridad['ValorNoSalarial'] += (is_null($acumSeguridad['ValorNoSalarial']) ? 0 : $acumSeguridad['ValorNoSalarial']);
										$regSeguridad['ValorPrestacional'] += (is_null($acumSeguridad['ValorPrestacional']) ? 0 : $acumSeguridad['ValorPrestacional']);
									}

									if ($regSeguridad['Valor'] > 0) 
									{
										$IdEmpleado = $regSeguridad['IdEmpleado'];
										$Horas = $regSeguridad['Horas'];

										$regEmpleado = getRegistro('EMPLEADOS', $IdEmpleado);
										$regCentro = getRegistro('CENTROS', $regEmpleado['idcentro']);

										$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

										if (! empty($regEmpleado['regimencesantias']))
											$NombreRegimenCesantias = getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['detalle'];
										else
											$NombreRegimenCesantias = '';

										// AJUSTE SEGUN DECRETO 1393
										if ($regSeguridad['ValorNoSalarial'] > 0)
										{
											$ValorAdicional40 = ($regSeguridad['Valor'] + $regSeguridad['ValorNoSalarial']) * .4;

											$ValorAdicional40 = max($regSeguridad['ValorNoSalarial'] - $ValorAdicional40, 0);
										}
										else
											$ValorAdicional40 = 0;

										if ($NombreRegimenCesantias == 'SALARIO INTEGRAL') {
											$Base = round(($regSeguridad['Valor'] + $ValorAdicional40) * .7, 0);
											$BaseSS = round(($regSeguridad['ValorSS'] + $ValorAdicional40) * .7, 0);
										} else {
											$Base = ($regSeguridad['Valor'] + $ValorAdicional40);
											$BaseSS = ($regSeguridad['ValorSS'] + $ValorAdicional40);
										}

										switch ($Periodicidad)
										{
											case 'QUINCENAL':
												if (is_null($regEmpleado['fechavencimiento']) AND is_null($regEmpleado['fecharetiro'])) {
													$Base = max($SueldoMinimo / 2, $Base);
													$BaseSS = max($SueldoMinimo / 2, $BaseSS);
												} else {
													$Base = $Base / 2;
													$BaseSS = $BaseSS / 2;
												}

												$Base = min($SueldoMinimo * 25 / 2, $Base);
												$BaseSS = min($SueldoMinimo * 25 / 2, $BaseSS);

												break;

											case 'MENSUAL';
												if ($regEmpleado['fechaingreso'] < $FechaInicialPeriodo)
												{
													if (is_null($regEmpleado['fechavencimiento']) AND is_null($regEmpleado['fecharetiro']) AND $Ciclo <> 2 AND $Ciclo <> 3) {
														$Base = max($SueldoMinimo, $Base);
														$BaseSS = max($SueldoMinimo, $BaseSS);
													}
												}

												$Base = min($SueldoMinimo * 25, $Base);
												$BaseSS = min($SueldoMinimo * 25, $BaseSS);

												break;

											case 'SEMANAL';
												if (is_null($regEmpleado['fechavencimiento']) AND is_null($regEmpleado['fecharetiro'])) {
													$Base = max($SueldoMinimo / 30 * 7, $Base);
													$BaseSS = max($SueldoMinimo / 30 * 7, $BaseSS);
												} else {
													$Base = $Base / 30 * 7;
													$BaseSS = $BaseSS / 30 * 7;
												}

												$Base = min($SueldoMinimo * 25 / 30 * 7, $Base);
												$BaseSS = min($SueldoMinimo * 25 / 30 * 7, $BaseSS);

												break;

											case 'DECADAL';
												if (is_null($regEmpleado['fechavencimiento']) AND is_null($regEmpleado['fecharetiro'])) {
													$Base = max($SueldoMinimo / 3, $Base);
													$BaseSS = max($SueldoMinimo / 3, $BaseSS);
												} else {
													$Base = $Base / 3;
													$BaseSS = $BaseSS / 3;
												}

												$Base = min($SueldoMinimo * 25 / 3, $Base);
												$BaseSS = min($SueldoMinimo * 25 / 3, $BaseSS);

												break;

											case 'CATORCENAL';
												if (is_null($regEmpleado['fechavencimiento']) AND is_null($regEmpleado['fecharetiro'])) {
													$Base = max($SueldoMinimo / 30 * 14, $Base);
													$BaseSS = max($SueldoMinimo / 30 * 14, $BaseSS);
												} else {
													$Base = $Base / 30 * 14;
													$BaseSS = $BaseSS / 30 * 14;
												}

												$Base = min($SueldoMinimo * 25 / 30 * 14, $Base);
												$BaseSS = min($SueldoMinimo * 25 / 30 * 14, $BaseSS);

												break;
										}

										if ($regEmpleado['fechaingreso'] >= $regPeriodo['fechainicial'] AND 
											$regEmpleado['fechaingreso'] <= $regPeriodo['fechafinal'])
											$FechaInicial = $regEmpleado['fechaingreso'];
										else
											$FechaInicial = $regPeriodo['fechainicial'];

										if ($regEmpleado['fechaliquidacion'] >= $regPeriodo['fechainicial'] AND 
											$regEmpleado['fechaliquidacion'] <= $regPeriodo['fechafinal'])
											if ($regEmpleado['diasseguridadsocialenretiro'] == 0)
												$FechaFinal = $regEmpleado['fechaliquidacion'];
											else
											{
												$FechaFinal = date('Y-m-d', strtotime($FechaInicial . ' + ' . $regEmp['diasseguridadsocialenretiro'] . ' days')); 
												$FechaFinal = date('Y-m-d', strtotime($FechaInicial . ' - 1 days')); 
											}
										else
											if (substr($regPeriodo['fechafinal'], 8, 2) == 31) 
												$FechaFinal = date('Y-m-d', strtotime($regPeriodo['fechafinal'] . ' - 1 days')); 
											else
												$FechaFinal = $regPeriodo['fechafinal'];

										$ValorDevolucionSalud = 0;

										// CALCULO PARA SEGURIDAD SOCIAL MES ANTERIOR
										if ($regEmpleado['horasmes'] == 120) 
											$DiasVac = round($regSeguridad['HorasVacaciones'] / 4, 0);
										else
											$DiasVac = ($regSeguridad['HorasVacaciones'] / 8);

										if ($DiasVac == 0.5)
											$DiasVac = 1;

										if ($DiasVac>0) {
											$fecha = new DateTime ($FechaInicialPeriodo);
											$inicio = $fecha->modify('first day of last month');
											$inicio = $inicio->format ('Y-m-d');

											$query = <<<EOD
												SELECT SUM(acu.valor) AS IBC
												FROM acumulados acu
												JOIN auxiliares aux on aux.id = acu.idconcepto
												JOIN mayores may on may.id = aux.idmayor
												WHERE acu.fechainicialperiodo = '$inicio' AND acu.idempleado = $IdEmpleado
													AND may.mayor + aux.auxiliar in (
														'01001', '01002', '01004', '01005', '01006', '01007', '01008', '01010', '01011', '01012',
														'01014', '01015', '01054', '02001', '02002', '02003', '02004', '02005', '02051', '03001',
														'03002', '05004', '06001', '06051', '09001', '10001', '10004', '10006', '10007', '10008',
														'10051', '16001', '17001', '17002', '50001', '50002', '99005', '99012'
													);
											EOD;
											$regIBC = $this->model->leer($query);

											$valVac = $regIBC['IBC']/30*$DiasVac;
											$BaseSS += $valVac;
											$BaseSS = min($SueldoMinimo * 25, $BaseSS);
										}

										for ($j = 0; $j < count($conceptos); $j++) 
										{ 
											$regConcepto = $conceptos[$j];

											$IdConcepto = $regConcepto['id'];

											switch ($regConcepto['NombreTipoRegistroAuxiliar'])
											{
												// SALUD Y PENSION
												case 'ES APORTE DE SALUD':
												case 'ES APORTE DE PENSIÓN':
													$ValorNovedad = round($BaseSS * $regConcepto['factorconversion'] / 100, 0);

													if (intval("$ValorNovedad") <> "$ValorNovedad" OR "$ValorNovedad" % 100 <> 0) 
														$ValorNovedad = round($ValorNovedad + 50, -2);

													if ($regConcepto['NombreTipoRegistroAuxiliar'] == 'ES APORTE DE PENSIÓN' AND $regEmpleado['idfondopensiones'] == 0)
														$ValorNovedad = 0;

													if ($ValorNovedad > 0) 
													{
														$TipoRetencion = $regConcepto['TipoRetencion'];
														$IdCentro = $regEmpleado['idcentro'];
														$TipoEmpleado = $regCentro['tipoempleado'];

														if ($regConcepto['NombreTipoRegistroAuxiliar'] == 'ES APORTE DE SALUD')
															$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $BaseSS, $regConcepto['factorconversion'], 0, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencion, $IdCentro, $TipoEmpleado, $regEmpleado['ideps'], 0);
														else
															$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $BaseSS, $regConcepto['factorconversion'], 0, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencion, $IdCentro, $TipoEmpleado, $regEmpleado['idfondopensiones'], 0);

														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);

														$ValorDevolucionSalud += $ValorNovedad;
													}

													break;
											
												// DEVOLUCION SALUD
												case 'ES DEVOLUCIÓN SALUD':
													if ($ValorDevolucionSalud > 0) 
													{
														$ValorNovedad = $ValorDevolucionSalud;

														$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $Base, $regConcepto['factorconversion'], 0, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
														$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
													}

													break;

												// FONDO DE SOLIDARIDAD
												case 'ES FONDO DE SOLIDARIDAD':
													if ($regEmpleado['idfondopensiones'] == 0)
														$ValorNovedad = 0;
													else
													{
														$IdConceptoFSP = $regFSP['id'];
														$TipoRetencionFSP = $regFSP['TipoRetencion'];

														if ($Base >= $SueldoMinimo * 4)
														{
															$ValorNovedad = $Base * 0.5 / 100;

															if ($ValorNovedad % 100 > 0) 
																$ValorNovedad = round($ValorNovedad + 50, -2);
															else
																$ValorNovedad = round($ValorNovedad, 0);

															$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoFSP, $Base, 0.5, 0, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencionFSP, $IdCentro, $TipoEmpleado, $regEmpleado['idfondopensiones'], 0);
															$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
														}
													}
				
													break;

												case 'ES FONDO DE SUBSISTENCIA':
													if ($regEmpleado['idfondopensiones'] == 0)
														$ValorNovedad = 0;
													else
													{
														$IdConceptoFS = $regFS['id'];
														$TipoRetencionFS = $regFS['TipoRetencion'];
														$ValorNovedad = 0;
														$PorcentajeFS = 0;

														if ($Base >= $SueldoMinimo * 4 AND 
															$Base < $SueldoMinimo * 16)
															$PorcentajeFS = 0.5;

														if ($Base >= $SueldoMinimo * 16 AND 
															$Base < $SueldoMinimo * 17)
															$PorcentajeFS = 0.7;

														if ($Base >= $SueldoMinimo * 17 AND 
															$Base < $SueldoMinimo * 18)
															$PorcentajeFS = 0.9;

														if ($Base >= $SueldoMinimo * 18 AND 
															$Base < $SueldoMinimo * 19)
															$PorcentajeFS = 1.1;

														if ($Base >= $SueldoMinimo * 19 AND 
															$Base < $SueldoMinimo * 20)
															$PorcentajeFS = 1.3;

														if ($Base >= $SueldoMinimo * 20)
															$PorcentajeFS = 1.5;

														$ValorNovedad = $Base * $PorcentajeFS / 100;

														if ($ValorNovedad > 0) 
														{
															if ($ValorNovedad % 100 > 0) 
																$ValorNovedad = round($ValorNovedad + 50, -2);
															else
																$ValorNovedad = round($ValorNovedad, 0);

															$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoFS, $Base, $PorcentajeFS, 0, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencionFS, $IdCentro, $TipoEmpleado, $regEmpleado['idfondopensiones'], 0);
															$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
														}
													}
											}
										}
											
										for ($j = 0; $j < count($conceptos); $j++) 
										{ 
											$regConcepto = $conceptos[$j];

											if ($regConcepto['NombreTipoRegistroAuxiliar'] <> 'ES DEVOLUCION SALUD') 
												continue;

											$IdConcepto = $regConcepto['id'];

											if ($ValorDevolucionSalud > 0 AND $Base > 0)
											{
												$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, 0, 0, 0, $ValorDevolucionSalud, 0, NULL, NULL, 'A', $TipoRetencion, $IdCentro, $TipoEmpleado, 0, 0);
												$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
											}
										}
									}
								}
							}
						}

						// RETENCION FUENTE SALARIOS
						if ($Ciclo != 2 AND $Ciclo != 3) 
						{
							$FechaInicial = $FechaInicialPeriodo;
							$FechaFinal = $FechaFinalPeriodo;
	
							$IdConceptoRF = $regRF['id'];
							$TipoRetencionRF = $regRF['TipoRetencion'];

							$query = <<<EOD
								SELECT $ArchivoNomina.IdEmpleado, 
										EMPLEADOS.IdCentro, 
										CENTROS.TipoEmpleado, 
										PARAMETROS1.Detalle AS MetodoRetencion, 
										EMPLEADOS.PorcentajeRetencion, 
										EMPLEADOS.CuotaVivienda, 
										EMPLEADOS.SaludYEducacion,
										EMPLEADOS.DeduccionDependientes, 
										EMPLEADOS.ExencionAfcFvpAnual, 
										EMPLEADOS.ExencionAnual25, 
										EMPLEADOS.ExencionAnual, 
										SUM(IIF(PARAMETROS2.Detalle = 'SALARIOS', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS IngresoBruto, 
										SUM(IIF(PARAMETROS2.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS PrimaLegal, 
										SUM(IIF(PARAMETROS2.Detalle = 'CESANTIAS', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS Cesantias, 
										SUM(IIF(PARAMETROS2.Detalle = 'SALUD / PENSION', IIF(PARAMETROS3.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS SaludPension, 
										SUM(IIF(PARAMETROS2.Detalle = 'AFC / FVP', IIF(PARAMETROS3.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS AfcFvp
									FROM $ArchivoNomina 
										INNER JOIN EMPLEADOS 
											ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
										LEFT JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN AUXILIARES 
											ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1
											ON EMPLEADOS.MetodoRetencion = PARAMETROS1.Id 
										INNER JOIN PARAMETROS AS PARAMETROS2
											ON MAYORES.TipoRetencion = PARAMETROS2.Id 
										INNER JOIN PARAMETROS AS PARAMETROS3
											ON AUXILIARES.Imputacion = PARAMETROS3.Id 
									WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
										$ArchivoNomina.Ciclo = $Ciclo 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									AND EMPLEADOS.IdCentro = $P_IdCentro 
								EOD;
					
							if (! empty($P_Empleado))
								$query .= <<<EOD
									AND EMPLEADOS.Documento = '$P_Empleado' 
								EOD;
					
							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
								EOD;

							$query .= <<<EOD
									$queryValidacionIcetex
									GROUP BY $ArchivoNomina.IdEmpleado, EMPLEADOS.IdCentro, CENTROS.TipoEmpleado, PARAMETROS1.Detalle, EMPLEADOS.PorcentajeRetencion, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.ExencionAfcFvpAnual, EMPLEADOS.ExencionAnual25, EMPLEADOS.ExencionAnual;
							EOD;

							$acumulados = $this->model->listarRegistros($query);

							if ($acumulados) 
							{
								for ($i = 0; $i < count($acumulados); $i++) 
								{ 
									$regAcumulado = $acumulados[$i];

									$IdEmpleado 		 	= $regAcumulado['IdEmpleado'];
									$IdCentro 			 	= $regAcumulado['IdCentro'];
									$TipoEmpleado 		 	= $regAcumulado['TipoEmpleado'];
									$MetodoRetencion		= $regAcumulado['MetodoRetencion'];
									$PorcentajeRetencion 	= $regAcumulado['PorcentajeRetencion'];
									$CuotaVivienda 			= $regAcumulado['CuotaVivienda'];
									$SaludYEducacion 		= $regAcumulado['SaludYEducacion'];
									$DeduccionDependientes	= $regAcumulado['DeduccionDependientes'];
									$IngresoBruto			= $regAcumulado['IngresoBruto'];
									$PrimaLegal				= $regAcumulado['PrimaLegal'];
									$Cesantias				= $regAcumulado['Cesantias'];
									$SaludPension			= $regAcumulado['SaludPension'];
									$AfcFvp					= $regAcumulado['AfcFvp'];

									if ($MetodoRetencion == 'PORCENTAJE FIJO') 
										$IngresoBruto += $PrimaLegal;

									$IngresoNeto1 = $IngresoBruto - $SaludPension;

									if ($AfcFvp > $IngresoBruto * .3)
									{
										$AfcFvp = round(min($IngresoBruto * .3, $ValorUVT * 316.66), 0);
										$AfcFvpAj = '*';
									}
									else
										$AfcFvpAj = '';

									if ($CuotaVivienda > $ValorUVT * 100) 
									{
										$CuotaVivienda = $ValorUVT * 100;
										$CuotaViviendaAj = '*';
									}
									else
										$CuotaViviendaAj = '';

									if ($SaludYEducacion > $ValorUVT * 16) 
									{
										$SaludYEducacion = $ValorUVT * 16;
										$SaludYEducacionAj = '*';
									}
									else
										$SaludYEducacionAj = '';

									if ($DeduccionDependientes) 
										$DeduccionDependientes = min($ValorUVT * 32, round($IngresoBruto * 0.1, 0));
									else
										$DeduccionDependientes = 0;

									$ValorDeducciones = $AfcFvp + $DeduccionDependientes + $CuotaVivienda + $SaludYEducacion;
									$ValorDeduccionesAj = '';

									// SE COMENTAN ESTAS LINEAS PARA ACTIVAR EN 2024
									// if ($IngresoNeto * .25 > $ValorUVT * 65.83)
									// 	$ValorDeducible25 = round($ValorUVT * 65.83, 0);
									// else
										$ValorDeducible25 = round(($IngresoNeto1 - $ValorDeducciones) * .25, 0);

									if ($regAcumulado['ExencionAnual25'] + $ValorDeducible25 > $ValorUVT * 790)
									{
										$ValorDeducible25 = max($ValorUVT * 790 - $regAcumulado['ExencionAnual25'], 0);
										$ValorDeducible25Aj = '*';
									}
									else
										$ValorDeducible25Aj = '';

									// if ($ValorDeducciones > min($IngresoNeto * .4, $ValorUVT * 1340))
									// 	$IngresoNeto = round($IngresoNeto * .6, 0);
									// else
									// 	$IngresoNeto -= $ValorDeducible25;

									if (($ValorDeducciones + $ValorDeducible25) > ($IngresoNeto1 * .4))
									{
										$ValorDeducciones = round($IngresoNeto1 * .4, 0);
										$ValorDeducible25 = 0;
									}

									if ($regAcumulado['ExencionAnual'] + $ValorDeducciones + $ValorDeducible25 + $AfcFvp > $ValorUVT * 1340)
									{
										$ValorDeducciones = max($ValorUVT * 1340 - $regAcumulado['ExencionAnual'], 0);
										$ValorDeduccionesAJ = '*';
										$ValorDeducible25 = 0;
									}
									else
										$ValorDeduccionesAJ = '';

									$IngresoNeto = $IngresoNeto1 - $ValorDeducciones - $ValorDeducible25;

									if ($MetodoRetencion == 'BUSQUEDA EN TABLA') 
									{
										if ($IngresoNeto <= $ValorUVT * 95)
											$ValorNovedad = 0;
										elseif ($IngresoNeto <= $ValorUVT * 150) 
											$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 95)) * .19, 0);
										elseif ($IngresoNeto <= $ValorUVT * 360)
											$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 150)) * .28, 0) + round($ValorUVT * 10, 0);
										elseif ($IngresoNeto <= $ValorUVT * 640)
											$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 360)) * .33, 0) + round($ValorUVT * 69, 0);
										elseif ($IngresoNeto <= $ValorUVT * 945)
											$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 640)) * .35, 0) + round($ValorUVT * 162, 0);
										elseif ($IngresoNeto <= $ValorUVT * 2300)
											$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 945)) * .37, 0) + round($ValorUVT * 268, 0);
										else
											$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 2300)) * .39, 0) + round($ValorUVT * 770, 0);
									}
									else
									{
										$ValorNovedad = round($IngresoNeto * $PorcentajeRetencion / 100, 0);
									}

									if ($ValorNovedad > 0) 
									{
										$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoRF, $IngresoNeto, $PorcentajeRetencion, 0, $ValorNovedad, 0, NULL, NULL, 'R', $TipoRetencionRF, $IdCentro, $TipoEmpleado, 0, 0);
										$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);

										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET ExencionAfcFvpMes = $AfcFvp, 
													ExencionMes25 = $ValorDeducible25, 
													ExencionMes = $ValorDeducciones 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;

										$ok = $this->model->query($query);
									}
								}
							}
						}
							
						// RETENCION FUENTE PRIMA DE SERVICIOS
						if (TRUE)
						{
							$FechaInicial = $FechaInicialPeriodo;
							$FechaFinal = $FechaFinalPeriodo;
	
							$query = <<<EOD
								SELECT AUXILIARES.*, 
										MAYORES.TipoRetencion 
									FROM AUXILIARES 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS 
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
									WHERE PARAMETROS.Detalle = 'ES RETENCIÓN PRIMA DE SERVICIOS'; 
							EOD;

							$regConcepto 		= $this->model->leer($query);
							$IdConceptoRF 		= $regConcepto['id'];
							$TipoRetencionRF 	= $regConcepto['TipoRetencion'];

							$query = <<<EOD
								SELECT $ArchivoNomina.IdEmpleado, 
										EMPLEADOS.IdCentro, 
										CENTROS.TipoEmpleado, 
										PARAMETROS1.Detalle AS MetodoRetencion, 
										EMPLEADOS.PorcentajeRetencion, 
										EMPLEADOS.CuotaVivienda, 
										EMPLEADOS.SaludYEducacion,
										EMPLEADOS.DeduccionDependientes, 
										EMPLEADOS.ExencionAnual25, 
										EMPLEADOS.ExencionAnual, 
										SUM(IIF(PARAMETROS2.Detalle = 'SALARIOS', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS IngresoBruto, 
										SUM(IIF(PARAMETROS2.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS PrimaLegal, 
										SUM(IIF(PARAMETROS2.Detalle = 'CESANTIAS', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS Cesantias, 
										SUM(IIF(PARAMETROS2.Detalle = 'SALUD / PENSION', IIF(PARAMETROS3.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS SaludPension, 
										SUM(IIF(PARAMETROS2.Detalle = 'AFC / FVP', IIF(PARAMETROS3.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS AfcFvp
									FROM $ArchivoNomina 
										INNER JOIN EMPLEADOS 
											ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
										LEFT JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN AUXILIARES 
											ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1
											ON EMPLEADOS.MetodoRetencion = PARAMETROS1.Id 
										INNER JOIN PARAMETROS AS PARAMETROS2
											ON MAYORES.TipoRetencion = PARAMETROS2.Id 
										INNER JOIN PARAMETROS AS PARAMETROS3
											ON AUXILIARES.Imputacion = PARAMETROS3.Id 
									WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
										$ArchivoNomina.Ciclo = $Ciclo AND 
										PARAMETROS1.Detalle = 'BUSQUEDA EN TABLA' 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									AND EMPLEADOS.IdCentro = $P_IdCentro 
								EOD;
					
							if (! empty($P_Empleado))
								$query .= <<<EOD
									AND EMPLEADOS.Documento = '$P_Empleado' 
								EOD;
					
							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
								EOD;

							$query .= <<<EOD
									$queryValidacionIcetex
									GROUP BY $ArchivoNomina.IdEmpleado, EMPLEADOS.IdCentro, CENTROS.TipoEmpleado, PARAMETROS1.Detalle, EMPLEADOS.PorcentajeRetencion, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.ExencionAnual25, EMPLEADOS.ExencionAnual; 
							EOD;

							$acumulados = $this->model->listarRegistros($query);

							if ($acumulados) 
							{
								for ($i = 0; $i < count($acumulados); $i++) 
								{ 
									$regAcumulado = $acumulados[$i];

									$IdEmpleado 		 	= $regAcumulado['IdEmpleado'];
									$IdCentro 			 	= $regAcumulado['IdCentro'];
									$TipoEmpleado 		 	= $regAcumulado['TipoEmpleado'];
									$MetodoRetencion		= $regAcumulado['MetodoRetencion'];
									$PorcentajeRetencion 	= $regAcumulado['PorcentajeRetencion'];
									$CuotaVivienda 			= $regAcumulado['CuotaVivienda'];
									$SaludYEducacion 		= $regAcumulado['SaludYEducacion'];
									$DeduccionDependientes	= $regAcumulado['DeduccionDependientes'];
									$IngresoBruto			= $regAcumulado['IngresoBruto'];
									$PrimaLegal				= $regAcumulado['PrimaLegal'];
									$Cesantias				= $regAcumulado['Cesantias'];
									$SaludPension			= $regAcumulado['SaludPension'];
									$AfcFvp					= $regAcumulado['AfcFvp'];

									if ($PrimaLegal <= 0)
										continue;

									$ValorDeducible25 = round($PrimaLegal * .25, 0);

									if ($regAcumulado['ExencionAnual25'] + $ValorDeducible25 > $ValorUVT * 790)
										$ValorDeducible25 = $ValorUVT * 790 - $regAcumulado['ExencionAnual25'];

									$IngresoNeto = $PrimaLegal - $ValorDeducible25;

									if (($IngresoNeto) <= $ValorUVT * 95)
										$ValorNovedad = 0;
									elseif (($IngresoNeto) <= $ValorUVT * 150) 
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 95)) * .19, 0);
									elseif (($IngresoNeto) <= $ValorUVT * 360)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 150)) * .28, 0) + round($ValorUVT * 10, 0);
									elseif (($IngresoNeto) <= $ValorUVT * 640)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 360)) * .33, 0) + round($ValorUVT * 69, 0);
									elseif (($IngresoNeto) <= $ValorUVT * 945)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 640)) * .35, 0) + round($ValorUVT * 162, 0);
									elseif (($IngresoNeto) <= $ValorUVT * 2300)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 945)) * .37, 0) + round($ValorUVT * 268, 0);
									else
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 2300)) * .39, 0) + round($ValorUVT * 770, 0);

									if ($ValorNovedad > 0) 
									{
										$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoRF, $IngresoNeto, $PorcentajeRetencion, 0, $ValorNovedad, 0, NULL, NULL, 'R', $TipoRetencionRF, $IdCentro, $TipoEmpleado, 0, 0);
										$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);

										$query = <<<EOD
											UPDATE EMPLEADOS 
												SET ExencionAfcFvpMes = $AfcFvp, 
													ExencionMes25 = $ValorDeducible25, 
													ExencionMes = $ValorDeducciones 
												WHERE EMPLEADOS.Id = $IdEmpleado;
										EOD;

										$ok = $this->model->query($query);
									}
								}
							}
						}

						// CREDITOS AUTOMATICOS A EMPLEADOS CON SALDO EN ROJO
						if ($Ciclo < 98) 
						{
							$query = <<<EOD
								SELECT $ArchivoNomina.IdEmpleado, 
										EMPLEADOS.IdCentro, 
										CENTROS.TipoEmpleado, 
										SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, $ArchivoNomina.Valor * -1)) AS Valor
									FROM $ArchivoNomina 
										INNER JOIN EMPLEADOS 
											ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
										INNER JOIN AUXILIARES 
											ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
										INNER JOIN CENTROS 
											ON EMPLEADOS.IdCentro = CENTROS.Id 
										INNER JOIN PARAMETROS 
											ON AUXILIARES.Imputacion = PARAMETROS.Id 
									WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
										$ArchivoNomina.Ciclo = $Ciclo 
							EOD;

							if (! empty($P_IdCentro))
								$query .= <<<EOD
									AND EMPLEADOS.IdCentro = $P_IdCentro 
								EOD;
					
							if (! empty($P_Empleado))
								$query .= <<<EOD
									AND EMPLEADOS.Documento = '$P_Empleado' 
								EOD;
					
							if (! empty($P_TipoEmpleados))
								$query .= <<<EOD
									AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
								EOD;

							$query .= <<<EOD
									$queryValidacionIcetex
									GROUP BY $ArchivoNomina.IdEmpleado, EMPLEADOS.IdCentro, CENTROS.TipoEmpleado;
							EOD;

							$saldos = $this->model->listarRegistros($query);

							if ($saldos) 
							{
								for ($i = 0; $i < count($saldos); $i++) 
								{ 
									$regSaldo = $saldos[$i];

									$IdEmpleado = $regSaldo['IdEmpleado'];
									$IdCentro = $regSaldo['IdCentro'];
									$TipoEmpleado = $regSaldo['TipoEmpleado'];
									$ValorNovedad = $regSaldo['Valor'];

									if ($regSaldo['Valor'] >= 0) 
										continue;
									else
										$ValorNovedad = abs($ValorNovedad);

									$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoPrestamo, 0, 0, 0, $ValorNovedad, 0, NULL, NULL, 'A', $TipoRetencionPrestamo, $IdCentro, $TipoEmpleado, 0, 0);
									$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);

									$Fecha = date('Y-m-d');

									$query = <<<EOD
										INSERT INTO PRESTAMOS
											(IdEmpleado, IdConcepto, TipoPrestamo, Fecha, ValorPrestamo, ValorCuota, Cuotas, SaldoPrestamo, SaldoCuotas, IdPeriodo, Ciclo) 
											VALUES (
												$IdEmpleado, $IdConceptoCuotaPrestamo, $TipoPrestamoEmpresa, '$Fecha', $ValorNovedad, $ValorNovedad, 1, $ValorNovedad, 1, $IdPeriodo, $Ciclo);
									EOD;

									$ok = $this->model->actualizarRegistros($query);
								}
							}
						}

						if (! $RetornaLiquidacion)
						{
							header('Location: ' . SERVERURL . '/liquidacionPrenomina/lista/1');
							exit();
						}
					}
				}
			}

			if (! $RetornaLiquidacion)
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionPrenomina/editar';
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
				$_SESSION['Lista'] = SERVERURL . '/liquidacionPrenomina/lista/1';

				$query = <<<EOD
					SELECT EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							EMPLEADOS.SueldoBasico, 
							EMPLEADOS.FechaVencimiento, 
							EMPLEADOS.FechaRetiro, 
							EMPLEADOS.ModalidadTrabajo, 
							EMPLEADOS.RegimenCesantias, 
							EMPLEADOS.TipoContrato, 
							EMPLEADOS.SubsidioTransporte, 
							EMPLEADOS.MetodoRetencion, 
							EMPLEADOS.IdCentro 
						FROM EMPLEADOS 
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
							EMPLEADOS.FechaIngreso <= '$FechaFinalPeriodo'
							$queryValidacionIcetex; 
				EOD;

				$regs = $this->model->listarRegistros($query);

				if ($regs) 
				{
					for ($i = 0; $i < count($regs); $i++) 
					{ 
						if ($regs[$i]['IdCentro'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definido un Centro de costo.<br>'; 
						if ($regs[$i]['SueldoBasico'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definido un Sueldo Básico.<br>'; 
						if (! is_null($regs[$i]['FechaVencimiento']) AND $regs[$i]['FechaVencimiento'] < $FechaInicialPeriodo)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') Tiene contrato vencido (' . $regs[$i]['FechaVencimiento'] . ').<br>'; 
						if (! is_null($regs[$i]['FechaRetiro']) AND $regs[$i]['FechaRetiro'] < $FechaInicialPeriodo)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') Tiene fecha de retiro (' . $regs[$i]['FechaVencimiento'] . ') estando activo.<br>'; 
						if ($regs[$i]['ModalidadTrabajo'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definida una Modalidad de trabajo.<br>'; 
						if ($regs[$i]['RegimenCesantias'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definido un Régimen de Cesantías.<br>'; 
						if ($regs[$i]['TipoContrato'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definido un Tipo de Contrato.<br>'; 
						if ($regs[$i]['SubsidioTransporte'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definido un Subsidio de Transporte.<br>'; 
						if ($regs[$i]['MetodoRetencion'] == 0)
							$data['mensajeError'] .= 'EMPLEADO ' . $regs[$i]['Documento'] . ' (' . $regs[$i]['Apellido1'] . ' ' . $regs[$i]['Apellido2'] . ' ' . $regs[$i]['Nombre1'] . ' ' . $regs[$i]['Nombre2'] . ') No tiene definido un Método de Retención.<br>'; 
					}
				}
				
				$this->views->getView($this, 'actualizar', $data);
			}
		}

		function compareObjects($obj1, $obj2, $fieldToIgnore) {
			foreach ($obj1 as $key => $value) {
				if ($key != $fieldToIgnore && (!isset($obj2->$key) OR $value != $obj2->$key)) {
					return false;
				}
			}
			return true;
		}
		
		public function lista($pagina)
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
			$_SESSION['Exportar'] = SERVERURL . '/liquidacionPrenomina/informePrenomina';
			$_SESSION['Informe'] = SERVERURL . '/liquidacionPrenomina/informePrenomina/1';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/liquidacionPrenomina/liquidar';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['LIQ_PRENOMINA']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['LIQ_PRENOMINA']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['LIQ_PRENOMINA']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['LIQ_PRENOMINA']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['LIQ_PRENOMINA']['Filtro']))
			{
				$_SESSION['LIQ_PRENOMINA']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['LIQ_PRENOMINA']['Filtro'];

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
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

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['LIQ_PRENOMINA']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['LIQ_PRENOMINA']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['LIQ_PRENOMINA']['Orden'])) 
					$_SESSION['LIQ_PRENOMINA']['Orden'] = "EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Imputacion,MAYORES.Mayor,AUXILIARES.Auxiliar";

			$query = <<<EOD
				WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND
					$ArchivoNomina.Ciclo = $Ciclo 
			EOD;
			
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
			{
				$query = <<<EOD
					WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
						$ArchivoNomina.Ciclo = $Ciclo 
				EOD;
			
				$data['registros'] = $this->model->contarRegistros($ArchivoNomina, $query);
				$query .= 'ORDER BY ' . $_SESSION['LIQ_PRENOMINA']['Orden']; 
				$data['rows'] = $this->model->exportarPrenomina($ArchivoNomina, $query);

				$Archivo = './descargas/' . $_SESSION['Login']['Usuario'] . '_LiquidacionPrenomina_' . date('YmdGis') . '.csv';

				$output = fopen($Archivo, 'w');

				fputcsv($output, array('FECHA INI. PERIODO', 'FECHA FIN. PERIODO', 'EMPLEADO', 'NOMBRE EMPLEADO', 'CONCEPTO', 'DESCRIPCION', 'BASE', 'CANTIDAD', 'TIEMPO', 'PAGOS', 'DEDUCCIONES', 'NETO', 'FECHA INI.', 'FECHA FIN.', 'TERCEROS'), ';');

				for ($i = 0; $i < count($data['rows']); $i++) 
				{ 
					$reg = $data['rows'][$i];

					foreach ($reg as $key => $value) 
					{
						if ($key == 'FechaInicialPeriodo' OR 
							$key == 'FechaFinalPeriodo' OR 
							$key == 'Base' OR
							$key == 'Horas' OR
							$key == 'Pagos' OR 
							$key == 'Deducciones' OR 
							$key == 'FechaInicial' OR 
							$key == 'FechaFinal')
							continue;

						$reg[$key] = utf8_decode($value);
					}

					$regDatos = array($reg['FechaInicialPeriodo'], $reg['FechaFinalPeriodo'], $reg['Documento'], $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], $reg['Mayor'] . $reg['Auxiliar'], $reg['NombreConcepto'], number_format($reg['Base'], 0, '.', ''), number_format($reg['Horas'], 2, '.', ''), $reg['Tiempo'], number_format($reg['Pagos'], 0, '.', ''), number_format($reg['Deducciones'], 0, '.', ''), $reg['Pagos'] > 0 ? number_format($reg['Pagos'], 0, '.', '') : - number_format($reg['Deducciones'], 0, '.', ''), $reg['FechaInicial'], $reg['FechaFinal'], $reg['NombreTercero']);

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
				if	( ! empty($lcFiltro) )
				{
					$aFiltro = explode(' ', $lcFiltro);
	
					for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
					{
						if ($lnCount == 0)
							$query .= ' AND ( ';
						else
							$query .= 'OR ';
	
						$query .= "EMPLEADOS.Documento LIKE '%" . $aFiltro[$lnCount] . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					}
	
					$query .= ') ';
				}

				$data['registros'] = $this->model->contarRegistros($ArchivoNomina, $query);
				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
	
				$query1 = $query;
				$query .= 'ORDER BY ' . $_SESSION['LIQ_PRENOMINA']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listarLiquidacionPrenomina($ArchivoNomina, $query1, $query);
		
				$this->views->getView($this, 'liquidacionPrenomina', $data);
			}
		}	

		public function informePrenomina($pagina)
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
			$_SESSION['Lista'] = SERVERURL . '/liquidacionPrenomina/liquidar';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['LIQ_PRENOMINA']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['LIQ_PRENOMINA']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['LIQ_PRENOMINA']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['LIQ_PRENOMINA']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['LIQ_PRENOMINA']['Filtro']))
			{
				$_SESSION['LIQ_PRENOMINA']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['LIQ_PRENOMINA']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['LIQ_PRENOMINA']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['LIQ_PRENOMINA']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['LIQ_PRENOMINA']['Orden'])) 
					$_SESSION['LIQ_PRENOMINA']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,MAYORES.Mayor,AUXILIARES.Auxiliar';

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
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

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			$query = <<<EOD
				WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND
					$ArchivoNomina.Ciclo = $Ciclo 
			EOD;

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if ($lnCount == 0)
						$query .= ' AND ( ';
					else
						$query .= 'OR ';

					$query .= "EMPLEADOS.Documento LIKE '%" . $aFiltro[$lnCount] . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ') ';
			}
			
			$data['registros'] = $this->model->contarRegistros($ArchivoNomina, $query);

			$query1 = $query;
			$query .= 'ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2'; 
			$data['rows'] = $this->model->informePrenomina($ArchivoNomina, $query1, $query);

			$this->views->getView($this, 'informePrenomina', $data);
		}	

		public function retencionFuente()
		{
			set_time_limit(0);

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");
			$reg5 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'");
			$reg6 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'TipoPrestamo' AND PARAMETROS.Detalle = 'PRÉSTAMO EMPRESA'");

			$Referencia = $reg1['valor'];
			$IdPeriodicidad = $reg2['valor'];

			$Periodicidad = getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$cPeriodicidad = substr($Periodicidad, 0, 1);
			$IdPeriodo = $reg3['valor'];
			$Ciclo = $reg4['valor'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			$ValorUVT = $reg5['valor'];
			$TipoPrestamoEmpresa = $reg6['id'];

			if ($regPeriodo) 
			{
				// SE LEEN LOS PARÁMETROS
				$data = array(
					'reg' => array(
						'Periodo' => $regPeriodo['periodo'], 
						'Ciclo' => $Ciclo, 
						'FechaInicial' => $regPeriodo['fechainicial'], 
						'FechaFinal' => $regPeriodo['fechafinal'],
						'IdCentro' => isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
						'Empleado' => isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
						'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : 0
						),
					'mensajeError' => ''
				);

				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];
			}
			else
				$data['mensajeError'] .= label('Perído definido no existe') . '<br>';

			// SE  BUSCA EL CONCEPTO DE RET.FTE.
			$query = <<<EOD
				SELECT AUXILIARES.*, 
						MAYORES.TipoRetencion 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ES RETENCIÓN SALARIOS'; 
			EOD;

			$regRF = $this->model->leerRegistro($query);

			if (empty($regRF)) 
				$data['mensajeError'] .= label('No hay definido un concepto de Retención Fuente') . '<br>';

			$IdIcetex = getId('CENTROS', "CENTROS.centro = 'S1376'");
			$queryValidacionIcetex = '';

			if (!$IdIcetex) $data['mensajeError'] .= label('Para poder usar el Ciclo 20 o 21 debe existir el centro de costo ICETEX con codigo de proyecto "S1376"') . '<br>';

			if ($Ciclo == 20 AND $IdIcetex) // PRENOMINA SIN ICETEX
				$queryValidacionIcetex = <<<EOD
					AND EMPLEADOS.IdProyecto <> $IdIcetex 
				EOD;

			if ($Ciclo == 21 AND $IdIcetex) // PRENOMINA SOLO ICETEX
				$queryValidacionIcetex = <<<EOD
					AND EMPLEADOS.IdProyecto = $IdIcetex 
				EOD;

			if (empty($data['mensajeError'])) 
			{
				if (isset($_REQUEST['IdCentro']))
				{
					$P_IdCentro = $_REQUEST['IdCentro'];
					$P_Empleado = $_REQUEST['Empleado'];
					$P_TipoEmpleados = $_REQUEST['TipoEmpleados'];

					$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

					$data['mensajeError'] = '';

					// RETENCION FUENTE SALARIOS
					if (TRUE) 
					{
						$FechaInicial = $FechaInicialPeriodo;
						$FechaFinal = $FechaFinalPeriodo;

						$IdConceptoRF = $regRF['id'];
						$TipoRetencionRF = $regRF['TipoRetencion'];

						$query = <<<EOD
							SELECT EMPLEADOS.Id AS IdEmpleado, 
									EMPLEADOS.Documento, 
									SUM(IIF(PARAMETROS1.Detalle = 'SALARIOS', IIF(PARAMETROS2.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS IngresoBruto, 
									SUM(IIF(PARAMETROS1.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS2.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS PrimaLegal, 
									SUM(IIF(PARAMETROS1.Detalle = 'CESANTIAS', IIF(PARAMETROS2.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS Cesantias, 
									SUM(IIF(PARAMETROS1.Detalle = 'SALUD / PENSION', IIF(PARAMETROS2.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS SaludPension, 
									SUM(IIF(PARAMETROS1.Detalle = 'AFC / FVP', IIF(PARAMETROS2.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS AfcFvp
								FROM $ArchivoNomina 
									INNER JOIN EMPLEADOS 
										ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
									LEFT JOIN CENTROS 
										ON EMPLEADOS.IdCentro = CENTROS.Id 
									INNER JOIN AUXILIARES 
										ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES 
										ON AUXILIARES.IdMayor = MAYORES.Id 
									INNER JOIN PARAMETROS AS PARAMETROS1
										ON MAYORES.TipoRetencion = PARAMETROS1.Id 
									INNER JOIN PARAMETROS AS PARAMETROS2
										ON AUXILIARES.Imputacion = PARAMETROS2.Id 
								WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
									$ArchivoNomina.Ciclo = $Ciclo 
						EOD;

						if (! empty($P_IdCentro))
							$query .= <<<EOD
								AND EMPLEADOS.IdCentro = $P_IdCentro 
							EOD;
				
						if (! empty($P_Empleado))
							$query .= <<<EOD
								AND EMPLEADOS.Documento = '$P_Empleado' 
							EOD;
				
						if (! empty($P_TipoEmpleados))
							$query .= <<<EOD
								AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
							EOD;

						$query .= <<<EOD
							$queryValidacionIcetex
							GROUP BY EMPLEADOS.Id, EMPLEADOS.Documento 
							ORDER BY EMPLEADOS.Documento;
						EOD;

						$acumulados = $this->model->listarRegistros($query);

						if ($acumulados) 
						{
							$aRetFte = array();

							for ($i = 0; $i < count($acumulados); $i++) 
							{ 
								$regAcumulado 	= $acumulados[$i];
								$IdEmpleado 	= $regAcumulado['IdEmpleado'];

								$query = <<<EOD
									SELECT EMPLEADOS.Documento, 
											EMPLEADOS.Apellido1, 
											EMPLEADOS.Apellido2, 
											EMPLEADOS.Nombre1, 
											EMPLEADOS.Nombre2, 
											PARAMETROS1.Detalle AS MetodoRetencion,
											EMPLEADOS.PorcentajeRetencion, 
											CENTROS.Centro, 
											CENTROS.Nombre AS NombreCentro, 
											PARAMETROS2.Detalle AS TipoEmpleado, 
											EMPLEADOS.CuotaVivienda, 
											EMPLEADOS.SaludYEducacion, 
											EMPLEADOS.DeduccionDependientes, 
											EMPLEADOS.ExencionAnual25, 
											EMPLEADOS.ExencionAnual   
										FROM EMPLEADOS  
											LEFT JOIN CENTROS 
												ON EMPLEADOS.IdCentro = CENTROS.Id 
											INNER JOIN PARAMETROS AS PARAMETROS1
												ON EMPLEADOS.MetodoRetencion = PARAMETROS1.Id 
											INNER JOIN PARAMETROS AS PARAMETROS2 
												ON CENTROS.TipoEmpleado = PARAMETROS2.Id 
										WHERE EMPLEADOS.Id = $IdEmpleado;
								EOD;

								$regEmpleado = $this->model->leer($query);

								$CuotaVivienda 			= $regEmpleado['CuotaVivienda'];
								$SaludYEducacion 		= $regEmpleado['SaludYEducacion'];
								$DeduccionDependientes	= $regEmpleado['DeduccionDependientes'];
								$PorcentajeRetencion	= $regEmpleado['PorcentajeRetencion'];
								
								$IngresoBruto			= $regAcumulado['IngresoBruto'];
								$PrimaLegal				= $regAcumulado['PrimaLegal'];
								$Cesantias				= $regAcumulado['Cesantias'];
								$SaludPension			= $regAcumulado['SaludPension'];
								$AfcFvp					= $regAcumulado['AfcFvp'];

								if ($regEmpleado['MetodoRetencion'] == 'PORCENTAJE FIJO') 
									$IngresoBruto += $PrimaLegal;

								$IngresoNeto1 = $IngresoBruto - $SaludPension;

								if ($AfcFvp > $IngresoBruto * .3)
								{
									$AfcFvp = round(min($IngresoBruto * .3, $ValorUVT * 316.66), 0);
									$AfcFvpAj = '*';
								}
								else
									$AfcFvpAj = '';

								if ($CuotaVivienda > $ValorUVT * 100) 
								{
									$CuotaVivienda = $ValorUVT * 100;
									$CuotaViviendaAj = '*';
								}
								else
									$CuotaViviendaAj = '';

								if ($SaludYEducacion > $ValorUVT * 16) 
								{
									$SaludYEducacion = $ValorUVT * 16;
									$SaludYEducacionAj = '*';
								}
								else
									$SaludYEducacionAj = '';

								if ($DeduccionDependientes) 
									$DeduccionDependientes = min($ValorUVT * 32, round($IngresoBruto * 0.1, 0));
								else
									$DeduccionDependientes = 0;

								$ValorDeducciones = $AfcFvp + $DeduccionDependientes + $CuotaVivienda + $SaludYEducacion;
								$ValorDeduccionesAj = '';

								// SE COMENTAN ESTAS LINEAS PARA ACTIVAR EN 2024
								// if ($IngresoNeto * .25 > $ValorUVT * 65.83)
								// 	$ValorDeducible25 = round($ValorUVT * 65.83, 0);
								// else
									$ValorDeducible25 = round(($IngresoNeto1 - $ValorDeducciones) * .25, 0);

								if ($regEmpleado['ExencionAnual25'] + $ValorDeducible25 > $ValorUVT * 790)
								{
									$ValorDeducible25 = max($ValorUVT * 790 - $regEmpleado['ExencionAnual25'], 0);
									$ValorDeducible25Aj = '*';
								}
								else
									$ValorDeducible25Aj = '';

								// if ($ValorDeducciones > min($IngresoNeto * .4, $ValorUVT * 1340))
								// 	$IngresoNeto = round($IngresoNeto * .6, 0);
								// else
								// 	$IngresoNeto -= $ValorDeducible25;

								if (($ValorDeducciones + $ValorDeducible25) > ($IngresoNeto1 * .4))
								{
									$ValorDeducciones = round($IngresoNeto1 * .4, 0);
									$ValorDeducible25 = 0;
								}

								if ($regEmpleado['ExencionAnual'] + $ValorDeducciones + $ValorDeducible25 + $AfcFvp > $ValorUVT * 1340)
								{
									$ValorDeducciones = max($ValorUVT * 1340 - $regEmpleado['ExencionAnual'], 0);
									$ValorDeduccionesAJ = '*';
									$ValorDeducible25 = 0;
								}
								else
									$ValorDeduccionesAJ = '';

								$IngresoNeto = $IngresoNeto1 - $ValorDeducciones - $ValorDeducible25;

								if ($regEmpleado['MetodoRetencion'] == 'BUSQUEDA EN TABLA') 
								{
									if ($IngresoNeto <= $ValorUVT * 95)
										$ValorNovedad = 0;
									elseif ($IngresoNeto <= $ValorUVT * 150) 
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 95)) * .19, 0);
									elseif ($IngresoNeto <= $ValorUVT * 360)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 150)) * .28, 0) + round($ValorUVT * 10, 0);
									elseif ($IngresoNeto <= $ValorUVT * 640)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 360)) * .33, 0) + round($ValorUVT * 69, 0);
									elseif ($IngresoNeto <= $ValorUVT * 945)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 640)) * .35, 0) + round($ValorUVT * 162, 0);
									elseif ($IngresoNeto <= $ValorUVT * 2300)
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 945)) * .37, 0) + round($ValorUVT * 268, 0);
									else
										$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 2300)) * .39, 0) + round($ValorUVT * 770, 0);
								}
								else
								{
									$ValorNovedad = round($IngresoNeto * $PorcentajeRetencion / 100, 0);
								}

								if ($ValorNovedad > 0) 
								{
									$aRetFte[] = array(
										'Periodo' 				=> $Periodo,
										'Ciclo' 				=> $Ciclo, 
										'FechaInicial' 			=> $regPeriodo['fechainicial'], 
										'FechaFinal' 			=> $regPeriodo['fechafinal'], 
										'TipoRetFte' 			=> 'SALARIOS', 
										'Documento' 			=> $regEmpleado['Documento'], 
										'Apellido1' 			=> $regEmpleado['Apellido1'], 
										'Apellido2' 			=> $regEmpleado['Apellido2'], 
										'Nombre1' 				=> $regEmpleado['Nombre1'], 
										'Nombre2' 				=> $regEmpleado['Nombre2'], 
										'Centro' 				=> $regEmpleado['Centro'], 
										'NombreCentro' 			=> $regEmpleado['NombreCentro'], 
										'TipoEmpleado' 			=> $regEmpleado['TipoEmpleado'], 
										'MetodoRetencion' 		=> $regEmpleado['MetodoRetencion'], 
										'PorcentajeRetencion' 	=> $regEmpleado['PorcentajeRetencion'], 
										'IngresoBruto' 			=> $IngresoBruto, 
										'SaludPension'			=> $SaludPension, 
										'AfcFvp'				=> $AfcFvp, 
										'AfcFvpAj'				=> $AfcFvpAj, 
										'TopeAfcFvp'			=> round($ValorUVT * 316.66, 0),
										'DeduccionDependientes' => $DeduccionDependientes, 
										'TopeDedDep'			=> $ValorUVT * 32, 
										'CuotaVivienda'			=> $CuotaVivienda, 
										'CuotaViviendaAj'		=> $CuotaViviendaAj, 
										'TopeCuotaVivienda'		=> $ValorUVT * 100, 
										'SaludYEducacion'		=> $SaludYEducacion, 
										'SaludYEducacionAj'		=> $SaludYEducacionAj, 
										'TopeSaludYEducacion'	=> $ValorUVT * 16, 
										'ValorDeducciones' 		=> $ValorDeducciones, 
										'ValorDeduccionesAj'	=> $ValorDeduccionesAj, 
										'ExencionAnual'		=> $regEmpleado['ExencionAnual'], 
										'TopeAnual'				=> $ValorUVT * 1340,
										'IngresoNeto1'			=> $IngresoNeto1, 
										'ValorDeducible25'		=> $ValorDeducible25, 
										'ValorDeducible25Aj'	=> $ValorDeducible25Aj, 
										'ExencionAnual25'		=> $regEmpleado['ExencionAnual25'], 
										'TopeAnual25'			=> $ValorUVT * 790,
										'IngresoNeto'			=> $IngresoNeto, 
										'ValorRetFte' 			=> $ValorNovedad);
								}
							}
						}
					}
							
					// RETENCION FUENTE PRIMA DE SERVICIOS
					if (TRUE)
					{
						$FechaInicial = $FechaInicialPeriodo;
						$FechaFinal = $FechaFinalPeriodo;

						$query = <<<EOD
							SELECT AUXILIARES.*, 
									MAYORES.TipoRetencion 
								FROM AUXILIARES 
									INNER JOIN MAYORES 
										ON AUXILIARES.IdMayor = MAYORES.Id 
									INNER JOIN PARAMETROS 
										ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
								WHERE PARAMETROS.Detalle = 'ES RETENCIÓN PRIMA DE SERVICIOS'; 
						EOD;

						$regConcepto 		= $this->model->leer($query);
						$IdConceptoRF 		= $regConcepto['id'];
						$TipoRetencionRF 	= $regConcepto['TipoRetencion'];

						$query = <<<EOD
							SELECT EMPLEADOS.Id AS IdEmpleado, 
									EMPLEADOS.Documento, 
									SUM(IIF(PARAMETROS1.Detalle = 'SALARIOS', IIF(PARAMETROS2.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS IngresoBruto, 
									SUM(IIF(PARAMETROS1.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS2.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS PrimaLegal, 
									SUM(IIF(PARAMETROS1.Detalle = 'CESANTIAS', IIF(PARAMETROS2.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS Cesantias, 
									SUM(IIF(PARAMETROS1.Detalle = 'SALUD / PENSION', IIF(PARAMETROS2.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS SaludPension, 
									SUM(IIF(PARAMETROS1.Detalle = 'AFC / FVP', IIF(PARAMETROS2.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS AfcFvp
								FROM $ArchivoNomina 
									INNER JOIN EMPLEADOS 
										ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
									LEFT JOIN CENTROS 
										ON EMPLEADOS.IdCentro = CENTROS.Id 
									INNER JOIN AUXILIARES 
										ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES 
										ON AUXILIARES.IdMayor = MAYORES.Id 
									INNER JOIN PARAMETROS AS PARAMETROS1
										ON MAYORES.TipoRetencion = PARAMETROS1.Id 
									INNER JOIN PARAMETROS AS PARAMETROS2
										ON AUXILIARES.Imputacion = PARAMETROS2.Id 
									INNER JOIN PARAMETROS AS PARAMETROS3
										ON EMPLEADOS.MetodoRetencion = PARAMETROS3.Id 
								WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
									$ArchivoNomina.Ciclo = $Ciclo AND 
									PARAMETROS1.Detalle = 'PRIMA LEGAL' AND 
									PARAMETROS3.Detalle = 'BUSQUEDA EN TABLA' 
						EOD;

						if (! empty($P_IdCentro))
							$query .= <<<EOD
								AND EMPLEADOS.IdCentro = $P_IdCentro 
							EOD;
				
						if (! empty($P_Empleado))
							$query .= <<<EOD
								AND EMPLEADOS.Documento = '$P_Empleado' 
							EOD;
				
						if (! empty($P_TipoEmpleados))
							$query .= <<<EOD
								AND CENTROS.TipoEmpleado = $P_TipoEmpleados 
							EOD;

						$query .= <<<EOD
							$queryValidacionIcetex
							GROUP BY EMPLEADOS.Id, EMPLEADOS.Documento 
							ORDER BY EMPLEADOS.Documento;
						EOD;

						$acumulados = $this->model->listarRegistros($query);

						if ($acumulados) 
						{
							for ($i = 0; $i < count($acumulados); $i++) 
							{ 
								$regAcumulado 	= $acumulados[$i];
								$IdEmpleado 	= $regAcumulado['IdEmpleado'];

								$query = <<<EOD
									SELECT EMPLEADOS.Documento, 
											EMPLEADOS.Apellido1, 
											EMPLEADOS.Apellido2, 
											EMPLEADOS.Nombre1, 
											EMPLEADOS.Nombre2, 
											PARAMETROS1.Detalle AS MetodoRetencion,
											EMPLEADOS.PorcentajeRetencion, 
											CENTROS.Centro, 
											CENTROS.Nombre AS NombreCentro, 
											PARAMETROS2.Detalle AS TipoEmpleado, 
											EMPLEADOS.CuotaVivienda, 
											EMPLEADOS.SaludYEducacion, 
											EMPLEADOS.DeduccionDependientes, 
											EMPLEADOS.ExencionAnual 
										FROM EMPLEADOS  
											LEFT JOIN CENTROS 
												ON EMPLEADOS.IdCentro = CENTROS.Id 
											INNER JOIN PARAMETROS AS PARAMETROS1
												ON EMPLEADOS.MetodoRetencion = PARAMETROS1.Id 
											INNER JOIN PARAMETROS AS PARAMETROS2 
												ON CENTROS.TipoEmpleado = PARAMETROS2.Id 
										WHERE EMPLEADOS.Id = $IdEmpleado;
								EOD;

								$regEmpleado = $this->model->leer($query);

								$MetodoRetencion		= $regEmpleado['MetodoRetencion'];
								$PorcentajeRetencion	= $regEmpleado['PorcentajeRetencion'];
								$PrimaLegal				= $regAcumulado['PrimaLegal'];

								if ($PrimaLegal <= 0)
									continue;

								$ValorDeducible25 = round($PrimaLegal * .25, 0);
								$ValorDeducible25Aj = '';

								if ($regEmpleado['ExencionAnual'] + $ValorDeducible25 > $ValorUVT * 790)
								{
									$ValorDeducible25 = $ValorUVT * 790 - $regEmpleado['ExencionAnual'];
									$ValorDeducible25Aj = '*';
								}

								$IngresoNeto = $PrimaLegal - $ValorDeducible25;

								if (($IngresoNeto) <= $ValorUVT * 95)
									$ValorNovedad = 0;
								elseif (($IngresoNeto) <= $ValorUVT * 150) 
									$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 95)) * .19, -3);
								elseif (($IngresoNeto) <= $ValorUVT * 360)
									$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 150)) * .28, -3) + round($ValorUVT * 10, -3);
								elseif (($IngresoNeto) <= $ValorUVT * 640)
									$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 360)) * .33, -3) + round($ValorUVT * 69, -3);
								elseif (($IngresoNeto) <= $ValorUVT * 945)
									$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 640)) * .35, -3) + round($ValorUVT * 162, -3);
								elseif (($IngresoNeto) <= $ValorUVT * 2300)
									$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 945)) * .37, -3) + round($ValorUVT * 268, -3);
								else
									$ValorNovedad = round(($IngresoNeto - ($ValorUVT * 2300)) * .39, -3) + round($ValorUVT * 770, -3);

								if ($ValorNovedad > 0) 
								{
									$aRetFte[] = array('Periodo' 		=> $Periodo,
												'Ciclo' 				=> $Ciclo, 
												'FechaInicial' 			=> $regPeriodo['fechainicial'], 
												'FechaFinal' 			=> $regPeriodo['fechafinal'], 
												'TipoRetFte' 			=> 'SALARIOS', 
												'Documento' 			=> $regEmpleado['Documento'], 
												'Apellido1' 			=> $regEmpleado['Apellido1'], 
												'Apellido2' 			=> $regEmpleado['Apellido2'], 
												'Nombre1' 				=> $regEmpleado['Nombre1'], 
												'Nombre2' 				=> $regEmpleado['Nombre2'], 
												'Centro' 				=> $regEmpleado['Centro'], 
												'NombreCentro' 			=> $regEmpleado['NombreCentro'], 
												'TipoEmpleado' 			=> $regEmpleado['TipoEmpleado'], 
												'MetodoRetencion' 		=> $regEmpleado['MetodoRetencion'], 
												'PorcentajeRetencion' 	=> $regEmpleado['PorcentajeRetencion'], 
												'IngresoBruto' 			=> $PrimaLegal, 
												'AfcFvp'				=> 0, 
												'CuotaVivienda'			=> 0, 
												'SaludYEducacion'		=> 0, 
												'DeduccionDependientes' => 0, 
												'ValorDeducciones' 		=> 0, 
												'IngresoNeto1'			=> $PrimaLegal, 
												'ValorDeducible25'		=> $ValorDeducible25, 
												'IngresoNeto'			=> $IngresoNeto, 
												'ValorRetFte' 			=> $ValorNovedad);
								}
							}
						}
					}

					// dep($aRetFte);
					if	(isset($aRetFte))
					{
						$_SESSION['DataRF'] = $aRetFte;

						header('Location: ' . SERVERURL . '/liquidacionPrenomina/listaRF/1');
						exit();
					}
					else
					{
						$data['mensajeError'] .= label('Perído no ha sido liquidado') . '<br>';

						$this->views->getView($this, 'retencionFuente', $data);
					}
				}
				else
				{
					$_SESSION['NuevoRegistro'] = '';
					$_SESSION['BorrarRegistro'] = '';
					$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionPrenomina/editar';
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

					if ($data) 
						$this->views->getView($this, 'retencionFuente', $data);
				}
			}
			else
				$this->views->getView($this, 'retencionFuente', $data);
		}

		public function listaRF($pagina)
		{
			// dep($_SESSION['DataRF']);
			// exit();

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/liquidacionPrenomina/listaRF/1';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/liquidacionPrenomina/informeRetFte/1';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/liquidacionPrenomina/retencionFuente';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['LIQ_PRENOMINA']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['LIQ_PRENOMINA']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['LIQ_PRENOMINA']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['LIQ_PRENOMINA']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['LIQ_PRENOMINA']['Filtro']))
			{
				$_SESSION['LIQ_PRENOMINA']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['LIQ_PRENOMINA']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['LIQ_PRENOMINA']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['LIQ_PRENOMINA']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['LIQ_PRENOMINA']['Orden'])) 
					$_SESSION['LIQ_PRENOMINA']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,MAYORES.Mayor,AUXILIARES.Auxiliar';

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia = $reg1['valor'];
			$IdPeriodicidad = $reg2['valor'];

			$Periodicidad = getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$cPeriodicidad = substr($Periodicidad, 0, 1);
			$IdPeriodo = $reg3['valor'];
			$Ciclo = $reg4['valor'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			$query = <<<EOD
				WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND
					$ArchivoNomina.Ciclo = $Ciclo 
			EOD;

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if ($lnCount == 0)
						$query .= ' AND ( ';
					else
						$query .= 'OR ';

					$query .= "EMPLEADOS.Documento LIKE '%" . $aFiltro[$lnCount] . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(AUXILIARES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ') ';
			}
			
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') 
			{
				$Archivo = './descargas/' . $_SESSION['Login']['Usuario'] . '_InformeRetFte_' . $Referencia . '_' . $cPeriodicidad . '_' . $Periodo . '_' . $Ciclo . '_' . date('YmdGis') . '.csv';

				$output = fopen($Archivo, 'w');

				fputcsv($output, array('PERIODO', 'CICLO', 'FECHA INI.', 'FECHA FIN.', 'TIPO RET.FTE.', 'DOCUMENTO', 'APELLIDO 1', 'APELLIDO 2', 'NOMBRE 1', 'NOMBRE 2', 'CENTRO', 'NOMBRE CENTRO', 'TIPO EMPLEADO', 'METODO RET.', 'PORC. RET.', 'INGRESO BRUTO', 'SALUD Y PENSION', 'AFC FVP', 'AJ. A TOPE', 'TOPE AFC FVP', 'DEDUCC. DEPENDIENTES', 'TOPE DED. DEP.', 'CUOTA VIVIENDA', 'TOPE CTA. VIVI.', 'SALUD', 'AJ. A TOPE', 'TOPE SALUD', 'VR. DEDUCCIONES', 'AJ. A TOPE', 'EXENCION ANUAL', 'TOPE ANUAL', 'INGRESO NETO 1', 'VR. DEDUCIBLE 25%', 'AJUSTE DEDUCIBLE', 'EXENCION ANUAL 25%', 'TOPE ANUAL 25%', 'INGRESO NETO', 'VR. RET.FTE'), ';');

				for ($i = 0; $i < count($_SESSION['DataRF']); $i++) 
				{ 
					$reg = $_SESSION['DataRF'][$i];

					for ($j = 0; $j < count($reg); $j++)
					{
						$regDatos = array($reg['Periodo'], $reg['Ciclo'], $reg['FechaInicial'], $reg['FechaFinal'], $reg['TipoRetFte'], $reg['Documento'], utf8_decode($reg['Apellido1']), utf8_decode($reg['Apellido2']), utf8_decode($reg['Nombre1']), utf8_decode($reg['Nombre2']), $reg['Centro'], $reg['NombreCentro'], utf8_decode($reg['TipoEmpleado']), $reg['MetodoRetencion'], $reg['PorcentajeRetencion'], $reg['IngresoBruto'], $reg['SaludPension'], $reg['AfcFvp'], $reg['AfcFvpAj'], $reg['TopeAfcFvp'], $reg['DeduccionDependientes'], $reg['TopeDedDep'], $reg['CuotaVivienda'], $reg['TopeCuotaVivienda'], $reg['SaludYEducacion'], $reg['SaludYEducacionAj'], $reg['TopeSaludYEducacion'], $reg['ValorDeducciones'], $reg['ValorDeduccionesAj'], $reg['ExencionAnual'], $reg['TopeAnual'], $reg['IngresoNeto1'], $reg['ValorDeducible25'], $reg['ValorDeducible25Aj'], $reg['ExencionAnual25'], $reg['TopeAnual25'], $reg['IngresoNeto'], $reg['ValorRetFte']);
					}

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
				$data['registros'] = count($_SESSION['DataRF']);
				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;

				$query1 = $query;
				$query .= 'ORDER BY ' . $_SESSION['LIQ_PRENOMINA']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $_SESSION['DataRF'];

				$this->views->getView($this, 'informeRetencionFuente', $data);
			}
		}	

		public function exportarPrenomina2()
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', NOMINA.Valor, 0)) AS Pagos,
						SUM(IIF(PARAMETROS1.Detalle <> 'PAGO', NOMINA.Valor, 0)) AS Deducciones,
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', NOMINA.Valor, -NOMINA.Valor)) AS Neto 
					FROM NOMINA_M_2022_12 AS NOMINA 
						INNER JOIN EMPLEADOS ON NOMINA.IdEmpleado = EMPLEADOS.Id
						INNER JOIN AUXILIARES ON NOMINA.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 ON AUXILIARES.Imputacion = PARAMETROS1.Id 
					GROUP BY EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2
					ORDER BY EMPLEADOS.Documento;
			EOD;

			$query = <<<EOD
				SELECT MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre,  
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', NOMINA.Valor, 0)) AS Pagos,
						SUM(IIF(PARAMETROS1.Detalle <> 'PAGO', NOMINA.Valor, 0)) AS Deducciones,
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', NOMINA.Valor, -NOMINA.Valor)) AS Neto 
					FROM NOMINA_M_2022_12 AS NOMINA 
						INNER JOIN AUXILIARES ON NOMINA.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 ON AUXILIARES.Imputacion = PARAMETROS1.Id 
					GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre 
					ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar;
			EOD;
		}

		function CalcularValorIBC($IdEmpleado, $FechaInicialPeriodo, $FechaFinalPeriodo, $SueldoMinimo, $filter = "")
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

			$ibc = $this->model->listarRegistros($query);
								
			if ($ibc) 
			{
				$HorasIBC = $ibc[0]['Horas'];
				$ValorIBC = $ibc[0]['Valor'];

				if ($ValorIBC == 0)
					$ValorIBC = $ibc[0]['SueldoBasico'];

				if ($HorasIBC == 0)
				{ 
					$HorasIBC = getHoursMonth();
				}

				if( $filter != "INCAPACIDAD"){
					$hoursMonht = getHoursMonth();
					$ValorIBC = round($ValorIBC / $HorasIBC * $hoursMonht, 0);
				}
				
	
				if ($ValorIBC < $SueldoMinimo)
					$ValorIBC = $SueldoMinimo;
			}
			else
				$ValorIBC = 0;

			return $ValorIBC;
		}
	}
?>