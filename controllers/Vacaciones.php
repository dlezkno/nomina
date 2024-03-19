<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Vacaciones extends Controllers
	{
		public function liquidarEnDinero($data = false)
		{
			ini_set('max_execution_time', 6000);

			// SE LEEN LOS PARÁMETROS
			if (! $data) 
			{
				$data = array(
					'reg' => array(
						'Empleado' => isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
						'DiasVacacionesDinero' => isset($_REQUEST['DiasVacacionesDinero']) ? $_REQUEST['DiasVacacionesDinero'] : 0,
						'EsImportacion' => FALSE
					),
					'mensajeError' => ''
				);
			}

			if ($data['reg']['DiasVacacionesDinero'] > 0)
			{
				// TRAE EL CONCEPTO
				$query = <<<EOD
					SELECT AUXILIARES.Id, 
							MAYORES.TipoRetencion 
						FROM AUXILIARES 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
						WHERE PARAMETROS.Detalle = 'ES VACACIONES EN DINERO';
				EOD;

				$reg = $this->model->leerRegistro($query);

				if (!$reg) $data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en dinero') . '<br>';

				$P_Empleado 				= $data['reg']['Empleado'];
				$P_DiasVacacionesDinero 	= $data['reg']['DiasVacacionesDinero'];

				// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
				$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];

				$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];

				if ($regPeriodo) 
					$IdPeriodo = $regPeriodo['id'];
				else
					$data['mensajeError'] .= label('Perído definido no existe') . '<br>';

				if (!empty($data['mensajeError']))
				{
					if (! $data['reg']['EsImportacion']) 
						$this->views->getView($this, 'VacacionesEnDinero', $data);

					return;
				}

				// VALIDAR LOS PARAMETROS (Dias a liquidar y fecha de inicio)
				// SE LEE EL EMPLEADO
				$query = <<<EOD
					SELECT EMPLEADOS.*, 
							CENTROS.Nombre AS NombreCentro, 
							CARGOS.Nombre AS NombreCargo
						FROM EMPLEADOS 
							INNER JOIN CENTROS 
								ON EMPLEADOS.IdCentro = CENTROS.Id 
							INNER JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE EMPLEADOS.Documento = '$P_Empleado' AND 
							PARAMETROS.Detalle = 'ACTIVO';
				EOD;

				$regEmpleado = $this->model->leerRegistro($query);

				if ($regEmpleado)
				{
					$IdEmpleado 	= $regEmpleado['id'];
					$SueldoBasico 	= $regEmpleado['sueldobasico'];

					$aumetoSalarial = getRegistro('AUMENTOSSALARIALES', 0, "idEmpleado = $IdEmpleado and Procesado = 0 and fechaAumento >= '$FechaInicialPeriodo' and fechaAumento <= '$FechaFinalPeriodo'");
					if ($aumetoSalarial) $SueldoBasico = $aumetoSalarial['sueldobasico'];

					$PromedioSalarioVariable = $this->getVariableAverage($regEmpleado, $FechaInicialPeriodo);

					$Salario = $SueldoBasico + $PromedioSalarioVariable;

					$FechaInicio 	= $FechaInicialPeriodo;
					$diasPendientes = $P_DiasVacacionesDinero;

					$causation = $this->getCausation($regEmpleado['documento'], $regEmpleado['fechaingreso']);
					$FechaCausacion = $causation['dateCausation'];
					$daysCausation = $causation['days'];

					while ($diasPendientes > 0) 
					{
						// SE CALCULAN LOS DIAS DE VACACIONES REALES EN TIEMPO
						$DiasVacaciones = $diasPendientes;
						if ($DiasVacaciones>(15-$daysCausation)) $DiasVacaciones = (15-$daysCausation);

						$ValorLiquidado = ROUND($Salario / 30 * $DiasVacaciones, 0);

						$dataVacation = array($IdEmpleado, $SueldoBasico, $PromedioSalarioVariable, $Salario, $FechaCausacion, null, $FechaInicio, null, $DiasVacaciones, 0, $DiasVacaciones, 0, 0, $DiasVacaciones, $ValorLiquidado, 0, $ValorLiquidado, 0,  0, '');
						$this->model->saveVacation($dataVacation);

						$diasPendientes -= $DiasVacaciones;
						$daysCausation += $DiasVacaciones;

						if ($daysCausation>=15) {
							$FechaCausacion = date('Y-m-d', strtotime($FechaCausacion . ' + 1 year'));
							$daysCausation = 0;
						}
					}
				}
				
				if (! $data['reg']['EsImportacion']) 
				{
					$_SESSION['VACACIONES']['Filtro'] = '';
					$this->listaT(1);
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/VacacionesEnDinero/editar';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] = SERVERURL . '/Vacaciones/importar';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL . '/Vacaciones/listaD/1';

				if ($data) 
					$this->views->getView($this, 'VacacionesEnDinero', $data);
			}
		}

		private function getSaturdaysSundaysHolidaysAnd31($date, $numDays) {
			$P_SabadoFestivo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'SabadoFestivo'")['valor'];
			$startDate = $date;

			$vacationDays = 0;
			$numSaturdaysSundaysHolidays = 0;
			$day31 = 0;

			while ($date <= FinMes($startDate) AND $numDays > 0) 
			{
				if  (
					(date('w', strtotime($date)) == 6 AND $P_SabadoFestivo) OR
					date('w', strtotime($date)) == 0 OR
					$this->itsHoliday($date)
				) {
					$numSaturdaysSundaysHolidays++;
				} elseif (date('d', strtotime($date)) == 31) {
					$numDays--;
					$day31++;
				} else {
					$numDays--;
					$vacationDays++;
				}

				$date = date('Y-m-d', strtotime($date . ' + 1 day'));
			}

			return array(
				'numSaturdaysSundaysHolidays' => $numSaturdaysSundaysHolidays,
				'day31' => $day31,
				'date' => $date,
				'vacationDays' => $vacationDays
			);
		}

		private function getVariableAverage($regEmpleado, $P_FechaInicioVacaciones) {
			if (!$regEmpleado) return;

			$IdEmpleado = $regEmpleado['id'];

			// SE BUSCAN VACACIONES GOZADAS
			$query = <<<EOD
				SELECT SUM(VACACIONES.DiasALiquidar) AS DiasLiquidados 
				FROM VACACIONES 
				WHERE VACACIONES.IdEmpleado = $IdEmpleado AND 
					VACACIONES.Procesado = 1 
			EOD;

			$vacaciones = $this->model->leerRegistro($query);

			$enjoyedVacation = 0;
			if ($vacaciones && $vacaciones['DiasLiquidados'])
				$enjoyedVacation = $vacaciones['DiasLiquidados'];

			$DiasTotalContrato = Dias360($P_FechaInicioVacaciones, $regEmpleado['fechaingreso']);
			$DiasPasivoVacacional = ($DiasTotalContrato * 15 / 360) - $enjoyedVacation;

			$DiasBaseCalculo = 360;
			if ($DiasPasivoVacacional<15) $DiasBaseCalculo = $DiasTotalContrato;

			// VALOR PROMEDIO DE LOS RECARGOS NOCTURNOS
			$query = <<<EOD
				SELECT MAYORES.Mayor 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ES SUELDO BÁSICO' OR 
						PARAMETROS.Detalle = 'ES SUELDO BÁSICO (SALARIO INTEGRAL)';
			EOD;

			$FechaInicialAC = date('Y-m-d', strtotime($P_FechaInicioVacaciones . ' - 12 month'));
			$FechaFinalAC 	= date('Y-m-d', strtotime($P_FechaInicioVacaciones . ' - 1 day'));
			
			$query = <<<EOD
				SELECT  MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						MIN(ACUMULADOS.fechainicialperiodo) AS FechaInicial, 
						MAX(ACUMULADOS.fechafinalperiodo) AS FechaFinal,									
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor 
					FROM ACUMULADOS 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					WHERE ACUMULADOS.IdEmpleado = $IdEmpleado 
						AND AUXILIARES.Id IN (
							231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
						) 
						AND ACUMULADOS.fechainicialperiodo >= '$FechaInicialAC' 
						AND ACUMULADOS.fechafinalperiodo <= '$FechaFinalAC' 
					GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar;
			EOD;

			$regAcumulados = $this->model->listarRegistros($query);

			$PromedioSalarioVariable = 0;
			if ($regAcumulados) {
				foreach ($regAcumulados as $regAcumulado) {
					$PromedioSalarioVariable += round($regAcumulado['Valor'] / $DiasBaseCalculo * 30, 0);
				}
			}

			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];

			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$regPeriodicidad = getRegistro('PARAMETROS', $IdPeriodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$ArchivoNomina = 'nomina_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo;

			// SACA DE LA NOMINA
			$query = <<<EOD
				SELECT  MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, $ArchivoNomina.Valor * -1)) AS Valor 
					FROM $ArchivoNomina 
						INNER JOIN AUXILIARES 
							ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					WHERE $ArchivoNomina.IdEmpleado = $IdEmpleado 
						AND AUXILIARES.Id IN (
							231, 232, 388, 389, 238, 239, 240, 275, 276, 278, 280, 366, 370, 392
						)
					GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar;
			EOD;

			$regsNomina = $this->model->listarRegistros($query);

			if ($regsNomina) {
				foreach ($regsNomina as $regNomina) {
					$PromedioSalarioVariable += round($regNomina['Valor'] / $DiasBaseCalculo * 30, 0);
				}
			}

			return $PromedioSalarioVariable;
		}

		private function getCausation($documento, $employeeIncomeDay) {
			$query = <<<EOD
			SELECT TOP 1 vac.fechacausacion AS dateCausation, SUM(vac.diasprocesados) AS days
			FROM vacaciones vac
			JOIN empleados emp on emp.id = vac.idempleado
			WHERE  emp.documento = '$documento'
			GROUP BY vac.fechacausacion
			ORDER BY vac.fechacausacion DESC;
			EOD;

			$lastCausation = $this->model->leerRegistro($query);

			$dateCausation = $employeeIncomeDay;
			$days = 0;

			if ($lastCausation AND $lastCausation['days']>=15) {
				$dateCausation = date('Y-m-d', strtotime($dateCausation . ' + 1 year'));
			} elseif ($lastCausation) return $lastCausation;

			return array(
				'dateCausation' => $dateCausation, 'days' => $days
			);
		}

		public function liquidarEnTiempo($data = false)
		{
			ini_set('max_execution_time', 6000);
			
			// SE LEEN LOS PARÁMETROS
			if (! $data) 
			{
				$data = array(
					'reg' => array(
						'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : 0,
						'IdCentro' => isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
						'IdCargo' => isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0,
						'Empleado' => isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
						'FechaInicioVacaciones' => isset($_REQUEST['FechaInicioVacaciones']) ? $_REQUEST['FechaInicioVacaciones'] : '', 
						'DiasVacacionesTiempo' => isset($_REQUEST['DiasVacacionesTiempo']) ? $_REQUEST['DiasVacacionesTiempo'] : 0, 
						'EsImportacion' => FALSE
					),
					'mensajeError' => ''
				);
			}

			if (! empty($data['reg']['FechaInicioVacaciones']))
			{
				$query = <<<EOD
					SELECT AUXILIARES.Id, 
							MAYORES.TipoRetencion 
						FROM AUXILIARES 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
						WHERE PARAMETROS.Detalle = 'ES VACACIONES EN TIEMPO';
				EOD;

				$reg = $this->model->leerRegistro($query);

				if (!$reg) $data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en tiempo') . '<br>';

				$P_TipoEmpleados 			= $data['reg']['TipoEmpleados'];
				$P_IdCentro 				= $data['reg']['IdCentro'];
				$P_IdCargo 					= $data['reg']['IdCargo'];
				$P_Empleado 				= $data['reg']['Empleado'];
				$P_FechaInicioVacaciones 	= $data['reg']['FechaInicioVacaciones'];
				$P_DiasVacacionesTiempo 	= $data['reg']['DiasVacacionesTiempo'];

				// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
				$IdPeriodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
				$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];

				$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
				$FechaInicialPeriodo = $regPeriodo['fechainicial'];
				$FechaFinalPeriodo = $regPeriodo['fechafinal'];

				if ($regPeriodo) 
					$IdPeriodo = $regPeriodo['id'];
				else
					$data['mensajeError'] .= label('Perído definido no existe') . '<br>';

				// VALIDAR LOS PARAMETROS (Dias a liquidar y fecha de inicio)
				if (!empty($data['mensajeError']))
				{
					if (! $data['reg']['EsImportacion']) 
						$this->views->getView($this, 'VacacionesEnTiempo', $data);

					return;
				}

				// SE LEEN LOS EMPLEADOS
				$query = <<<EOD
					SELECT EMPLEADOS.*, 
							CENTROS.Nombre AS NombreCentro, 
							CARGOS.Nombre AS NombreCargo
						FROM EMPLEADOS 
							INNER JOIN CENTROS 
								ON EMPLEADOS.IdCentro = CENTROS.Id 
							INNER JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1
								ON EMPLEADOS.Estado = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2
								ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
						WHERE EMPLEADOS.PeriodicidadPago = $IdPeriodicidad AND 
							PARAMETROS1.Detalle = 'ACTIVO' AND 
				EOD;

				if (! empty($TipoEmpleados))
					$query .= <<<EOD
						EMPLEADOS.TipoEmpleado = $P_TipoEmpleados AND 
					EOD;

				if (! empty($P_IdCentro))
					$query .= <<<EOD
						EMPLEADOS.IdCentro = $P_IdCentro AND 
					EOD;

				if (! empty($P_IdCargo))
					$query .= <<<EOD
						EMPLEADOS.IdCargo = $P_IdCargo AND 
					EOD;

				if (! empty($P_Empleado))
					$query .= <<<EOD
						EMPLEADOS.Documento = '$P_Empleado' AND 
					EOD;

				$query .= <<<EOD
						EMPLEADOS.FechaIngreso <= '$FechaFinalPeriodo' AND 
						EMPLEADOS.FechaRetiro IS NULL AND 
						PARAMETROS2.Detalle <> 'APRENDIZ SENA'
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
				EOD;

				$empleados = $this->model->listarRegistros($query);

				for ($i = 0; $i < count($empleados) ; $i++) 
				{ 
					$regEmpleado = $empleados[$i];

					$IdEmpleado 	= $regEmpleado['id'];
					$SueldoBasico 	= $regEmpleado['sueldobasico'];

					$aumetoSalarial = getRegistro('AUMENTOSSALARIALES', 0, "idEmpleado = $IdEmpleado and Procesado = 0 and fechaAumento >= '$FechaInicialPeriodo' and fechaAumento <= '$FechaFinalPeriodo'");
					if ($aumetoSalarial) $SueldoBasico = $aumetoSalarial['sueldobasico'];

					$PromedioSalarioVariable = $this->getVariableAverage($regEmpleado, $FechaInicialPeriodo);

					$Salario = $SueldoBasico + $PromedioSalarioVariable;

					$FechaInicio 	= $P_FechaInicioVacaciones;
					$diasPendientes = $P_DiasVacacionesTiempo;

					$causation = $this->getCausation($regEmpleado['documento'], $regEmpleado['fechaingreso']);
					$FechaCausacion = $causation['dateCausation'];
					$daysCausation = $causation['days'];

					$fechaFinal = $FechaFinalPeriodo;

					while ($diasPendientes > 0) 
					{
						// SE CALCULAN LOS DIAS DE VACACIONES REALES EN TIEMPO
						$vacationCalculation = $this->getSaturdaysSundaysHolidaysAnd31($FechaInicio, min($diasPendientes, (15-$daysCausation)));
						$numSaturdaysSundaysHolidays = $vacationCalculation['numSaturdaysSundaysHolidays'];
						$day31 = $vacationCalculation['day31'];
						$FechaIngreso = $vacationCalculation['date'];
						$DiasVacaciones = $vacationCalculation['vacationDays'];

						$ValorLiquidado = ROUND($Salario / 30 * $DiasVacaciones, 0);
						$ValorFestivos = ROUND($Salario / 30 * $numSaturdaysSundaysHolidays, 0);
						$ValorDias31 = ROUND($Salario / 30 * $day31, 0);

						$dataVacation = array($IdEmpleado, $SueldoBasico, $PromedioSalarioVariable, $Salario, $FechaCausacion, $fechaFinal, $FechaInicio, $FechaIngreso, $DiasVacaciones, $DiasVacaciones, 0, $numSaturdaysSundaysHolidays, $day31, $DiasVacaciones+$day31, $ValorLiquidado, $ValorLiquidado, 0, $ValorFestivos,  $ValorDias31, '');
						$this->model->saveVacation($dataVacation);

						$diasPendientes -= ($DiasVacaciones + $day31);
						$daysCausation += ($DiasVacaciones + $day31);

						if ($daysCausation>=15) {
							$FechaCausacion = date('Y-m-d', strtotime($FechaCausacion . ' + 1 year'));
							$daysCausation = 0;
						}

						$FechaInicio = $FechaIngreso;
						$fechaFinal = date('Y-m-t', strtotime($FechaIngreso));
					}
				}

				if (! $data['reg']['EsImportacion']) 
				{
					$_SESSION['VACACIONES']['Filtro'] = '';
					$this->listaT(1);
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/VacacionesEnTiempo/editar';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] = SERVERURL . '/Vacaciones/importar';
				$_SESSION['ImportarArchivo'] = '';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL . '/Vacaciones/listaT/1';

				if ($data) 
					$this->views->getView($this, 'VacacionesEnTiempo', $data);
			}
		}

		private function getDayIncome($date) {
			$weekday = date('w', strtotime($date));

			while ($weekday==6 OR $weekday==0 OR $this->itsHoliday($date)) {
				$date = date('Y-m-d', strtotime($date . ' + 1 day'));
				$weekday = date('w', strtotime($date));
			}

			return $date;
		}

		private function itsHoliday($date) {
			$query = <<<EOD
				SELECT DIASFESTIVOS.Fecha 
					FROM DIASFESTIVOS
					WHERE DIASFESTIVOS.Fecha = '$date';
			EOD;

			$regFestivos = $this->model->leerRegistro($query);

			return ($regFestivos AND $date == $regFestivos['Fecha']);
		}
		
		public function listaT($pagina)
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = SERVERURL . '/Vacaciones/borrar';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			// $_SESSION['ExportarArchivo'] = SERVERURL . '/Vacaciones/guardarEnTiempo';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/Vacaciones/liquidarEnTiempo';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['VACACIONES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['VACACIONES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['VACACIONES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['VACACIONES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['VACACIONES']['Filtro']))
			{
				$_SESSION['VACACIONES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['VACACIONES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['VACACIONES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['VACACIONES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['VACACIONES']['Orden'])) 
					$_SESSION['VACACIONES']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,VACACIONES.FechaInicio';

			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$query = <<<EOD
				WHERE VACACIONES.Procesado = 0 AND 
					VACACIONES.ValorEnTiempo > 0 AND 
					VACACIONES.FechaLiquidacion >= '$FechaInicialPeriodo' AND 
					VACACIONES.FechaLiquidacion <= '$FechaFinalPeriodo' 
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

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ')';
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['VACACIONES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarVacaciones($query);
			$this->views->getView($this, 'liquidacionVacacionesT', $data);
		}	

		public function listaD($pagina)
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = SERVERURL . '/Vacaciones/borrar';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			// $_SESSION['ExportarArchivo'] = SERVERURL . '/Vacaciones/guardarEnDinero';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/Vacaciones/liquidarEnDinero';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['VACACIONES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['VACACIONES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['VACACIONES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['VACACIONES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['VACACIONES']['Filtro']))
			{
				$_SESSION['VACACIONES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['VACACIONES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['VACACIONES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['VACACIONES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['VACACIONES']['Orden'])) 
					$_SESSION['VACACIONES']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,VACACIONES.FechaCausacion';

			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];

			$query = <<<EOD
				WHERE VACACIONES.Procesado = 0 AND 
					VACACIONES.ValorEnDinero > 0 AND 
					VACACIONES.FechaInicio >= '$FechaInicialPeriodo'
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

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ')';
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['VACACIONES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarVacaciones($query);
			$this->views->getView($this, 'liquidacionVacacionesD', $data);
		}	

		public function guardarEnTiempo()
		{
			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia = $reg1['valor'];
			$IdPeriodicidad = $reg2['valor'];

			$Periodicidad = getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$Periodo = $reg3['valor'];
			$Ciclo = $reg4['valor'];

			$query = <<<EOD
				PERIODOS.Referencia = $Referencia AND 
				PERIODOS.Periodicidad = $IdPeriodicidad AND 
				PERIODOS.Periodo = $Periodo;
			EOD;

			$regPeriodo = getRegistro('PERIODOS', 0, $query);

			if ($regPeriodo) 
				$IdPeriodo = $regPeriodo['id'];
			else
				$data['mensajeError'] .= label('Perído definido no existe') . '<br>';

			$FechaInicialPeriodo = ComienzoMes($regPeriodo['fechainicial']);
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];
			
			$query = <<<EOD
				SELECT AUXILIARES.Id, 
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
				$TipoRetencionVT = $reg['TipoRetencion'];
			}
			else
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en tiempo') . '<br>';

			$query = <<<EOD
				SELECT AUXILIARES.Id, 
						MAYORES.TipoRetencion 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ES VACACIONES EN DOMINICAL Y FESTIVO';
			EOD;

			$reg = $this->model->leerRegistro($query);

			if ($reg)
			{
				$IdConceptoVDF = $reg['Id'];
				$TipoRetencionVDF = $reg['TipoRetencion'];
			}
			else
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en dominical y festivo') . '<br>';

			$query = <<<EOD
				SELECT AUXILIARES.Id, 
						MAYORES.TipoRetencion 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ES VACACIONES EN DÍA 31';
			EOD;

			$reg = $this->model->leerRegistro($query);

			if ($reg)
			{
				$IdConceptoVD31 = $reg['Id'];
				$TipoRetencionVD31 = $reg['TipoRetencion'];
			}
			else
				$data['mensajeError'] .= label('No hay definido un concepto de Vacaciones en día 31') . '<br>';

			$query = <<<EOD
				SELECT AUXILIARES.Id, 
						MAYORES.TipoRetencion 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
					WHERE PARAMETROS.Detalle = 'ES PERÍODO DE VACACIONES';
			EOD;

			$reg = $this->model->leerRegistro($query);

			if ($reg)
			{
				$IdPeriodoVacaciones = $reg['Id'];
			}
			else
				$data['mensajeError'] .= label('No hay definido un concepto de Período de vacaciones') . '<br>';

			if (empty($data['mensajeError'])) 
			{
				// SE GUARDAN LAS NOVEDADES
				$query = <<<EOD
					SELECT VACACIONES.*, 
							EMPLEADOS.IdCentro,
							EMPLEADOS.TipoEmpleado 
						FROM VACACIONES 
							INNER JOIN EMPLEADOS 
								ON VACACIONES.IdEmpleado = EMPLEADOS.Id 
						WHERE VACACIONES.FechaLiquidacion = '$FechaFinalPeriodo' AND 
							VACACIONES.Procesado = 0 
						ORDER BY VACACIONES.IdEmpleado;
				EOD;

				$data = $this->model->listarRegistros($query);

				for ($i = 0; $i < count($data); $i++) 
				{ 
					$reg = $data[$i];

					$IdEmpleado = $reg['idempleado'];
					$Horas = $reg['diasentiempo'] * 8;
					$ValorNovedad = $reg['valorentiempo'];
					$IdCentro = $reg['IdCentro'];
					$TipoEmpleado = $reg['TipoEmpleado'];

					$query = <<<EOD
						SELECT NOMINA.Id
							FROM NOMINA
							WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
								NOMINA.Ciclo = $Ciclo AND 
								NOMINA.IdEmpleado = $IdEmpleado AND 
								NOMINA.IdConcepto = $IdConceptoVT; 
					EOD;

					$regNom = $this->model->leerRegistro($query);

					if ($regNom) 
					{
						$Id = $regNom['Id'];

						$query = <<<EOD
							UPDATE NOMINA 
								SET NOMINA.Horas = NOMINA.Horas + $Horas, 
									NOMINA.Valor = NOMINA.Valor + $ValorNovedad 
								WHERE NOMINA.Id = $Id;
						EOD;
					}
					else
					{
						$query = <<<EOD
							INSERT INTO NOMINA 
								(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
								VALUES
								($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVT, $Horas, $ValorNovedad, 0, 'V', $TipoRetencionVT, $IdCentro, $TipoEmpleado);
						EOD;
					}

					$ok = $this->model->actualizarRegistros($query);

					$Horas = $reg['diasfestivos'] * 8;
					$ValorNovedad = $reg['valorfestivos'];

					if ($ValorNovedad > 0)
					{
						$query = <<<EOD
							SELECT NOMINA.Id
								FROM NOMINA
								WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
									NOMINA.Ciclo = $Ciclo AND 
									NOMINA.IdEmpleado = $IdEmpleado AND 
									NOMINA.IdConcepto = $IdConceptoVDF; 
						EOD;

						$regNom = $this->model->leerRegistro($query);

						if ($regNom) 
						{
							$Id = $regNom['Id'];

							$query = <<<EOD
								UPDATE NOMINA 
									SET NOMINA.Horas = NOMINA.Horas + $Horas, 
										NOMINA.Valor = NOMINA.Valor + $ValorNovedad 
									WHERE NOMINA.Id = $Id;
							EOD;
						}
						else
						{
							$query = <<<EOD
								INSERT INTO NOMINA 
									(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
									VALUES
									($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVDF, $Horas, $ValorNovedad, 0, 'V', $TipoRetencionVDF, $IdCentro, $TipoEmpleado);
							EOD;
						}

						$ok = $this->model->actualizarRegistros($query);
					}
					else
					{
						$query = <<<EOD
							SELECT NOMINA.Id
								FROM NOMINA
								WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
									NOMINA.Ciclo = $Ciclo AND 
									NOMINA.IdEmpleado = $IdEmpleado AND 
									NOMINA.IdConcepto = $IdConceptoVDF; 
						EOD;

						$regNom = $this->model->leerRegistro($query);

						if ($regNom) 
						{
							$Id = $regNom['Id'];

							$query = <<<EOD
								DELETE FROM NOMINA
									WHERE NOMINA.Id = $Id;
							EOD;

							$ok = $this->model->actualizarRegistros($query);
						}
					}

					$Horas = $reg['dias31'] * 8;
					$ValorNovedad = $reg['valordia31'];

					if ($ValorNovedad > 0)
					{
						$query = <<<EOD
							SELECT NOMINA.Id
								FROM NOMINA
								WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
									NOMINA.Ciclo = $Ciclo AND 
									NOMINA.IdEmpleado = $IdEmpleado AND 
									NOMINA.IdConcepto = $IdConceptoVD31 
						EOD;

						$regNom = $this->model->leerRegistro($query);

						if ($regNom) 
						{
							$Id = $regNom['Id'];

							$query = <<<EOD
								UPDATE NOMINA 
									SET NOMINA.Horas = NOMINA.Horas + $Horas, 
										NOMINA.Valor = NOMINA.Valor + $ValorNovedad 
									WHERE NOMINA.Id = $Id;
							EOD;
						}
						else
						{
							$query = <<<EOD
								INSERT INTO NOMINA 
									(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
									VALUES
									($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVD31, $Horas, $ValorNovedad, 0, 'V', $TipoRetencionVD31, $IdCentro, $TipoEmpleado);
							EOD;
						}

						$ok = $this->model->actualizarRegistros($query);
					}
					else
					{
						$query = <<<EOD
							SELECT NOMINA.Id
								FROM NOMINA
								WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
									NOMINA.Ciclo = $Ciclo AND 
									NOMINA.IdEmpleado = $IdEmpleado AND 
									NOMINA.IdConcepto = $IdConceptoVD31; 
						EOD;

						$regNom = $this->model->leerRegistro($query);

						if ($regNom) 
						{
							$Id = $regNom['Id'];

							$query = <<<EOD
								DELETE FROM NOMINA
									WHERE NOMINA.Id = $Id;
							EOD;

							$ok = $this->model->actualizarRegistros($query);
						}
					}

					$FechaInicial = $reg['fechainicio'];
					$FechaFinal = date('Y-m-d', strtotime($reg['fechaingreso'] . ' - 1 day'));

					$query = <<<EOD
						SELECT NOMINA.Id
							FROM NOMINA
							WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
								NOMINA.Ciclo = $Ciclo AND 
								NOMINA.IdEmpleado = $IdEmpleado AND 
								NOMINA.IdConcepto = $IdPeriodoVacaciones; 
					EOD;

					$regNom = $this->model->leerRegistro($query);

					if ($regNom) 
					{
						$Id = $regNom['Id'];

						$query = <<<EOD
							UPDATE NOMINA 
								SET NOMINA.FechaFinal = '$FechaFinal' 
								WHERE NOMINA.Id = $Id;
						EOD;
					}
					else
					{
						$query = <<<EOD
							INSERT INTO NOMINA 
								(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, FechaInicial, FechaFinal, Afecta, IdCentro, TipoEmpleado) 
								VALUES
								($IdPeriodo, $Ciclo, $IdEmpleado, $IdPeriodoVacaciones, 0, 0, 0, 'V', 
								'$FechaInicial', '$FechaFinal', $TipoRetencionVT, $IdCentro, $TipoEmpleado);
						EOD;
					}

					$ok = $this->model->actualizarRegistros($query);

					$Id = $reg['id'];

					// SE CAMBIA EL ESTADO A 2 PARA RETIRARLO DE LA LIQUIDACION DE VACACIONES
					$query = <<<EOD
						UPDATE VACACIONES
							SET Procesado = 2, 
								IdPeriodo = $IdPeriodo, 
								Ciclo = $Ciclo
							WHERE VACACIONES.Id = $Id;
					EOD;

					$ok = $this->model->actualizarRegistros($query);
				}
			}

			$_SESSION['VACACIONES']['Filtro'] = '';
			$this->listaT(1);
		}

		public function guardarEnDinero()
		{
			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Referencia = $reg1['valor'];
			$IdPeriodicidad = $reg2['valor'];

			$Periodicidad = getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$Periodo = $reg3['valor'];
			$Ciclo = $reg4['valor'];

			$query = <<<EOD
				PERIODOS.Referencia = $Referencia AND 
				PERIODOS.Periodicidad = $IdPeriodicidad AND 
				PERIODOS.Periodo = $Periodo;
			EOD;

			$regPeriodo = getRegistro('PERIODOS', 0, $query);

			if ($regPeriodo) 
				$IdPeriodo = $regPeriodo['id'];
			else
				$data['mensajeError'] .= label('Perído definido no existe') . '<br>';

			$FechaInicialPeriodo = ComienzoMes($regPeriodo['fechainicial']);
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];
			
			$query = <<<EOD
				SELECT AUXILIARES.Id, 
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
				$TipoRetencionVD = $reg['TipoRetencion'];
			}

			// SE GUARDAN LAS NOVEDADES
			$query = <<<EOD
				SELECT VACACIONES.*, 
						EMPLEADOS.IdCentro,
						EMPLEADOS.TipoEmpleado 
					FROM VACACIONES 
						INNER JOIN EMPLEADOS 
							ON VACACIONES.IdEmpleado = EMPLEADOS.Id 
					WHERE VACACIONES.FechaLiquidacion = '$FechaFinalPeriodo' AND 
						VACACIONES.Procesado = 0 
					ORDER BY VACACIONES.IdEmpleado;
			EOD;

			$data = $this->model->listarRegistros($query);

			for ($i = 0; $i < count($data); $i++) 
			{ 
				$reg = $data[$i];

				$IdEmpleado = $reg['idempleado'];
				$Horas = $reg['diasendinero'] * 8;
				$ValorNovedad = $reg['valorendinero'];
				$IdCentro = $reg['IdCentro'];
				$TipoEmpleado = $reg['TipoEmpleado'];

				$query = <<<EOD
					SELECT NOMINA.Id
						FROM NOMINA
						WHERE NOMINA.IdPeriodo = $IdPeriodo AND 
							NOMINA.Ciclo = $Ciclo AND 
							NOMINA.IdEmpleado = $IdEmpleado AND 
							NOMINA.IdConcepto = $IdConceptoVD; 
				EOD;

				$regNom = $this->model->leerRegistro($query);

				if ($regNom) 
				{
					$Id = $regNom['Id'];

					$query = <<<EOD
						UPDATE NOMINA 
							SET NOMINA.Horas = NOMINA.Horas + $Horas, 
								NOMINA.Valor = NOMINA.Valor + $ValorNovedad 
							WHERE NOMINA.Id = $Id;
					EOD;
				}
				else
				{
					$query = <<<EOD
						INSERT INTO NOMINA 
							(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
							VALUES
							($IdPeriodo, $Ciclo, $IdEmpleado, $IdConceptoVD, $Horas, $ValorNovedad, 0, 'V', $TipoRetencionVD, $IdCentro, $TipoEmpleado);
					EOD;
				}

				$ok = $this->model->actualizarRegistros($query);

				$Id = $reg['id'];

					// SE CAMBIA EL ESTADO A 2 PARA RETIRARLO DE LA LIQUIDACION DE VACACIONES
				$query = <<<EOD
					UPDATE VACACIONES
						SET Procesado = 2, 
							IdPeriodo = $IdPeriodo, 
							Ciclo = $Ciclo
						WHERE VACACIONES.Id = $Id;
				EOD;

				$ok = $this->model->actualizarRegistros($query);
			}

			$_SESSION['VACACIONES']['Filtro'] = '';
			$this->listaD(1);
		}

		public function borrarEnTiempo($Id)
		{
			$query = <<<EOD
				DELETE FROM VACACIONES 
					WHERE VACACIONES.Id = $Id AND 
						VACACIONES.Procesado = 0;
			EOD;

			$this->model->actualizarRegistros($query);

			$this->listaT(1);
		} 

		public function borrarEnDinero($Id)
		{
			$query = <<<EOD
				DELETE FROM VACACIONES 
					WHERE VACACIONES.Id = $Id AND 
						VACACIONES.Procesado = 0;
			EOD;

			$this->model->actualizarRegistros($query);

			$this->listaD(1);
		} 

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 600);
					
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Empleado 		= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
								$NombreEmpleado = trim($oHoja->getCell('B' . $i)->getCalculatedValue());
								$DiasEnTiempo 	= $oHoja->getCell('C' . $i)->getCalculatedValue();
								$DiasEnDinero 	= $oHoja->getCell('D' . $i)->getCalculatedValue();
								$FechaInicio 	= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('E' . $i)->getCalculatedValue())->format('Y-m-d');
								
								$SabadoFestivo 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'SabadoFestivo'")['valor'];

								if ($DiasEnDinero > 0)
								{
									$data = array(
										'reg' => array(
											'Empleado' => $Empleado,
											'DiasVacacionesDinero' => $DiasEnDinero, 
											'EsImportacion' => TRUE
										),
										'mensajeError' => ''
									);

									$this->liquidarEnDinero($data);
								}

								if ($DiasEnTiempo > 0) 
								{
									$data = array(
										'reg' => array(
											'TipoEmpleados' => 0,
											'IdCentro' => 0,
											'IdCargo' => 0,
											'Empleado' => $Empleado,
											'FechaInicioVacaciones' => $FechaInicio, 
											'DiasVacacionesTiempo' => $DiasEnTiempo, 
											'SabadoFestivo' => ($SabadoFestivo == 1 ? TRUE : FALSE), 
											'EsImportacion' => TRUE
										),
										'mensajeError' => ''
									);

									$this->liquidarEnTiempo($data);
								}
							}

							$_SESSION['VACACIONES']['Filtro'] = '';
							$this->listaT(1);
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
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = SERVERURL . '/Vacaciones/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/Vacaciones/listaT/1';
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function importarLibro()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 600);
					
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Empleado 		= $oHoja->getCell('A' . $i)->getCalculatedValue();
								$NombreEmpleado = $oHoja->getCell('D' . $i)->getCalculatedValue();
								$FormaPago 		= $oHoja->getCell('E' . $i)->getCalculatedValue();
								$FechaPago 		= $oHoja->getCell('F' . $i)->getCalculatedValue();
								$ValorPago 		= $oHoja->getCell('G' . $i)->getCalculatedValue();
								$FechaInicio 	= $oHoja->getCell('H' . $i)->getCalculatedValue();
								$FechaSiguiente = $oHoja->getCell('I' . $i)->getCalculatedValue();
								$Periodo 		= $oHoja->getCell('J' . $i)->getCalculatedValue();
								$Dias 			= $oHoja->getCell('K' . $i)->getCalculatedValue();

								if ($ValorPago == 0)
									continue;

								$regEmpleado = getRegistro('EMPLEADOS', 0, "EMPLEADOS.Documento = '" . $Empleado . "'");
								$IdEmpleado = $regEmpleado['id'];

								if ($IdEmpleado > 0)
									if ($ValorPago > 1)
										$SueldoBasico = round($ValorPago / $Dias * 30, 0);
									else
										$SueldoBasico = $regEmplado['sueldobasico'];
								else
								{
									$data['mensajeError'] .= "Empleado no existe ($Empleado - $NombreEmpleado) <br>";
									continue;
								}

								$FechaCausacion = substr($Periodo, 6, 4) . '-' . substr($Periodo, 3, 2) . '-' . substr($Periodo, 0, 2);
								$FechaPago = substr($FechaPago, 6, 4) . '-' . substr($FechaPago, 3, 2) . '-' . substr($FechaPago, 0, 2);
								$FechaInicio = substr($FechaInicio, 6, 4) . '-' . substr($FechaInicio, 3, 2) . '-' . substr($FechaInicio, 0, 2);
								$FechaSiguiente = substr($FechaSiguiente, 6, 4) . '-' . substr($FechaSiguiente, 3, 2) . '-' . substr($FechaSiguiente, 0, 2);

								$query = <<<EOD
									INSERT INTO VACACIONES
										(IdEmpleado, SueldoBasico, RecargoNocturno, SalarioBase, DiasSancionYLicencia,
										FechaCausacion, FechaLiquidacion, FechaInicio, FechaIngreso,
										DiasALiquidar, DiasEnTiempo, DiasEnDinero,
										ValorEnTiempo, ValorEnDinero, Observaciones, DiasProcesados, Procesado)
										VALUES (
										$IdEmpleado, 
										$SueldoBasico,
										0,
										$SueldoBasico,
										0,
										'$FechaCausacion',
										'$FechaPago',
										'$FechaInicio', 
										'$FechaSiguiente',
										$Dias,
										IIF('$FormaPago' = 'TIEMPO', $Dias, 0),
										IIF('$FormaPago' = 'DINERO', $Dias, 0),
										IIF('$FormaPago' = 'TIEMPO', $ValorPago, 0),
										IIF('$FormaPago' = 'DINERO', $ValorPago, 0),
										'$Periodo', 
										$Dias, 
										1);
								EOD;

								$ok = $this->model->actualizarRegistros($query);
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importarLibro', $data);
							else
								header('Location: ' . SERVERURL . '/Vacaciones/listaT/1');
							
							exit();
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
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = SERVERURL . '/Vacaciones/importarT';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/Vacaciones/listaT/1';
			
				$this->views->getView($this, 'importarLibro', $data);
			}
		}

		public function importarD()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 600);
					
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Empleado = $oHoja->getCell('A' . $i)->getCalculatedValue();
								$FechaIngreso = $oHoja->getCell('B' . $i)->getCalculatedValue();
								$FechaIngreso = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('C' . $i)->getCalculatedValue())->format('Y-m-d');
								$NombreEmpleado = $oHoja->getCell('C' . $i)->getCalculatedValue();
								$FormaPago = $oHoja->getCell('D' . $i)->getCalculatedValue();
								$FechaPago = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('E' . $i)->getCalculatedValue())->format('Y-m-d');
								$ValorPago = $oHoja->getCell('F' . $i)->getCalculatedValue();
								$FechaInicio = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('G' . $i)->getCalculatedValue())->format('Y-m-d');
								$FechaSiguiente = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('H' . $i)->getCalculatedValue())->format('Y-m-d');
								$Periodo = $oHoja->getCell('I' . $i)->getCalculatedValue();
								$Dias = $oHoja->getCell('J' . $i)->getCalculatedValue();

								$query = <<<EOD
									SELECT EMPLEADOS.*, 
										PARAMETROS.Detalle 
									FROM EMPLEADOS
									INNER JOIN PARAMETROS 
										ON EMPLEADOS.Estado = PARAMETROS.Id 
									WHERE EMPLEADOS.Documento = '$Empleado'; 
								EOD;

								$reg = $this->model->buscarNovedad($query);

								if ($reg AND $reg['detalle'] == 'ACTIVO')
								{
									$IdEmpleado = $reg['id'];
									$SueldoBasico = $reg['sueldobasico'];
								}
								else
								{
									$data['mensajeError'] .= 'Empleado no existe (' . $Empleado . ') <br>';
									continue;
								}

								$FechaCausacion = substr($Periodo, 6, 4) . '-' . substr($Periodo, 3, 2) . '-' . substr($Periodo, 0, 2);
								$FechaPago = substr($FechaPago, 6, 4) . '-' . substr($FechaPago, 3, 2) . '-' . substr($FechaPago, 0, 2);
								$FechaInicio = substr($FechaInicio, 6, 4) . '-' . substr($FechaInicio, 3, 2) . '-' . substr($FechaInicio, 0, 2);
								$FechaSiguiente = substr($FechaSiguiente, 6, 4) . '-' . substr($FechaSiguiente, 3, 2) . '-' . substr($FechaSiguiente, 0, 2);

								$query = <<<EOD
									INSERT INTO VACACIONES
										(IdEmpleado, SueldoBasico, RecargoNocturno, SalarioBase, DiasSancionYLicencia,
										FechaCausacion, FechaLiquidacion, FechaInicio, FechaIngreso,
										DiasALiquidar, DiasEnTiempo, DiasEnDinero,
										ValorEnTiempo, ValorEnDinero, Observaciones, DiasProcesados, Procesado)
										VALUES (
										$IdEmpleado, 
										$SueldoBasico,
										0,
										$SueldoBasico,
										0,
										'$FechaCausacion',
										'$FechaPago',
										'$FechaInicio', 
										'$FechaSiguiente',
										$Dias,
										IF ($FormaPago = 'TIEMPO' THEN $Dias ELSE 0),
										IF ($FormaPago = 'DINERO' THEN $Dias ELSE 0),
										IF ($FormaPago = 'TIEMPO' THEN $ValorPago ELSE 0),
										IF ($FormaPago = 'DINERO' THEN $ValorPago ELSE 0),
										'$Periodo', 
										$Dias, 
										1);
								EOD;

								$ok = $this->model->actualizarRegistros($query);
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/Vacaciones/listaD/1');
							
							exit;
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
				$_SESSION['Importar'] = '';
				$_SESSION['ImportarArchivo'] = SERVERURL . '/Vacaciones/importarD';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/Vacaciones/listaD/1';
			
				$this->views->getView($this, 'importar', $data);
			}
		}

		public function listaLibroVacaciones($pagina)
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
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['LIBROVACACIONES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['LIBROVACACIONES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['LIBROVACACIONES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['LIBROVACACIONES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['LIBROVACACIONES']['Filtro']))
			{
				$_SESSION['LIBROVACACIONES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['LIBROVACACIONES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['LIBROVACACIONES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['LIBROVACACIONES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['LIBROVACACIONES']['Orden'])) 
					$_SESSION['LIBROVACACIONES']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$query = 'WHERE VACACIONES.Procesado = 1 ';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if ($lnCount == 0)
						$query .= ' AND ( ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ')';
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['LIBROVACACIONES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarVacaciones($query);
			$this->views->getView($this, 'libroVacaciones', $data);
		}

		public function lista($pagina) {
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/vacaciones/lista';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/vacaciones/lista';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';

			$_SESSION['Paginar'] = TRUE;

			$_SESSION['REP_VAC']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['REP_VAC']['Pagina'];

			if( isset($_REQUEST['IdCentro']) ) {
				$_SESSION['REP_VAC']['IdCentro'] = $_REQUEST['IdCentro'];
			}

			if( isset($_REQUEST['Filtro']) ) {
				$_SESSION['REP_VAC']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['REP_VAC']['Pagina'] = 1;
				$pagina = 1;
			}

			if(!isset($_SESSION['REP_VAC']['Filtro'])) {
				$_SESSION['REP_VAC']['Filtro'] = '';
			}

			if(!isset($_SESSION['REP_VAC']['IdCentro'])) {
				$_SESSION['REP_VAC']['IdCentro'] = '';
			}

			$lcFiltro = $_SESSION['REP_VAC']['Filtro'];

			$IdCentro = $_SESSION['REP_VAC']['IdCentro'];

			if (isset($_REQUEST['Orden'])) {
				$_SESSION['REP_VAC']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['REP_VAC']['Pagina'] = 1;
				$pagina = 1;
			}
			else if (! isset($_SESSION['REP_VAC']['Orden'])) 
					$_SESSION['REP_VAC']['Orden'] = "va.fechainicio";

			$query = <<<EOD
				SELECT
					e.documento Documento,
					CASE WHEN e.sueldobasico >= 15080000 THEN 'S' ELSE 'N' END Integral,
					e.fechaingreso "Fecha Ingreso Emp",
					e.nombre1 + ' ' + e.nombre1 + ' ' + e.apellido1 + ' ' + e.apellido2 "Nombre Completo",
					CASE
						WHEN va.diasentiempo <> 0 AND va.diasendinero = 0 THEN 'TIEMPO'
						WHEN va.diasentiempo = 0 AND va.diasendinero <> 0 THEN 'DINERO'
						ELSE 'MIXTO'
					END "SubTipo",
					va.fechaliquidacion "Fecha Pago",
					va.valorentiempo + va.valorendinero + va.valorfestivos + va.valordia31 "Valor Pagado",
					va.fechainicio "Fecha Inicial Disfrute",
					va.fechaingreso "Fecha Final Disfrute",
					convert(varchar, va.fechacausacion) + ' - ' + convert(
						varchar,
						DATEADD(day, -1, DATEADD(year, 1, va.fechacausacion))
					) Periodo,
					va.diasaliquidar Dias
				FROM nomina.vacaciones va
				INNER JOIN nomina.empleados e ON e.id = va.idempleado
			EOD;

			$IdPeriodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			if (isset($_REQUEST['fecInicio']) AND !empty($_REQUEST['fecInicio']))
				$FechaInicialPeriodo = $_REQUEST['fecInicio'];
			else if (
				isset($_SESSION['REP_VAC']['reg']) AND
				isset($_SESSION['REP_VAC']['reg']['fecInicio']) AND
				!empty($_SESSION['REP_VAC']['reg']['fecInicio'])
			) $FechaInicialPeriodo = $_SESSION['REP_VAC']['reg']['fecInicio'];

			if (isset($_REQUEST['fecFin']) AND !empty($_REQUEST['fecFin']))
				$FechaFinalPeriodo = $_REQUEST['fecFin'];
			else if (
				isset($_SESSION['REP_VAC']['reg']) AND
				isset($_SESSION['REP_VAC']['reg']['fecFin']) AND
				!empty($_SESSION['REP_VAC']['reg']['fecFin'])
			) $FechaFinalPeriodo = $_SESSION['REP_VAC']['reg']['fecFin'];

			$_SESSION['REP_VAC']['reg'] = array(
				"fecInicio" => $FechaInicialPeriodo,
				"fecFin" => $FechaFinalPeriodo,
				"IdCentro" => $IdCentro
			);

			$where = <<<EOD
				WHERE va.fechainicio >= '$FechaInicialPeriodo' AND va.fechainicio <= '$FechaFinalPeriodo' 
			EOD;

			if (isset($IdCentro) AND !empty($IdCentro)) $where .= "AND (e.idcentro = ".$IdCentro." OR e.idproyecto = ".$IdCentro.") ";

			$data['registros'] = $this->model->contarRegistrosReporteVacaciones($where);
			$query .= $where;

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') {
				$data['rows'] = $this->model->listar($query);

				$query .= 'ORDER BY ' . $_SESSION['REP_VAC']['Orden'];
				$Archivo = './descargas/' . $_SESSION['Login']['Usuario'] . '_Libro_de_vacaciones_disfrutadas_' . date('YmdGis') . '.csv';

				generateCSV($Archivo, $data['rows']);
				exit();
			}
			else {
				if	(!empty($lcFiltro)) {
					$aFiltro = explode(' ', $lcFiltro);

					for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ ) {
						if ($lnCount == 0)
							$query .= ' AND ( ';
						else
							$query .= 'OR ';
	
						$query .= "e.documento LIKE '%" . $aFiltro[$lnCount] . "%' ";
						$query .= "OR UPPER(REPLACE(e.apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(e.apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(e.nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
						$query .= "OR UPPER(REPLACE(e.nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					}
	
					$query .= ') ';
				}

				$lineas = LINES;
				$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;

				$query .= 'ORDER BY ' . $_SESSION['REP_VAC']['Orden'];
				$query .= ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
				$data['rows'] = $this->model->listar($query);

				$this->views->getView($this, 'reporteVacaciones', $data);
			}
		}
	}
?>
