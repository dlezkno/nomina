<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class LiquidacionContrato extends Controllers
	{
		public function liquidar($aEmpleados = array(), $activar = 0)
		{
			ini_set('max_execution_time', 0);

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

			$PeriodoAcumulado = getRegistro('PERIODOSACUMULADOS', 0, "PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND PERIODOSACUMULADOS.Ciclo = $Ciclo")['acumulado'];

			// SUELDO BASICO
			$IdSueldoBasico = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES SUELDO BÁSICO'");
			$IdSueldoBasico = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $IdSueldoBasico");

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
			$IdPrimaLegalDescuento = getId('AUXILIARES', "AUXILIARES.nombre = 'DCTO. > VALOR PAGADO POR PRIMA LEGAL'");
			$IdMayorPrimaLegal = getRegistro('AUXILIARES', $IdPrimaLegal)['idmayor'];
			$TipoRetencionPrimaLegal = getRegistro('MAYORES', $IdMayorPrimaLegal)['tiporetencion'];
			$IdMayorPrimaLegalDescuento = getRegistro('AUXILIARES', $IdPrimaLegalDescuento)['idmayor'];
			$TipoRetencionPrimaLegalDescuento = getRegistro('MAYORES', $IdMayorPrimaLegalDescuento)['tiporetencion'];

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

			if ($activar == 1)
			{
				$query = <<<EOD
					SELECT EMPLEADOS.Id 
						FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE PARAMETROS.Detalle = 'RETIRADO' AND 
							EMPLEADOS.FechaLiquidacion IS NULL;
				EOD;

				$empleados = $this->model->listar($query);

				$aEmpleados = array();

				for ($i = 0; $i < count($empleados); $i++)
				{
					$aEmpleados[] = $empleados[$i]['Id'];
				}
			}

			for	($i = 0; $i < count($aEmpleados); $i++)
			{
				$regEmpleado = getRegistro('EMPLEADOS', $aEmpleados[$i]);

				$IdEmpleado = $regEmpleado['id'];
				$Documento = $regEmpleado['documento'];
				$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];
				$Cargo = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
				$IdCentro = $regEmpleado['idcentro'];
				$TipoEmpleado = getRegistro('CENTROS', $IdCentro)['tipoempleado'];
				$FechaIngreso = $regEmpleado['fechaingreso'];
				$FechaRetiro = $regEmpleado['fecharetiro'];
				$FechaVencimiento = $regEmpleado['fechavencimiento'];
				$SueldoBasico = $regEmpleado['sueldobasico'];
				
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
				$NombreTipoContrato = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
				$NombreMotivoRetiro = getRegistro('PARAMETROS', $regEmpleado['motivoretiro'])['detalle'];

				// SE BORRAN LIQUIDACIONES ANTERIORES
				$query = <<<EOD
					DELETE FROM $ArchivoNomina 
						WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND
							$ArchivoNomina.IdPeriodo = $IdPeriodo AND 
							$ArchivoNomina.Ciclo = 98 AND 
							$ArchivoNomina.Liquida <> 'T';
				EOD;

				$ok = $this->model->query($query);

				// SE REVISAN NOVEDADES EN EL PERIODO PARA INCORPORAR
				if ($PeriodoAcumulado == 1 AND FALSE)
				{
					$query = <<<EOD
						SELECT ACUMULADOS.IdConcepto, 
								AUXILIARES.Nombre AS NombreConcepto, 
								PARAMETROS.Detalle AS ModoLiquidacion, 
								ACUMULADOS.Horas, 
								ACUMULADOS.Valor 
							FROM ACUMULADOS 
								INNER JOIN AUXILIARES 
									ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.ModoLiquidacion = PARAMETROS.Id 
							WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
								ACUMULADOS.FechaInicialPeriodo <= '$FechaRetiro' AND 
								ACUMULADOS.FechaFinalPeriodo >= '$FechaRetiro';
					EOD;

					$acumulados = $this->model->listar($query);

					if ($acumulados)
					{
						// YA SE HA LIQUIDADO LA NOMINA EN EL PERÍODO ACTUAL DE RETIRO DEL EMPLEADO
						for ($j = 0; $j < count($acumulados); $j++)
						{
							$regAcumulados = $acumulados[$j];
							$IdMayor = getRegistro('AUXILIARES', $regAcumulados['IdConcepto'])['idmayor'];
							$TipoRetencion = getRegistro('MAYORES', $IdMayor)['tiporetencion'];

							if ($regAcumulados['ModoLiquidacion'] == 'AUTOMÁTICO')
							{
								$TiempoTrabajo = Dias360($FechaRetiro, max($FechaIngreso, ComienzoMes($FechaRetiro)));

								if ($regAcumulados['Horas'] > $TiempoTrabajo * 8)
								{
									$ValorNovedad = $regAcumulados['Valor'] - round($regAcumulados['Valor'] / $regAcumulados['Horas'] * ($TiempoTrabajo * 8), 0);
									$Horas = $regAcumulados['Horas'] - ($TiempoTrabajo * 8);
								}
								else
									$ValorNovedad = 0;

								// SE GUARDA LA NOVEDAD CON IMPUTACION CONTRARIA
								if ($ValorNovedad > 0)
								{
									$IdConcepto = $regAcumulados['IdConcepto'];
									$ValorNovedad *= -1;

									$datos = array($IdPeriodo, 98, $IdEmpleado, $IdConcepto, 0, $Horas, $ValorNovedad, 0, 'N', $TipoRetencion, $IdCentro, $TipoEmpleado, 0);
									$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
								}
							}
						}
					}
				}
				else
				{
					// AQUI SE LIQUIDA LA PRENOMINA DEL EMPLEADO
					$controller = 'liquidacionPrenomina';
					$controllerFile = 'controllers/' . $controller . '.php';

					require_once($controllerFile);
					$controller = new $controller();
					$controller->liquidar(0, $Documento, 0, 98);

					// SE TRASLADAN LAS NOVEDADES AL CICLO 98
					$query = <<<EOD
						UPDATE $ArchivoNomina 
							SET Ciclo = 98, 
								Liquida = 'T' 
							WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
								$ArchivoNomina.Ciclo = $Ciclo AND 
								$Ciclo < 98 AND 
								$ArchivoNomina.Liquida = 'N';
					EOD;
	
					$ok = $this->model->query($query);
				}

				// LIQUIDACION DE CESANTIAS E INTERESES
				if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
					$NombreTipoContrato <> 'PASANTÍA')
				{
					if ($NombreRegimenCesantias == 'RÉGIMEN TRADICIONAL') 
						$FechaInicialCesantias = $FechaIngreso;
					else
						$FechaInicialCesantias = MAX($FechaIngreso, ComienzoAno($FechaRetiro));

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
								ACUMULADOS.FechaFinalPeriodo <= '$FechaRetiro';
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
						$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
					else
						$DiasSancionYLicencias = 0;

					if ($regEmpleado['diasano'] == 360) 
						$DiasCesantias = (Dias360($FechaRetiro, $FechaInicialCesantias) - $DiasSancionYLicencias);
					else
						$DiasCesantias = (Dias365($FechaRetiro, $FechaInicialCesantias) - $DiasSancionYLicencias);

					// SE CALCULA EL PROMEDIO SALARIAL DE ACUMULADOS
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
								ACUMULADOS.FechaFinalPeriodo <= '$FechaRetiro' AND 
								AUXILIARES.Id IN (
									227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
								);
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['BaseCesantias']))
						$PromedioSalarial = round($regAcumulados['BaseCesantias'] / $DiasCesantias * 30, 0);
					else
						$PromedioSalarial = 0;
							
					// SE CALCULA EL PROMEDIO SALARIAL DEL PERIODO EN LIQUIDACION
					$query = <<<EOD
						SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BaseCesantias  
							FROM $ArchivoNomina 
								INNER JOIN AUXILIARES 
									ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
								INNER JOIN MAYORES 
									ON AUXILIARES.idMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.Imputacion = PARAMETROS.Id 
							WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
								AUXILIARES.Id IN (
									227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
								);
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['BaseCesantias']))
						$PromedioSalarial += round(($regAcumulados['BaseCesantias'] / $DiasCesantias) * 30, 0);

					$SalarioBaseCesantias = $SueldoBasico + $ValorSubsidioTransporte + $PromedioSalarial;

					$Horas = ($DiasCesantias + $DiasSancionYLicencias) * 8;
					$ValorCesantias = round($SalarioBaseCesantias * ($DiasCesantias + $DiasSancionYLicencias) / 360, 0);

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
								ACUMULADOS.FechaFinalPeriodo <= '$FechaRetiro' AND
								MAYORES.mayor + AUXILIARES.auxiliar NOT IN ('53003', '54002');
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
						$AnticipoCesantias = $regAcumulados['Valor'];
					else
						$AnticipoCesantias = 0;

					$ValorCesantias -= $AnticipoCesantias;

					$datos = array($IdPeriodo, 98, $IdEmpleado, $IdCesantias, $SalarioBaseCesantias, $Horas, $ValorCesantias, 0, 'N', $TipoRetencionCesantias, $IdCentro, $TipoEmpleado, 0);
					$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);

					// INTERES SOBRE LAS CESANTIAS
					$ValorInteresCesantias = round($ValorCesantias * ($DiasCesantias + $DiasSancionYLicencias) * 0.12 / 360, 0);
					
					$datos = array($IdPeriodo, 98, $IdEmpleado, $IdInteresCesantias, $ValorCesantias, 0, $ValorInteresCesantias, 0, 'N', $TipoRetencionInteresCesantias, $IdCentro, $TipoEmpleado, 0);
					$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
				}
				else
					$DiasSancionYLicencias = 0;

				// LIQUIDACION PRIMA LEGAL
				if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
					$NombreTipoContrato <> 'PASANTÍA')
				{
					$FechaInicialPrimaLegal = max($FechaIngreso, ComienzoSemestre($FechaRetiro));

					$regUltimoPago = getRegistro(
						'ACUMULADOS', 0,
						"ACUMULADOS.IdEmpleado = $IdEmpleado AND ACUMULADOS.IdConcepto = $IdPrimaLegal ORDER BY ACUMULADOS.fechafinalperiodo DESC");
					$ultimoPago = $regUltimoPago['fechafinalperiodo'];
					$ultimoPagoBase = $regUltimoPago['base'];
					if ($ultimoPago && $ultimoPago>$FechaInicialPrimaLegal)
						$FechaInicialPrimaLegal = date('Y-m-d', strtotime('first day of next month', strtotime($ultimoPago))); 

					// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA
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
								ACUMULADOS.FechaFinalPeriodo <= '$FechaRetiro';
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
						$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
					else
						$DiasSancionYLicencias = 0;

					if ($regEmpleado['diasano'] == 360) {
						$DiasPrimaLegalTotal = Dias360($FechaRetiro, $FechaInicialPrimaLegal);
						$DiasPrimaLegalDescuento = $ultimoPago ? (Dias360($ultimoPago, $FechaRetiro) -2) : 0;
					} else {
						$DiasPrimaLegalTotal = Dias365($FechaRetiro, $FechaInicialPrimaLegal);
						$DiasPrimaLegalDescuento = $ultimoPago ? (Dias365($ultimoPago, $FechaRetiro) - 2) : 0;
					}

					$DiasPrimaLegalTotal = $DiasPrimaLegalTotal < 0 ? 0 : $DiasPrimaLegalTotal;
					$DiasPrimaLegalDescuento = $DiasPrimaLegalDescuento < 0 ? 0 : $DiasPrimaLegalDescuento;

					$DiasPrimaLegal = $DiasPrimaLegalTotal - $DiasSancionYLicencias;

					// VALOR SALARIO PROMEDIO
					$query = <<<EOD
						SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS BasePrima 
							FROM ACUMULADOS 
								INNER JOIN AUXILIARES 
									ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.Imputacion = PARAMETROS.Id 
							WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
								ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal' AND
								AUXILIARES.Id IN (
									227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
								);
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['BasePrima']))
						if ($DiasPrimaLegal >= 30)
							$PromedioSalarioVariable = round($regAcumulados['BasePrima'] / $DiasPrimaLegal * 30, 0);
						else
							$PromedioSalarioVariable = $regAcumulados['BasePrima'];
					else
						$PromedioSalarioVariable = 0;

					// SE CALCULA EL PROMEDIO SALARIAL DEL PERIODO EN LIQUIDACION
					$query = <<<EOD
						SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BasePrimas  
							FROM $ArchivoNomina 
								INNER JOIN AUXILIARES 
									ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
								INNER JOIN MAYORES 
									ON AUXILIARES.idMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.Imputacion = PARAMETROS.Id 
							WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
								AUXILIARES.Id IN (
									227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
								);
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['BasePrimas']) AND $DiasPrimaLegal>0)
						$PromedioSalarioVariable += round($regAcumulados['BasePrimas'] / $DiasPrimaLegal * 30, 0);

					$SalarioBasePrimaLegal = $SueldoBasico + $ValorSubsidioTransporte + $PromedioSalarioVariable;

					$Horas = $DiasPrimaLegalTotal * 8;
					$ValorPrimaLegal = round($SalarioBasePrimaLegal * $DiasPrimaLegalTotal / 360, 0);

					if ($ValorPrimaLegal) {
						$datos = array($IdPeriodo, 98, $IdEmpleado, $IdPrimaLegal, $SalarioBasePrimaLegal, $Horas, $ValorPrimaLegal, 0, 'N', $TipoRetencionPrimaLegal, $IdCentro, $TipoEmpleado, 0);
						$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
					}

					$HorasDescuento = $DiasPrimaLegalDescuento * 8;
					$ValorPrimaLegalDescuento = round($ultimoPagoBase * $DiasPrimaLegalDescuento / 360, 0);

					if ($ValorPrimaLegalDescuento) {
						$datos = array($IdPeriodo, 98, $IdEmpleado, $IdPrimaLegalDescuento, $ultimoPagoBase, $HorasDescuento, $ValorPrimaLegalDescuento, 0, 'N', $TipoRetencionPrimaLegalDescuento, $IdCentro, $TipoEmpleado, 0);
						$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
					}
				}

				// LIQUIDACION DE VACACIONES
				if ($NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
					$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
					$NombreTipoContrato <> 'PASANTÍA')
				{
					// SE BUSCAN PAGOS DE VACACIONES
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

					$enjoyedVacation = 0;
					foreach ($vacaciones as $vacation) {
						$enjoyedVacation += $vacation['DiasLiquidados'];
					}

					if ($vacaciones)
						$FechaInicialVacaciones = $vacaciones[0]['FechaCausacion'];
					else
						$FechaInicialVacaciones = max($FechaIngreso, date('Y-m-d', strtotime($FechaRetiro . ' - 1 year + 1 day')));

					// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA
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
								ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialVacaciones' AND 
								ACUMULADOS.FechaFinalPeriodo <= '$FechaRetiro';
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
						$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
					else
						$DiasSancionYLicencias = 0;

					if ($regEmpleado['diasano'] == 360) 
						$DiasVacacionesTotal = Dias360($FechaRetiro, $FechaInicialVacaciones);
					else
						$DiasVacacionesTotal = Dias365($FechaRetiro, $FechaInicialVacaciones);

					$DiasVacaciones = $DiasVacacionesTotal - $DiasSancionYLicencias;

					// VALOR PROMEDIO DE LOS RECARGOS NOCTURNOS
					$FechaInicioPromedio = date('Y-m-d', strtotime($FechaRetiro . ' - 1 year + 1 day'));

					$FechaInicioPromedio = max($FechaIngreso, $FechaInicioPromedio);

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
								AUXILIARES.Id IN (
									231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
								) AND 
								ACUMULADOS.FechaInicialPeriodo >= '$FechaInicioPromedio';
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['BaseVacaciones']) AND $DiasVacaciones>0)
						$PromedioSalarioVariable = round($regAcumulados['BaseVacaciones'] / $DiasVacaciones * 30, 0);
					else
						$PromedioSalarioVariable = 0;

					$query = <<<EOD
						SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BaseVacaciones 
							FROM $ArchivoNomina 
								INNER JOIN AUXILIARES 
									ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
								INNER JOIN MAYORES 
									ON AUXILIARES.IdMayor = MAYORES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.Imputacion = PARAMETROS.Id 
							WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
								AUXILIARES.Id IN (
									231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
								);
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['BaseVacaciones']))
						$PromedioSalarioVariable += round($regAcumulados['BaseVacaciones'] / $DiasVacaciones * 30, 0);

					$SalarioBaseVacaciones = $SueldoBasico + $PromedioSalarioVariable;

					$DiasTotalContrato = Dias360($FechaRetiro, $FechaIngreso);
					$DiasALiquidar = ($DiasTotalContrato * 15 / 360) - $enjoyedVacation;
					if ($DiasVacaciones > 0 AND $DiasALiquidar > 0)
					{
						$ValorVacaciones = round($SalarioBaseVacaciones / 30 * $DiasALiquidar, 0);

						$Horas = $DiasALiquidar * 8;

						$datos = array($IdPeriodo, 98, $IdEmpleado, $IdVacaciones, $SalarioBaseVacaciones, $Horas, $ValorVacaciones, 0, 'N', $TipoRetencionVacaciones, $IdCentro, $TipoEmpleado, 0);
						$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
					}
				}

				// INDEMNIZACION
				if ($NombreMotivoRetiro == 'TERMINACIÓN CONTRATO SIN JUSTA CAUSA')
				{
					// SE CALCULA LA BASE PARA LA INDENISACIÓN
					$query = <<<EOD
						SELECT SUM(acu.valor)/12 AS base
						FROM acumulados acu
						JOIN auxiliares aux on aux.id = acu.idconcepto
						WHERE acu.idempleado = $IdEmpleado AND
							acu.fechainicialperiodo >= DATEADD (month, -12, '$FechaInicialPeriodo') AND 
							acu.fechafinalperiodo <= '$FechaFinalPeriodo' AND 
							aux.id in (
								227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232,
								388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
							)
					EOD;

					$regBaseIndenisacion = $this->model->leerRegistro($query);
					$baseIndenisacion = $SueldoBasico + $regBaseIndenisacion['base'];

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
								ACUMULADOS.FechaFinalPeriodo <= '$FechaRetiro';
					EOD;

					$regAcumulados = $this->model->leerRegistro($query);

					if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
						$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
					else
						$DiasSancionYLicencias = 0;

					if ($NombreTipoContrato == 'INDEFINIDO')
					{
						$DiasIndemnizacion = dias360($FechaRetiro, $FechaIngreso) - $DiasSancionYLicencias;
						$AnosIndemnizacion = $DiasIndemnizacion / 360;

						if ($NombreRegimenCesantias == 'SALARIO INTEGRAL')
							$ValorIndemnizacion = round(($baseIndenisacion / 30 * 20) + ($baseIndenisacion / 30 * max(0, ($AnosIndemnizacion - 1)) * 15), 0);
						else
							$ValorIndemnizacion = round($baseIndenisacion + ($baseIndenisacion / 30 * max(0, ($AnosIndemnizacion - 1)) * 20), 0);
					}
					elseif ($NombreTipoContrato == 'DE LABOR U OBRA CONTRATADA')
					{
						$DiasIndemnizacion = dias360($FechaVencimiento, $FechaRetiro);
						if($DiasIndemnizacion < 15){
							$DiasIndemnizacion = 15;
						}
						$ValorIndemnizacion = round($baseIndenisacion / 2, 0);
					}
					else
					{
						$DiasIndemnizacion = dias360($FechaVencimiento, $FechaRetiro);
						$ValorIndemnizacion = round($baseIndenisacion / 30 * $DiasIndemnizacion, 0);
					}
				}
				else
					$ValorIndemnizacion = 0;

				if ($ValorIndemnizacion > 0)
				{

					$Horas = $DiasIndemnizacion * 8;

					$datos = array($IdPeriodo, 98, $IdEmpleado, $IdIndemnizacion, $baseIndenisacion, $Horas, $ValorIndemnizacion, 0, 'N', $TipoRetencionIndemnizacion, $IdCentro, $TipoEmpleado, 0);
					$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
				}

				// RETENCION FUENTE INDEMNIZACION
				if ($ValorIndemnizacion > 0 AND $SueldoBasico > $ValorUVT * 204)
				{
					$ValorRetFte = round($ValorIndemnizacion * .2, 0);
					$datos = array($IdPeriodo, 98, $IdEmpleado, $IdRetFteIndemnizacion, $ValorIndemnizacion, 0, $ValorRetFte, 0, 'N', $TipoRetencionRetFteIndemnizacion, $IdCentro, $TipoEmpleado, 0);
					$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
				}

				$this->imprimirLiquidacion($IdEmpleado, $DiasSancionYLicencias, $ArchivoNomina);

				
			}

			header('Location: ' . SERVERURL . '/liquidacionContrato/lista/1/0');
			exit();
		}

		public function pagar($aEmpleados)
		{
			ini_set('max_execution_time', 0);

			$Periodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = 98;

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$regPeriodicidad = getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			for	($i = 0; $i < count($aEmpleados); $i++)
			{
				$IdEmpleado = $aEmpleados[$i];

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
								$ArchivoNomina.Ciclo = $Ciclo AND 
								$ArchivoNomina.IdEmpleado = $IdEmpleado 
							ORDER BY $ArchivoNomina.IdEmpleado, $ArchivoNomina.IdConcepto;
				EOD;

				$ok = $this->model->actualizarRegistros($query);

				$FechaLiquidacion = date('Y-m-d');

				$query = <<<EOD
					UPDATE EMPLEADOS
						SET FechaLiquidacion = '$FechaLiquidacion'  
						WHERE EMPLEADOS.Id = $IdEmpleado;
				EOD;

				$this->model->query($query);
			}

			header('Location: ' . SERVERURL . '/liquidacionContrato/lista/1/0');
			exit();
		}

		public function pagarReliquidacion($aEmpleados)
		{
			ini_set('max_execution_time', 0);

			$Periodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = 99;

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$regPeriodicidad = getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			for	($i = 0; $i < count($aEmpleados); $i++)
			{
				$IdEmpleado = $aEmpleados[$i];

				$query = <<<EOD
					INSERT INTO ACUMULADOS 
						(IdPeriodo, Ciclo, FechaInicialPeriodo, FechaFinalPeriodo, IdEmpleado, IdConcepto, Base, Horas, Valor, Saldo, 
						Liquida, Afecta, ClaseCr, IdCentro, TipoEmpleado, IdCredito, Fecha, FechaInicial, FechaFinal, IdTercero)
						SELECT $ArchivoNomina.IdPeriodo, 
								$ArchivoNomina.Ciclo, 
								PERIODOS.FechaInicial AS FechaInicialPeriodo, 
								PERIODOS.FechaFinal AS FechaFinalPeriodo, 
								$ArchivoNomina.IdEmpleado, 
								$ArchivoNomina.IdConcepto, 
								$ArchivoNomina.Base, 
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
								$ArchivoNomina.Ciclo = $Ciclo AND 
								$ArchivoNomina.IdEmpleado = $IdEmpleado 
							ORDER BY $ArchivoNomina.IdEmpleado, $ArchivoNomina.IdConcepto;
				EOD;

				$ok = $this->model->actualizarRegistros($query);

				$FechaLiquidacion = date('Y-m-d');

				$query = <<<EOD
					UPDATE EMPLEADOS
						SET FechaLiquidacion = '$FechaLiquidacion'  
						WHERE EMPLEADOS.Id = $IdEmpleado;
				EOD;

				$this->model->query($query);

				$query = <<<EOD
					DELETE FROM $ArchivoNomina 
						WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
							$ArchivoNomina.Ciclo = $Ciclo;
				EOD;

				$this->model->query($query);
			}

			header('Location: ' . SERVERURL . '/liquidacionContrato/reliquidar');
			exit();
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
		
			$lcTitulo = utf8_decode('LIQUIDACIÓN CONTRATO DE TRABAJO');
			$lcOrientacion = 'P';
			$lcEncabezado = '';
			

			$PDF->AddFont('Tahoma','','tahoma.php');
			$PDF->AddPage($lcOrientacion);
			$PDF->SetFont('Tahoma', '', 8);

			$regEmpleado = getRegistro('EMPLEADOS', $IdEmpleado);
			$Documento = $regEmpleado['documento'];
			$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];

			$NombreEmpleado = str_replace('/', ' ', $NombreEmpleado);
			
			$NombreCargo = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
			$TipoContrato = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
			$MotivoRetiro = getRegistro('PARAMETROS', $regEmpleado['motivoretiro'])['detalle'];
			$DiasTrabajados = dias360($regEmpleado['fecharetiro'], $regEmpleado['fechaingreso']);

			$P_ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte'")['valor'];
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

			$query = <<<EOD
				SELECT
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						NOMINA.Base, 
						NOMINA.Horas, 
						PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
						IIF(PARAMETROS1.Detalle = 'PAGO', NOMINA.Valor, 0) AS Pagos, 
						IIF(PARAMETROS1.Detalle = 'PAGO', 0, NOMINA.Valor) AS Deducciones,
						PARAMETROS1.detalle
					FROM $ArchivoNomina AS NOMINA 
						INNER JOIN AUXILIARES 
							ON NOMINA.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1  
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2  
							ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
					WHERE NOMINA.IdEmpleado = $IdEmpleado AND 
						NOMINA.Ciclo = 98 
					ORDER BY PARAMETROS1.Detalle DESC, MAYORES.Mayor, AUXILIARES.Auxiliar;
			EOD;

			$datos = $this->model->listar($query);

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

			for ($i = 0; $i < count($datos); $i++)
			{
				$reg = $datos[$i];

				$PDF->Cell(55, 5, utf8_decode($reg['NombreConcepto']), 0, 0, 'L'); 
				if ($reg['Base'] > 0)
					$PDF->Cell(25, 5, '$' . number_format($reg['Base'], 0), 0, 0, 'R'); 
				else
					$PDF->Cell(25, 5, '', 0, 0, 'R'); 

				if ($reg['Horas'] > 0)
				{
					if ($reg['NombreTipoLiquidacion'] == 'DÍAS')
						$PDF->Cell(25, 5, number_format($reg['Horas'] / 8, 2) . utf8_decode(' DÍAS'), 0, 0, 'R'); 
					else
						$PDF->Cell(25, 5, number_format($reg['Horas'], 2) . ' HORAS', 0, 0, 'R'); 
				}
				else
					$PDF->Cell(25, 5, '', 0, 0, 'R'); 

				if ($reg['Pagos'] > 0)
				{
					$PDF->Cell(30, 5, '$' . number_format($reg['Pagos'], 0), 0, 0, 'R'); 
					$TotalPagos += $reg['Pagos'];
				}
				elseif ($reg['Pagos'] < 0)
				{
					$PDF->Cell(30, 5, '', 0, 0, 'R'); 
					$PDF->Cell(30, 5, '$' . number_format(abs($reg['Pagos']), 0), 0, 0, 'R'); 
					$TotalDeducciones += abs($reg['Pagos']);
				}
				else
					$PDF->Cell(30, 5, '', 0, 0, 'R'); 
	
				if ($reg['Deducciones'] > 0)
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
	
		public function imprimirReliquidacion($IdEmpleado, $ArchivoNomina)
		{
			global $lcOrientacion;
			global $lcTitulo;
			global $lcSubTitulo;
			global $lcEncabezado;
			global $lcEncabezado2;
			$PDF = new PDF(); 
			$PDF->AliasNbPages();
		
			$lcTitulo = utf8_decode('RELIQUIDACIÓN CONTRATO DE TRABAJO');
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

			$regEmpleado = getRegistro('EMPLEADOS', $IdEmpleado);
			$Documento = $regEmpleado['documento'];
			$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];
			$NombreCargo = getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
			$TipoContrato = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
			$MotivoRetiro = getRegistro('PARAMETROS', $regEmpleado['motivoretiro'])['detalle'];
			$DiasTrabajados = dias360($regEmpleado['fecharetiro'], $regEmpleado['fechaingreso']);

			$P_ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte'")['valor'];
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

			$query = <<<EOD
				SELECT MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						NOMINA.Base, 
						NOMINA.Horas, 
						PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
						IIF(PARAMETROS1.Detalle = 'PAGO', NOMINA.Valor, 0) AS Pagos, 
						IIF(PARAMETROS1.Detalle = 'PAGO', 0, NOMINA.Valor) AS Deducciones 
					FROM $ArchivoNomina AS NOMINA 
						INNER JOIN AUXILIARES 
							ON NOMINA.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1  
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2  
							ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
					WHERE NOMINA.IdEmpleado = $IdEmpleado AND 
						NOMINA.Ciclo = 99 
					ORDER BY PARAMETROS1.Detalle DESC, MAYORES.Mayor, AUXILIARES.Auxiliar;
			EOD;

			$datos = $this->model->listar($query);

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
			// $PDF->Cell(50, 5, utf8_decode('DÍAS EN SANCIÓN Y/O LICENCIAS:'), 0, 0, 'L'); 
			// $PDF->Cell(60, 5, number_format($DiasSancionYLicencias, 0) . utf8_decode(' días'), 0, 0, 'L'); 
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

			for ($i = 0; $i < count($datos); $i++)
			{
				$reg = $datos[$i];

				$PDF->Cell(55, 5, utf8_decode($reg['NombreConcepto']), 0, 0, 'L'); 
				if ($reg['Base'] > 0)
					$PDF->Cell(25, 5, '$' . number_format($reg['Base'], 0), 0, 0, 'R'); 
				else
					$PDF->Cell(25, 5, '', 0, 0, 'R'); 

				if ($reg['Horas'] > 0)
				{
					if ($reg['NombreTipoLiquidacion'] == 'DÍAS')
						$PDF->Cell(25, 5, number_format($reg['Horas'] / 8, 2) . utf8_decode(' DÍAS'), 0, 0, 'R'); 
					else
						$PDF->Cell(25, 5, number_format($reg['Horas'], 2) . ' HORAS', 0, 0, 'R'); 
				}
				else
					$PDF->Cell(25, 5, '', 0, 0, 'R'); 

				if ($reg['Pagos'] > 0)
					$PDF->Cell(30, 5, '$' . number_format($reg['Pagos'], 0), 0, 0, 'R'); 
				else
					$PDF->Cell(30, 5, '', 0, 0, 'R'); 
	
				if ($reg['Deducciones'] > 0)
					$PDF->Cell(30, 5, '$' . number_format($reg['Deducciones'], 0), 0, 0, 'R'); 
				else
					$PDF->Cell(30, 5, '', 0, 0, 'R'); 

				$TotalPagos += $reg['Pagos'];
				$TotalDeducciones += $reg['Deducciones'];

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


			$PDF->Output('F', 'descargas/ReliquidacionContrato_' . $Documento . '_' . $NombreEmpleado . '.PDF'); 
		}
	
		public function lista($pagina, $activar)
		{
			$data['mensajeError'] = '';
			$data['activar'] = $activar;

			$Cesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES CESANTÍAS'");
			$IdCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Cesantias");

			if ($IdCesantias == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Cesantías') . '<br>';

			$InteresCesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES INTERÉS DE CESANTÍAS'");
			$IdInteresCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $InteresCesantias");

			if ($IdInteresCesantias == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Interés de cesantías') . '<br>';

			$AnticipoCesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES ANTICIPO DE CESANTÍAS'");
			$IdAnticipoCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $AnticipoCesantias");
	
			if ($IdAnticipoCesantias == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Anticipo de cesantías') . '<br>';

			$Vacaciones = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES VACACIONES EN DINERO'");
			$IdVacaciones = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Vacaciones");
		
			if ($IdVacaciones == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en dinero') . '<br>';

			if (! isset($_REQUEST['okLiquidar']))
			{
				if (! isset($_REQUEST['okPagar']))
				{
					$_SESSION['NuevoRegistro'] = '';
					$_SESSION['BorrarRegistro'] = '';
					$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionContrato/liquidar';
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
				
					$_SESSION['Paginar'] = TRUE;

					$_SESSION['LIQ_CONTRATO']['Pagina'] = $pagina;
					$_SESSION['PaginaActual'] = $_SESSION['LIQ_CONTRATO']['Pagina'];
					
					if	( isset($_REQUEST['Filtro']) )
					{
						$_SESSION['LIQ_CONTRATO']['Filtro'] = $_REQUEST['Filtro'];
						$_SESSION['LIQ_CONTRATO']['Pagina'] = 1;
						$pagina = 1;
					}

					if (! isset($_SESSION['LIQ_CONTRATO']['Filtro']))
					{
						$_SESSION['LIQ_CONTRATO']['Filtro'] = '';
					}

					$lcFiltro = $_SESSION['LIQ_CONTRATO']['Filtro'];

					if (isset($_REQUEST['Orden']))
					{
						$_SESSION['LIQ_CONTRATO']['Orden'] = $_REQUEST['Orden'];
						$_SESSION['LIQ_CONTRATO']['Pagina'] = 1;
						$pagina = 1;
					}
					else
						if (! isset($_SESSION['LIQ_CONTRATO']['Orden'])) 
							$_SESSION['LIQ_CONTRATO']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

					$EmpleadoRetirado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");

					$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
					$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
					$FechaInicialPeriodo = $regPeriodo['fechainicial'];
					$FechaFinalPeriodo = $regPeriodo['fechafinal'];

					$query = <<<EOD
						WHERE EMPLEADOS.Estado = $EmpleadoRetirado AND 
							EMPLEADOS.FechaLiquidacion IS NULL 
					EOD;

							// EMPLEADOS.FechaRetiro >= '$FechaInicialPeriodo' AND 
							// EMPLEADOS.FechaRetiro <= '$FechaFinalPeriodo' AND 

					if	( ! empty($lcFiltro) )
					{
						$aFiltro = explode(' ', $lcFiltro);

						for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
						{

							$query .= 'AND (';
							$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= ') ';
						}
					}
					
					$data['registros'] = $this->model->contarRegistros($query);
					$lineas = LINES;
					$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
					$query .= 'ORDER BY ' . $_SESSION['LIQ_CONTRATO']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
					$data['rows'] = $this->model->listarEmpleadosRetirados($query);

					for ($i = 0; $i < count($data['rows']); $i++)
					{
						if ($data['rows'][$i]['IdBanco'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $data['rows'][$i]['Documento'] . ' (' . $data['rows'][$i]['Apellido1'] . ' ' . $data['rows'][$i]['Apellido2'] . ' ' . $data['rows'][$i]['Nombre1'] . ' ' . $data['rows'][$i]['Nombre2'] . ') no tiene definido un BANCO.<br>';
	
						if ($data['rows'][$i]['CuentaBancaria'] == '') 
							$data['mensajeError'] .= 'Empleado ' . $data['rows'][$i]['Documento'] . ' (' . $data['rows'][$i]['Apellido1'] . ' ' . $data['rows'][$i]['Apellido2'] . ' ' . $data['rows'][$i]['Nombre1'] . ' ' . $data['rows'][$i]['Nombre2'] . ') no tiene definida una CUENTA BANCARIA.<br>';
	
						if ($data['rows'][$i]['TipoCuentaBancaria'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $data['rows'][$i]['Documento'] . ' (' . $data['rows'][$i]['Apellido1'] . ' ' . $data['rows'][$i]['Apellido2'] . ' ' . $data['rows'][$i]['Nombre1'] . ' ' . $data['rows'][$i]['Nombre2'] . ') no tiene definido un TIPO DE CUENTA BANCARIA.<br>';
					}

					$this->views->getView($this, 'empleadosRetirados', $data);
				}
				else
					$this->pagar($_REQUEST['okPagar']);
			}
			else
				$this->liquidar($_REQUEST['okLiquidar'], $activar);
		}	

		public function listar($pagina, $activar)
		{
			$data['mensajeError'] = '';
			$data['activar'] = $activar;

			$Cesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES CESANTÍAS'");
			$IdCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Cesantias");

			if ($IdCesantias == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Cesantías') . '<br>';

			$InteresCesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES INTERÉS DE CESANTÍAS'");
			$IdInteresCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $InteresCesantias");

			if ($IdInteresCesantias == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Interés de cesantías') . '<br>';

			$AnticipoCesantias = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES ANTICIPO DE CESANTÍAS'");
			$IdAnticipoCesantias = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $AnticipoCesantias");
	
			if ($IdAnticipoCesantias == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Anticipo de cesantías') . '<br>';

			$Vacaciones = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES VACACIONES EN DINERO'");
			$IdVacaciones = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $Vacaciones");
		
			if ($IdVacaciones == 0)
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en dinero') . '<br>';

			if (! isset($_REQUEST['okReLiquidar']))
			{
				$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
				$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
				$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
				$Ciclo = 99;
				$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];
	
				$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
				$Periodo = $regPeriodo['periodo'];
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];
	
				$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
				$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);
	
				$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

				if (! isset($_REQUEST['okPagar']))
				{
					$_SESSION['NuevoRegistro'] = '';
					$_SESSION['BorrarRegistro'] = '';
					$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionContrato/reliquidar';

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
					$_SESSION['Lista'] = SERVERURL . '/liquidacionContrato/reliquidar';
				
					$_SESSION['Paginar'] = TRUE;

					$_SESSION['LIQ_CONTRATO']['Pagina'] = $pagina;
					$_SESSION['PaginaActual'] = $_SESSION['LIQ_CONTRATO']['Pagina'];
					
					if	( isset($_REQUEST['Filtro']) )
					{
						$_SESSION['LIQ_CONTRATO']['Filtro'] = $_REQUEST['Filtro'];
						$_SESSION['LIQ_CONTRATO']['Pagina'] = 1;
						$pagina = 1;
					}

					if (! isset($_SESSION['LIQ_CONTRATO']['Filtro']))
					{
						$_SESSION['LIQ_CONTRATO']['Filtro'] = '';
					}

					$lcFiltro = $_SESSION['LIQ_CONTRATO']['Filtro'];

					if (isset($_REQUEST['Orden']))
					{
						$_SESSION['LIQ_CONTRATO']['Orden'] = $_REQUEST['Orden'];
						$_SESSION['LIQ_CONTRATO']['Pagina'] = 1;
						$pagina = 1;
					}
					else
						if (! isset($_SESSION['LIQ_CONTRATO']['Orden'])) 
							$_SESSION['LIQ_CONTRATO']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

					$EmpleadoRetirado = getId('PARAMETROS', "PARAMETROS.Parametro = 'EstadoEmpleado' AND PARAMETROS.Detalle = 'RETIRADO'");

					$query = <<<EOD
						WHERE $ArchivoNomina.Ciclo = $Ciclo 
					EOD;
							// AND EMPLEADOS.FechaLiquidacion IS NOT NULL 

					if	( ! empty($lcFiltro) )
					{
						$aFiltro = explode(' ', $lcFiltro);

						for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
						{
							$query .= 'AND (';
							$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= ') ';
						}
					}
					
					$data['registros'] = $this->model->contarReliquidados($query, $ArchivoNomina);
					$lineas = LINES;
					$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
					$query .= 'ORDER BY ' . $_SESSION['LIQ_CONTRATO']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
					$data['rows'] = $this->model->listarEmpleadosReliquidados($query, $ArchivoNomina);

					for ($i = 0; $i < count($data['rows']); $i++)
					{
						if ($data['rows'][$i]['IdBanco'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $data['rows'][$i]['Documento'] . ' (' . $data['rows'][$i]['Apellido1'] . ' ' . $data['rows'][$i]['Apellido2'] . ' ' . $data['rows'][$i]['Nombre1'] . ' ' . $data['rows'][$i]['Nombre2'] . ') no tiene definido un BANCO.<br>';
	
						if ($data['rows'][$i]['CuentaBancaria'] == '') 
							$data['mensajeError'] .= 'Empleado ' . $data['rows'][$i]['Documento'] . ' (' . $data['rows'][$i]['Apellido1'] . ' ' . $data['rows'][$i]['Apellido2'] . ' ' . $data['rows'][$i]['Nombre1'] . ' ' . $data['rows'][$i]['Nombre2'] . ') no tiene definida una CUENTA BANCARIA.<br>';
	
						if ($data['rows'][$i]['TipoCuentaBancaria'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $data['rows'][$i]['Documento'] . ' (' . $data['rows'][$i]['Apellido1'] . ' ' . $data['rows'][$i]['Apellido2'] . ' ' . $data['rows'][$i]['Nombre1'] . ' ' . $data['rows'][$i]['Nombre2'] . ') no tiene definido un TIPO DE CUENTA BANCARIA.<br>';
					}

					$this->views->getView($this, 'empleadosReliquidados', $data);
				}
				else{
					$this->pagarReliquidacion($_REQUEST['okPagar']);
				}
			}
			else{
				$this->reliquidar($_REQUEST['okReLiquidar']);
				// $this->imprimirReliquidacion($_REQUEST['okReLiquidar'], $ArchivoNomina);
			}
		}	

		public function reliquidar($aEmpleados = array())
		{
			ini_set('max_execution_time', 0);

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionContrato/reliquidar';
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
			$_SESSION['Lista'] = SERVERURL . '/liquidacionContrato/listar/1/0';

			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo = 99;
			$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			$data = array(
				'reg' => array(
					'IdPeriodo' 	=> $IdPeriodo,
					'Ciclo' 		=> $Ciclo,
					'IdEmpleado' 	=> 0,
					'IdCentro' 		=> 0,
					'TipoEmpleado' 	=> 0,
					'Conceptos' 	=> array()
				),	
				'mensajeError' => ''
			);		

			$arrayDocs = array();
			if (isset($_REQUEST['Documento']) AND ! empty($_REQUEST['Documento'])) 
			{
				$arrayDocs =array($_REQUEST['Documento']);
			}elseif(count($aEmpleados) > 0){
				$arrayDocs = $aEmpleados;
			}

			if(count($arrayDocs) > 0){
			
				for($i = 0; $i < count($arrayDocs); $i++)
				{
					$Documento = $arrayDocs[$i];				

						$query = <<<EOD
							SELECT EMPLEADOS.* 
								FROM EMPLEADOS 
									INNER JOIN PARAMETROS 
										ON EMPLEADOS.Estado = PARAMETROS.Id 
								WHERE EMPLEADOS.Documento = '$Documento' AND 
									PARAMETROS.Detalle = 'RETIRADO' AND 
									EMPLEADOS.FechaLiquidacion IS NOT NULL
									ORDER BY fecharetiro;
						EOD;

						$regEmpleado = $this->model->listarRegistros($query);
						

						if (count($regEmpleado) > 0)
						{
							$regEmpleado = $regEmpleado[count($regEmpleado)-1];
							$regCentro = getRegistro('CENTROS', $regEmpleado['idcentro']);
							$data['reg']['IdEmpleado'] 		= $regEmpleado['id'];
							$data['reg']['IdCentro'] 		= $regEmpleado['idcentro'];
							$data['reg']['TipoEmpleado'] 	= $regCentro['tipoempleado'];
						}
						else{
							$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
						}
						
						if (isset($_REQUEST['aConcepto']))
						{
							for ($i = 0; $i < count($_REQUEST['aConcepto']); $i++)
							{
								if	( empty($_REQUEST['aConcepto'][$i]) )
									$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Concepto') . '</strong><br>';
								else
								{
									for ($j = 0; $j < $i; $j++)
									{
										if ($_REQUEST['aConcepto'][$j] == $_REQUEST['aConcepto'][$i])
											$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong>: ' . $_REQUEST['aConcepto'][$j] . ' ' . label('ya está reportado') . '<br>';
									}

									$regMayor = getRegistro('MAYORES', 0, "MAYORES.Mayor = '" . substr($_REQUEST['aConcepto'][$i], 0, 2) . "'");

									if ($regMayor) 
									{
										$TipoRetencion = $regMayor['tiporetencion'];

										$IdMayor = $regMayor['id'];
										$Auxiliar = substr($_REQUEST['aConcepto'][$i], 2, 3);

										$query = <<<EOD
											SELECT AUXILIARES.* 
												FROM AUXILIARES 
												WHERE AUXILIARES.IdMayor = $IdMayor AND 
													AUXILIARES.Auxiliar = '$Auxiliar' AND 
													AUXILIARES.Borrado = 0;
										EOD;

										$regAuxiliar = $this->model->leer($query);

										if ($regAuxiliar) 
										{
											$data['reg']['Conceptos'][$i]['IdConcepto'] = $regAuxiliar['id'];
											$data['reg']['Conceptos'][$i]['TipoRetencion'] = $TipoRetencion;
										}
										else
											$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
									}
									else
										$data['mensajeError'] .= '<strong>' . label('Concepto') . '</strong> ' . label('no existe') . '<br>';
								}

								if	( ! empty($_REQUEST['aTercero'][$i]) )
								{
									$regTercero = getRegistro('TERCEROS', 0, "TERCEROS.Documento = '" . $_REQUEST['aTercero'] . "'");

									if ($regTercero) 
									{
										$data['reg']['Conceptos'][$i]['IdTercero'] = $regTercero['id'];
									}
									else
										$data['mensajeError'] .= '<strong>' . label('Tercero') . '</strong> ' . $_REQUEST['aTercero'] . ' ' . label('no existe') . '<br>';
								}
								else
									$data['reg']['Conceptos'][$i]['IdTercero'] = 0;

								if ($_REQUEST['aHoras'][$i] > 0)
								{
									$data['reg']['Conceptos'][$i]['Base'] = $regEmpleado['sueldobasico'];
									$data['reg']['Conceptos'][$i]['Horas'] = $_REQUEST['aHoras'][$i];
									$data['reg']['Conceptos'][$i]['Valor'] = round($regEmpleado['sueldobasico'] / $regEmpleado['horasmes'] * $_REQUEST['aHoras'][$i] * $regAuxiliar['factorconversion'], 0);
								}

								if ($_REQUEST['aValor'][$i] > 0)
								{
									$data['reg']['Conceptos'][$i]['Base'] = 0;
									$data['reg']['Conceptos'][$i]['Horas'] = 0;
									$data['reg']['Conceptos'][$i]['Valor'] = $_REQUEST['aValor'][$i];
								}
							}
						}

						if	( ! $data['mensajeError'] )
						{
							if (count($data['reg']['Conceptos']) > 0){
								$this->model->guardarNovedades($ArchivoNomina, $data['reg']);
							}
				
							// SUELDO BASICO
							$IdSueldoBasico = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES SUELDO BÁSICO'");
							$IdSueldoBasico = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $IdSueldoBasico");

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
				
							$ValorUVT = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'")['valor'];
							$P_ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte'")['valor'];

							$IdEmpleado 	= $regEmpleado['id'];
							$Documento 		= $regEmpleado['documento'];
							$NombreEmpleado = $regEmpleado['apellido1'] . ' ' . $regEmpleado['apellido2'] . ' ' . $regEmpleado['nombre1'] . ' ' . $regEmpleado['nombre2'];
							$Cargo 			= getRegistro('CARGOS', $regEmpleado['idcargo'])['nombre'];
							$IdCentro 		= $regEmpleado['idcentro'];
							$TipoEmpleado 	= getRegistro('CENTROS', $IdCentro)['tipoempleado'];
							$FechaIngreso 	= $regEmpleado['fechaingreso'];
							$FechaRetiro 	= isset($_REQUEST['FechaRetiro']) ? $_REQUEST['FechaRetiro'] : $regEmpleado['fecharetiro'];
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
							$NombreTipoContrato = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];
							$NombreMotivoRetiro = getRegistro('PARAMETROS', $regEmpleado['motivoretiro'])['detalle'];
				
							// SE BORRAN LIQUIDACIONES ANTERIORES
							$query = <<<EOD
								DELETE FROM $ArchivoNomina 
									WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND
										$ArchivoNomina.IdPeriodo = $IdPeriodo AND 
										$ArchivoNomina.Ciclo = 99 AND 
										$ArchivoNomina.Liquida <> 'T';
							EOD;

							$ok = $this->model->query($query);

							// AQUI SE LIQUIDA LA PRENOMINA DEL EMPLEADO
							$controller = 'liquidacionPrenomina';
							$controllerFile = 'controllers/' . $controller . '.php';

							require_once($controllerFile);
							$controller = new $controller();
							$controller->liquidar(0, $Documento, 0, 99);

							// SE TRASLADAN LAS NOVEDADES AL CICLO 99
							$query = <<<EOD
								UPDATE $ArchivoNomina 
									SET Ciclo = 99, 
										Liquida = 'T' 
									WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
										$ArchivoNomina.Ciclo = $Ciclo AND 
										$Ciclo < 98;
							EOD;
			
							$ok = $this->model->query($query);


							// LIQUIDACION DE CESANTIAS E INTERESES
							if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
								$NombreTipoContrato <> 'PASANTÍA')
							{
								if ($NombreRegimenCesantias == 'RÉGIMEN TRADICIONAL') 
									$FechaInicialCesantias = $FechaIngreso;
								else
									$FechaInicialCesantias = MAX($FechaIngreso, ComienzoAno($FechaRetiro));

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
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialCesantias';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
									$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
								else
									$DiasSancionYLicencias = 0;

								if ($regEmpleado['diasano'] == 360) 
									$DiasCesantias = (Dias360($FechaRetiro, $FechaInicialCesantias) - $DiasSancionYLicencias);
								else
									$DiasCesantias = (Dias365($FechaRetiro, $FechaInicialCesantias) - $DiasSancionYLicencias);

								// SE CALCULA EL PROMEDIO SALARIAL DE ACUMULADOS
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
											MAYORES.BaseCesantias = 1 AND 
											AUXILIARES.Id <> $IdSueldoBasico;
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BaseCesantias']))
									$PromedioSalarial = round($regAcumulados['BaseCesantias'] / $DiasCesantias * 30, 0);
								else
									$PromedioSalarial = 0;
										
								// SE CALCULA EL PROMEDIO SALARIAL DEL PERIODO EN LIQUIDACION
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BaseCesantias  
										FROM $ArchivoNomina 
											INNER JOIN AUXILIARES 
												ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.idMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											MAYORES.BaseCesantias = 1 AND 
											AUXILIARES.Id <> $IdSueldoBasico;
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BaseCesantias']))
									$PromedioSalarial += round($regAcumulados['BaseCesantias'] / $DiasCesantias * 30, 0);

								$SalarioBaseCesantias = $SueldoBasico + $ValorSubsidioTransporte + $PromedioSalarial;
								
								$Horas = ($DiasCesantias + $DiasSancionYLicencias) * 8;
								$ValorCesantias = round($SalarioBaseCesantias * ($DiasCesantias + $DiasSancionYLicencias) / 360, 0);

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
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialCesantias';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
									$AnticipoCesantias = $regAcumulados['Valor'];
								else
									$AnticipoCesantias = 0;

								$ValorCesantias -= $AnticipoCesantias;

								if ($ValorCesantias > 0)
								{
									$datos = array($IdPeriodo, 99, $IdEmpleado, $IdCesantias, $SalarioBaseCesantias, $Horas, $ValorCesantias, 0, 'N', $TipoRetencionCesantias, $IdCentro, $TipoEmpleado, 0);
									$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
								}

								// INTERES SOBRE LAS CESANTIAS
								$ValorInteresCesantias = round($ValorCesantias * ($DiasCesantias + $DiasSancionYLicencias) * 0.12 / 360, 0);
								
								if ($ValorInteresCesantias > 0)
								{
									$datos = array($IdPeriodo, 99, $IdEmpleado, $IdInteresCesantias, $ValorCesantias, 0, $ValorInteresCesantias, 0, 'N', $TipoRetencionInteresCesantias, $IdCentro, $TipoEmpleado, 0);
									$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
								}
							}
							else{
								$DiasSancionYLicencias = 0;
							}
				
							// LIQUIDACION PRIMA LEGAL
							if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
								$NombreTipoContrato <> 'PASANTÍA')
							{
								$FechaInicialPrimaLegal = max($FechaIngreso, ComienzoSemestre($FechaRetiro));

								// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA
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
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
									$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
								else
									$DiasSancionYLicencias = 0;

								if ($regEmpleado['diasano'] == 360) 
									$DiasPrimaLegal = Dias360($FechaRetiro, $FechaInicialPrimaLegal) - $DiasSancionYLicencias;
								else
									$DiasPrimaLegal = Dias365($FechaRetiro, $FechaInicialPrimaLegal) - $DiasSancionYLicencias;

								// VALOR SALARIO PROMEDIO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS BasePrima 
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES 
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.BasePrimas = 1 AND 
											AUXILIARES.Id <> $IdSueldoBasico AND 
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BasePrima']))
									if ($DiasPrimaLegal >= 30)
										$PromedioSalarioVariable = round($regAcumulados['BasePrima'] / $DiasPrimaLegal * 30, 0);
									else
										$PromedioSalarioVariable = $regAcumulados['BasePrima'];
								else
									$PromedioSalarioVariable = 0;

								// SE CALCULA EL PROMEDIO SALARIAL DEL PERIODO EN LIQUIDACION
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BasePrimas  
										FROM $ArchivoNomina 
											INNER JOIN AUXILIARES 
												ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.idMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											MAYORES.BasePrimas = 1 AND 
											AUXILIARES.Id <> $IdSueldoBasico;
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BasePrimas']))
									if ($DiasPrimaLegal > 30)
										$PromedioSalarioVariable += round($regAcumulados['BasePrimas'] / $DiasPrimaLegal * 30, 0);
									else
										$PromedioSalarioVariable += $regAcumulados['BasePrimas'];

								$SalarioBasePrimaLegal = $SueldoBasico + $ValorSubsidioTransporte + $PromedioSalarioVariable;

								$Horas = $DiasPrimaLegal * 8;
								$ValorPrimaLegal = round($SalarioBasePrimaLegal * $DiasPrimaLegal / 360, 0);

								// SE BUSCAN LOS PAGOS DE PRIMAS DEL PERIODO
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
											MAYORES.Nombre = 'PRIMA LEGAL' AND 
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
									$AnticipoPrimaLegal = $regAcumulados['Valor'];
								else
									$AnticipoPrimaLegal = 0;

								$ValorPrimaLegal -= $AnticipoPrimaLegal;

								if ($ValorPrimaLegal > 0)
								{
									$datos = array($IdPeriodo, 99, $IdEmpleado, $IdPrimaLegal, $SalarioBasePrimaLegal, $Horas, $ValorPrimaLegal, 0, 'N', $TipoRetencionPrimaLegal, $IdCentro, $TipoEmpleado, 0);
									$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
								}
							}
				
							// LIQUIDACION DE VACACIONES
							if ($NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
								$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
								$NombreTipoContrato <> 'PASANTÍA')
							{
								// SE BUSCAN PAGOS DE VACACIONES
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
									$FechaInicialVacaciones = $vacaciones[0]['FechaCausacion'];
									$DiasLiquidados = $vacaciones[0]['DiasLiquidados'];
								}
								else
								{
									if ($FechaIngreso < date('Y-m-d', strtotime($FechaRetiro . ' - 1 year + 1 day')))
										$FechaInicialVacaciones = $FechaIngreso;
									else
										$FechaInicialVacaciones = max($FechaIngreso, date('Y-m-d', strtotime($FechaRetiro . ' - 1 year + 1 day')));
									
									$DiasLiquidados = 0;
								}

								// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA
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
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialVacaciones';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
									$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
								else
									$DiasSancionYLicencias = 0;

								if ($regEmpleado['diasano'] == 360) 
									$DiasVacaciones = Dias360($FechaRetiro, $FechaInicialVacaciones) - $DiasSancionYLicencias;
								else
									$DiasVacaciones = Dias365($FechaRetiro, $FechaInicialVacaciones) - $DiasSancionYLicencias;

								// VALOR PROMEDIO DE LOS RECARGOS NOCTURNOS
								$FechaInicioPromedio = date('Y-m-d', strtotime($FechaRetiro . ' - 1 year + 1 day'));

								$FechaInicioPromedio = max($FechaIngreso, $FechaInicioPromedio);

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
											MAYORES.BaseVacaciones = 1 AND 
											AUXILIARES.Id  IN (
												231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
											) AND 
											ACUMULADOS.FechaInicialPeriodo >= '$FechaInicioPromedio';
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BaseVacaciones']))
								{
									if ($DiasVacaciones > 360)
										$PromedioSalarioVariable = round($regAcumulados['BaseVacaciones'] / 360 * 30, 0);
									else
										$PromedioSalarioVariable = round($regAcumulados['BaseVacaciones'] / $DiasVacaciones * 30, 0);
								}
								else
									$PromedioSalarioVariable = 0;

								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BaseVacaciones 
										FROM $ArchivoNomina 
											INNER JOIN AUXILIARES 
												ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
											MAYORES.BaseVacaciones = 1 AND 
											AUXILIARES.Id  IN (
												231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
											);
								EOD;

								$regAcumulados = $this->model->leerRegistro($query);

								if ($regAcumulados AND ! is_null($regAcumulados['BaseVacaciones']))
									$PromedioSalarioVariable += round($regAcumulados['BaseVacaciones'] / $DiasVacaciones * 30, 0);

								$SalarioBaseVacaciones = $SueldoBasico + $PromedioSalarioVariable;
									
								if ($DiasVacaciones > 0)
								{
									$DiasALiquidar = ($DiasVacaciones + $DiasSancionYLicencias) * 15 / 360;
									$DiasALiquidar -= $DiasLiquidados;

									$ValorVacaciones = round($SalarioBaseVacaciones / 30 * $DiasALiquidar, 0);

									$Horas = $DiasALiquidar * 8;

									// SE BUSCAN LOS PAGOS DE VACACIONES DEL PERIODO
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
												MAYORES.Nombre = 'VACACIONES EN DINERO' AND 
												ACUMULADOS.Ciclo = 98;
									EOD;

									$regAcumulados = $this->model->leerRegistro($query);

									if ($regAcumulados AND ! is_null($regAcumulados['Valor'])) 
										$AnticipoVacaciones = $regAcumulados['Valor'];
									else
										$AnticipoVacaciones = 0;

									$ValorVacaciones -= $AnticipoVacaciones;

									if ($ValorVacaciones > 0)
									{
										$datos = array($IdPeriodo, 99, $IdEmpleado, $IdVacaciones, $SalarioBaseVacaciones, $Horas, $ValorVacaciones, 0, 'N', $TipoRetencionVacaciones, $IdCentro, $TipoEmpleado, 0);
										$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
									}
								}
							}

							if ($FechaRetiro > $regEmpleado['fecharetiro'])
							{
								$query = <<<EOD
									UPDATE EMPLEADOS 
										SET FechaRetiro = '$FechaRetiro' 
										WHERE EMPLEADOS.Id = $IdEmpleado;
								EOD;

								$this->model->query($query);
							}
				
							$this->imprimirReliquidacion($IdEmpleado, $ArchivoNomina);
						}
						
					}
				header('Location: ' . SERVERURL . '/liquidacionContrato/listar/1/0');
				exit();
			}
			$this->views->getView($this, 'reliquidarContrato', $data);
			exit;
		}
	}
?>
