<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class LiquidacionPrima extends Controllers
	{
		public function liquidar() {
			$data = array(
				'reg' => array(
					'Empleado' 		=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : NULL
					),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['LiqPrimaSave'])) {
				$data['mensajeError'] = !isset($_REQUEST['Action'])
					? $this->liquidarPrima() : $this->acumular();
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionPrima/liquidar';
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
			$_SESSION['Lista'] = SERVERURL . '/liquidacionPrima/lista/97/1';

			$this->views->getView($this, 'actualizar', $data);
		}

		private function liquidarPrima() {
			set_time_limit(0);

			$Ciclo = $_REQUEST['CicloPrima']; // Ciclo para PAGO DE PRIMA

			// SE LEEN EL PERIODO DEFINIDO PARA PAGO DE PRIMA
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];

			$regPeriodoPar = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$IdPeriodo = $regPeriodoPar['valor'];
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);

			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$PrimaLegal = getId('PARAMETROS', "PARAMETROS.Parametro = 'TipoRegistroAuxiliar' AND PARAMETROS.Detalle = 'ES PRIMA DE SERVICIOS'");
			$IdPrimaLegal = getId('AUXILIARES', "AUXILIARES.TipoRegistroAuxiliar = $PrimaLegal");
			$IdMayorPrimaLegal = getRegistro('AUXILIARES', $IdPrimaLegal)['idmayor'];
			$TipoRetencionPrimaLegal = getRegistro('MAYORES', $IdMayorPrimaLegal)['tiporetencion'];

			$mensajeError = '';

			if (!isset($Ciclo) OR ($Ciclo<>'96' AND $Ciclo<>'97')) $mensajeError = label('Período o Ciclo incorrectos') . '<br>';
			if (!$regPeriodo) return label('Perído definido no existe') . '<br>';

			$query = <<<EOD
				PERIODOSACUMULADOS.IdPeriodo  = $IdPeriodo AND 
				PERIODOSACUMULADOS.Ciclo = $Ciclo; 
			EOD;

			$regPA = getRegistro('PERIODOSACUMULADOS', 0, $query);

			if ($regPA AND $regPA['acumulado'] == 1)
				$mensajeError .= label('Período - Ciclo ya está liquidado y acumulado') . '<br>';

			if (empty($mensajeError)) {
				$Periodo = $regPeriodo['periodo'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];

				$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;
				$P_Empleado = isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : NULL;

				$query = <<<EOD
					SELECT
						EMPLEADOS.id,
						EMPLEADOS.regimencesantias,
						EMPLEADOS.tipocontrato,
						EMPLEADOS.fechaingreso,
						EMPLEADOS.diasano,
						EMPLEADOS.sueldobasico,
						EMPLEADOS.subsidiotransporte,
						EMPLEADOS.idcentro
					FROM EMPLEADOS
					INNER JOIN PARAMETROS ON EMPLEADOS.Estado = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ACTIVO'
				EOD;

				if ($P_Empleado) $query .= " AND EMPLEADOS.Documento = '$P_Empleado'";

				$IdIcetex = getId('CENTROS', "CENTROS.centro = 'S1376'");
				if ($Ciclo == '96') $query .= " AND EMPLEADOS.IdProyecto = $IdIcetex";
				else  $query .= " AND EMPLEADOS.IdProyecto <> $IdIcetex";

				$regs = $this->model->listarRegistros($query);

				if ($regs) {
					for ($i = 0; $i < count($regs); $i++) {
						$regEmpleado =  $regs[$i];
						$IdEmpleado = $regEmpleado['id'];
						$FechaIngreso = $regEmpleado['fechaingreso'];
						$SueldoBasico = $regEmpleado['sueldobasico'];

						// SE REVISAN SI HAY AUMENTOS SALARIALES
						$query = <<<EOD
							SELECT AUMENTOSSALARIALES.SueldoBasico
							FROM AUMENTOSSALARIALES 
							WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND
								AUMENTOSSALARIALES.Procesado = 0;
						EOD;

						$regAumento = $this->model->leerRegistro($query);
						if ($regAumento) $SueldoBasico = $regAumento['SueldoBasico'];

						// SE BORRAN LIQUIDACIONES ANTERIORES
						$query = <<<EOD
							DELETE FROM $ArchivoNomina 
								WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND
									$ArchivoNomina.IdPeriodo = $IdPeriodo AND 
									$ArchivoNomina.Ciclo = $Ciclo AND
									Liquida <> 'T';
						EOD;

						$ok = $this->model->query($query);

						$NombreRegimenCesantias = getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['detalle'];
						$NombreTipoContrato = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];

						$IdCentro = $regEmpleado['idcentro'];
						$TipoEmpleado = getRegistro('CENTROS', $IdCentro)['tipoempleado'];

						// PAGO PRIMA LEGAL
						if ($NombreRegimenCesantias <> 'SALARIO INTEGRAL' AND 
							$NombreTipoContrato <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
							$NombreTipoContrato <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
							$NombreTipoContrato <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
							$NombreTipoContrato <> 'PASANTÍA')
						{
							$FechaInicialPrimaLegal = max($FechaIngreso, ComienzoSemestre($FechaFinalPeriodo));
							$FechaInicialPrimaLegal2 = date('Y-m-01', strtotime($FechaInicialPrimaLegal));

							// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA
							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Horas, -ACUMULADOS.Horas)) AS Horas 
								FROM ACUMULADOS 
									INNER JOIN AUXILIARES ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
									INNER JOIN PARAMETROS AS PARAMETROS1 ON AUXILIARES.Imputacion = PARAMETROS1.Id  
									INNER JOIN PARAMETROS AS PARAMETROS2 ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id  
								WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
									(PARAMETROS2.Detalle = 'ES SANCIÓN' OR 
									PARAMETROS2.Detalle = 'ES LICENCIA NO REMUNERADA') AND 
									ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal2' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaFinalPeriodo';
							EOD;

							$regAcumulados = $this->model->leerRegistro($query);

							if ($regAcumulados AND ! is_null($regAcumulados['Horas']))
								$DiasSancionYLicencias = round(abs($regAcumulados['Horas']) / 8, 0);
							else
								$DiasSancionYLicencias = 0;

							if ($regEmpleado['diasano'] == 360)
								$DiasPrimaLegalTotal = Dias360($FechaFinalPeriodo, $FechaInicialPrimaLegal) - 1;
							else
								$DiasPrimaLegalTotal = Dias365($FechaFinalPeriodo, $FechaInicialPrimaLegal) - 1;

							$DiasPrimaLegal = $DiasPrimaLegalTotal - $DiasSancionYLicencias;

							// VALOR SALARIO PROMEDIO
							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS BasePrima 
									FROM ACUMULADOS 
										INNER JOIN AUXILIARES ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS ON AUXILIARES.Imputacion = PARAMETROS.Id 
									WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
										ACUMULADOS.FechaInicialPeriodo >= '$FechaInicialPrimaLegal2' AND
										AUXILIARES.Id IN (
											227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232, 233,
											234, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
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

							// SE CALCULA EL PROMEDIO SALARIAL
							$query = <<<EOD
								SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor)) AS BasePrimas  
									FROM $ArchivoNomina 
										INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES ON AUXILIARES.idMayor = MAYORES.Id 
										INNER JOIN PARAMETROS ON AUXILIARES.Imputacion = PARAMETROS.Id 
									WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado AND 
										AUXILIARES.Id IN (
											227, 228, 229, 230, 334, 335, 384, 385, 386, 387, 231, 232, 233,
											234, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
										) AND $ArchivoNomina.Ciclo = 1;
							EOD;

							$regAcumulados = $this->model->leerRegistro($query);

							if ($regAcumulados AND ! is_null($regAcumulados['BasePrimas']))
								$PromedioSalarioVariable += round($regAcumulados['BasePrimas'] / $DiasPrimaLegal * 30, 0);

							$SalarioBasePrimaLegal = $SueldoBasico + $PromedioSalarioVariable;

							$Horas = $DiasPrimaLegalTotal * 8;
							$ValorPrimaLegal = round($SalarioBasePrimaLegal * $DiasPrimaLegalTotal / 360, 0);

							$datos = array($IdPeriodo, $Ciclo, $IdEmpleado, $IdPrimaLegal, $SalarioBasePrimaLegal, $Horas, $ValorPrimaLegal, 0, 'N', $TipoRetencionPrimaLegal, $IdCentro, $TipoEmpleado, 0);
							$ok = $this->model->guardarNovedad($ArchivoNomina, $datos);
						}
					}
				}

				$this->retencionFuente($ArchivoNomina, $IdPeriodo, $Ciclo, $P_Empleado);

				header('Location: ' . SERVERURL . '/liquidacionPrima/lista/'.$Ciclo.'/1');
				exit();
			}

			return $mensajeError;
		}

		private function retencionFuente($ArchivoNomina, $IdPeriodo, $Ciclo, $P_Empleado) {
			$ValorUVT = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorUVT'")['valor'];

			$query = <<<EOD
				SELECT AUXILIARES.*, 
					MAYORES.TipoRetencion 
				FROM AUXILIARES
				INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
				INNER JOIN PARAMETROS ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
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
					EMPLEADOS.ExencionAfcFvpAnual, 
					EMPLEADOS.ExencionAnual25, 
					EMPLEADOS.ExencionAnual, 
					SUM(IIF(PARAMETROS2.Detalle = 'SALARIOS', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS IngresoBruto, 
					SUM(IIF(PARAMETROS2.Detalle = 'PRIMA LEGAL', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS PrimaLegal, 
					SUM(IIF(PARAMETROS2.Detalle = 'CESANTIAS', IIF(PARAMETROS3.Detalle = 'PAGO', $ArchivoNomina.Valor, -$ArchivoNomina.Valor), 0)) AS Cesantias, 
					SUM(IIF(PARAMETROS2.Detalle = 'SALUD / PENSION', IIF(PARAMETROS3.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS SaludPension, 
					SUM(IIF(PARAMETROS2.Detalle = 'AFC / FVP', IIF(PARAMETROS3.Detalle = 'PAGO', -$ArchivoNomina.Valor, $ArchivoNomina.Valor), 0)) AS AfcFvp
				FROM $ArchivoNomina 
				INNER JOIN EMPLEADOS ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
				LEFT JOIN CENTROS ON EMPLEADOS.IdCentro = CENTROS.Id 
				INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
				INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
				INNER JOIN PARAMETROS AS PARAMETROS1 ON EMPLEADOS.MetodoRetencion = PARAMETROS1.Id 
				INNER JOIN PARAMETROS AS PARAMETROS2 ON MAYORES.TipoRetencion = PARAMETROS2.Id 
				INNER JOIN PARAMETROS AS PARAMETROS3 ON AUXILIARES.Imputacion = PARAMETROS3.Id 
				WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
					$ArchivoNomina.Ciclo = $Ciclo 
			EOD;
	
			if (! empty($P_Empleado))
				$query .= <<<EOD
					AND EMPLEADOS.Documento = '$P_Empleado' 
				EOD;

			$query .= <<<EOD
				GROUP BY $ArchivoNomina.IdEmpleado, EMPLEADOS.IdCentro, CENTROS.TipoEmpleado, PARAMETROS1.Detalle, EMPLEADOS.PorcentajeRetencion, EMPLEADOS.CuotaVivienda, EMPLEADOS.SaludYEducacion, EMPLEADOS.DeduccionDependientes, EMPLEADOS.ExencionAfcFvpAnual, EMPLEADOS.ExencionAnual25, EMPLEADOS.ExencionAnual;
			EOD;

			$acumulados = $this->model->listarRegistros($query);

			if ($acumulados) {
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
					else $IngresoBruto = $PrimaLegal;

					$IngresoNeto1 = $IngresoBruto - $SaludPension;

					if ($AfcFvp > $IngresoBruto * .3) $AfcFvp = round(min($IngresoBruto * .3, $ValorUVT * 316.66), 0);

					if ($CuotaVivienda > $ValorUVT * 100) $CuotaVivienda = $ValorUVT * 100;

					if ($SaludYEducacion > $ValorUVT * 16) $SaludYEducacion = $ValorUVT * 16;

					if ($DeduccionDependientes)
						$DeduccionDependientes = min($ValorUVT * 32, round($IngresoBruto * 0.1, 0));
					else $DeduccionDependientes = 0;

					$ValorDeducciones = $AfcFvp + $DeduccionDependientes + $CuotaVivienda + $SaludYEducacion;

					$ValorDeducible25 = round(($IngresoNeto1 - $ValorDeducciones) * .25, 0);

					if ($regAcumulado['ExencionAnual25'] + $ValorDeducible25 > $ValorUVT * 790)
						$ValorDeducible25 = max($ValorUVT * 790 - $regAcumulado['ExencionAnual25'], 0);

					if (($ValorDeducciones + $ValorDeducible25) > ($IngresoNeto1 * .4)) {
						$ValorDeducciones = round($IngresoNeto1 * .4, 0);
						$ValorDeducible25 = 0;
					}

					if ($regAcumulado['ExencionAnual'] + $ValorDeducciones + $ValorDeducible25 + $AfcFvp > $ValorUVT * 1340) {
						$ValorDeducciones = max($ValorUVT * 1340 - $regAcumulado['ExencionAnual'], 0);
						$ValorDeducible25 = 0;
					}

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
						$ok = $this->model->guardarNovedadRetencion($ArchivoNomina, $datos);

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET ExencionAfcFvpMes = EMPLEADOS.ExencionAfcFvpMes + $AfcFvp, 
									ExencionMes25 = EMPLEADOS.ExencionMes25 + $ValorDeducible25, 
									ExencionMes = EMPLEADOS.ExencionMes + $ValorDeducciones 
								WHERE EMPLEADOS.Id = $IdEmpleado;
						EOD;

						$ok = $this->model->query($query);
					}
				}
			}
		}

		public function acumular() {
			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$Periodicidad 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$Referencia 		= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodo 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];

			$Ciclo = $_REQUEST['CicloPrima']; // Ciclo para PAGO DE PRIMA

			$regPeriodo 		= getRegistro('PERIODOS', $IdPeriodo);
			$Periodo 			= $regPeriodo['periodo'];

			$regPeriodicidad 	= getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad 		= substr($regPeriodicidad['detalle'], 0, 1);

			$mensajeError = '';
			if (!isset($Ciclo) OR ($Ciclo<>'96' AND $Ciclo<>'97')) $mensajeError = label('Período o Ciclo incorrectos') . '<br>';

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Acumular') {
				$query = <<<EOD
						PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
						PERIODOSACUMULADOS.Ciclo = $Ciclo; 
				EOD;

				$regPA = getRegistro('PERIODOSACUMULADOS', 0, $query);

				if ($regPA AND $regPA['acumulado'] == 1)
					$mensajeError .= label('Período - Ciclo ya está liquidado y acumulado') . '<br>';

				if (empty($mensajeError)) {
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
								INNER JOIN PERIODOS ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
								WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND 
									$ArchivoNomina.Ciclo = $Ciclo 
								ORDER BY $ArchivoNomina.IdEmpleado, $ArchivoNomina.IdConcepto;
					EOD;

					$ok = $this->model->actualizarRegistros($query);

					// SE MARCA EL PERIODO COMO ACUMULADO
					$query = <<<EOD
						INSERT INTO PERIODOSACUMULADOS 
						(IdPeriodo, Ciclo, Acumulado, SoloNovedades) 
						VALUES (
							$IdPeriodo, 
							$Ciclo,
							1,
							0);
					EOD;

					$ok = $this->model->actualizarRegistros($query);

					header('Location: ' . SERVERURL . '/dashboard/dashboard');
					exit;
				}
			} elseif (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Reversar') {
				$query = <<<EOD
						PERIODOSACUMULADOS.IdPeriodo = $IdPeriodo AND 
						PERIODOSACUMULADOS.Ciclo = $Ciclo; 
				EOD;

				$regPA = getRegistro('PERIODOSACUMULADOS', 0, $query);

				if (!$regPA OR $regPA['acumulado'] == 0)
					$mensajeError .= label('Período - Ciclo no se encuentra liquidado y acumulado') . '<br>';

				if (empty($mensajeError)) {
					$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

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
			}
			return $mensajeError;
		}

		public function lista($Ciclo, $pagina) {
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/liquidacionPrima/lista';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/liquidacionPrima/liquidar';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['LIQ_PRIMA']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['LIQ_PRIMA']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) ) {
				$_SESSION['LIQ_PRIMA']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['LIQ_PRIMA']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['LIQ_PRIMA']['Filtro'])) {
				$_SESSION['LIQ_PRIMA']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['LIQ_PRIMA']['Filtro'];

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			if (isset($_REQUEST['Orden'])) {
				$_SESSION['LIQ_PRIMA']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['LIQ_PRIMA']['Pagina'] = 1;
				$pagina = 1;
			}
			else if (! isset($_SESSION['LIQ_PRIMA']['Orden'])) 
				$_SESSION['LIQ_PRIMA']['Orden'] = "EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,AUXILIARES.Imputacion,MAYORES.Mayor,AUXILIARES.Auxiliar";

			$query = <<<EOD
				WHERE $ArchivoNomina.IdPeriodo = $IdPeriodo AND
					$ArchivoNomina.Ciclo = $Ciclo 
			EOD;
			
			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') {
				$data['registros'] = $this->model->contarRegistros($ArchivoNomina, $query);
				$query .= 'ORDER BY ' . $_SESSION['LIQ_PRIMA']['Orden']; 
				$data['rows'] = $this->model->exportarPrima($ArchivoNomina, $query);

				$Archivo = './descargas/' . $_SESSION['Login']['Usuario'] . '_PagoPrima_' . date('YmdGis') . '.csv';

				$output = fopen($Archivo, 'w');

				fputcsv($output, array('FECHA INI. PERIODO', 'FECHA FIN. PERIODO', 'EMPLEADO', 'NOMBRE EMPLEADO', 'CONCEPTO', 'DESCRIPCION', 'BASE', 'CANTIDAD', 'TIEMPO', 'PAGOS', 'DEDUCCIONES', 'NETO', 'FECHA INI.', 'FECHA FIN.', 'TERCEROS'), ';');

				for ($i = 0; $i < count($data['rows']); $i++) { 
					$reg = $data['rows'][$i];

					foreach ($reg as $key => $value) {
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
			else {
				if	( ! empty($lcFiltro) ) {
					$aFiltro = explode(' ', $lcFiltro);
	
					for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ ) {
						if ($lnCount == 0) $query .= ' AND ( ';
						else $query .= 'OR ';
	
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

				$data['CicloPrima'] = $Ciclo;
				$data['registros'] = $this->model->contarRegistros($ArchivoNomina, $query);
				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
	
				$query1 = $query;
				$query .= 'ORDER BY ' . $_SESSION['LIQ_PRIMA']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listarLiquidacionPrima($ArchivoNomina, $query1, $query);
		
				$this->views->getView($this, 'liquidacionPrima', $data);
			}
		}
	}
?>
