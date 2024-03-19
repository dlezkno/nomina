<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class SimulacionLiquidacionContrato extends Controllers
	{
		public function parametros()
		{
			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'IdCentro' 			=> isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'Empleado' 			=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'FechaLiquidacion' 	=> isset($_REQUEST['FechaLiquidacion']) ? $_REQUEST['FechaLiquidacion'] : ''
					),
				'mensajeError' => ''
			);

			if	(isset($_REQUEST['IdCentro']))
			{
				if	($_REQUEST['IdCentro'] > 0)
					$IdCentro = $_REQUEST['IdCentro'];
				else
					$IdCentro = 0;

				if	(! empty($_REQUEST['Empleado']))
					$Empleado = $_REQUEST['Empleado'];
				else
					$Empleado = '';

				if (empty($IdCentro) AND empty($Empleado))
					$data['mensajeError'] .= "Digite un <strong>Centro de costos o un Empleado</strong><br>";

				$FechaLiquidacion = $_REQUEST['FechaLiquidacion'];

				if (empty($FechaLiquidacion))
					$data['mensajeError'] .= "Digite una <strong>Fecha de liquidación</strong><br>";

				if	(empty($data['mensajeError']))
				{
					// CESANTIAS
					$Cesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES CESANTÍAS'");
					$IdCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Cesantias");
					$IdMayorCesantias = getRegistro('AUXILIARES', $IdCesantias)['idmayor'];
					$TipoRetencionCesantias = getRegistro('MAYORES', $IdMayorCesantias)['tiporetencion'];

					// INTERES DE CESANTIAS
					$InteresCesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES INTERÉS DE CESANTÍAS'");
					$IdInteresCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $InteresCesantias");
					$IdMayorInteresCesantias = getRegistro('AUXILIARES', $IdInteresCesantias)['idmayor'];
					$TipoRetencionInteresCesantias = getRegistro('MAYORES', $IdMayorInteresCesantias)['tiporetencion'];

					// PRIMA LEGAL
					$PrimaLegal = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES PRIMA DE SERVICIOS'");
					$IdPrimaLegal = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $PrimaLegal");
					$IdMayorPrimaLegal = getRegistro('AUXILIARES', $IdPrimaLegal)['idmayor'];
					$TipoRetencionPrimaLegal = getRegistro('MAYORES', $IdMayorPrimaLegal)['tiporetencion'];

					// VACACIONES
					$Vacaciones = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES VACACIONES EN DINERO'");
					$IdVacaciones = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Vacaciones");
					$IdMayorVacaciones = getRegistro('AUXILIARES', $IdVacaciones)['idmayor'];
					$TipoRetencionVacaciones = getRegistro('MAYORES', $IdMayorVacaciones)['tiporetencion'];

					// INDEMNIZACION
					$Indemnizacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES INDEMNIZACIÓN'");
					$IdIndemnizacion = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Indemnizacion");
					$IdMayorIndemnizacion = getRegistro('AUXILIARES', $IdIndemnizacion)['idmayor'];
					$TipoRetencionIndemnizacion = getRegistro('MAYORES', $IdMayorIndemnizacion)['tiporetencion'];

					// RETENCION FUENTE INDEMNIZACION
					$RetFteIndemnizacion = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES RETENCIÓN FUENTE INDEMNIZACIÓN'");
					$IdRetFteIndemnizacion = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $RetFteIndemnizacion");
					$IdMayorRetFteIndemnizacion = getRegistro('AUXILIARES', $IdRetFteIndemnizacion)['idmayor'];
					$TipoRetencionRetFteIndemnizacion = getRegistro('MAYORES', $IdMayorRetFteIndemnizacion)['tiporetencion'];

					$ValorUVT = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'")['valor'];
					$P_ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte'")['valor'];

					$query = <<<EOD
						SELECT EMPLEADOS.Id 
							FROM EMPLEADOS 
							WHERE
					EOD;

					if (! empty($IdCentro))
						$query .= "EMPLEADOS.IdCentro = $IdCentro ";

					if (! empty($Empleado))
						if (empty($IdCentro))
							$query .= "EMPLEADOS.Documento = '$Empleado' ";
						else
							$query .= "AND EMPLEADOS.Documento = '$Empleado' ";

					$empleados = $this->model->listar($query);

					if ($empleados)
					{
						for	($i = 0; $i < count($empleados); $i++)
						{
							$regEmpleado = getRegistro('EMPLEADOS', $empleados[$i]);

							$IdEmpleado 	= $regEmpleado['id'];
							$Documento 		= $regEmpleado['documento'];
							$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];
							$Cargo 			= getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
							$IdCentro 		= $regEmpleado['idcentro'];
							$TipoEmpleado 	= getRegistro('CENTROS', $IdCentro)['tipoempleado'];
							$FechaIngreso 	= $regEmpleado['fechaingreso'];
							$FechaRetiro 	= $regEmpleado['fecharetiro'];
							$SueldoBasico 	= $regEmpleado['sueldobasico'];
							
							$SubsidioTransporte = getRegistro('PARAMETROS', $regEmpleado['subsidiotransporte'])['detalle'];

							switch ($SubsidioTransporte)
							{
								case 'SUBSIDIO COMPLETO':
									$ValorSubsidioTransporte = $P_ValorSubsidioTransporte;
									break;
								case 'MEDIO SUBSIDIO':
									$ValorSubsidioTransporte = round($P_ValorSubsidioTransporte / 2, 0);
									break;
								case 'NO RECIBE SUBSIDIO':
									$ValorSubsidioTransporte = 0;
									break;
							}

							$NombreRegimenCesantias = getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['detalle'];
							$NombreModalidadTrabajo = getRegistro('PARAMETROS', $regEmpleado['modalidadtrabajo'])['detalle'];
							$NombreTipoContrato 	= getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
							$NombreMotivoRetiro 	= getRegistro('PARAMETROS', $regEmpleado['motivoretiro'])['detalle'];

							// SE BORRAN LIQUIDACIONES ANTERIORES
							$query = <<<EOD
								DELETE FROM $ArchivoNomina 
									WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND
										$ArchivoNomina.IdPeriodo = $IdPeriodo AND 
										$ArchivoNomina.Ciclo = 98 AND 
										$ArchivoNomina.Liquida <> 'T';
							EOD;

							$ok = $this->model->query($query);

							if ($NombreModalidadTrabajo == 'SUELDO BÁSICO') 
							{
								// NOTA: Se verifica si hay variacion de sueldo en los últimos tres meses
								$Fecha3MesesAntes = max($FechaIngreso, date('Y-m-d', strtotime($FechaLiquidacion . ' - 3 months')));

								$query = <<<EOD
									SELECT AUMENTOSSALARIALES.* 
										FROM AUMENTOSSALARIALES 
										WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
											AUMENTOSSALARIALES.FechaAumento >= '$Fecha3MesesAntes' AND 
											AUMENTOSSALARIALES.FechaAumento <= '$FechaLiquidacion';
								EOD;

								$aumentos = $this->model->listar($query);

								if ($aumentos) 
									$CalculaSalario = TRUE;
								else
									$CalculaSalario = FALSE;
							}
							else
								$CalculaSalario = FALSE;

							// LIQUIDACION DE CESANTIAS E INTERESES
							if ($NombreRegimenCesantias == 'RÉGIMEN TRADICIONAL') 
								$FechaInicialCesantias = $FechaIngreso;
							else
								$FechaInicialCesantias = MAX($FechaIngreso, ComienzoAno($FechaLiquidacion));

							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS BaseCesantias  
									FROM ACUMULADOS 
										INNER JOIN AUXILIARES 
											ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.idMayor = MAYORES.Id 
										INNER JOIN PARAMETROS 
											ON AUXILIARES.Imputacion = PARAMETROS.Id 
									WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
										ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialCesantias' AND 
										ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion' AND 
										MAYORES.BaseCesantias = 1;
							EOD;

							$regAcumulados = $this->model->leerRegistro($query);

							if ($regAcumulados AND ! is_null($regAcumulados['BaseCesantias']))
								$BaseCesantias = $regAcumulados['BaseCesantias'];
							else
								$BaseCesantias = 0;

							// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA DEL EMPLEADO EN EL PERIODO A LIQUIDAR
							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Horas, -ACUMULADOS.Horas)) AS Horas 
									FROM ACUMULADOS 
										INNER JOIN AUXILIARES 
											ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1 
											ON AUXILIARES.Imputacion = PARAMETROS1.Id  
										INNER JOIN PARAMETROS AS PARAMETROS2 
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id  
									WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
										(PARAMETROS2.Detalle = 'ES SANCIÓN' OR 
										PARAMETROS2.Detalle = 'ES LICENCIA NO REMUNERADA') AND 
										ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialCesantias' AND 
										ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion';
							EOD;

							$regAcumulados = $this->model->leerRegistro($query);

							if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
								$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
							else
								$DiasSancionYLicencias = 0;

							if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
								$NombreTipoContrato <> 'PASANTÍA')
							{
								if ($regEmpleado['diasano'] == 360) 
									$DiasCesantias = (Dias360($FechaLiquidacion, $FechaInicialCesantias) - $DiasSancionYLicencias);
								else
									$DiasCesantias = (Dias365($FechaLiquidacion, $FechaInicialCesantias) - $DiasSancionYLicencias);
										
								if ($CalculaSalario) 
								{
									// SE CALCULA EL SALARIO SI HAY VARIACIONES EN LOS ULTIMOS TRES MESES
									$query = <<<EOD
										SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS Valor 
											FROM ACUMULADOS 
												INNER JOIN AUXILIARES 
													ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
												INNER JOIN MAYORES 
													ON AUXILIARES.IdMayor = MAYORES.Id 
												INNER JOIN PARAMETROS 
													ON AUXILIARES.Imputacion = PARAMETROS.Id 
											WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
												MAYORES.BaseCesantias = 1 AND 
												ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialCesantias' AND 
												ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion';
									EOD;

									$regAcumulados = $this->model->leerRegistro($query);

									if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
										$SalarioBaseCesantias = round($regAcumulados['Valor'] / Dias360($FechaLiquidacion, $FechaInicialCesantias) * 30, 0);
									else
										$SalarioBaseCesantias = $SueldoBasico + $ValorSubsidioTransporte;
								}
								else
									$SalarioBaseCesantias = $SueldoBasico + $ValorSubsidioTransporte;

								$Horas = $DiasCesantias * 8;
								$ValorCesantias = round($SalarioBaseCesantias * $DiasCesantias / 360, 0);

								// SE BUSCAN LOS ANTICIPOS DE CESANTIAS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS Valor 
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES 
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
										WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Nombre = 'CESANTÍAS' AND 
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialCesantias' AND 
											ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
									$AnticipoCesantias = $regAcumulados['Valor'];
								else
									$AnticipoCesantias = 0;

								$ValorCesantias -= $AnticipoCesantias;

								$datos[] = array($IdPeriodo, 98, $IdEmpleado, $IdCesantias, $SalarioBaseCesantias, $Horas, $ValorCesantias, 0, 'N', $TipoRetencionCesantias, $IdCentro, $TipoEmpleado, 0);

								// INTERES SOBRE LAS CESANTIAS
								$ValorInteresCesantias = round($ValorCesantias * $DiasCesantias * 0.12 / 360, 0);
								
								$datos[] = array($IdPeriodo, 98, $IdEmpleado, $IdInteresCesantias, $ValorCesantias, 0, $ValorInteresCesantias, 0, 'N', $TipoRetencionInteresCesantias, $IdCentro, $TipoEmpleado, 0);
							}

							// LIQUIDACION PRIMA LEGAL
							$FechaInicialPrimaLegal = max($FechaIngreso, ComienzoSemestre($FechaLiquidacion));

							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, -ACUMULADOS.Horas)) AS HorasPrimas, 
										SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS BasePrimas   
									FROM ACUMULADOS 
										INNER JOIN AUXILIARES 
											ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.idMayor = MAYORES.Id 
										INNER JOIN PARAMETROS 
											ON AUXILIARES.Imputacion = PARAMETROS.Id 
									WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
										ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal' AND 
										ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion' AND 
										MAYORES.BasePrimas = 1;
							EOD;

							$regAcumulados = $this->model->leerRegistro($query);

							if ($regAcumulados AND ! is_null($regAcumulados['BasePrimas']))
								$SalarioBasePrima = $regAcumulados['BasePrimas'];
							else
								$SalarioBasePrima = $SueldoBasico + $ValorSubsidioTransporte;

							// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA DEL EMPLEADO EN EL PERIODO A LIQUIDAR
							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Horas, -ACUMULADOS.Horas)) AS Horas 
									FROM ACUMULADOS 
										INNER JOIN AUXILIARES 
											ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS AS PARAMETROS1 
											ON AUXILIARES.Imputacion = PARAMETROS1.Id  
										INNER JOIN PARAMETROS AS PARAMETROS2 
											ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id  
									WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
										(PARAMETROS2.Detalle = 'ES SANCIÓN' OR 
										PARAMETROS2.Detalle = 'ES LICENCIA NO REMUNERADA') AND 
										ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal' AND 
										ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion';
							EOD;

							$regAcumulados = $this->model->leerRegistro($query);

							if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
								$DiasSancionYLicencias = round($regAcumulados['Horas'] / 8, 0);
							else
								$DiasSancionYLicencias = 0;

							if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
								$NombreTipoContrato <> 'PASANTÍA')
							{
								if ($regEmpleado['diasano'] == 360) 
									$DiasPrimaLegal = Dias360($FechaLiquidacion, $FechaInicialPrimaLegal) - $DiasSancionYLicencias;
								else
									$DiasPrimaLegal = Dias365($FechaLiquidacion, $FechaInicialPrimaLegal) - $DiasSancionYLicencias;

								if ($CalculaSalario) 
								{
									// SE CALCULA EL SALARIO SI HAY VARIACIONES EN LOS ULTIMOS TRES MESES
									$query = <<<EOD
										SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS Valor 
											FROM ACUMULADOS 
												INNER JOIN AUXILIARES 
													ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
												INNER JOIN MAYORES 
													ON AUXILIARES.IdMayor = MAYORES.Id 
												INNER JOIN PARAMETROS 
													ON AUXILIARES.Imputacion = PARAMETROS.Id 
											WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
												MAYORES.BasePrimas = 1 AND 
												ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal' AND 
												ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion';
									EOD;

									$regAcumulados = $this->model->leerRegistro($query);

									if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
										$SalarioBasePrima = round($regAcumulados['Valor'] / Dias360($FechaLiquidacion, $FechaInicialPrimaLegal) * 30, 0);
									else
										$SalarioBasePrima = $SueldoBasico + $ValorSubsidioTransporte;
								}
								else
									$SalarioBasePrima = $SueldoBasico + $ValorSubsidioTransporte;
				
								$Horas = $DiasPrimaLegal * 8;
								$ValorPrimaLegal = round($SalarioBasePrima * $DiasPrimaLegal / 360, 0);

								$datos[] = array($IdPeriodo, 98, $IdEmpleado, $IdPrimaLegal, $SalarioBasePrima, $Horas, $ValorPrimaLegal, 0, 'N', $TipoRetencionPrimaLegal, $IdCentro, $TipoEmpleado, 0);
							}

							// LIQUIDACION DE VACACIONES
							if ($NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
								$NombreTipoContrato <> 'PASANTÍA')
							{
								// VALOR PROMEDIO DE LOS RECARGOS NOCTURNOS
								$query = <<<EOD
									SELECT MAYORES.Mayor 
										FROM AUXILIARES 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
										WHERE PARAMETROS.Detalle = 'ES SUELDO BÁSICO' OR 
											PARAMETROS.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL);';
								EOD;

								$regMayores = $this->model->leerRegistro($query);

								if ($regMayores) 
									$MayorSueldo = $regMayores['Mayor'];
								else
									$MayorSueldo = '';

								$FechaInicialVacaciones = max($FechaIngreso, date('Y-m-d', strtotime($FechaLiquidacion . ' - 1 year + 1 day')));

								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS BaseVacaciones 
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES 
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor <> '$MayorSueldo' AND 
											MAYORES.BaseVacaciones = 1 AND 
											ACUMULADOS.FechaInicial >= '$FechaInicialVacaciones';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BaseVacaciones']))
								{
									if ($regEmpleado['diasano'] == 360) 
										$PromedioSalarioVariable = round($regAcumulados['BaseVacaciones'] / Dias360($FechaLiquidacion, $FechaInicialVacaciones) * 30, 0);
									else
										$PromedioSalarioVariable = round($regAcumulados['BaseVacaciones'] / Dias365($FechaLiquidacion, $FechaInicialVacaciones) * 30, 0);
								}
								else
									$PromedioSalarioVariable = 0;

								$SalarioBaseVacaciones = $SueldoBasico + $PromedioSalarioVariable;
				
								$query = <<<EOD
									SELECT VACACIONES.FechaCausacion, 
											SUM(VACACIONES.DiasALiquidar) AS DiasLiquidados 
										FROM VACACIONES 
										WHERE VACACIONES.IdEmpleado = $IdEmpleado AND 
											VACACIONES.Procesado = 1 
										GROUP BY VACACIONES.FechaCausacion 
										ORDER BY VACACIONES.FechaCausacion DESC;
								EOD;

								$vacaciones = $this->model->listarRegistros($query);

								if ($vacaciones)
								{
									$FechaFinalVacaciones = date('Y-m-d', strtotime($vacaciones[0]['FechaCausacion'] . ' + 1 year - 1 day'));
									
									if ($FechaFinalVacaciones < $FechaRetiro)
										$DiasVacaciones = 15 - $vacaciones[0]['DiasLiquidados'];
									else
										if ($regEmpleado['diasano'] == 360) 
											$DiasVacaciones = round(Dias360($FechaLiquidacion, $vacaciones[0]['FechaCausacion']) * 15 / 360, 2) - $vacaciones[0]['DiasLiquidados'];
										else
											$DiasVacaciones = round(Dias365($FechaLiquidacion, $vacaciones[0]['FechaCausacion']) * 15 / 365, 2) - $vacaciones[0]['DiasLiquidados'];

									if ($FechaFinalVacaciones < $FechaLiquidacion)
										if ($regEmpleado['diasano'] == 360) 
											$DiasVacaciones += round(Dias360($FechaLiquidacion, $FechaFinalVacaciones) * 15 / 360, 2);
										else
											$DiasVacaciones += round(Dias365($FechaLiquidacion, $FechaFinalVacaciones) * 15 / 365, 2);
								}
								else
									if ($regEmpleado['diasano'] == 360) 
										$DiasVacaciones = round(Dias360($FechaLiquidacion, $FechaIngreso) * 15 / 360, 2);
									else
										$DiasVacaciones = round(Dias365($FechaLiquidacion, $FechaIngreso) * 15 / 365, 2);

								$DiasVacaciones2 = round($DiasVacaciones, 0);

								if ($DiasVacaciones > 0)
								{
									// SE CALCULAN LOS DIAS DE VACACIONES REALES EN TIEMPO
									// $FechaInicio = date('Y-m-d', strtotime($FechaRetiro . ' + 1 day'));
									// $Fecha = $FechaInicio;
									// $FechaIngresoVacaciones = date('Y-m-d', strtotime($FechaInicio . ' + ' . ($DiasVacaciones2) . ' days'));
									// $DiasFestivos = 0;
									// $Dias31 = 0;

									// while ($Fecha <= $FechaIngresoVacaciones) 
									// {
									// 	if (date('d', strtotime($Fecha)) == 31 AND 
									// 		date('w', strtotime($Fecha)) <> 6 AND 
									// 		date('w', strtotime($Fecha)) <> 0)
									// 		$Dias31++;
									// 	else
									// 	{
									// 		if (date('w', strtotime($Fecha)) == 6) // SABADO
									// 			$DiasFestivos++;
						
									// 		if (date('w', strtotime($Fecha)) == 0 ) // DOMINGO
									// 			$DiasFestivos++;
									// 	}

									// 	$query = <<<EOD
									// 		SELECT DIASFESTIVOS.Fecha 
									// 			FROM DIASFESTIVOS
									// 			WHERE DIASFESTIVOS.Fecha = '$Fecha';
									// 	EOD;

									// 	$regFestivos = $this->model->leerRegistro($query);

									// 	if ($regFestivos AND (date('w', strtotime($Fecha)) <> 0 OR date('w', strtotime($Fecha)) == 6))
									// 		$DiasFestivos++;

									// 	$Fecha = date('Y-m-d', strtotime($Fecha . ' + 1 day'));
									// }
				
									// $DiasVacaciones += $DiasFestivos + $Dias31;
									$Horas = $DiasVacaciones * 8;
									$ValorVacaciones = round($SalarioBaseVacaciones / 30 * $DiasVacaciones, 0);

									$datos[] = array($IdPeriodo, 98, $IdEmpleado, $IdVacaciones, $SalarioBaseVacaciones, $Horas, $ValorVacaciones, 0, 'N', $TipoRetencionVacaciones, $IdCentro, $TipoEmpleado, 0);
								}
							}

							// INDEMNIZACION
							if ($NombreMotivoRetiro == 'TERMINACIÓN CONTRATO SIN JUSTA CAUSA')
							{
								// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA DEL EMPLEADO DESDE SU INGRESO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Horas, -ACUMULADOS.Horas)) AS Horas 
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES 
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS AS PARAMETROS1 
												ON AUXILIARES.Imputacion = PARAMETROS1.Id  
											INNER JOIN PARAMETROS AS PARAMETROS2 
												ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id  
										WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											(PARAMETROS2.Detalle = 'ES SANCIÓN' OR 
											PARAMETROS2.Detalle = 'ES LICENCIA NO REMUNERADA') AND 
											ACUMULADOS.FechaInicialPeriodo >= '$FechaIngreso' AND 
											ACUMULADOS.FechaFinalPeriodo <= '$FechaLiquidacion';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
									$DiasSancionYLicencias = round($regAcumulados['Horas'] / 8, 0);
								else
									$DiasSancionYLicencias = 0;

								if ($NombreTipoContrato == 'INDEFINIDO')
								{
									$DiasIndemnizacion = dias360($FechaLiquidacion, $FechaIngreso) - $DiasSancionYLicencias;
									$AnosIndemnizacion = $DiasIndemnizacion / 360;

									if ($NombreRegimenCesantias == 'SALARIO INTEGRAL')
										$ValorIndemnizacion = round(($SueldoBasico / 30 * 20) + ($SueldoBasico / 30 * max(0, ($AnosIndemnizacion - 1)) * 15), 0);
									else
										$ValorIndemnizacion = round($SueldoBasico + ($SueldoBasico / 30 * max(0, ($AnosIndemnizacion - 1)) * 20), 0);
								}
								elseif ($NombreTipoContrato == 'DE LABOR U OBRA CONTRATADA')
								{
									$DiasIndemnizacion = 15;
									$ValorIndemnizacion = round($SueldoBasico / 2, 0);
								}
								else
								{
									$DiasIndemnizacion = dias360($FechaVencimiento - $FechaLiquidacion) - $DiasSancionYLicencias;
									$ValorIndemnizacion = round($SueldoBasico / 30 * $DiasIndemnizacion, 0);
								}
							}
							else
								$ValorIndemnizacion = 0;

							if ($ValorIndemnizacion > 0)
							{

								$Horas = $DiasIndemnizacion * 8;

								$datos[] = array($IdPeriodo, 98, $IdEmpleado, $IdIndemnizacion, $SueldoBasico, $Horas, $ValorIndemnizacion, 0, 'N', $TipoRetencionIndemnizacion, $IdCentro, $TipoEmpleado, 0);
							}

							// RETENCION FUENTE INDEMNIZACION
							if ($ValorIndemnizacion > 0 AND $SueldoBasico > $ValorUVT * 204)
							{
								$ValorRetFte = round($ValorIndemnizacion * .2, 0);
								$datos[] = array($IdPeriodo, 98, $IdEmpleado, $IdRetFteIndemnizacion, $ValorIndemnizacion, 0, $ValorRetFte, 0, 'N', $TipoRetencionRetFteIndemnizacion, $IdCentro, $TipoEmpleado, 0);
							}

							$this->imprimirLiquidacion($IdEmpleado, $DiasSancionYLicencias, $datos);
						}
					}
				}
			}

			$_SESSION['NuevoRegistro'] 		= '';
			$_SESSION['BorrarRegistro'] 	= '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/simulacionLiquidacionContrato/parametros';
			$_SESSION['Retroceder'] 		= '';
			$_SESSION['Avanzar'] 			= '';
			$_SESSION['Novedades'] 			= '';
			$_SESSION['Importar'] 			= '';
			$_SESSION['ImportarArchivo'] 	= '';
			$_SESSION['Exportar'] 			= '';
			$_SESSION['ExportarArchivo'] 	= '';
			$_SESSION['Informe'] 			= '';
			$_SESSION['GenerarInforme'] 	= '';
			$_SESSION['Correo'] 			= '';
			$_SESSION['Lista'] 				= '';

			$_SESSION['SIMULACION']['Filtro'] = '';

			if ($data) 
				$this->views->getView($this, 'actualizar', $data);
		}

		public function imprimirLiquidacion($IdEmpleado, $DiasSancionYLicencias, $ArchivoNomina)
		{
			global $lcOrientacion;
			global $lcTitulo;
			global $lcSubTitulo;
			global $lcEncabezado;
			global $lcEncabezado2;
		
			$PDF = new PDF(); 
			$PDF->AliasNbPages();
		
			$lcTitulo = utf8_decode('SIMULACIÓN LIQUIDACIÓN CONTRATO DE TRABAJO');
			$lcOrientacion = 'P';
			$lcEncabezado = '';
			// $lcEncabezado .= str_pad(utf8_decode('DOCUMENTO'), 15);
			// $lcEncabezado .= str_pad(utf8_decode('NOMBRE EMPLEADO'), 45);
			// $lcEncabezado .= str_pad(utf8_decode('INGRESO'), 10);
			// $lcEncabezado .= str_pad(utf8_decode('CARGO'), 63);
			// $lcEncabezado .= str_pad(utf8_decode('SUELDO BÁS.'), 13);
			// $lcEncabezado .= str_pad(utf8_decode('CENTRO'), 20);

			$PDF->AddFont('Tahoma','','tahoma.php');
			$PDF->AddPage($lcOrientacion);
			$PDF->SetFont('Tahoma', '', 8);

			$regEmpleado 	= getRegistro('EMPLEADOS', $IdEmpleado);
			$Documento 		= $regEmpleado['documento'];
			$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];
			$NombreCargo 	= getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
			$TipoContrato 	= getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
			$MotivoRetiro 	= getRegistro('PARAMETROS', $regEmpleado['motivoretiro'])['detalle'];
			$DiasTrabajados = dias360($regEmpleado['fecharetiro'], $regEmpleado['fechaingreso']);

			$P_ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte'")['valor'];
			$SubsidioTransporte 		= getRegistro('PARAMETROS', $regEmpleado['subsidiotransporte'])['detalle'];

			switch ($SubsidioTransporte)
			{
				case 'SUBSIDIO COMPLETO':
					$ValorSubsidioTransporte = $P_ValorSubsidioTransporte;
					break;
				case 'MEDIO SUBSIDIO':
					$ValorSubsidioTransporte = round($P_ValorSubsidioTransporte / 2, 0);
					break;
				case 'NO RECIBE SUBSIDIO':
					$ValorSubsidioTransporte = 0;
					break;
			}

			$PDF->Cell(50, 5, utf8_decode('DOCUMENTO IDENTIFICACIÓN:'), 0, 0, 'L'); 
			$PDF->Cell(25, 5, number_format($regEmpleado['documento'], 0), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('NOMBRE EMPLEADO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, utf8_decode($NombreEmpleado), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('CARGO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, utf8_decode($NombreCargo), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('TIPO CONTRATO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, utf8_decode($TipoContrato), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('SUELDO BÁSICO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, '$' . number_format($regEmpleado['sueldobasico'], 0), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('SUBSIDIO TRANSPORTE:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, '$' . number_format($ValorSubsidioTransporte, 0), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('MOTIVO DEL RETIRO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, utf8_decode($MotivoRetiro), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('FECHA INGRESO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, utf8_decode($regEmpleado['fechaingreso']), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('FECHA RETIRO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, utf8_decode($regEmpleado['fecharetiro']), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('TIEMPO DE SERVICIO:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, number_format($DiasTrabajados, 0) . utf8_decode(' días'), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(50, 5, utf8_decode('DÍAS EN SANCIÓN Y/O LICENCIAS:'), 0, 0, 'L'); 
			$PDF->Cell(60, 5, number_format($DiasSancionYLicencias, 0) . utf8_decode(' días'), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
			$PDF->Ln(); 

			$lcConceptos = '';
			$lcConceptos .= str_pad(utf8_decode('CONCEPTO'), 70);
			$lcConceptos .= str_pad(utf8_decode('BASE'), 25);
			$lcConceptos .= str_pad(utf8_decode('TIEMPO'), 30);
			$lcConceptos .= str_pad(utf8_decode('PAGOS'), 25);
			$lcConceptos .= str_pad(utf8_decode('DEDUCCIONES'), 25);

			$PDF->Cell(50, 5, utf8_decode($lcConceptos), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

			$TotalPagos = 0;
			$TotalDeducciones = 0;

// IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Base, Horas, Valor, Saldo, Liquida, Afecta, 
// IdCentro, TipoEmpleado, IdTercero
			for ($i = 0; $i < count($datos); $i++)
			{
				$regNovedad = $datos[$i];

				$IdConcepto = $regNovedad['IdConcepto'];

				$query = <<<EOD
					SELECT MAYORES.Mayor, 
							AUXILIARES.Auxiliar, 
							AUXILIARES.Nombre AS NombreConcepto, 
							PARAMETROS1.Detalle AS Imputacion, 
							PARAMETROS2.Detalle AS NombreTipoLiquidacion 
						FROM AUXILIARES 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1  
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2  
								ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						WHERE AUXILIARES.IdConcepto = $IdConcepto;
				EOD;

				$regConcepto = $this->model->leer($query);

				$PDF->Cell(55, 5, utf8_decode($regConcepto['NombreConcepto']), 0, 0, 'L'); 
				if ($reg['Base'] > 0)
					$PDF->Cell(25, 5, '$' . number_format($regNovedad['Base'], 0), 0, 0, 'R'); 
				else
					$PDF->Cell(25, 5, '', 0, 0, 'R'); 

				if ($reg['Horas'] > 0)
				{
					if ($reg['NombreTipoLiquidacion'] == 'DÍAS')
						$PDF->Cell(25, 5, number_format($regNovedad['Horas'] / 8, 2) . utf8_decode(' DÍAS'), 0, 0, 'R'); 
					else
						$PDF->Cell(25, 5, number_format($regNovedad['Horas'], 2) . ' HORAS', 0, 0, 'R'); 
				}
				else
					$PDF->Cell(25, 5, '', 0, 0, 'R'); 

				if ($regConcepto['Imputacion'] == 'PAGO')
				{
					$PDF->Cell(30, 5, '$' . number_format($regNovedad['Valor'], 0), 0, 0, 'R'); 
					$TotalPagos += $regNovedad['Valor'];
				}
				else
				{
					$PDF->Cell(30, 5, '', 0, 0, 'R'); 
					$PDF->Cell(30, 5, '$' . number_format(abs($regNovedad['Valor']), 0), 0, 0, 'R'); 
					$TotalDeducciones += abs($regNovedad['Valor']);
				}
	
				if ($regConcepto['Imputacion'] == 'DEDUCCION')
				{
					$PDF->Cell(30, 5, '$' . number_format($reg['Deducciones'], 0), 0, 0, 'R'); 
					$TotalDeducciones += $reg['Deducciones'];
				}
				// else
				// 	$PDF->Cell(30, 5, '', 0, 0, 'R'); 

				$PDF->Ln(); 
			} 

			$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
			$PDF->Cell(105, 5, 'TOTALES', 0, 0, 'R'); 
			$PDF->Cell(30, 5, '$' . number_format($TotalPagos, 0), 0, 0, 'R'); 
			$PDF->Cell(30, 5, '$' . number_format($TotalDeducciones, 0), 0, 0, 'R'); 
			$PDF->Ln(); 
			$PDF->Cell(105, 5, 'NETO A PAGAR', 0, 0, 'R'); 
			$PDF->Cell(30, 5, '$' . number_format($TotalPagos - $TotalDeducciones, 0), 0, 0, 'R'); 
			$PDF->Ln(); 
			$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 
			$MontoEscrito = montoEscrito($TotalPagos - $TotalDeducciones);
			$PDF->Cell(105, 5, 'SON: ' . utf8_decode(strtoupper($MontoEscrito)), 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Line($PDF->GetX(), $PDF->GetY(), 200, $PDF->GetY()); 

			$Aceptacion = 'HAGO CONSTAR QUE ENCUENTRO CORRECTA LA LIQUIDACIÓN DE PRESTACIONES SOCIALES, POR LO QUE DECLARO PAZ Y SALVO POR TODO CONCEPTO LABORAL A COMWARE S.A. Y AUTORIZO EXPRESAMENTE LOS DESCUENTOS EFECTUADOS.';

			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->MultiCell(0, 5, utf8_decode($Aceptacion), 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->Ln(); 
			$PDF->Cell(65, 5, '________________________________________', 0, 0, 'L'); 
			$PDF->Cell(65, 5, '________________________________________', 0, 0, 'L'); 
			$PDF->Cell(65, 5, '________________________________________', 0, 0, 'L'); 
			$PDF->Ln(); 
			$PDF->Cell(65, 5, utf8_decode('EMPLEADOR'), 0, 0, 'L'); 
			$PDF->Cell(65, 5, utf8_decode('TESTIGO'), 0, 0, 'L'); 
			$PDF->Cell(65, 5, utf8_decode('EMPLEADO'), 0, 0, 'L'); 


			$PDF->Output('F', 'descargas/LiquidacionContrato_' . $Documento . '_' . $NombreEmpleado . '.PDF'); 
		}
	

	}
?>