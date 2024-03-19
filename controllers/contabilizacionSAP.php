<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class contabilizacionSAP extends Controllers
	{
		public function parametros()
		{
			set_time_limit(0);

            // SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");

			$regSENA = getRegistro('TERCEROS', 0, "TERCEROS.Nombre = 'SENA'");
			$regICBF = getRegistro('TERCEROS', 0, "TERCEROS.Nombre = 'ICBF'");

			$Referencia = isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : $reg1['valor'];
			$Periodicidad = isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : $reg2['valor'];
			$IdComprobante = isset($_REQUEST['IdComprobante']) ? $_REQUEST['IdComprobante'] : 0;

			if (isset($_REQUEST['Periodo'])) 
			{
				$Periodo = $_REQUEST['Periodo'];

				$query = <<<EOD
					PERIODOS.Referencia = $Referencia AND 
					PERIODOS.Periodicidad = $Periodicidad AND 
					PERIODOS.Periodo = $Periodo
				EOD;

				$regPeriodo = getRegistro('PERIODOS', 0, $query);
			}
			else
			{
				$regPeriodo = getRegistro('PERIODOS', $reg3['valor']);
				$Periodo = $regPeriodo['periodo'];
			}

			$regPeriodicidad = getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad = substr($regPeriodicidad['detalle'], 0, 1);

			$IdPeriodo = $regPeriodo['id'];
			$FechaInicialPeriodo = $regPeriodo['fechainicial'];
			$FechaFinalPeriodo = $regPeriodo['fechafinal'];

			$CuentaNomina = getRegistro('PARAMETROS', 0, "Parametro = 'CuentaNomina' ")['valor'];
			$CuentaNomina = str_pad($CuentaNomina, 12, '0', STR_PAD_RIGHT);

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Referencia' => $Referencia, 
					'Periodicidad' => $Periodicidad, 
					'Periodo' => $Periodo, 
					'IdComprobante' => $IdComprobante 
					),
				'mensajeError' => ''
			);

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.IdCargo, 
						EMPLEADOS.IdEPS, 
						EMPLEADOS.IdFondoCesantias, 
						EMPLEADOS.IdFondoPensiones, 
						EMPLEADOS.IdCajaCompensacion, 
						EMPLEADOS.CodigoSAP, 
						PARAMETROS2.Detalle AS TipoContrato, 
						PARAMETROS3.Detalle AS RegimenCesantias, 
						EMPLEADOS.SubtipoCotizante, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CENTROS.FechaVencimiento AS FechaVencimientoCentro, 
						PROYECTOS.Centro AS Proyecto, 
						PROYECTOS.Nombre AS NombreProyecto, 
						PROYECTOS.FechaVencimiento AS FechaVencimientoProyecto  
					FROM EMPLEADOS 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON EMPLEADOS.RegimenCesantias = PARAMETROS3.Id 
						LEFT JOIN CENTROS  
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS  
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
					WHERE PARAMETROS1.Detalle = 'ACTIVO' AND 
						(EMPLEADOS.IdCargo = 0 OR 
						EMPLEADOS.IdEPS = 0 OR 
						EMPLEADOS.IdFondoCesantias = 0 OR 
						EMPLEADOS.IdFondoPensiones = 0 OR 
						EMPLEADOS.IdCajaCompensacion = 0 OR
						EMPLEADOS.CodigoSAP = '');
			EOD;

			$empleados = $this->model->listar($query);

			if ($empleados) 
			{
				for ($i = 0; $i < count($empleados); $i++) 
				{ 
					$regEmpleado = $empleados[$i];

					if ($regEmpleado['IdCargo'] == 0) 
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido un CARGO.<br>';

					if ($regEmpleado['IdEPS'] == 0) 
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido una EPS.<br>';

					if ($regEmpleado['TipoContrato'] <> 'APRENDIZAJE - ETAPA LECTIVA' AND  
						$regEmpleado['TipoContrato'] <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
						$regEmpleado['TipoContrato'] <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
						$regEmpleado['TipoContrato'] <> 'PASANTÍA')
					{
						if ($regEmpleado['RegimenCesantias'] <> 'SALARIO INTEGRAL') 
						{
							if ($regEmpleado['IdFondoCesantias'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido un FONDO DE CESANTÍAS.<br>';
						}

						if ($regEmpleado['IdFondoPensiones'] == 0 AND $regEmpleado['SubtipoCotizante'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido un FONDO DE PENSIONES.<br>';

						if ($regEmpleado['IdCajaCompensacion'] == 0) 
							$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido una CAJA DE COMPENSACIÓN.<br>';
					}

					if (empty($regEmpleado['CodigoSAP'])) 
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') no tiene definido un Código SAP.<br>';

					if (! empty($regEmpleado['FechaVencimientoCentro']) AND $regEmpleado['FechaVencimientoCentro'] < $FechaFinalPeriodo)
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') está en un centro de costo cerrado (' . $regEmpleado['Centro'] . ')<br>';

					if (! empty($regEmpleado['Proyecto']) AND ! empty($regEmpleado['FechaVencimientoProyecto']) AND $regEmpleado['FechaVencimientoProyecto'] < $FechaFinalPeriodo)
						$data['mensajeError'] .= 'Empleado ' . $regEmpleado['Documento'] . ' (' . $regEmpleado['Apellido1'] . ' ' . $regEmpleado['Apellido2'] . ' ' . $regEmpleado['Nombre1'] . ' ' . $regEmpleado['Nombre2'] . ') está en un proyecto cerrado (' . $regEmpleado['Proyecto'] . ')<br>';
				}
			}

			$query = <<<EOD
				SELECT MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre, 
						COMPROBANTES.IdConcepto 
					FROM AUXILIARES  
						INNER JOIN MAYORES
							ON AUXILIARES.IdMayor = MAYORES.Id  
						LEFT JOIN COMPROBANTES 
							ON AUXILIARES.Id = COMPROBANTES.IdConcepto  
					WHERE AUXILIARES.Borrado = 0 AND 
						COMPROBANTES.IdConcepto IS NULL 
					ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar;
			EOD;

			$conceptos = $this->model->listar($query);

			if ($conceptos) 
			{
				for ($i = 0; $i < count($conceptos); $i++) 
				{ 
					$regConcepto = $conceptos[$i];

					$data['mensajeError'] .= 'Concepto ' . $regConcepto['Mayor'] . $regConcepto['Auxiliar'] . ' (' . $regConcepto['Nombre'] . ') no está definido en el Comprobante contable.<br>';
				}
			}

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') {
				if ($IdPeriodo>0 AND isset($_REQUEST['Referencia'])) {
					$ArchivoSAP1 = 'descargas/' . $_SESSION['Login']['Usuario'] . '_ComprobanteSAP_H_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo . '_' . date('YmdGis') . '.txt';
					$ArchivoSAP2 = 'descargas/' . $_SESSION['Login']['Usuario'] . '_ComprobanteSAP_D_' . $cPeriodicidad . '_' . $Referencia . '_' . $Periodo . '_' . date('YmdGis') . '.txt';

					$query = <<<EOD
						SELECT DETALLESSAP.*, EMPLEADOS.Documento as DocumentoEmpleado
						FROM DETALLESSAP
						JOIN EMPLEADOS ON EMPLEADOS.Id = DETALLESSAP.IdEmpleado
						WHERE DETALLESSAP.IdPeriodo=$IdPeriodo
					EOD;

					if ($IdComprobante > 0) $query .= "AND DETALLESSAP.IdComprobante = $IdComprobante";

					$query .= 'ORDER BY DETALLESSAP.LineNum';

					$datos = $this->model->listar($query);

					if ($datos) {
						for ($i = 0; $i < count($datos); $i++) {
							$regDatos = $datos[$i];

							$Documento = $regDatos['ConsecID'];
							$FechaFinalP = $regDatos['DueDate'];

							if ($i == 0) {
								// ARCHIVO ENCABEZADO
								$datosEnc[] = array('ConsecID', 'RecordKey', 'DueDate', 'Memo', 'Reference', 'TaxDate', 'TransactionCode', 'Series', 'ReferenceDate', 'CodComp', 'Procesado', 'DocumentoEmpleado');
								$datosEnc[] = array('ConsecID', 'RecordKey', 'DueDate', 'Memo', 'Reference', 'TaxDate', 'TransactionCode', 'Series', 'ReferenceDate', 'CodComp', 'Procesado', 'DocumentoEmpleado');
								$datosEnc[] = array($Documento, $Documento, $FechaFinalP, 'LIQUIDACION TODOS ' . $FechaFinalP, '', $FechaFinalP, 'NOM', 0, $FechaFinalP, 1, 'NULL', 'DocumentoEmpleado');

								// ARCHIVO DETALLE
								$datosDet[] = array('ConsecID', 'RecordKey', 'LineNum', 'AccountCode', 'ShortName', 'CostingCode', 'Projectcode', 'Debit', 'Credit', 'DueDate', 'LineMemo', 'Reference2', 'ReferenceDate1', 'ReferenceDate2', 'TaxDate', 'U_infoco01', 'U_codRet', 'U_BaseRet', 'U_TarifaRet', 'Procesado', 'CodCompania', 'OcrCode2', 'DocumentoEmpleado');
								$datosDet[] = array('ConsecID', 'RecordKey', 'LineNum', 'AccountCode', 'ShortName', 'CostingCode', 'Projectcode', 'Debit', 'Credit', 'DueDate', 'LineMemo', 'Reference2', 'ReferenceDate1', 'ReferenceDate2', 'TaxDate', 'U_infoco01', 'U_codRet', 'U_BaseRet', 'U_TarifaRet', 'Procesado', 'CodCompania', 'OcrCode2', 'DocumentoEmpleado');
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
								$regDatos['OcrCode2'],
								$regDatos['DocumentoEmpleado']
							);
						}

						$fp1 = fopen($ArchivoSAP1, 'w');

						for ($i = 0; $i < count($datosEnc); $i++) {
							for ($j = 0; $j < count($datosEnc[$i]); $j++) {
								fwrite($fp1, $datosEnc[$i][$j]);

								if ($j < count($datosEnc[$i]) - 1) fwrite($fp1, "\t");
							}

							fwrite($fp1, PHP_EOL);
						}

						fclose($fp1);

						$fp2 = fopen($ArchivoSAP2, 'w');

						for ($i = 0; $i < count($datosDet); $i++) {
							for ($j = 0; $j < count($datosDet[$i]); $j++) {
								fwrite($fp2, $datosDet[$i][$j]);

								if ($j < count($datosDet[$i]) - 1) fwrite($fp2, "\t");
							}

							fwrite($fp2, PHP_EOL);
						}
						
						fclose($fp2);
					} else $data['mensajeError'] .= "No se encontraro datos para exportar en el periodo <b>$Periodo</b> - año <b>$Referencia</b>.<br>";
				} else $data['mensajeError'] .= 'Debe seleccionar el periodo y el año que desea exportar.<br>';
			} elseif (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'EXPORTAR' && file_exists($_REQUEST['Archivo'])) {
				$type = 'applicatio/force-download';

				header('Content-Description: File Transfer');
				header("Content-Type: $type");
				header('Content-Disposition: attachment; filename=' . basename($_REQUEST['Archivo']));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($_REQUEST['Archivo']));
				ob_clean();
				flush();
				readfile($_REQUEST['Archivo']);

				header('Location: ' . SERVERURL . '/contabilizacionSAP/parametros');
				exit();
			} elseif (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'ELIMINAR' AND file_exists($_REQUEST['Archivo'])) {
				unlink($_REQUEST['Archivo']); 

				header('Location: ' . SERVERURL . '/contabilizacionSAP/parametros');
				exit();
			} elseif (isset($_REQUEST['Referencia'])) {
				if ($IdComprobante > 0 AND $IdComprobante == 8) {
					$query = <<<EOD
						DELETE FROM nomina.DETALLESSAP
						WHERE IdPeriodo = $IdPeriodo
							AND IdComprobante = $IdComprobante;
					EOD;

					$ok = $this->model->query($query);

					$FechaInicial = $regPeriodo['fechainicial'];
					$FechaFinal = $regPeriodo['fechafinal'];

					$Mes = strtoupper(NombreMes(date('m', strtotime($FechaFinal))));
					$Ano = date('Y', strtotime($FechaFinal));

					$query = <<<EOD
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo
					EOD;

					$datosAcumulados = $this->model->comprobanteSAP($query);

					if (count($datosAcumulados) > 0) {
						$Secuencia = 0;

						for ($i = 0; $i < count($datosAcumulados); $i++) 
						{ 
							$regAcumulados 		= $datosAcumulados[$i];

							$IdLogPila			= $regAcumulados['id'];
							$IdEmpleado 		= $regAcumulados['IdEmpleado'];
							$Archivo 			= $regAcumulados['Archivo'];
							$IdsArchivo 		= $regAcumulados['IdsArchivo'];
							$regEmpleado 		= getRegistro('EMPLEADOS', $IdEmpleado);

							$TipoEmpleado 		= $regAcumulados['TipoEmpleado'];
							$IdEPS 				= $regAcumulados['IdEPS'];
							$IdFondoPensiones 	= $regAcumulados['IdFondoPensiones'];
							$IdFondoCesantias 	= $regAcumulados['IdFondoCesantias'];
							$IdARL 				= $regAcumulados['IdARL'];
							$IdCajaCompensacion = $regAcumulados['IdCajaCompensacion'];
							$Centro 			= $regAcumulados['Centro'];
							$Proyecto 			= $regAcumulados['Proyecto'];

							$NombreCuenta = "SALARIO";

							$query = <<<EOD
								SELECT CENTROS.Centro, 
										CENTROS.TipoEmpleado, 
										DISPERSIONPORCENTRO.Porcentaje 
									FROM DISPERSIONPORCENTRO 
										INNER JOIN CENTROS ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
										INNER JOIN PERIODOS ON DISPERSIONPORCENTRO.IdPeriodo = PERIODOS.Id 
									WHERE PERIODOS.FechaInicial >= '$FechaInicial' AND 
										PERIODOS.FechaFinal <= '$FechaFinal' AND 
										DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado;
							EOD;

							$datosDispersion = $this->model->listar($query);

							$query = <<<EOD
								SELECT
									COMPROBANTES.IdTipoDoc, 
									TIPODOC.TipoDocumento, 
									TIPODOC.Nombre AS NombreComprobante, 
									TIPODOC.Secuencia, 
									COMPROBANTES.Detalle, 
									COMPROBANTES.CuentaDb, 
									COMPROBANTES.DetallaCentroDb, 
									COMPROBANTES.CuentaCr, 
									COMPROBANTES.DetallaCentroCr, 
									COMPROBANTES.Porcentaje, 
									PARAMETROS.Detalle AS TipoTercero, 
									COMPROBANTES.Exonerable 
								FROM COMPROBANTES 
									INNER JOIN TIPODOC ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
									INNER JOIN AUXILIARES ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
									INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
									LEFT JOIN PARAMETROS ON COMPROBANTES.TipoTercero = PARAMETROS.Id
								WHERE COMPROBANTES.IdTipoDoc = $IdComprobante AND 
									COMPROBANTES.TipoEmpleado = $TipoEmpleado 
								ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar;
							EOD;

							$datosComprobante = $this->model->listar($query);

							if ($datosComprobante) {
								for ($j = 0; $j < count($datosComprobante); $j++) 
								{ 
									$regComprobante 	= $datosComprobante[$j];

									$Documento 			= $Referencia . str_pad($Periodo, 2, '0', STR_PAD_LEFT);
									$NombreCuenta 		= $regComprobante['Detalle'];

									if ($regComprobante['TipoDocumento'] <> 'NOM' AND $regComprobante['TipoDocumento'] <> 'PARAF')
										$NombreCuenta 	= utf8_decode($regComprobante['NombreComprobante']);
									$CuentaDb 			= str_pad($regComprobante['CuentaDb'], 12, '0', STR_PAD_RIGHT);
									$DetallaCentroDb 	= $regComprobante['DetallaCentroDb'] == 1 ? TRUE : FALSE;
									$CuentaCr 			= str_pad($regComprobante['CuentaCr'], 12, '0', STR_PAD_RIGHT);
									$DetallaCentroCr 	= $regComprobante['DetallaCentroCr'] == 1 ? TRUE : FALSE;
									$TipoTercero 		= $regComprobante['TipoTercero'];

									$CodigoSAPDb = '';
									$CodigoSAP2Db = '';
									$CodigoSAPCr = '';
									$CodigoSAP2Cr = '';

									$Valor = 0;

									switch ($TipoTercero)
									{
										case 'DETALLA POR EMPLEADO':
											$CodigoSAPDb = $regEmpleado['codigosap'];
											$CodigoSAPCr = $regEmpleado['codigosap'];
											break;
										case 'DETALLA POR EPS - EMPLEADO':
											switch ($NombreCuenta) {
												case 'SALUD - EMPLEADO':
												case 'SALUD - EMPRESA':
													$Valor = $regAcumulados['ValorSalud'];
													break;
											}
											$reg = $IdEPS > 0 ? getRegistro('TERCEROS', $IdEPS) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR ARL - EMPLEADO':
											$Valor = $regAcumulados['ValorARL'];
											$reg = $IdARL > 0 ? getRegistro('TERCEROS', $IdARL) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina)
											{
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											}
											else
											{
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR FONDO DE PENSIONES - EMPLEADO':
											switch ($NombreCuenta) {
												case 'PENSION  - EMPLEADO':
												case 'PENSION  - EMPRESA':
													$Valor = $regAcumulados['ValorPension'];
													break;
												case 'FONDO DE SOLIDARIDAD':
													$Valor = $regAcumulados['ValorSolidaridad'];
													break;
												case 'FONDO DE SUBSISTENCIA':
													$Valor = $regAcumulados['ValorSubsistencia'];
													break;
											}
											$reg = $IdFondoPensiones > 0 ? getRegistro('TERCEROS', $IdFondoPensiones) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR CCF - EMPLEADO':
											$Valor = $regAcumulados['ValorCCF'];
											$reg = $IdCajaCompensacion > 0 ? getRegistro('TERCEROS', $IdCajaCompensacion) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR TERCERO - EMPLEADO':
											if ($regComprobante['Detalle'] == 'SENA') {
												$Valor = $regAcumulados['ValorSENA'];
												$reg = $regSENA;
											} elseif ($regComprobante['Detalle'] == 'ICBF') {
												$Valor = $regAcumulados['ValorICBF'];
												$reg = $regICBF;
											} else {
												$reg = NULL;
												if (isset($IdsArchivo) AND $IdsArchivo <> '') $reg = $this->model->leer(<<<EOD
													SELECT ter.*
													FROM $Archivo nom
													JOIN terceros ter ON ter.id=nom.idtercero
													WHERE nom.id in ($IdsArchivo)
														AND nom.idtercero IS NOT NULL
														AND nom.idtercero <> 0
														AND nom.idperiodo = $IdPeriodo;
												EOD);
											}

											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
									}

									if ($Valor<=0) continue;

									switch ($regComprobante['TipoDocumento']) {
										case 'NOM':
											$Reference2 = "INTERFAZ NOMINA $Mes DE $Ano";
											break;
										case 'PARAF':
											$Reference2 = "INTERFAZ PARAFISCALES $Mes DE $Ano";
											break;
										default:
											$Reference2 = "INTERFAZ PROVISIONES $Mes DE $Ano";
											break;
									}

									if ($datosDispersion) {
										for ($k = 0; $k < count($datosDispersion); $k++)  { 
											$regDispersion = $datosDispersion[$k];

											$IdTipoDoc = $regComprobante['IdTipoDoc'];
											$TipoEmpleado = $regDispersion['TipoEmpleado'];
											$Porcentaje3 = $regComprobante['Porcentaje'];

											$query = <<<EOD
												SELECT COMPROBANTES.CuentaDb, 
														COMPROBANTES.CuentaCr 
													FROM COMPROBANTES 
														INNER JOIN TIPODOC 
															ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
														INNER JOIN AUXILIARES 
															ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
														INNER JOIN MAYORES 
															ON AUXILIARES.IdMayor = MAYORES.Id 
														LEFT JOIN PARAMETROS 
															ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
													WHERE COMPROBANTES.IdTipoDoc = $IdTipoDoc AND 
														COMPROBANTES.TipoEmpleado = $TipoEmpleado AND 
														COMPROBANTES.Porcentaje = $Porcentaje3 
													ORDER BY COMPROBANTES.Id, MAYORES.Mayor, AUXILIARES.Auxiliar;
											EOD;

											$regDispersion2 = $this->model->leer($query);

											$CuentaDb = $regDispersion2['CuentaDb'];
											$CuentaCr = $regDispersion2['CuentaCr'];

											$ValorDisp = round($Valor * $regDispersion['Porcentaje'] / 100, 0);

											$CentroDb 	= 'R0000';
											$ProyectoDb = 'N000';

											if ($DetallaCentroDb) {
												if (left($regDispersion['Centro'], 1) == 'S') {
													$CentroDb = '04099'; 
													$ProyectoDb = $regDispersion['Centro']; 
												}
												else $CentroDb = $regDispersion['Centro'];
											}

											$CentroCr 	= 'R0000';
											$ProyectoCr = 'N000';

											if ($DetallaCentroCr) {
												if (left($regDispersion['Centro'], 1) == 'S') {
													$CentroCr = '04099'; 
													$ProyectoCr = $regDispersion['Centro']; 
												}
												else $CentroCr = $regDispersion['Centro'];
											}

											if ($ValorDisp>0) {
												$datos = array('Documento' 	=> $Documento, 
														'Secuencia' 	=> $Secuencia, 
														'CuentaDb' 		=> $CuentaDb, 
														'CentroDb' 		=> $CentroDb, 
														'ProyectoDb' 	=> $ProyectoDb, 
														'CuentaCr' 		=> $CuentaCr, 
														'CentroCr' 		=> $CentroCr, 
														'ProyectoCr' 	=> $ProyectoCr, 
														'Valor' 		=> $ValorDisp, 
														'FechaFinalP' 	=> str_replace('-', '', $FechaFinal), 
														'NombreCuenta' 	=> $NombreCuenta, 
														'Reference2' 	=> $Reference2, 
														'CodigoSAPDb' 	=> $CodigoSAPDb, 
														'CodigoSAP2Db' 	=> $CodigoSAP2Db, 
														'CodigoSAPCr' 	=> $CodigoSAPCr, 
														'CodigoSAP2Cr' 	=> $CodigoSAP2Cr, 
														'IdEmpleado' 	=> $IdEmpleado,
														'IdPeriodo' 	=> $IdPeriodo,
														'IdComprobante' => $IdComprobante,
														'IdLogPila' 	=> $IdLogPila
													);

												$Secuencia = $this->model->guardarRegistroSAP($datos);
											}
										}
									} else {
										$CentroDb = 'R0000';
										$ProyectoDb = 'N000';

										if ($DetallaCentroDb) {
											if (! empty($Proyecto)) {
												$CentroDb = '04099'; 
												$ProyectoDb = $Proyecto; 
											}
											else $CentroDb = $Centro;
										}

										$CentroCr = 'R0000';
										$ProyectoCr = 'N000';

										if ($DetallaCentroCr) {
											if (! empty($Proyecto)) {
												$CentroCr = '04099'; 
												$ProyectoCr = $Proyecto; 
											}
											else $CentroCr = $Centro;
										}

										$datos = array('Documento' 	=> $Documento, 
												'Secuencia' 	=> $Secuencia, 
												'CuentaDb' 		=> $CuentaDb, 
												'CentroDb' 		=> $CentroDb, 
												'ProyectoDb' 	=> $ProyectoDb, 
												'CuentaCr' 		=> $CuentaCr, 
												'CentroCr' 		=> $CentroCr, 
												'ProyectoCr' 	=> $ProyectoCr, 
												'Valor' 		=> $Valor, 
												'FechaFinalP' 	=> str_replace('-', '', $FechaFinal), 
												'NombreCuenta' 	=> $NombreCuenta, 
												'Reference2'	=> $Reference2, 
												'CodigoSAPDb' 	=> $CodigoSAPDb, 
												'CodigoSAP2Db' 	=> $CodigoSAP2Db, 
												'CodigoSAPCr' 	=> $CodigoSAPCr, 
												'CodigoSAP2Cr' 	=> $CodigoSAP2Cr, 
												'IdEmpleado' 	=> $IdEmpleado,
												'IdPeriodo' 	=> $IdPeriodo,
												'IdComprobante' => $IdComprobante,
												'IdLogPila' 	=> $IdLogPila
											);

										$Secuencia = $this->model->guardarRegistroSAP($datos);
									}
								}
							}
						}

						if (empty($data['mensajeError'])) 
						{
							header('Location: ' . SERVERURL . '/contabilizacionSAP/lista/1');
							exit();
						}
					}
				} elseif ($IdComprobante > 0) { // Version vieja para provisiones
					$query = <<<EOD
						DELETE FROM nomina.DETALLESSAP
						WHERE IdPeriodo = $IdPeriodo
							AND IdComprobante = $IdComprobante;
					EOD;

					$ok = $this->model->query($query);

					$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ValorSueldoMinimo'")['valor'];

					$FechaInicial = $regPeriodo['fechainicial'];
					$FechaFinal = $regPeriodo['fechafinal'];

					$FechaInicialP = str_replace('-', '', $FechaInicial);
					$FechaFinalP = str_replace('-', '', $FechaFinal);

					$Mes = strtoupper(NombreMes(date('m', strtotime($FechaFinal))));
					$Ano = date('Y', strtotime($FechaFinal));

					$ExoneracionEmpresa = getRegistro('PARAMETROS', 0, "Parametro = 'ExoneracionEmpresa' ")['valor'];

					$query = <<<EOD
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo
					EOD;

					$datosAcumulados = $this->model->comprobanteSAP2($query);

					if (count($datosAcumulados) > 0) 
					{
						$Secuencia = 0;

						for ($i = 0; $i < count($datosAcumulados); $i++) 
						{ 
							$regAcumulados 		= $datosAcumulados[$i];

							$IdEmpleado 		= $regAcumulados['IdEmpleado'];
							$regEmpleado 		= getRegistro('EMPLEADOS', $IdEmpleado);
							$RegimenCesantias 	= getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['detalle'];
							$TipoContrato 		= getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['detalle'];

							$IdConcepto 		= $regAcumulados['IdConcepto'];
							$regConcepto 		= getRegistro('AUXILIARES', $IdConcepto);

							$TipoEmpleado 		= $regAcumulados['TipoEmpleado'];
							$IdEPS 				= $regAcumulados['IdEPS'];
							$IdFondoPensiones 	= $regAcumulados['IdFondoPensiones'];
							$IdFondoCesantias 	= $regAcumulados['IdFondoCesantias'];
							$IdARL 				= $regAcumulados['IdARL'];
							$IdCajaCompensacion = $regAcumulados['IdCajaCompensacion'];
							$Auxiliar 			= $regAcumulados['Mayor'] . $regAcumulados['Auxiliar'];
							$NombreCuenta 		= $regAcumulados['NombreConcepto'];
							$EsDispersable 		= $regAcumulados['EsDispersable'] == 1 ? TRUE : FALSE;
							$Base 				= $regAcumulados['Base'];
							$ValorNomina 		= $regAcumulados['Valor'];
							$Centro 			= $regAcumulados['Centro'];
							$Proyecto 			= $regAcumulados['Proyecto'];
							$IdTercero			= $regAcumulados['IdTercero'];

							if ($IdComprobante > 0) {
								$query = <<<EOD
									SELECT COMPROBANTES.IdTipoDoc, 
											TIPODOC.TipoDocumento, 
											TIPODOC.Nombre AS NombreComprobante, 
											TIPODOC.Secuencia, 
											COMPROBANTES.Detalle, 
											COMPROBANTES.CuentaDb, 
											COMPROBANTES.DetallaCentroDb, 
											COMPROBANTES.CuentaCr, 
											COMPROBANTES.DetallaCentroCr, 
											COMPROBANTES.Porcentaje, 
											PARAMETROS.Detalle AS TipoTercero, 
											COMPROBANTES.Exonerable 
										FROM COMPROBANTES 
											INNER JOIN TIPODOC ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
											INNER JOIN AUXILIARES ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
										WHERE COMPROBANTES.IdTipoDoc = $IdComprobante AND 
											COMPROBANTES.IdConcepto = $IdConcepto AND 
											COMPROBANTES.TipoEmpleado = $TipoEmpleado 
											ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar;
								EOD;
							}
							else {
								$query = <<<EOD
									SELECT COMPROBANTES.IdTipoDoc, 
											TIPODOC.TipoDocumento, 
											TIPODOC.Nombre AS NombreComprobante, 
											TIPODOC.Secuencia, 
											COMPROBANTES.Detalle, 
											COMPROBANTES.CuentaDb, 
											COMPROBANTES.DetallaCentroDb, 
											COMPROBANTES.CuentaCr, 
											COMPROBANTES.DetallaCentroCr, 
											COMPROBANTES.Porcentaje, 
											PARAMETROS.Detalle AS TipoTercero, 
											COMPROBANTES.Exonerable 
										FROM COMPROBANTES  
											INNER JOIN TIPODOC ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
											INNER JOIN AUXILIARES ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
											LEFT JOIN PARAMETROS ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
										WHERE COMPROBANTES.IdConcepto = $IdConcepto AND 
											COMPROBANTES.TipoEmpleado = $TipoEmpleado AND 
											TIPODOC.TipoDocumento <> 'NOM' 
										ORDER BY COMPROBANTES.Id, MAYORES.Mayor, AUXILIARES.Auxiliar;
								EOD;
							}

							$datosComprobante = $this->model->listar($query);

							if ($datosComprobante) {
								for ($j = 0; $j < count($datosComprobante); $j++) 
								{ 
									$regComprobante 	= $datosComprobante[$j];

									$Documento 			= $Referencia . str_pad($Periodo, 2, '0', STR_PAD_LEFT);
									$NombreCuenta 		= $regComprobante['Detalle'];

									if ($regComprobante['TipoDocumento'] <> 'NOM' AND $regComprobante['TipoDocumento'] <> 'PARAF')
										$NombreCuenta 	= utf8_decode($regComprobante['NombreComprobante']);
									$CuentaDb 			= str_pad($regComprobante['CuentaDb'], 12, '0', STR_PAD_RIGHT);
									$DetallaCentroDb 	= $regComprobante['DetallaCentroDb'] == 1 ? TRUE : FALSE;
									$CuentaCr 			= str_pad($regComprobante['CuentaCr'], 12, '0', STR_PAD_RIGHT);
									$DetallaCentroCr 	= $regComprobante['DetallaCentroCr'] == 1 ? TRUE : FALSE;
									$Porcentaje 		= $regComprobante['Porcentaje'];
									$TipoTercero 		= $regComprobante['TipoTercero'];

									$CodigoSAPDb = '';
									$CodigoSAP2Db = '';
									$CodigoSAPCr = '';
									$CodigoSAP2Cr = '';

									switch ($TipoTercero) {
										case 'DETALLA POR EMPLEADO':
											$CodigoSAPDb = $regEmpleado['codigosap'];
											$CodigoSAPCr = $regEmpleado['codigosap'];
											break;
										case 'DETALLA POR EPS - EMPLEADO':
											$reg = $IdEPS > 0 ? getRegistro('TERCEROS', $IdEPS) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR ARL - EMPLEADO':
											$reg = $IdARL > 0 ? getRegistro('TERCEROS', $IdARL) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina)
											{
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											}
											else
											{
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR FONDO DE CESANTÍAS - EMPLEADO':
											$reg = $IdFondoCesantias > 0 ? getRegistro('TERCEROS', $IdFondoCesantias) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											}
											else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR FONDO DE PENSIONES - EMPLEADO':
											$reg = $IdFondoPensiones > 0 ? getRegistro('TERCEROS', $IdFondoPensiones) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR CCF - EMPLEADO':
											$reg = $IdCajaCompensacion > 0 ? getRegistro('TERCEROS', $IdCajaCompensacion) : NULL;
											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
										case 'DETALLA POR TERCERO - EMPLEADO':
											$reg = $IdTercero > 0 ? getRegistro('TERCEROS', $IdTercero) : NULL;
											if ($regComprobante['Detalle'] == 'SENA') $reg = $regSENA;
											elseif ($regComprobante['Detalle'] == 'ICBF') $reg = $regICBF;

											if ($CuentaDb == $CuentaNomina) {
												$CodigoSAPDb = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
												$CodigoSAP2Db = $regEmpleado['codigosap'];
											}

											if ($CuentaCr == $CuentaNomina) {
												$CodigoSAPCr = $regEmpleado['codigosap'];
												if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
											} else {
												if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
												$CodigoSAP2Cr = $regEmpleado['codigosap'];
											}
											break;
									}

									if ($Porcentaje == 0) {
										$Porcentaje2 = getRegistro('PARAMETROS', $regEmpleado['nivelriesgo'])['valor2'];
									}

									if ($regConcepto['tiporegistroauxiliar'] > 0)  {
										$TipoRegistroAuxiliar = getRegistro('PARAMETROS', $regConcepto['tiporegistroauxiliar'])['detalle'];
									}

									if ($ExoneracionEmpresa == 1)  {
										if ($TipoRegistroAuxiliar == 'ES APORTE DE SALUD' AND $regComprobante['Exonerable'] == 1)  {
											if ($RegimenCesantias <> 'SALARIO INTEGRAL') continue;
										}

										if ($regComprobante['TipoDocumento'] == 'PARAF' AND $regComprobante['Exonerable'] == 1) {
											if ($RegimenCesantias <> 'SALARIO INTEGRAL') continue;
										}
									}

									if ($RegimenCesantias == 'SALARIO INTEGRAL' AND $regComprobante['TipoDocumento'] <> 'PROVV' AND $regComprobante['TipoDocumento'] <> 'NOM' AND $regComprobante['TipoDocumento'] <> 'PARAF') 
										continue;

									if (($TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR $TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR $TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR $TipoContrato == 'PASANTÍA') AND $regComprobante['TipoDocumento'] <> 'NOM') 
										continue;

									switch ($regComprobante['TipoDocumento']) {
										case 'NOM':
											$Reference2 = "INTERFAZ NOMINA $Mes DE $Ano";
											break;
										case 'PARAF':
											$Reference2 = "INTERFAZ PARAFISCALES $Mes DE $Ano";
											break;
										default:
											$Reference2 = "INTERFAZ PROVISIONES $Mes DE $Ano";
											break;
									}
			
									if ($EsDispersable) {
										$query = <<<EOD
											SELECT CENTROS.Centro, 
													CENTROS.TipoEmpleado, 
													DISPERSIONPORCENTRO.Porcentaje 
												FROM DISPERSIONPORCENTRO 
													INNER JOIN CENTROS 
														ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
													INNER JOIN PERIODOS 
														ON DISPERSIONPORCENTRO.IdPeriodo = PERIODOS.Id 
												WHERE PERIODOS.FechaInicial >= '$FechaInicial' AND 
													PERIODOS.FechaFinal <= '$FechaFinal' AND 
													DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado;
										EOD;

										$datosDispersion = $this->model->listar($query);

										if ($datosDispersion) {
											for ($k = 0; $k < count($datosDispersion); $k++)  { 
												$regDispersion = $datosDispersion[$k];

												$IdTipoDoc = $regComprobante['IdTipoDoc'];
												$TipoEmpleado = $regDispersion['TipoEmpleado'];
												$Porcentaje3 = $regComprobante['Porcentaje'];

												$query = <<<EOD
													SELECT COMPROBANTES.CuentaDb, 
															COMPROBANTES.CuentaCr 
														FROM COMPROBANTES 
															INNER JOIN TIPODOC 
																ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
															INNER JOIN AUXILIARES 
																ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
															INNER JOIN MAYORES 
																ON AUXILIARES.IdMayor = MAYORES.Id 
															LEFT JOIN PARAMETROS 
																ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
														WHERE COMPROBANTES.IdTipoDoc = $IdTipoDoc AND 
															COMPROBANTES.IdConcepto = $IdConcepto AND 
															COMPROBANTES.TipoEmpleado = $TipoEmpleado AND 
															COMPROBANTES.Porcentaje = $Porcentaje3 
														ORDER BY COMPROBANTES.Id, MAYORES.Mayor, AUXILIARES.Auxiliar;
												EOD;

												$regDispersion2 = $this->model->leer($query);

												$CuentaDb = $regDispersion2['CuentaDb'];
												$CuentaCr = $regDispersion2['CuentaCr'];

												if ($regComprobante['TipoDocumento'] == 'NOM')
													$Valor = round($ValorNomina * $Porcentaje / 100 * $regDispersion['Porcentaje'] / 100, 0);
												else {
													if ($RegimenCesantias == 'SALARIO INTEGRAL')
														$Factor = .7;
													else
														$Factor = 1;

													if ($Porcentaje == 0)
														$Valor = round($Base * $Factor * $Porcentaje2 / 100  * $regDispersion['Porcentaje'] / 100, 0);
													else
														$Valor = round($ValorNomina * $Factor * $Porcentaje / 100 * $regDispersion['Porcentaje'] / 100, 0);
												}

												$CentroDb 	= 'R0000';
												$ProyectoDb = 'N000';

												if ($DetallaCentroDb) {
													if (left($regDispersion['Centro'], 1) == 'S') {
														$CentroDb = '04099'; 
														$ProyectoDb = $regDispersion['Centro']; 
													}
													else $CentroDb = $regDispersion['Centro'];
												}

												$CentroCr 	= 'R0000';
												$ProyectoCr = 'N000';

												if ($DetallaCentroCr) {
													if (left($regDispersion['Centro'], 1) == 'S') {
														$CentroCr = '04099'; 
														$ProyectoCr = $regDispersion['Centro']; 
													}
													else $CentroCr = $regDispersion['Centro'];
												}

												$datos = array('Documento' 	=> $Documento, 
															'Secuencia' 	=> $Secuencia, 
															'CuentaDb' 		=> $CuentaDb, 
															'CentroDb' 		=> $CentroDb, 
															'ProyectoDb' 	=> $ProyectoDb, 
															'CuentaCr' 		=> $CuentaCr, 
															'CentroCr' 		=> $CentroCr, 
															'ProyectoCr' 	=> $ProyectoCr, 
															'Valor' 		=> $Valor, 
															'FechaFinalP' 	=> $FechaFinalP, 
															'NombreCuenta' 	=> $NombreCuenta, 
															'Reference2' 	=> $Reference2, 
															'CodigoSAPDb' 	=> $CodigoSAPDb, 
															'CodigoSAP2Db' 	=> $CodigoSAP2Db, 
															'CodigoSAPCr' 	=> $CodigoSAPCr, 
															'CodigoSAP2Cr' 	=> $CodigoSAP2Cr,
															'IdEmpleado' 	=> $IdEmpleado,
															'IdPeriodo' 	=> $IdPeriodo,
															'IdComprobante' => $IdComprobante,
															'IdLogPila' 	=> 0
														);

												$Secuencia = $this->model->guardarRegistroSAP($datos);
											}
										}
										else $EsDispersable = FALSE;
									}
								
									if (! $EsDispersable) {
										if ($regComprobante['TipoDocumento'] == 'NOM')
											$Valor = round($ValorNomina * $Porcentaje / 100, 0);
										else {
											if ($RegimenCesantias == 'SALARIO INTEGRAL')
												$Factor = .7;
											else
												$Factor = 1;

											if ($Porcentaje == 0)
												$Valor = round($Base * $Factor * $Porcentaje2 / 100, 0);
											else
												$Valor = round($ValorNomina * $Factor * $Porcentaje / 100, 0);
										}

										$CentroDb = 'R0000';
										$ProyectoDb = 'N000';

										if ($DetallaCentroDb) {
											if (! empty($Proyecto)) {
												$CentroDb = '04099'; 
												$ProyectoDb = $Proyecto; 
											}
											else $CentroDb = $Centro;
										}

										$CentroCr = 'R0000';
										$ProyectoCr = 'N000';

										if ($DetallaCentroCr) {
											if (! empty($Proyecto)) {
												$CentroCr = '04099'; 
												$ProyectoCr = $Proyecto; 
											}
											else $CentroCr = $Centro;
										}

										$datos = array('Documento' 	=> $Documento, 
													'Secuencia' 	=> $Secuencia, 
													'CuentaDb' 		=> $CuentaDb, 
													'CentroDb' 		=> $CentroDb, 
													'ProyectoDb' 	=> $ProyectoDb, 
													'CuentaCr' 		=> $CuentaCr, 
													'CentroCr' 		=> $CentroCr, 
													'ProyectoCr' 	=> $ProyectoCr, 
													'Valor' 		=> $Valor, 
													'FechaFinalP' 	=> $FechaFinalP, 
													'NombreCuenta' 	=> $NombreCuenta, 
													'Reference2'	=> $Reference2, 
													'CodigoSAPDb' 	=> $CodigoSAPDb, 
													'CodigoSAP2Db' 	=> $CodigoSAP2Db, 
													'CodigoSAPCr' 	=> $CodigoSAPCr, 
													'CodigoSAP2Cr' 	=> $CodigoSAP2Cr, 
													'IdEmpleado' 	=> $IdEmpleado,
													'IdPeriodo' 	=> $IdPeriodo,
													'IdComprobante' => $IdComprobante,
													'IdLogPila' 	=> 0
												);

										$Secuencia = $this->model->guardarRegistroSAP($datos);
									}
								}
							}

							if (($TipoContrato == 'APRENDIZAJE - ETAPA PRÁCTICA' OR 
								$TipoContrato == 'APRENDIZAJE - ETAPA LECTIVA' OR 
								$TipoContrato == 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' OR 
								$TipoContrato == 'PASANTÍA') AND 
								$regComprobante['TipoDocumento'] <> 'NOM'
							) {
								$query = <<<EOD
									SELECT AUXILIARES.*, 
											MAYORES.TipoRetencion, 
											PARAMETROS.Detalle AS NombreTipoRegistroAuxiliar 
										FROM AUXILIARES 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS.Id 
										WHERE PARAMETROS.Detalle = 'ES APORTE DE SALUD';
								EOD;

								$regConcepto = $this->model->leer($query);

								if ($regConcepto)
									$IdConcepto = $regConcepto['id'];
								else
									$IdConcepto = 0;

								if ($IdComprobante > 0) {
									$query = <<<EOD
										SELECT COMPROBANTES.IdTipoDoc, 
												TIPODOC.TipoDocumento, 
												TIPODOC.Nombre AS NombreComprobante, 
												TIPODOC.Secuencia, 
												COMPROBANTES.Detalle, 
												COMPROBANTES.CuentaDb, 
												COMPROBANTES.DetallaCentroDb, 
												COMPROBANTES.CuentaCr, 
												COMPROBANTES.DetallaCentroCr, 
												COMPROBANTES.Porcentaje, 
												PARAMETROS.Detalle AS TipoTercero, 
												COMPROBANTES.Exonerable 
											FROM COMPROBANTES 
												INNER JOIN TIPODOC ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
												INNER JOIN AUXILIARES ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
												INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
												INNER JOIN PARAMETROS ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
											WHERE COMPROBANTES.IdTipoDoc = $IdComprobante AND 
												COMPROBANTES.IdConcepto = $IdConcepto AND 
												COMPROBANTES.TipoEmpleado = $TipoEmpleado 
												ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar;
									EOD;
								}
								else {
									$query = <<<EOD
										SELECT COMPROBANTES.IdTipoDoc, 
												TIPODOC.TipoDocumento, 
												TIPODOC.Nombre AS NombreComprobante, 
												TIPODOC.Secuencia, 
												COMPROBANTES.Detalle, 
												COMPROBANTES.CuentaDb, 
												COMPROBANTES.DetallaCentroDb, 
												COMPROBANTES.CuentaCr, 
												COMPROBANTES.DetallaCentroCr, 
												COMPROBANTES.Porcentaje, 
												PARAMETROS.Detalle AS TipoTercero, 
												COMPROBANTES.Exonerable 
											FROM COMPROBANTES  
												INNER JOIN TIPODOC ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
												INNER JOIN AUXILIARES ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
												INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
												LEFT JOIN PARAMETROS ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
											WHERE COMPROBANTES.IdConcepto = $IdConcepto AND 
												COMPROBANTES.TipoEmpleado = $TipoEmpleado 
											ORDER BY COMPROBANTES.Id, MAYORES.Mayor, AUXILIARES.Auxiliar;
									EOD;
								}

								$datosComprobante = $this->model->listar($query);

								if ($datosComprobante) {
									for ($j = 0; $j < count($datosComprobante); $j++) {
										$regComprobante 	= $datosComprobante[$j];

										$Documento 			= $Referencia . str_pad($Periodo, 2, '0', STR_PAD_LEFT);
										$NombreCuenta 		= utf8_decode($regComprobante['Detalle']);

										if ($regComprobante['TipoDocumento'] <> 'NOM' AND $regComprobante['TipoDocumento'] <> 'PARAF')
											$NombreCuenta 	= utf8_decode($regComprobante['NombreComprobante']);

										if ($regAcumulados['Imputacion'] == 'PAGO') {
											$CuentaDb 			= str_pad($regComprobante['CuentaDb'], 12, '0', STR_PAD_RIGHT);
											$DetallaCentroDb 	= $regComprobante['DetallaCentroDb'] == 1 ? TRUE : FALSE;
											$CuentaCr 			= str_pad($regComprobante['CuentaCr'], 12, '0', STR_PAD_RIGHT);
											$DetallaCentroCr 	= $regComprobante['DetallaCentroCr'] == 1 ? TRUE : FALSE;
										}
										else {
											$CuentaDb 			= str_pad($regComprobante['CuentaCr'], 12, '0', STR_PAD_RIGHT);
											$DetallaCentroDb 	= $regComprobante['DetallaCentroCr'] == 1 ? TRUE : FALSE;
											$CuentaCr 			= str_pad($regComprobante['CuentaDb'], 12, '0', STR_PAD_RIGHT);
											$DetallaCentroCr 	= $regComprobante['DetallaCentroDb'] == 1 ? TRUE : FALSE;
										}

										$Porcentaje 		= $regComprobante['Porcentaje'];
										$TipoTercero 		= $regComprobante['TipoTercero'];

										if ($regComprobante['Detalle'] == 'SALUD - EMPLEADO')
											continue;

										if ($regComprobante['Detalle'] == 'CAJA DE COMPENSACION')
											continue;

										if ($regComprobante['Detalle'] == 'SALUD - EMPRESA') {
											if ($ValorNomina < $SueldoMinimo)
												$ValorNomina = $SueldoMinimo;

											$Porcentaje = 16;
										}

										$CodigoSAPDb = '';
										$CodigoSAP2Db = '';
										$CodigoSAPCr = '';
										$CodigoSAP2Cr = '';

										switch ($TipoTercero) {
											case 'DETALLA POR EMPLEADO':
												$CodigoSAPDb = $regEmpleado['codigosap'];
												$CodigoSAPCr = $regEmpleado['codigosap'];
												break;
											case 'DETALLA POR EPS - EMPLEADO':
												$reg = $IdEPS > 0 ? getRegistro('TERCEROS', $IdEPS) : NULL;
												if ($CuentaDb == $CuentaNomina) {
													$CodigoSAPDb = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
													$CodigoSAP2Db = $regEmpleado['codigosap'];
												}

												if ($CuentaCr == $CuentaNomina) {
													$CodigoSAPCr = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
													$CodigoSAP2Cr = $regEmpleado['codigosap'];
												}
												break;
											case 'DETALLA POR ARL - EMPLEADO':
												$reg = $IdARL > 0 ? getRegistro('TERCEROS', $IdARL) : NULL;

												if ($CuentaDb == $CuentaNomina) {
													$CodigoSAPDb = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
													$CodigoSAP2Db = $regEmpleado['codigosap'];
												}

												if ($CuentaCr == $CuentaNomina) {
													$CodigoSAPCr = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
													$CodigoSAP2Cr = $regEmpleado['codigosap'];
												}
												break;
											case 'DETALLA POR FONDO DE CESANTÍAS - EMPLEADO':
												$reg = $IdFondoCesantias > 0 ? getRegistro('TERCEROS', $IdFondoCesantias) : NULL;

												if ($CuentaDb == $CuentaNomina) {
													$CodigoSAPDb = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
													$CodigoSAP2Db = $regEmpleado['codigosap'];
												}

												if ($CuentaCr == $CuentaNomina) {
													$CodigoSAPCr = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
													$CodigoSAP2Cr = $regEmpleado['codigosap'];
												}
												break;
											case 'DETALLA POR FONDO DE PENSIONES - EMPLEADO':
												$reg = $IdFondoPensiones > 0 ? getRegistro('TERCEROS', $IdFondoPensiones) : NULL;

												if ($CuentaDb == $CuentaNomina) {
													$CodigoSAPDb = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
													$CodigoSAP2Db = $regEmpleado['codigosap'];
												}

												if ($CuentaCr == $CuentaNomina) {
													$CodigoSAPCr = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
													$CodigoSAP2Cr = $regEmpleado['codigosap'];
												}
												break;
											case 'DETALLA POR CCF - EMPLEADO':
												$reg = $IdCajaCompensacion > 0 ? getRegistro('TERCEROS', $IdCajaCompensacion) : NULL;

												if ($CuentaDb == $CuentaNomina) {
													$CodigoSAPDb = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
													$CodigoSAP2Db = $regEmpleado['codigosap'];
												}

												if ($CuentaCr == $CuentaNomina) {
													$CodigoSAPCr = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
													$CodigoSAP2Cr = $regEmpleado['codigosap'];
												}
												break;
											case 'DETALLA POR TERCERO - EMPLEADO':
												$reg = $IdTercero > 0 ? getRegistro('TERCEROS', $IdTercero) : NULL;
												if ($regComprobante['Detalle'] == 'SENA') $reg = $regSENA;
												elseif ($regComprobante['Detalle'] == 'ICBF') $reg = $regICBF;

												if ($CuentaDb == $CuentaNomina) {
													$CodigoSAPDb = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Db = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPDb = $reg['codigosap'];
													$CodigoSAP2Db = $regEmpleado['codigosap'];
												}

												if ($CuentaCr == $CuentaNomina) {
													$CodigoSAPCr = $regEmpleado['codigosap'];
													if ($reg and $reg['codigosap'] <> '') $CodigoSAP2Cr = $reg['codigosap'];
												} else {
													if ($reg and $reg['codigosap'] <> '') $CodigoSAPCr = $reg['codigosap'];
													$CodigoSAP2Cr = $regEmpleado['codigosap'];
												}
												break;
										}

										if ($Porcentaje == 0) {
											$Porcentaje2 = getRegistro('PARAMETROS', $regEmpleado['nivelriesgo'])['valor2'];
											$Base = $ValorNomina;
										}

										if ($regConcepto['tiporegistroauxiliar'] > 0)  {
											$TipoRegistroAuxiliar = getRegistro('PARAMETROS', $regConcepto['tiporegistroauxiliar'])['detalle'];
										}

										switch ($regComprobante['TipoDocumento']) {
											case 'NOM':
												$Reference2 = "INTERFAZ NOMINA $Mes DE $Ano";
												break;
											case 'PARAF':
												$Reference2 = "INTERFAZ PARAFISCALES $Mes DE $Ano";
												break;
											default:
												$Reference2 = "INTERFAZ PROVISIONES $Mes DE $Ano";
												break;
										}
				
										if ($EsDispersable) {
											$query = <<<EOD
												SELECT CENTROS.Centro, 
														CENTROS.TipoEmpleado, 
														DISPERSIONPORCENTRO.Porcentaje 
													FROM DISPERSIONPORCENTRO 
														INNER JOIN CENTROS 
															ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
														INNER JOIN PERIODOS 
															ON DISPERSIONPORCENTRO.IdPeriodo = PERIODOS.Id 
													WHERE PERIODOS.FechaInicial >= '$FechaInicial' AND 
														PERIODOS.FechaFinal <= '$FechaFinal' AND 
														DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado;
											EOD;

											$datosDispersion = $this->model->listar($query);

											if ($datosDispersion) {
												for ($k = 0; $k < count($datosDispersion); $k++)  { 
													$regDispersion = $datosDispersion[$k];

													$IdTipoDoc = $regComprobante['IdTipoDoc'];
													$TipoEmpleado = $regDispersion['TipoEmpleado'];
													$Porcentaje3 = $regComprobante['Porcentaje'];

													$query = <<<EOD
														SELECT COMPROBANTES.CuentaDb, 
																COMPROBANTES.CuentaCr 
															FROM COMPROBANTES  
																INNER JOIN TIPODOC 
																	ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
																INNER JOIN AUXILIARES 
																	ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
																INNER JOIN MAYORES 
																	ON AUXILIARES.IdMayor = MAYORES.Id 
																LEFT JOIN PARAMETROS 
																	ON COMPROBANTES.TipoTercero = PARAMETROS.Id  
															WHERE COMPROBANTES.IdTipoDoc = $IdTipoDoc AND 
																COMPROBANTES.IdConcepto = $IdConcepto AND 
																COMPROBANTES.TipoEmpleado = $TipoEmpleado AND 
																COMPROBANTES.Porcentaje = $Porcentaje3 
															ORDER BY COMPROBANTES.Id, MAYORES.Mayor, AUXILIARES.Auxiliar;
													EOD;

													$regDispersion2 = $this->model->leer($query);

													$CuentaDb = $regDispersion2['CuentaDb'];
													$CuentaCr = $regDispersion2['CuentaCr'];

													if ($RegimenCesantias == 'SALARIO INTEGRAL') $Factor = .7;
													else $Factor = 1;

													if ($Porcentaje == 0)
														$Valor = round($Base * $Factor * $Porcentaje2 / 100  * $regDispersion['Porcentaje'] / 100, 0);
													else
														$Valor = round($Base * $Factor * $Porcentaje / 100 * $regDispersion['Porcentaje'] / 100, 0);

													$CentroDb 	= 'R0000';
													$ProyectoDb = 'N000';

													if ($DetallaCentroDb) {
														if (left($regDispersion['Centro'], 1) == 'S') {
															$CentroDb = '04099'; 
															$ProyectoDb = $regDispersion['Centro']; 
														} else $CentroDb = $regDispersion['Centro'];
													}

													$CentroCr 	= 'R0000';
													$ProyectoCr = 'N000';
													if ($DetallaCentroCr) {
														if (left($regDispersion['Centro'], 1) == 'S') {
															$CentroCr = '04099'; 
															$ProyectoCr = $regDispersion['Centro']; 
														} else $CentroCr = $regDispersion['Centro'];
													}

													$datos = array('Documento' 	=> $Documento, 
																'Secuencia' 	=> $Secuencia, 
																'CuentaDb' 		=> $CuentaDb, 
																'CentroDb' 		=> $CentroDb, 
																'ProyectoDb' 	=> $ProyectoDb, 
																'CuentaCr' 		=> $CuentaCr, 
																'CentroCr' 		=> $CentroCr, 
																'ProyectoCr' 	=> $ProyectoCr, 
																'Valor' 		=> $Valor, 
																'FechaFinalP' 	=> $FechaFinalP, 
																'NombreCuenta' 	=> $NombreCuenta, 
																'Reference2' 	=> $Reference2, 
																'CodigoSAPDb' 	=> $CodigoSAPDb, 
																'CodigoSAP2Db' 	=> $CodigoSAP2Db, 
																'CodigoSAPCr' 	=> $CodigoSAPCr, 
																'CodigoSAP2Cr' 	=> $CodigoSAP2Cr, 
																'IdEmpleado' 	=> $IdEmpleado,
																'IdPeriodo' 	=> $IdPeriodo,
																'IdComprobante' => $IdComprobante,
																'IdLogPila' 	=> 0
															);

													$Secuencia = $this->model->guardarRegistroSAP($datos);
												}
											}
											else $EsDispersable = FALSE;
										}
									
										if (! $EsDispersable) {
											if ($regComprobante['TipoDocumento'] == 'PARAF') {
												if ($Porcentaje == 0)
													$Valor = round($Base * $Porcentaje2 / 100, -2);
												else
													$Valor = round($Base * $Porcentaje / 100, -2);
											} else {
												if ($Porcentaje == 0)
													$Valor = round($ValorNomina * $Porcentaje2 / 100, -2);
												else
													$Valor = round($ValorNomina * $Porcentaje / 100, -2);
											}

											$CentroDb = 'R0000';
											$ProyectoDb = 'N000';

											if ($DetallaCentroDb) {
												if (! empty($Proyecto)) {
													$CentroDb = '04099'; 
													$ProyectoDb = $Proyecto; 
												} else $CentroDb = $Centro;
											}

											$CentroCr = 'R0000';
											$ProyectoCr = 'N000';

											if ($DetallaCentroCr) {
												if (! empty($Proyecto)) {
													$CentroCr = '04099'; 
													$ProyectoCr = $Proyecto; 
												}
												else $CentroCr = $Centro;
											}

											$datos = array('Documento' 	=> $Documento, 
														'Secuencia' 	=> $Secuencia, 
														'CuentaDb' 		=> $CuentaDb, 
														'CentroDb' 		=> $CentroDb, 
														'ProyectoDb' 	=> $ProyectoDb, 
														'CuentaCr' 		=> $CuentaCr, 
														'CentroCr' 		=> $CentroCr, 
														'ProyectoCr' 	=> $ProyectoCr, 
														'Valor' 		=> $Valor, 
														'FechaFinalP' 	=> $FechaFinalP, 
														'NombreCuenta' 	=> $NombreCuenta, 
														'Reference2'	=> $Reference2, 
														'CodigoSAPDb' 	=> $CodigoSAPDb, 
														'CodigoSAP2Db' 	=> $CodigoSAP2Db, 
														'CodigoSAPCr' 	=> $CodigoSAPCr, 
														'CodigoSAP2Cr' 	=> $CodigoSAP2Cr, 
														'IdEmpleado' 	=> $IdEmpleado,
														'IdPeriodo' 	=> $IdPeriodo,
														'IdComprobante' => $IdComprobante,
														'IdLogPila' 	=> 0
													);

											$Secuencia = $this->model->guardarRegistroSAP($datos);
										}
									}

								}
							}
						}

						$query = <<<EOD
							UPDATE DETALLESSAP 
								SET Debit = DETALLESSAP.Debit - DETALLESSAP.Credit, 
									Credit = 0 
								WHERE DETALLESSAP.Debit <> 0 AND 
									DETALLESSAP.Credit <> 0 AND 
									DETALLESSAP.Debit > DETALLESSAP.Credit
									AND IdPeriodo = $IdPeriodo
									AND IdComprobante = $IdComprobante;
						EOD;

						$this->model->query($query);

						$query = <<<EOD
							UPDATE DETALLESSAP 
								SET Credit = DETALLESSAP.Credit - DETALLESSAP.Debit, 
									Debit = 0 
								WHERE DETALLESSAP.Debit <> 0 AND 
									DETALLESSAP.Credit <> 0 AND 
									DETALLESSAP.Credit > DETALLESSAP.Debit
									AND IdPeriodo = $IdPeriodo
									AND IdComprobante = $IdComprobante;
						EOD;

						$this->model->query($query);

						$query = <<<EOD
							DELETE FROM DETALLESSAP
								WHERE DETALLESSAP.Debit = DETALLESSAP.Credit AND
									DETALLESSAP.Debit > 0
									AND IdPeriodo = $IdPeriodo
									AND IdComprobante = $IdComprobante
						EOD;

						$this->model->query($query);

						if (empty($data['mensajeError'])) 
						{
							header('Location: ' . SERVERURL . '/contabilizacionSAP/lista/1');
							exit();
						}
					}
				} else $data['mensajeError'] .= 'Debe seleccionar el comprobante que desea generar.<br>';
			}

            $_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/contabilizacionSAP/parametros';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/contabilizacionSAP/parametros';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/contabilizacionSAP/lista/1';

			if ($data) 
				$this->views->getView($this, 'parametros', $data);
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
?>
