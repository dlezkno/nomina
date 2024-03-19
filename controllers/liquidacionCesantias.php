<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class LiquidacionCesantias extends Controllers
	{
		public function liquidar()
		{
			ini_set('max_execution_time', 6000);

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'TipoEmpleados' => isset($_REQUEST['TipoEmpleados']) ? $_REQUEST['TipoEmpleados'] : 0,
					'IdCentro' => isset($_REQUEST['IdCentro']) ? $_REQUEST['IdCentro'] : 0,
					'IdCargo' => isset($_REQUEST['IdCargo']) ? $_REQUEST['IdCargo'] : 0,
					'Empleado' => isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'FechaCorte' => isset($_REQUEST['FechaCorte']) ? $_REQUEST['FechaCorte'] : date('Y-m-d'), 
					'CalculaAnoAnterior' => isset($_REQUEST['CalculaAnoAnterior']) ? TRUE : FALSE,
					'TransfiereFC' => isset($_REQUEST['TransfiereFC']) ? TRUE : FALSE,
					'GenerarNovedades' => isset($_REQUEST['GenerarNovedades']) ? TRUE : FALSE
					),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['TipoEmpleados']))
			{
				$query = <<<EOD
					SELECT AUXILIARES.Id, 
							MAYORES.TipoRetencion 
						FROM AUXILIARES 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
						WHERE PARAMETROS.Detalle = 'ES CESANTÍAS';
				EOD;

				$reg = $this->model->leerRegistro($query);

				if ($reg)
				{
					$IdConceptoCesantias = $reg['Id'];
					$TipoRetencionC = $reg['TipoRetencion'];
				}
				else
					$data['mensajeError'] .= label('No hay definido un concepto de Cesantías') . '<br>';

				$query = <<<EOD
					SELECT AUXILIARES.Id, 
							MAYORES.TipoRetencion 
						FROM AUXILIARES 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
						WHERE PARAMETROS.Detalle = 'ES INTERÉS DE CESANTÍAS';
				EOD;

				$reg = $this->model->leerRegistro($query);

				if ($reg)
				{
					$IdConceptoInteres = $reg['Id'];
					$TipoRetencionI = $reg['TipoRetencion'];
				}
				else
					$data['mensajeError'] .= label('No hay definido un concepto de Interés de cesantías') . '<br>';

				$query = <<<EOD
					SELECT AUXILIARES.Id, 
							MAYORES.TipoRetencion 
						FROM AUXILIARES 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id
						WHERE PARAMETROS.Detalle = 'ES TRANSFERENCIA A FONDO DE CESANTIAS';
				EOD;

				$reg = $this->model->leerRegistro($query);

				if ($reg)
				{
					$IdConceptoFC = $reg['Id'];
					$TipoRetencionFC = $reg['TipoRetencion'];
				}
				else
					$data['mensajeError'] .= label('No hay definido un concepto de Interés de cesantías') . '<br>';

				$ValorSubsidioTransporte = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSubsidioTransporte'")['valor'];

				$P_TipoEmpleados = $_REQUEST['TipoEmpleados'];
				$P_IdCentro = $_REQUEST['IdCentro'];
				$P_IdCargo = $_REQUEST['IdCargo'];
				$P_Empleado = $_REQUEST['Empleado'];
				$FechaCorte = $_REQUEST['FechaCorte'];

				if (substr($FechaCorte, 8, 2) == '31')
					$FechaCorte = substr($FechaCorte, 0, 8) . '30';

				// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
				$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
				$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
				$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
				$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

				$Referencia = $reg1['valor'];
				$IdPeriodicidad = $reg2['valor'];

				$Periodicidad = getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
				$IdPeriodo = $reg3['valor'];
				$Ciclo = $reg4['valor'];

				$query = <<<EOD
					PERIODOS.Referencia = $Referencia AND 
					PERIODOS.Periodicidad = $IdPeriodicidad AND 
					PERIODOS.Periodo = $IdPeriodo;
				EOD;

				$regPeriodo = getRegistro('PERIODOS', 0, $query);

				if (! $regPeriodo) 
					$data['mensajeError'] .= label('Perído definido no existe') . '<br>';

				$FechaInicial = ComienzoMes($regPeriodo['fechafinal']);
				$FechaFinal = $regPeriodo['fechafinal'];
			
				if (empty($data['mensajeError'])) 
				{
					$query = <<<EOD
						TRUNCATE TABLE nomina.CESANTIAS;
					EOD;

					$this->model->query($query);

					// SE LEEN LOS EMPLEADOS
					$query = <<<EOD
						SELECT EMPLEADOS.*, 
								CENTROS.Nombre AS NombreCentro, 
								CARGOS.Nombre AS NombreCargo,
								PARAMETROS2.Detalle AS NombreRegimenCesantias, 
								PARAMETROS4.Detalle AS NombreModalidadTrabajo, 
								PARAMETROS5.Detalle AS NombreSubsidioTransporte 
							FROM EMPLEADOS 
								INNER JOIN CENTROS 
									ON EMPLEADOS.IdCentro = CENTROS.Id 
								INNER JOIN CARGOS 
									ON EMPLEADOS.IdCargo = CARGOS.Id 
								INNER JOIN PARAMETROS AS PARAMETROS1
									ON EMPLEADOS.Estado = PARAMETROS1.Id 
								INNER JOIN PARAMETROS AS PARAMETROS2
									ON EMPLEADOS.RegimenCesantias = PARAMETROS2.Id 
								INNER JOIN PARAMETROS AS PARAMETROS3
									ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
								INNER JOIN PARAMETROS AS PARAMETROS4
									ON EMPLEADOS.ModalidadTrabajo = PARAMETROS4.Id 
								INNER JOIN PARAMETROS AS PARAMETROS5 
									ON EMPLEADOS.SubsidioTransporte = PARAMETROS5.Id 
							WHERE 
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
							EMPLEADOS.PeriodicidadPago = $IdPeriodicidad AND 
							PARAMETROS1.Detalle = 'ACTIVO' AND 
							EMPLEADOS.FechaIngreso <= '$FechaCorte' AND 
							PARAMETROS2.Detalle <> 'SALARIO INTEGRAL' AND 
							PARAMETROS3.Detalle <> 'APRENDIZ SENA'
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
					EOD;

					$empleados = $this->model->listarRegistros($query);

					for ($i = 0; $i < count($empleados) ; $i++) 
					{ 
						$regEmpleado = $empleados[$i];

						$IdEmpleado = $regEmpleado['id'];
						$FechaIngreso = $regEmpleado['fechaingreso'];
						$SueldoBasico = $regEmpleado['sueldobasico'];

						switch ($regEmpleado['NombreSubsidioTransporte'])
						{
							case 'SUBSIDIO COMPLETO':
								$SubsidioTransporte = $ValorSubsidioTransporte;
								break;
							case 'MEDIO SUBSIDIO':
								$SubsidioTransporte = round($ValorSubsidioTransporte / 2, 0);
								break;
							default:
								$SubsidioTransporte = 0;
								break;
						}

						if ($regEmpleado['NombreRegimenCesantias'] == 'RÉGIMEN TRADICIONAL') 
							$FechaInicial = $FechaIngreso;
						else
							$FechaInicial = max($FechaIngreso, ComienzoAno($FechaCorte));

						// SE BUSCA EL SALARIO Y SUBSIDIO ANTES DEL ULTIMO AUMENTO
						// if ($data['reg']['CalculaAnoAnterior'] == 1) 
						// {
						// 	$query = <<<EOD
						// 		SELECT AUMENTOSSALARIALES.SueldoBasico, 
						// 				AUMENTOSSALARIALES.SubsidioTransporte 
						// 			FROM AUMENTOSSALARIALES 
						// 			WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
						// 				AUMENTOSSALARIALES.FechaAumento < '$FechaInicial' AND 
						// 				AUMENTOSSALARIALES.Procesado = 1 
						// 			ORDER BY AUMENTOSSALARIALES.FechaAumento DESC;
						// 	EOD;

						// 	$aumentos = $this->model->listarRegistros($query);
							
						// 	if ($aumentos) 
						// 	{
						// 		$regAumento = $aumentos[0];

						// 		$SueldoBasico = $regAumento['SueldoBasico'];
						// 		$SubsidioTransporte = $regAumento['SubsidioTransporte'];
						// 	}
						// }

						// SE CALCULAN LOS DIAS DE SANCION Y LICENCIA DEL EMPLEADO EN EL PERIODO A LIQUIDAR
						$query = <<<EOD
							SELECT PARAMETROS1.Detalle, 
									ACUMULADOS.Horas, 
									ACUMULADOS.FechaInicialPeriodo, 
									ACUMULADOS.FechaFinalPeriodo 
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
									ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaCorte';
						EOD;

						$diasSancion = $this->model->listarRegistros($query);
						
						$DiasSancionYLicencias = 0;

						if ($diasSancion) 
						{
							for ($j = 0; $j < count($diasSancion); $j++) 
							{ 
								$reg = $diasSancion[$j];
								// $Fecha1 = new DateTime($reg['FechaFinal']);
								// $Fecha2 = new DateTime($reg['FechaInicial']);
								// $DiasSancionYLicencias += $Fecha1->diff($Fecha2)->days + 1;
								$DiasSancionYLicencias += round($reg['Horas'] / 8, 0);
							}
						}

						if ($regEmpleado['diasano'] == 360) 
							$DiasCesantias = (Dias360($FechaCorte, $FechaInicial) - $DiasSancionYLicencias);
						else
							$DiasCesantias = (Dias365($FechaCorte, $FechaInicial) - $DiasSancionYLicencias);
							
						if ($regEmpleado['NombreModalidadTrabajo'] == 'SUELDO BÁSICO') 
						{
							// NOTA: Se verifica si hay variacion de sueldo en los últimos tres meses
							$Fecha3MesesAntes = max($FechaIngreso, date('Y-m-d', strtotime($FechaCorte . ' - 3 months')));

							$query = <<<EOD
								SELECT AUMENTOSSALARIALES.* 
									FROM AUMENTOSSALARIALES 
									WHERE AUMENTOSSALARIALES.IdEmpleado = $IdEmpleado AND 
										AUMENTOSSALARIALES.FechaAumento >= '$Fecha3MesesAntes' AND 
										AUMENTOSSALARIALES.FechaAumento <= '$FechaCorte';
							EOD;

							$aumentos = $this->model->listar($query);

							if ($aumentos) 
								$CalculaSalario = TRUE;
							else
								$CalculaSalario = FALSE;

							// $query = <<<EOD
							// 	SELECT SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS Valor 
							// 		FROM ACUMULADOS 
							// 			INNER JOIN AUXILIARES 
							// 				ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							// 			INNER JOIN PARAMETROS AS PARAMETROS1 
							// 				ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							// 			INNER JOIN PARAMETROS AS PARAMETROS2 
							// 				ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id 
							// 			INNER JOIN MAYORES 
							// 				ON AUXILIARES.IdMayor = MAYORES.Id 
							// 			INNER JOIN PARAMETROS AS PARAMETROS3 
							// 				ON MAYORES.ClaseConcepto = PARAMETROS3.Id 
							// 		WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							// 			PARAMETROS2.Detalle = 'ES SUELDO BÁSICO' AND 
							// 			PARAMETROS3.Detalle = 'SALARIO' AND 
							// 			ACUMULADOS.FechaInicialPeriodo >= '$Fecha3MesesAntes' AND 
							// 			ACUMULADOS.FechaFinalPeriodo <= '$FechaCorte';
							// EOD;

							// $variacion = $this->model->listar($query);

							// if ($variacion[0]['Valor'] == NULL)
							// 	$LiquidaSalario = FALSE;
							// else
							// 	if ($variacion AND $variacion[0]['Valor'] / 3 <> $SueldoBasico) 
							// 		$LiquidaSalario = TRUE;
							// 	else
							// 		$LiquidaSalario = FALSE;
						}
						else
							$CalculaSalario = FALSE;

						if ($CalculaSalario) 
						{
							// SE CALCULA EL SALARIO SI HAY VARIACIONES EN LOS ULTIMOS TRES MESES
							$query = <<<EOD
								SELECT MIN(ACUMULADOS.FechaInicialPeriodo) AS FechaInicial, 
										MAX(ACUMULADOS.FechaFinalPeriodo) AS FechaFinal, 
										SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS Valor 
									FROM ACUMULADOS 
										INNER JOIN AUXILIARES 
											ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
										INNER JOIN MAYORES 
											ON AUXILIARES.IdMayor = MAYORES.Id 
										INNER JOIN PARAMETROS 
											ON AUXILIARES.Imputacion = PARAMETROS.Id 
									WHERE ACUMULADOS.IdEmpleado = $IdEmpleado AND 
										MAYORES.BaseCesantias = 1 AND 
										ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
										ACUMULADOS.FechaFinalPeriodo <= '$FechaCorte';
							EOD;

							$salario = $this->model->listar($query);

							if ($salario) 
								$SalarioBase = round($salario[0]['Valor'] / Dias360($FechaCorte, $FechaInicial) * 30, 0);
							else
								$SalarioBase = $SueldoBasico + $SubsidioTransporte;
						}
						else
							$SalarioBase = $SueldoBasico + $SubsidioTransporte;

						$ValorCesantias = round($SalarioBase * ($DiasCesantias -  $DiasSancionYLicencias) / 360, 0);

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
									ACUMULADOS.FechaInicialPeriodo >= '$FechaInicial' AND 
									ACUMULADOS.FechaFinalPeriodo <= '$FechaCorte';
						EOD;

						$anticipos = $this->model->listar($query);

						if ($anticipos) 
							$AnticipoCesantias = ($anticipos[0]['Valor'] == NULL ? 0 : $anticipos[0]['Valor']);
						else
							$AnticipoCesantias = 0;

						$InteresCesantias = round(($ValorCesantias - $AnticipoCesantias) * $DiasCesantias * 0.12 / 360, 0);

						$Fecha = date('Y-m-d');

						if ($regEmpleado['NombreRegimenCesantias'] == 'RÉGIMEN TRADICIONAL') 
						{
							$query = <<<EOD
								INSERT INTO CESANTIAS 
									(IdEmpleado, FechaLiquidacion, FechaIngreso, FechaInicio, DiasCesantias, DiasSancionYLicencias, SueldoBasico, SalarioBase, ValorCesantias, AnticipoCesantias, InteresCesantias) 
									VALUES (
										$IdEmpleado, 
										'$Fecha', 
										'$FechaIngreso', 
										'$FechaInicial', 
										$DiasCesantias, 
										$DiasSancionYLicencias, 
										$SueldoBasico, 
										$SalarioBase, 
										0, 
										0, 
										$InteresCesantias);
							EOD;
						}
						else
						{
							$query = <<<EOD
								INSERT INTO CESANTIAS 
									(IdEmpleado, FechaLiquidacion, FechaIngreso, FechaInicio, DiasCesantias, DiasSancionYLicencias, SueldoBasico, SalarioBase, ValorCesantias, AnticipoCesantias, InteresCesantias) 
									VALUES (
										$IdEmpleado, 
										'$Fecha', 
										'$FechaIngreso', 
										'$FechaInicial', 
										$DiasCesantias, 
										$DiasSancionYLicencias, 
										$SueldoBasico, 
										$SalarioBase, 
										$ValorCesantias, 
										$AnticipoCesantias, 
										$InteresCesantias);
							EOD;
						}

						$ok = $this->model->query($query);
					}

					if ($data['reg']['GenerarNovedades']) 
					{
						// SE GUARDAN LAS NOVEDADES
						$query = <<<EOD
							SELECT CESANTIAS.IdEmpleado, 
									CESANTIAS.DiasCesantias, 
									CESANTIAS.DiasSancionYLicencias, 
									CESANTIAS.ValorCesantias, 
									CESANTIAS.AnticipoCesantias, 
									CESANTIAS.InteresCesantias, 
									EMPLEADOS.IdCentro,
									EMPLEADOS.TipoEmpleado 
								FROM CESANTIAS  
									INNER JOIN EMPLEADOS 
										ON CESANTIAS.IdEmpleado = EMPLEADOS.Id
								ORDER BY CESANTIAS.IdEmpleado;
						EOD;

						$data = $this->model->listarRegistros($query);

						for ($i = 0; $i < count($data); $i++) 
						{ 
							$reg = $data[$i];

							// CESANTIAS
							$IdEmpleado = $reg['IdEmpleado'];
							$IdConcepto = $IdConceptoCesantias;
							$Horas = ($reg['DiasCesantias']  - $reg['DiasSancionYLicencias']) * 8;
							$ValorNovedad = $reg['ValorCesantias'] - $reg['AnticipoCesantias'];
							$TipoRetencion = $TipoRetencionC;
							$IdCentro = $reg['IdCentro'];
							$TipoEmpleado = $reg['TipoEmpleado'];

							if ($ValorNovedad > 0 AND $data['reg']['TransfiereFC'] == 1) 
							{
								// VALOR DE CESANTIAS A PAGAR
								$query = <<<EOD
									INSERT INTO NOMINA 
										(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
										VALUES
										($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $Horas, $ValorNovedad, 0, 'N', $TipoRetencion, $IdCentro, $TipoEmpleado);
								EOD;
				
								$ok = $this->model->actualizarRegistros($query);

								// VALOR DE CESANTIAS A TRANSFERIR AL FONDO DE CESANTIAS
								$IdConcepto = $IdConceptoFC;
								$TipoRetencion = $TipoRetencionFC;

								$query = <<<EOD
									INSERT INTO NOMINA 
										(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
										VALUES
										($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $Horas, $ValorNovedad, 0, 'N', $TipoRetencion, $IdCentro, $TipoEmpleado);
								EOD;
				
								$ok = $this->model->actualizarRegistros($query);
							}

							// INTERES CESANTIAS
							$IdConcepto = $IdConceptoInteres;
							$ValorNovedad = $reg['InteresCesantias'];
							$TipoRetencion = $TipoRetencionI;

							$query = <<<EOD
								INSERT INTO NOMINA 
									(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, Saldo, Liquida, Afecta, IdCentro, TipoEmpleado) 
									VALUES
									($IdPeriodo, $Ciclo, $IdEmpleado, $IdConcepto, $Horas, $ValorNovedad, 0, 'N', $TipoRetencion, $IdCentro, $TipoEmpleado);
							EOD;
			
							$ok = $this->model->actualizarRegistros($query);
						}
					}
						
					$_REQUEST['url'] = SERVERURL . '/liquidacionCesantias/lista';
					$_SESSION['LIQ_CESANTIAS']['Filtro'] = '';

					$this->lista(1);
				}
				else
					$this->views->getView($this, 'actualizar', $data);
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/liquidacionCesantias/editar';
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
				$_SESSION['Lista'] = SERVERURL . '/liquidacionCesantias/lista/1';

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
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
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/liquidacionCesantias/liquidar';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['LIQ_CESANTIAS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['LIQ_CESANTIAS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['LIQ_CESANTIAS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['LIQ_CESANTIAS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['LIQ_CESANTIAS']['Filtro']))
			{
				$_SESSION['LIQ_CESANTIAS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['LIQ_CESANTIAS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['LIQ_CESANTIAS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['LIQ_CESANTIAS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['LIQ_CESANTIAS']['Orden'])) 
					$_SESSION['LIQ_CESANTIAS']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CARGOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['LIQ_CESANTIAS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarLiquidacionCesantias($query);
			$this->views->getView($this, 'liquidacionCesantias', $data);
		}	
	}
?>
