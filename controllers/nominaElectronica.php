<?php
	require_once('./templates/vendor/autoload.php');

	class nominaElectronica extends Controllers
	{
		private $DIR_NE = 'documentsNE';
		private $token = null;

		public function parametrosOld()
		{
			set_time_limit(0);

			// SE LEEN EL PERIODO DEFINIDO PARA LIQUIDACION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='ReferenciaEnLiquidacion'");
			$reg2 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='PeriodicidadEnLiquidacion'");
			$reg3 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='PeriodoEnLiquidacion'");
			$reg4 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='CicloEnLiquidacion'");
			$reg5 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='ValorUVT'");
			$reg6 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='TipoPrestamo' AND PARAMETROS.Detalle='PRÉSTAMO EMPRESA'");

			$Referencia 		= $reg1['valor'];
			$IdPeriodicidad 	= $reg2['valor'];

			$Periodicidad		= getRegistro('PARAMETROS', $IdPeriodicidad)['detalle'];
			$cPeriodicidad		= substr($Periodicidad, 0, 1);
			$IdPeriodo			= $reg3['valor'];

			$regPeriodo			= getRegistro('PERIODOS', $IdPeriodo);
			$Periodo			= $regPeriodo['periodo'];

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Empleado' 	=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'Periodo' 	=> isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : $Periodo,
					),
				'mensajeError' => ''
			);

			if	(isset($_REQUEST['Empleado']))
			{
				if	(! empty($_REQUEST['Empleado']))
					$Empleado = $_REQUEST['Empleado'];
				else
					$Empleado = '';

				if	(! empty($_REQUEST['Periodo']))
					$Periodo = $_REQUEST['Periodo'];
				else
					$Periodo = $Periodo;

				if	(empty($data['mensajeError']))
				{
					$IdPeriodo = getId('PERIODOS', "PERIODOS.Referencia = '$Referencia' AND PERIODOS.Periodo = $Periodo");
					$regPeriodo			= getRegistro('PERIODOS', $IdPeriodo);

					$NitEmpresa			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='NitEmpresa'")['detalle'];
					$DVEmpresa 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='DigitoVerificacionEmpresa'")['detalle'];
					$DireccionEmpresa 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='DireccionEmpresa'")['detalle'];

					// EMPLEADOS CON TOTALES
					$query = <<<EOD
						SELECT EMPLEADOS.Documento, 
								EMPLEADOS.Id AS IdEmpleado, 
								SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS Devengos, 
								SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS Deducciones 
							FROM ACUMULADOS 
								INNER JOIN EMPLEADOS 
									ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
								INNER JOIN AUXILIARES 
									ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.Imputacion = PARAMETROS.Id 
							WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
								EMPLEADOS.SecuenciaNE = 0 
					EOD;

					if (! empty($Empleado))
					{
						$query .= <<<EOD
							AND EMPLEADOS.Documento = '$Empleado' 
						EOD;
					}

					$query = <<<EOD
						SELECT TOP 500 tbl.* FROM (
							$query
							GROUP BY EMPLEADOS.Documento, EMPLEADOS.Id
						) as tbl
						ORDER BY tbl.Documento;
					EOD;

					$empleados = $this->model->listar($query);

					if ($empleados)
					{
						$dirName = $this->DIR_NE . '/' . $regPeriodo['referencia'] . '/' . $regPeriodo['periodo'] . '/';
						if	( ! is_dir($dirName) ) mkdir($dirName, 0777, true);

						for ($i = 0; $i < count($empleados); $i++)
						{
							$IdEmpleado = $empleados[$i]['IdEmpleado'];

							$regEmpleado = getRegistro('EMPLEADOS', $IdEmpleado);

							if ($regEmpleado)
							{
								$IdEmpleado 	= $regEmpleado['id'];
								$TipoContrato 	= getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['valor'];

								switch ($TipoContrato)
								{
									case 1:
									case 2:
										$TipoContrato = 1;
										break;
									case 3:
									case 5:
									case 7:
										$TipoContrato = 19;
										break;
									case 4:
										$TipoContrato = 12;
										break;
									default:
										$TipoContrato = 1;
										break;
								}

								$TipoIdentificacion = getRegistro('PARAMETROS', $regEmpleado['tipoidentificacion'])['valor'];

								switch ($TipoIdentificacion)
								{
									case 1:
										$TipoIdentificacion = 31;
										break;
									case 2:
										$TipoIdentificacion = 13;
										break;
									case 3:
										$TipoIdentificacion = 50;
										break;
									case 4:
										$TipoIdentificacion = 22;
										break;
									case 5:
										$TipoIdentificacion = 11;
										break;
									case 6:
										$TipoIdentificacion = 12;
										break;
									case 7:
										$TipoIdentificacion = 41;
										break;
									case 8:
									case 9:
										$TipoIdentificacion = 47;
										break;
									default:
										$TipoIdentificacion = 13;
										break;
								}

								$RegimenCesantias = getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['valor'];

								if ($RegimenCesantias == 2)
									$RegimenCesantias = 'true';
								else
									$RegimenCesantias = 'false';

								$TipoContrato2 = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['valor'];

								switch ($TipoContrato2)
								{
									case 1:
										$TipoContrato2 = 2;
										break;
									case 2:
										$TipoContrato2 = 1;
										break;
									case 3:
									case 6:
									case 7:
										$TipoContrato2 = 4;
										break;
									case 4:
										$TipoContrato2 = 3;
										break;
									case 5:
										$TipoContrato2 = 5;
										break;
									default:
										$TipoContrato2 = 2;
										break;
								}

								$FechaIngreso 			= $regEmpleado['fechaingreso'];
								$FechaRetiro 			= is_null($regEmpleado['fecharetiro']) ? '' : $regEmpleado['fecharetiro'];
								$FechaLiquidacionInicio = $regPeriodo['fechainicial'];
								$FechaLiquidacionFin 	= $regPeriodo['fechafinal'];
								$TiempoLaborado 		= dias360(date('Y-m-d'), $regEmpleado['fechaingreso']);
								$FechaGeneracion 		= date('Y-m-d');

								$SubtipoCotizante		= $regEmpleado['subtipocotizante'];
								$Documento				= $regEmpleado['documento'];
								$Apellido1				= $regEmpleado['apellido1'];
								$Apellido2				= $regEmpleado['apellido2'];

								if (empty($Apellido2))
									$Apellido2 = 'N/A';

								$Nombre1				= $regEmpleado['nombre1'];

								if (empty($Nombre1))
									$Nombre1 = 'N/A';
								
								$Nombre2				= $regEmpleado['nombre2']; 

								$NombreEmpleado			= "$Apellido1 $Apellido2 $Nombre1 $Nombre2";
								$DireccionEmpleado		= $regEmpleado['direccion'];

								if (empty($DireccionEmpleado))
									$DireccionEmpleado = $DireccionEmpresa;

								$SueldoBasico			= $regEmpleado['sueldobasico'];
								$CodigoSAP				= $regEmpleado['codigosap'];
								$CuentaBancaria 		= $regEmpleado['cuentabancaria'];
								
								$Banco = getRegistro('BANCOS', $regEmpleado['idbanco'])['nombre'];

								if (! $Banco)
									$Banco = '';

								$TipoCuentaBancaria = getRegistro('PARAMETROS', $regEmpleado['tipocuentabancaria'])['detalle'];

								$Usuario 	= 'ADMIN_COMWARE';

								$Secuencia = getRegistro('PERIODOS', $IdPeriodo)['secuenciane'] + 1;

								$Consecutivo = str_pad($Periodo, 2, '0', STR_PAD_LEFT) . str_pad($Secuencia, 4, '0', STR_PAD_LEFT);

								$archivoXML = <<<EOD
									<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
										<soapenv:Header/>
										<soapenv:Body>
											<tem:EnviarNominaIndividual>
												<tem:Clave>/QOkJKubBxGTo0WX5xF9YK4XrAwSzlVjQxA3EuXCTXdY6/g6BvLp72FhoR5+sArzzZrFwYrdNY+Z9oTInX0dzQnVi1B2Oa0po94PnnhuxLg=</tem:Clave>
												<tem:NominaIndividual>
													<tem:UsuarioTransaccionERP>$Usuario</tem:UsuarioTransaccionERP>
													<tem:NombreIntegracion>COMWARE</tem:NombreIntegracion>
													<tem:Consecutivo>$Consecutivo</tem:Consecutivo>
													<tem:Prefijo>NOMI</tem:Prefijo>
													<tem:Periodo>
														<tem:FechaIngreso>$FechaIngreso</tem:FechaIngreso>
								EOD;

								if (! empty($FechaRetiro))
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:FechaRetiro>$FechaRetiro</tem:FechaRetiro>
									EOD;
								}

								$archivoXML .= PHP_EOL;

								$archivoXML .= <<<EOD
														<tem:FechaLiquidacionInicio>$FechaLiquidacionInicio</tem:FechaLiquidacionInicio>
														<tem:FechaLiquidacionFin>$FechaLiquidacionFin</tem:FechaLiquidacionFin>
														<tem:TiempoLaborado>$TiempoLaborado</tem:TiempoLaborado>
														<tem:FechaGen>$FechaLiquidacionFin</tem:FechaGen>
													</tem:Periodo>
													<tem:LugarGeneracionXML>
														<tem:Pais>CO</tem:Pais>
														<tem:DepartamentoEstado>11</tem:DepartamentoEstado>
														<tem:MunicipioCiudad>11001</tem:MunicipioCiudad>
													</tem:LugarGeneracionXML>
													<tem:InformacionGeneral>
														<tem:TipoXML>102</tem:TipoXML>
														<tem:FechaGen>$FechaGeneracion</tem:FechaGen>
														<tem:PeriodoNomina>5</tem:PeriodoNomina>
														<tem:TipoMoneda>COP</tem:TipoMoneda>
													</tem:InformacionGeneral>
													<tem:Empleador>
														<tem:RazonSocial>COMWARE S.A.</tem:RazonSocial>
														<tem:NIT>$NitEmpresa</tem:NIT>
														<tem:DV>$DVEmpresa</tem:DV>
														<tem:Pais>CO</tem:Pais>
														<tem:DepartamentoEstado>11</tem:DepartamentoEstado>
														<tem:MunicipioCiudad>11001</tem:MunicipioCiudad>
														<tem:Direccion>$DireccionEmpresa</tem:Direccion>
													</tem:Empleador>
													<tem:Trabajador>
														<tem:TipoTrabajador>$TipoContrato</tem:TipoTrabajador>
														<tem:SubtipoTrabajador>$SubtipoCotizante</tem:SubtipoTrabajador>
														<tem:AltoRiesgoPension>false</tem:AltoRiesgoPension>
														<tem:TipoDocumento>$TipoIdentificacion</tem:TipoDocumento>
														<tem:NumeroDocumento>$Documento</tem:NumeroDocumento>
														<tem:PrimerApellido>$Apellido1</tem:PrimerApellido>
														<tem:SegundoApellido>$Apellido2</tem:SegundoApellido>
														<tem:PrimerNombre>$Nombre1</tem:PrimerNombre>
														<tem:OtrosNombres>$Nombre2</tem:OtrosNombres>
														<tem:LugarTrabajoPais>CO</tem:LugarTrabajoPais>
														<tem:LugarTrabajoDepartamentoEstado>11</tem:LugarTrabajoDepartamentoEstado>
														<tem:LugarTrabajoMunicipioCiudad>11001</tem:LugarTrabajoMunicipioCiudad>
														<tem:LugarTrabajoDireccion>$DireccionEmpleado</tem:LugarTrabajoDireccion>
														<tem:SalarioIntegral>$RegimenCesantias</tem:SalarioIntegral>
														<tem:TipoContrato>$TipoContrato2</tem:TipoContrato>
														<tem:Sueldo>$SueldoBasico</tem:Sueldo>
														<tem:CodigoTrabajador>$CodigoSAP</tem:CodigoTrabajador>
													</tem:Trabajador>
													<tem:Pago>
														<tem:Forma>1</tem:Forma>
														<tem:Metodo>46</tem:Metodo>
														<tem:Banco>$Banco</tem:Banco>
														<tem:TipoCuenta>$TipoCuentaBancaria</tem:TipoCuenta>
														<tem:NumeroCuenta>$CuentaBancaria</tem:NumeroCuenta>
													</tem:Pago>
													<tem:FechaPago>
														<tem:FechaPago>$FechaLiquidacionFin</tem:FechaPago>
													</tem:FechaPago>
								EOD;

								$TotalDevengos		= 0;
								$TotalDeducciones	= 0;

								// PARAMETROS DEVENGADOS TYPE
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '01' AND 
											AUXILIARES.Auxiliar IN ('001', '002', '003', '005', '006', '007', '051');
									EOD;

								$regNomina=$this->model->leer($query);

								if ($regNomina)
								{
									$DiasTrabajados 	= $regNomina['Horas'] / 8;
									$SueldoTrabajado 	= $regNomina['Valor'];
								}
								else
								{
									$DiasTrabajados 	= "0";
									$SueldoTrabajado 	= "0.00";
								}

								$TotalDevengos += $SueldoTrabajado;

								$archivoXML .= PHP_EOL;

								if ($SueldoTrabajado > 0)
								{
									$archivoXML .= <<<EOD
														<tem:Devengados>
															<tem:DiasTrabajados>$DiasTrabajados</tem:DiasTrabajados>
															<tem:SueldoTrabajado>$SueldoTrabajado</tem:SueldoTrabajado>
									EOD;
								}
								else
								{
									$archivoXML .= <<<EOD
														<tem:Devengados>
															<tem:DiasTrabajados>$DiasTrabajados</tem:DiasTrabajados>
									EOD;
								}

								// TRANSPORTE
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '04' AND AUXILIARES.Auxiliar = '001';
								EOD;

								$regNomina=$this->model->leer($query);

								$Transporte = $regNomina['Valor'];

								if ($Transporte > 0)
								{
									$archivoXML .= PHP_EOL;
			
									$archivoXML .= <<<EOD
															<tem:Transporte>
																<tem:AuxilioTransporte>$Transporte</tem:AuxilioTransporte>
															</tem:Transporte>
									EOD;
					
									$TotalDevengos += $Transporte;
								}

								// HORAS EXTRAS DIURNAS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '02' AND 
											AUXILIARES.Auxiliar IN ('001', '051');
								EOD;

								$regNomina=$this->model->leer($query);

								$HED			= $regNomina['Horas'];
								$ValorHED 		= $regNomina['Valor'];

								if ($ValorHED > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HED>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HED</tem:Cantidad>
																<tem:Porcentaje>25</tem:Porcentaje>
																<tem:Pago>$ValorHED</tem:Pago>
															</tem:HED>
									EOD;
									
									$TotalDevengos += $ValorHED;
								}

								// HORAS EXTRAS NOCTURNAS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '02' AND 
											AUXILIARES.Auxiliar IN ('002', '052');
								EOD;

								$regNomina=$this->model->leer($query);

								$HEN			= $regNomina['Horas'];
								$ValorHEN 		= $regNomina['Valor'];

								if ($ValorHEN > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HEN>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HEN</tem:Cantidad>
																<tem:Porcentaje>75</tem:Porcentaje>
																<tem:Pago>$ValorHEN</tem:Pago>
															</tem:HEN>
									EOD;
									
									$TotalDevengos += $ValorHEN;
								}

								// RECARGO NOCTURNO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '03' AND 
											AUXILIARES.Auxiliar IN ('001', '051');
								EOD;

								$regNomina	= $this->model->leer($query);

								$HRN		= $regNomina['Horas'];
								$ValorHRN 	= $regNomina['Valor'];

								if ($ValorHRN > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HRN>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HRN</tem:Cantidad>
																<tem:Porcentaje>35</tem:Porcentaje>
																<tem:Pago>$ValorHRN</tem:Pago>
															</tem:HRN>
									EOD;
									
									$TotalDevengos += $ValorHRN;
								}

								// HORAS EXTRAS DIURNAS FESTIVAS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '02' AND 
											AUXILIARES.Auxiliar IN ('003', '053');
								EOD;

								$regNomina	= $this->model->leer($query);

								$HEDDF		= $regNomina['Horas'];
								$ValorHEDDF	= $regNomina['Valor'];

								if ($ValorHEDDF > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HEDDF>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HEDDF</tem:Cantidad>
																<tem:Porcentaje>100</tem:Porcentaje>
																<tem:Pago>$ValorHEDDF</tem:Pago>
															</tem:HEDDF>
									EOD;
									
									$TotalDevengos += $ValorHEDDF;
								}

								// HORAS RECARGO DIURNO DOMINICAL FESTIVAS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '02' AND (
												AUXILIARES.Auxiliar = '005' OR 
												AUXILIARES.Auxiliar = '007'
											);
								EOD;

								$regNomina	= $this->model->leer($query);

								$HRDDF		= $regNomina['Horas'];
								$ValorHRDDF	= $regNomina['Valor'];

								if ($ValorHRDDF > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HRDDF>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HRDDF</tem:Cantidad>
																<tem:Porcentaje>75</tem:Porcentaje>
																<tem:Pago>$ValorHRDDF</tem:Pago>
															</tem:HRDDF>
									EOD;
									
									$TotalDevengos += $ValorHRDDF;
								}

								// HORAS EXTRAS NOCTURNAS FESTIVAS 
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '02' AND 
											AUXILIARES.Auxiliar IN ('004', '054');
								EOD;

								$regNomina	= $this->model->leer($query);

								$HENDF		= $regNomina['Horas'];
								$ValorHENDF	= $regNomina['Valor'];

								if ($ValorHENDF > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HENDF>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HENDF</tem:Cantidad>
																<tem:Porcentaje>150</tem:Porcentaje>
																<tem:Pago>$ValorHENDF</tem:Pago>
															</tem:HENDF>
									EOD;
									
									$TotalDevengos += $ValorHENDF;
								}

								// HORAS RECARGO NOCTURNO DOMINICAL FESTIVAS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '03' AND 
											AUXILIARES.Auxiliar IN ('002','008', '052');
								EOD;

								$regNomina 	= $this->model->leer($query);

								$HRNDF		= $regNomina['Horas'];
								$ValorHRNDF	= $regNomina['Valor'];

								if ($ValorHRNDF > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:HRNDF>
																<tem:HoraInicio></tem:HoraInicio>
																<tem:HoraFin></tem:HoraFin>
																<tem:Cantidad>$HRNDF</tem:Cantidad>
																<tem:Porcentaje>110</tem:Porcentaje>
																<tem:Pago>$ValorHRNDF</tem:Pago>
															</tem:HRNDF>
									EOD;
									
									$TotalDevengos += $ValorHRNDF;
								}

								// VACACIONES EN TIEMPO
								$query = <<<EOD
									SELECT ACUMULADOS.FechaInicial, 
											ACUMULADOS.FechaFinal, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '50' 
										GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
								EOD;

								$nomina = $this->model->listar($query);

								if ($nomina)
								{
									for ($j = 0; $j < count($nomina); $j++)
									{
										$regNomina 	 = $nomina[$j];

										$FechaInicio = $regNomina['FechaInicial'];
										$FechaFin 	 = $regNomina['FechaFinal'];
										$Cantidad	 = $regNomina['Horas'] / 8;
										$Pago		 = $regNomina['Valor'];

										$archivoXML .= PHP_EOL;

										$archivoXML .= <<<EOD
																<tem:VacacionesComunes>
																	<tem:FechaInicio>$FechaInicio</tem:FechaInicio>
																	<tem:FechaFin>$FechaFin</tem:FechaFin>
																	<tem:Cantidad>$Cantidad</tem:Cantidad>
																	<tem:Pago>$Pago</tem:Pago>
																</tem:VacacionesComunes>
										EOD;
									
										$TotalDevengos += $Pago;
									}
								}

								// VACACIONES EN DINERO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '51';
								EOD;

								$regNomina  = $this->model->leer($query);

								$Cantidad	= $regNomina['Horas'] / 8;
								$Pago		= $regNomina['Valor']; 

								if ($Pago > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:VacacionesCompensadas>
																<tem:Cantidad>$Cantidad</tem:Cantidad>
																<tem:Pago>$Pago</tem:Pago>
															</tem:VacacionesCompensadas>
									EOD;
								
									$TotalDevengos += $Pago;
								}

								// PRIMA LEGAL
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '52';
								EOD;

								$regNomina  = $this->model->leer($query);

								$Cantidad 	= $regNomina['Horas'] / 8;
								$Pago 		= $regNomina['Valor'];

								if ($Pago > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Primas>
																<tem:Cantidad>$Cantidad</tem:Cantidad>
																<tem:Pago>$Pago</tem:Pago>
															</tem:Primas>
									EOD;
								
									$TotalDevengos += $Pago;
								}

								// CESANTIAS
								$query = <<<EOD
									SELECT SUM(IIF(MAYORES.Mayor='53', 
												IIF(PARAMETROS.Detalle = 'PAGO', 
													ACUMULADOS.Horas, 
													ACUMULADOS.Horas * -1), 0)) AS HorasCesantias, 
											SUM(IIF(MAYORES.Mayor='53', 
												IIF(PARAMETROS.Detalle = 'PAGO', 
													ACUMULADOS.Valor, 
													ACUMULADOS.Valor * -1), 0)) AS ValorCesantias, 
											SUM(IIF(MAYORES.Mayor='54', 
												IIF(PARAMETROS.Detalle = 'PAGO', 
													ACUMULADOS.Horas, 
													ACUMULADOS.Horas * -1), 0)) AS HorasInteres, 
											SUM(IIF(MAYORES.Mayor='54', 
												IIF(PARAMETROS.Detalle = 'PAGO', 
													ACUMULADOS.Valor, 
													ACUMULADOS.Valor * -1), 0)) AS ValorInteres
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor IN ('53', '54');
								EOD;

								$regNomina		= $this->model->leer($query);

								$Pago 			= $regNomina['ValorCesantias'];
								$Porcentaje		= round($regNomina['ValorInteres'] / (is_null($regNomina['ValorCesantias']) ? 1 : $regNomina['ValorCesantias']) * 100, 2);
								$PagoIntereses	= $regNomina['ValorInteres'];

								if ($Pago > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Cesantias>
																<tem:Pago>$Pago</tem:Pago>
																<tem:Porcentaje>$Porcentaje</tem:Porcentaje>
																<tem:PagoIntereses>$PagoIntereses</tem:PagoIntereses>
															</tem:Cesantias>
									EOD;
								
									$TotalDevengos += $Pago + $PagoIntereses;
								}

								// INCAPACIDADES
								$query = <<<EOD
									SELECT MAYORES.Mayor, 
											AUXILIARES.Auxiliar,
											ACUMULADOS.FechaInicial,
											ACUMULADOS.FechaFinal, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar IN ('008', '009', '014')) OR
											(MAYORES.Mayor = '05' AND AUXILIARES.Auxiliar = '004'))
										GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar, ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
								EOD;

								$nomina=$this->model->listar($query);

								if ($nomina)
								{
									for ($j = 0; $j < count($nomina); $j++)
									{
										$regNomina		= $nomina[$j];
										$FechaInicio 	= $regNomina['FechaInicial'];
										$FechaFin		= $regNomina['FechaFinal'];
										$Cantidad		= $regNomina['Horas'] / 8;

										switch ($regNomina['Auxiliar'])
										{
											case '008':
												$Tipo = 1;
												break;
											case '014':
												$Tipo = 3;
												break;
											case '004':
												$Tipo = 3;
												break;
										}

										$Pago = $regNomina['Valor'];

										$archivoXML .= PHP_EOL;

										$archivoXML .= <<<EOD
																<tem:Incapacidad>
																	<tem:FechaInicio>$FechaInicio</tem:FechaInicio>
																	<tem:FechaFin>$FechaFin</tem:FechaFin>
																	<tem:Cantidad>$Cantidad</tem:Cantidad>
																	<tem:Tipo>$Tipo</tem:Tipo>
																	<tem:Pago>$Pago</tem:Pago>
																</tem:Incapacidad>
										EOD;
									
										$TotalDevengos += $Pago;
									}
								}

								// LICENCIA MATERNIDAD / PATERNIDAD
								$query = <<<EOD
									SELECT ACUMULADOS.FechaInicial, 
											ACUMULADOS.FechaFinal, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor='01' AND AUXILIARES.Auxiliar = '010' 
										GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
								EOD;

								$nomina = $this->model->listar($query);

								if ($nomina)
								{
									for ($j = 0; $j < count($nomina); $j++)
									{
										$regNomina		= $nomina[$j];

										$FechaInicio 	= $regNomina['FechaInicial'];
										$FechaFin		= $regNomina['FechaFinal'];
										$Cantidad		= $regNomina['Horas'] / 8;
										$Pago			= $regNomina['Valor'];

										$archivoXML .= PHP_EOL;

										$archivoXML .= <<<EOD
																<tem:LicenciaMP>
																	<tem:FechaInicio>$FechaInicio</tem:FechaInicio>
																	<tem:FechaFin>$FechaFin</tem:FechaFin>
																	<tem:Cantidad>$Cantidad</tem:Cantidad>
																	<tem:Pago>$Pago</tem:Pago>
																</tem:LicenciaMP>
										EOD;
									
										$TotalDevengos += $Pago;
									}
								}

								// LICENCIAS REMUNERADAS
								$query = <<<EOD
									SELECT ACUMULADOS.FechaInicial, 
											ACUMULADOS.FechaFinal, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											(MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar IN ('011', '012', '015'))  
										GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
								EOD;

								$nomina = $this->model->listar($query);

								if ($nomina)
								{
									for ($j = 0; $j < count($nomina); $j++)
									{
										$regNomina		= $nomina[$j];

										$FechaInicio 	= $regNomina['FechaInicial'];
										$FechaFin		= $regNomina['FechaFinal'];
										$Cantidad		= $regNomina['Horas'] / 8;
										$Pago			= $regNomina['Valor'];

										$archivoXML .= PHP_EOL;

										$archivoXML .= <<<EOD
																<tem:LicenciaR>
																	<tem:FechaInicio>$FechaInicio</tem:FechaInicio>
																	<tem:FechaFin>$FechaFin</tem:FechaFin>
																	<tem:Cantidad>$Cantidad</tem:Cantidad>
																	<tem:Pago>$Pago</tem:Pago>
																</tem:LicenciaR>
										EOD;
									
										$TotalDevengos += $Pago;
									}
								}

								// LICENCIAS NO REMUNERADAS
								$query = <<<EOD
									SELECT ACUMULADOS.FechaInicial, 
											ACUMULADOS.FechaFinal, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar IN ('052', '054')) OR 
											(MAYORES.Mayor = '17' AND AUXILIARES.Auxiliar = '051'))
										GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
								EOD;

								// BONIFICACIONES
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '10' AND AUXILIARES.Auxiliar <> '051';
								EOD;

								$regNomina		= $this->model->leer($query);

								$BonificacionS	= $regNomina['Valor'];
								$BonificacionNS	= 0;

								if ($BonificacionS > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Bonificacion>
																<tem:BonificacionS>$BonificacionS</tem:BonificacionS>
																<tem:BonificacionNS>$BonificacionNS</tem:BonificacionNS>
															</tem:Bonificacion>
									EOD;
								
									$TotalDevengos += $BonificacionS + $BonificacionNS;
								}

								// AUXILIOS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar NOT IN ('021', '028', '051', '052', '067')) OR 
											(MAYORES.Mayor = '09' AND AUXILIARES.Auxiliar = '001'));
								EOD;

								$regNomina	= $this->model->leer($query);

								$AuxilioS	= 0;
								$AuxilioNS	= $regNomina['Valor'];

								if ($AuxilioNS > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Auxilio>
																<tem:AuxilioS>$AuxilioS</tem:AuxilioS>
																<tem:AuxilioNS>$AuxilioNS</tem:AuxilioNS>
															</tem:Auxilio>
									EOD;
								
									$TotalDevengos += $AuxilioS + $AuxilioNS;
								}

								// OTRO CONCEPTO
								$query = <<<EOD
									SELECT AUXILIARES.Nombre, 
									SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '07' AND AUXILIARES.Auxiliar = '001') OR 
											(MAYORES.Mayor = '08' AND AUXILIARES.Auxiliar IN ('001', '002')) OR 
											(MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar = '001') OR 
											(MAYORES.Mayor = '99' AND AUXILIARES.Auxiliar IN ('001', '010')))  
										GROUP BY AUXILIARES.Nombre;
								EOD;

								$nomina	= $this->model->listar($query);

								if ($nomina)
								{
									for ($j = 0; $j < count($nomina); $j++)
									{
										$regNomina		= $nomina[$j];

										$Descripcion 	= $regNomina['Nombre'];
										$ValorS			= 0;
										$ValorNS		= $regNomina['Valor'];

										if ($ValorNS > 0)
										{
											$archivoXML .= PHP_EOL;

											$archivoXML .= <<<EOD
																	<tem:OtroConcepto>
																		<tem:DescripcionConcepto>$Descripcion</tem:DescripcionConcepto>
																		<tem:ConceptoS>$ValorS</tem:ConceptoS>
																		<tem:ConceptoNS>$ValorNS</tem:ConceptoNS>
																	</tem:OtroConcepto>
											EOD;
										
											$TotalDevengos += $ValorS + $ValorNS;
										}
									}
								}

								// COMISIONES
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '06';
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Comisiones>
																<tem:Comision>$Valor</tem:Comision>
															</tem:Comisiones>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								// PAGOS A TERCEROS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '20' AND 
											AUXILIARES.Auxiliar IN ('061', '062');
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor 		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:PagosTerceros>
																<tem:PagoTercero>$Valor</tem:PagoTercero>
															</tem:PagosTerceros>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								// DOTACION
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '99' AND AUXILIARES.Auxiliar='013';
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Dotacion>$Valor</tem:Dotacion>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								// APOYO SOSTENIMIENTO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar in ('004', '017');
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:ApoyoSost>$Valor</tem:ApoyoSost>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								// BONIFICACION RETIRO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar = '028';
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor 		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:BonifRetiro>$Valor</tem:BonifRetiro>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								// INDEMNIZACIONES
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '55';
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Indemnizacion>$Valor</tem:Indemnizacion>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								// REINTEGROS
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto=AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '07' AND AUXILIARES.Auxiliar = '051') OR 
											(MAYORES.Mayor = '08' AND AUXILIARES.Auxiliar IN ('051', '052')) OR 
											(MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar IN ('021', '051')) OR 
											(MAYORES.Mayor = '15' AND AUXILIARES.Auxiliar = '051') OR 
											(MAYORES.Mayor = '17' AND AUXILIARES.Auxiliar = '054') OR 
											(MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar IN ('002', '003')));
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= <<<EOD
														<tem:Reintegro>$Valor</tem:Reintegro>
									EOD;
								
									$TotalDevengos += $Valor;
								}

								$archivoXML .= PHP_EOL;

								$archivoXML .= <<<EOD
													</tem:Devengados>
													<tem:Deducciones>
								EOD;

								// SALUD
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '11' AND 
											AUXILIARES.Auxiliar = '001';
								EOD;

								$regNomina = $this->model->leer($query);

								if ($regNomina)
									$Valor = is_null($regNomina['Valor']) ? 0 : $regNomina['Valor'];
								else	
									$Valor = 0;

								$archivoXML .= PHP_EOL;

								$archivoXML .= <<<EOD
														<tem:Salud>
															<tem:Porcentaje>4</tem:Porcentaje>
															<tem:Deduccion>$Valor</tem:Deduccion>
														</tem:Salud>
								EOD;

								$TotalDeducciones += $Valor;

								// PENSION
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '11' AND 
											AUXILIARES.Auxiliar = '002';
								EOD;

								$regNomina = $this->model->leer($query);

								if ($regNomina)
									$Valor = is_null($regNomina['Valor']) ? 0 : $regNomina['Valor'];
								else	
									$Valor = 0;

								$archivoXML .= PHP_EOL;

								$archivoXML .= <<<EOD
														<tem:FondoPension>
															<tem:Porcentaje>4</tem:Porcentaje>
															<tem:Deduccion>$Valor</tem:Deduccion>
														</tem:FondoPension>
								EOD;
							
								$TotalDeducciones += $Valor;

								// FONDO DE SOLIDARIDAD
								$query = <<<EOD
									SELECT ACUMULADOS.Base, 
											ACUMULADOS.Valor 
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '13' AND 
											AUXILIARES.Auxiliar = '001';
								EOD;

								$regNomina = $this->model->leer($query);

								if ($regNomina)
								{
									$PorcentajeFSP 	= 0.5;
									$ValorFSP 		= $regNomina['Valor'];
								}
								else
								{
									$PorcentajeFSP 	= 0;
									$ValorFSP 		= 0;
								}

								$query = <<<EOD
									SELECT ACUMULADOS.Base, 
											ACUMULADOS.Valor 
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '13' AND 
											AUXILIARES.Auxiliar = '002';
								EOD;

								$regNomina = $this->model->leer($query);

								if ($regNomina)
								{
									$PorcentajeFS 	= 0;
									$ValorFS 		= $regNomina['Valor'];
								}
								else
								{
									$PorcentajeFS 	= 0;
									$ValorFS 		= 0;
								}

								$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='ValorSueldoMinimo'")['valor'];

								if ($ValorFS > 0)
								{
									if ($regNomina['Base'] >= $SueldoMinimo * 4 AND 
										$regNomina['Base'] < $SueldoMinimo * 16)
										$PorcentajeFS = 0.5;

									if ($regNomina['Base'] >= $SueldoMinimo * 16 AND 
										$regNomina['Base'] < $SueldoMinimo * 17)
										$PorcentajeFS = 0.7;

									if ($regNomina['Base'] >= $SueldoMinimo * 17 AND 
										$regNomina['Base'] < $SueldoMinimo * 18)
										$PorcentajeFS = 0.9;

									if ($regNomina['Base'] >= $SueldoMinimo * 18 AND 
										$regNomina['Base'] < $SueldoMinimo * 19)
										$PorcentajeFS = 1.1;

									if ($regNomina['Base'] >= $SueldoMinimo * 19 AND 
										$regNomina['Base'] < $SueldoMinimo * 20)
										$PorcentajeFS = 1.3;

									if ($regNomina['Base'] >= $SueldoMinimo * 20)
										$PorcentajeFS = 1.5;
								}

								if ($ValorFSP > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:FondoSP>
																<tem:Porcentaje>$PorcentajeFSP</tem:Porcentaje>
																<tem:DeduccionSP>$ValorFSP</tem:DeduccionSP>
																<tem:PorcentajeSub>$PorcentajeFS</tem:PorcentajeSub>
																<tem:DeduccionSub>$ValorFS</tem:DeduccionSub>
															</tem:FondoSP>
									EOD;
								
									$TotalDeducciones += $ValorFSP + $ValorFS;
								}

								// SANCIONES
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '16';
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor 		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Sancion>
																<tem:SancionPublic>0</tem:SancionPublic>
																<tem:SancionPriv>$Valor</tem:SancionPriv>
															</tem:Sancion>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// LIBRANZAS
								$query = <<<EOD
									SELECT TERCEROS.Nombre, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
											INNER JOIN TERCEROS 
												ON ACUMULADOS.IdTercero = TERCEROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '18' AND AUXILIARES.Auxiliar = '052') OR 
											(MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar =  '053')) 
										GROUP BY TERCEROS.Nombre;
								EOD;

								$nomina = $this->model->listar($query);

								if ($nomina)
								{
									for ($j = 0; $j < count($nomina); $j++)
									{
										$regNomina 	= $nomina[$j];

										$Nombre		= $regNomina['Nombre'];
										$Valor 		= $regNomina['Valor'];

										$archivoXML .= PHP_EOL;

										$archivoXML .= <<<EOD
																<tem:Libranza>
																	<tem:Descripcion>$Nombre</tem:Descripcion>
																	<tem:Deduccion>$Valor</tem:Deduccion>
																</tem:Libranza>
										EOD;
									
										$TotalDeducciones += $Valor;
									}
								}

								$aPagosTerceros = array();

								$aAnticipos		= array();

								// OTRAS DEDUCCIONES
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '99' AND AUXILIARES.Auxiliar IN ('002', '004', '005', '006', '007', '008', '011', '012', '014', '015', '016', '017')) OR
											(MAYORES.Mayor = '04' AND AUXILIARES.Auxiliar = '051') OR 
											(MAYORES.Mayor = '10' AND AUXILIARES.Auxiliar = '051') OR 
											(MAYORES.Mayor = '11' AND AUXILIARES.Auxiliar = '003') OR 
											(MAYORES.Mayor = '17' AND AUXILIARES.Auxiliar IN ('001', '002')) OR 
											(MAYORES.Mayor = '05' AND AUXILIARES.Auxiliar = '051') OR 
											(MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar = '053'));
								EOD;

								$regNomina = $this->model->leer($query);

								if ($regNomina)
								{
									$Valor 		= $regNomina['Valor'];

									if ($Valor > 0)
									{
										$archivoXML .= PHP_EOL;

										$archivoXML .= <<<EOD
																<tem:OtrasDeducciones>
																	<tem:OtraDeduccion>$Valor</tem:OtraDeduccion>
																</tem:OtrasDeducciones>
										EOD;
									
										$TotalDeducciones += $Valor;
									}
								}

								// PENSION VOLUNTARIA
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '21' AND 
											AUXILIARES.Auxiliar = '002';
								EOD;

								$regNomina 	= $this->model->leer($query);

								$Valor 		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:PensionVoluntaria>$Valor</tem:PensionVoluntaria>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// RETENCION FUENTE
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '15' AND AUXILIARES.Auxiliar IN ('001', '005');
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:RetencionFuente>$Valor</tem:RetencionFuente>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// AFC
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '21' AND 
											AUXILIARES.Auxiliar = '001';
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:AFC>$Valor</tem:AFC>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// COOPERATIVA
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '18' AND AUXILIARES.Auxiliar IN ('001', '051') 
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Cooperativa>$Valor</tem:Cooperativa>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// EMBARGOS FISCALES
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '19'
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:EmbargoFiscal>$Valor</tem:EmbargoFiscal>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// EDUCACION
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '20' AND 
											AUXILIARES.Auxiliar = '060';
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Educacion>$Valor</tem:Educacion>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// REINTEGRO
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											((MAYORES.Mayor = '02' AND AUXILIARES.Auxiliar IN ('051', '052', '053', '054')) OR 
											(MAYORES.Mayor = '03' AND AUXILIARES.Auxiliar IN ('051', '052')));
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Reintegro>$Valor</tem:Reintegro>
									EOD;
								
									$TotalDeducciones += $Valor;
								}

								// DEUDA
								$query = <<<EOD
									SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
											SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
										FROM ACUMULADOS 
											INNER JOIN AUXILIARES
												ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
											INNER JOIN MAYORES 
												ON AUXILIARES.IdMayor = MAYORES.Id 
											INNER JOIN PARAMETROS 
												ON AUXILIARES.Imputacion = PARAMETROS.Id 
										WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
											ACUMULADOS.IdEmpleado = $IdEmpleado AND 
											MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar IN ('051', '054', '055', '056', '057', '058', '059', '063');
								EOD;

								$regNomina	= $this->model->leer($query);

								$Valor		= $regNomina['Valor'];

								if ($Valor > 0)
								{
									$archivoXML .= PHP_EOL;

									$archivoXML .= <<<EOD
															<tem:Deuda>$Valor</tem:Deuda>
									EOD;

									$TotalDeducciones += $Valor;
								}

								$archivoXML .= PHP_EOL;

								$archivoXML .= <<<EOD
													</tem:Deducciones>
								EOD;

								$Neto = $TotalDevengos - $TotalDeducciones;

								$archivoXML .= PHP_EOL;

								$archivoXML .= <<<EOD
													<tem:Redondeo>0</tem:Redondeo>
													<tem:DevengadosTotal>$TotalDevengos</tem:DevengadosTotal>
													<tem:DeduccionesTotal>$TotalDeducciones</tem:DeduccionesTotal>
													<tem:ComprobanteTotal>$Neto</tem:ComprobanteTotal>
												</tem:NominaIndividual>
											</tem:EnviarNominaIndividual>
										</soapenv:Body>
									</soapenv:Envelope>
								EOD;

								$archivoXML = trim($archivoXML);

								file_put_contents($dirName . 'nomina_' . $Documento . '_' . $IdEmpleado . '_' . $Referencia . '_' . $Periodo . '.xml', $archivoXML);

								if ($TotalDevengos == $empleados[$i]['Devengos'] AND 
									$TotalDeducciones == $empleados[$i]['Deducciones'])
								{
									file_put_contents($dirName . 'nomina_' . $Documento . '_' . $IdEmpleado . '_' . $Referencia . '_' . $Periodo . '.xml', $archivoXML);

									$query = <<<EOD
										UPDATE EMPLEADOS 
											SET SecuenciaNE = $Consecutivo 
											WHERE EMPLEADOS.Id = $IdEmpleado;
									EOD;

									$ok = $this->model->query($query);

									$query = <<<EOD
										UPDATE PERIODOS 
											SET SecuenciaNE = PERIODOS.SecuenciaNE + 1 
											WHERE PERIODOS.Id = $IdPeriodo;
									EOD;

									$ok = $this->model->query($query);

									$curl = curl_init();

									curl_setopt_array(
											$curl, 
											array(
												CURLOPT_URL => 'https://co.edocnube.com/5.0/wsnomina/wsedoc_nomina.svc',
												CURLOPT_RETURNTRANSFER => true,
												CURLOPT_ENCODING => '',
												CURLOPT_MAXREDIRS => 10,
												CURLOPT_TIMEOUT => 0,
												CURLOPT_FOLLOWLOCATION => true,
												CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
												CURLOPT_CUSTOMREQUEST => 'POST',
												CURLOPT_POSTFIELDS => $archivoXML,
												CURLOPT_HTTPHEADER => array(
													'Content-Type: text/xml;charset=UTF-8',
													'SOAPAction: http://tempuri.org/Iwsedoc_nomina/EnviarNominaIndividual',
													'Cookie: ASP.NET_SessionId=0kcpusfjgvvfctzxxckaadxr'
												)
											)
									);

									$response = curl_exec($curl);

									curl_close($curl);
								}
								else {
									$query = <<<EOD
										UPDATE EMPLEADOS 
											SET SecuenciaNE = -1
											WHERE EMPLEADOS.Id = $IdEmpleado;
									EOD;

									$this->model->query($query);

									$data['mensajeError'] = label("Empleado $Documento - $NombreEmpleado con conceptos faltantes por registrar") . '<br>';
								}
							}
							else
							{
								$data['mensajeError'] = label("Inconsistencia: empleado $Documento - $NombreEmpleado no existe") . '<br>';
							}
						}
					}
					else
					{
						$data['mensajeError'] = label("No hay datos disponibles") . '<br>';
					}
				}
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/nominaElectronica/parametros';
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
				$this->views->getView($this, 'actualizar', $data);
		}

		public function parametros() {
			set_time_limit(0);

			// SE LEEN EL PERIODO DEFINIDO PARA TRANSMISION
			$reg1 = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='PeriodoEnLiquidacion'");
			$IdPeriodo			= isset($_REQUEST['IdPeriodo']) ? $_REQUEST['IdPeriodo'] : $reg1['valor'];
			$regPeriodo			= getRegistro('PERIODOS', $IdPeriodo);
			if (!$regPeriodo) {
				$IdPeriodo = $reg1['valor'];
				$regPeriodo	= getRegistro('PERIODOS', $IdPeriodo);
			}

			$Periodo			= $regPeriodo['periodo'];
			$Referencia 		= $regPeriodo['referencia'];

			$periodos = $this->model->listar(<<<EOD
				SELECT
					DISTINCT per.id,
					CONVERT(varchar, per.fechainicial) + ' : ' + CONVERT(varchar, per.fechafinal) detalle
				FROM periodos per
				JOIN periodosacumulados pac ON per.id = pac.idperiodo
				WHERE pac.acumulado=1
				ORDER BY 2 DESC;
			EOD);

			$this->token = $this->authAportesEnLinea();
			$lastNELog = getRegistro('nomina.log_ne', 0, "nomina.log_ne.idTrack IS NOT NULL ORDER BY nomina.log_ne.fechaactualizacion DESC");
			$regNovedadState = $this->getAportesEnLinaState($lastNELog['idTrack'], $lastNELog["tipoDocumento"], $lastNELog["numeroDocumento"], $lastNELog["nit"], $lastNELog["periodoNomina"]);

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Empleado' 	=> isset($_REQUEST['Empleado']) ? $_REQUEST['Empleado'] : '',
					'IdPeriodo' 	=> isset($_REQUEST['IdPeriodo']) ? $_REQUEST['IdPeriodo'] : $IdPeriodo,
					'Periodos'	=> $periodos,
					'EstadoUltimaTransmision' => isset($regNovedadState) ? $regNovedadState->estado : NULL
					),
				'mensajeError' => ''
			);

			if	(isset($_REQUEST['Empleado'])) {
				if	(! empty($_REQUEST['Empleado'])) $Empleado = $_REQUEST['Empleado'];
				else $Empleado = '';

				if	(empty($data['mensajeError'])) {
					$IdPeriodo = getId('PERIODOS', "PERIODOS.Referencia = '$Referencia' AND PERIODOS.Periodo = $Periodo");
					$regPeriodo			= getRegistro('PERIODOS', $IdPeriodo);

					$NitEmpresa			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='NitEmpresa'")['detalle'];
					$DVEmpresa 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='DigitoVerificacionEmpresa'")['detalle'];
					$DireccionEmpresa 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='DireccionEmpresa'")['detalle'];

					// EMPLEADOS CON TOTALES
					$query = <<<EOD
						SELECT EMPLEADOS.Documento, 
								EMPLEADOS.Id AS IdEmpleado, 
								SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS Devengos, 
								SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS Deducciones 
							FROM ACUMULADOS 
								INNER JOIN EMPLEADOS 
									ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
								INNER JOIN AUXILIARES 
									ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
								INNER JOIN PARAMETROS 
									ON AUXILIARES.Imputacion = PARAMETROS.Id 
							WHERE ACUMULADOS.IdPeriodo = $IdPeriodo
					EOD;

					if (! empty($Empleado)) {
						$Empleado = str_replace(" ", "", $Empleado);
						$Empleado = str_replace(",", "','", $Empleado);
						$query .= <<<EOD
							AND EMPLEADOS.Documento in ('$Empleado') 
						EOD;
					}

					$query = <<<EOD
						$query
						GROUP BY EMPLEADOS.Documento, EMPLEADOS.Id
						ORDER BY EMPLEADOS.Documento;
					EOD;

					$empleados = $this->model->listar($query);

					if ($empleados) {
						$FechaLiquidacionInicio = $regPeriodo['fechainicial'];
						$FechaLiquidacionFin 	= $regPeriodo['fechafinal'];

						$jsonDatos = array(
							"periodo" => array(
								"fechaLiquidacionInicio" => "$FechaLiquidacionInicio",
								"fechaLiquidacionFin" => "$FechaLiquidacionFin",
								"fechaGen" => "$FechaLiquidacionFin"
							),
							"informacionGeneral" => array(
								"periodoNomina" => '5', // SOLO DE 0 A 5
								"tipoXML" => "102",
								"version" => "V1.0: Documento Soporte de Pago de Nómina Electrónica"
							),
							"lugarGeneracionXML" => array(
								"pais" => "CO",
								"departamentoEstado" => "11",
								"municipioCiudad" => "001",
							),
							"empleador" => array(
								"razonSocial" => "COMWARE S.A.",
								"nit" => $NitEmpresa,
								"dv" => $DVEmpresa,
								"pais" => "CO",
								"departamentoEstado" => "11",
								"municipioCiudad" => "001",
								"direccion" => "$DireccionEmpresa"
							),
							"trabajador" => array()
						);

						$dirName = $this->DIR_NE . '/' . $regPeriodo['referencia'] . '/' . $Periodo . '/';
						if	( ! is_dir($dirName) ) mkdir($dirName, 0777, true);

						$idsNENuevos = '';

						for ($i = 0; $i < count($empleados); $i++) {
							$IdEmpleado = $empleados[$i]['IdEmpleado'];

							$regEmpleado = getRegistro('EMPLEADOS', $IdEmpleado);

							$Documento			= $regEmpleado['documento'];
							$Apellido1				= $regEmpleado['apellido1'];
							$Apellido2				= $regEmpleado['apellido2'];

							if (empty($Apellido2)) $Apellido2 = 'N/A';

							$Nombre1 = $regEmpleado['nombre1'];
							if (empty($Nombre1)) $Nombre1 = 'N/A';

							$Nombre2 = $regEmpleado['nombre2']; 

							$NombreEmpleado = "$Apellido1 $Apellido2 $Nombre1 $Nombre2";

							if ($regEmpleado) {
								$logNE = $this->model->leer("SELECT * FROM nomina.log_ne WHERE idperiodo=$IdPeriodo AND idempleado=$IdEmpleado;");

								$Secuencia = getRegistro('PERIODOS', $IdPeriodo)['secuenciane'] + 1;
								$Consecutivo = $logNE ? $logNE['consecutivo'] : str_pad($Periodo, 2, '0', STR_PAD_LEFT) . str_pad($Secuencia, 4, '0', STR_PAD_LEFT);

								$regNovedad = null;
								foreach ($jsonDatos['trabajador'] as $emp) {
									if ($regEmpleado['documento'] == $emp["numeroDocumento"]) {
										$regNovedad = $emp;
										break;
									}
								}

								if ($regNovedad) {
									$data['mensajeError'] .= label("Empleado $Documento - $NombreEmpleado (contrato $IdEmpleado) con reingreso, no se puede transmitir en este momento por limitantes de Aportes en linea, por favor intentar mas tarde") . '<br>';
									continue;
								}

								$jsonDatosEmpleado = $this->obtenerJSONEmpleado($regEmpleado, $DireccionEmpresa, $IdPeriodo, $Consecutivo, $FechaLiquidacionInicio, $FechaLiquidacionFin, $regNovedad);
								if (!$jsonDatosEmpleado) {
									$data['mensajeError'] .= label("Empleado $Documento - $NombreEmpleado con conceptos faltantes por registrar") . '<br>';
									continue;
								} else if (isset($logNE['idTrack']) AND !is_null($logNE['idTrack'])) {
									$data['mensajeError'] .= label("Empleado $Documento - $NombreEmpleado ya fue transmitido") . '<br>';
									continue;
								}

								$TotalDevengos		= $jsonDatosEmpleado['devengadosTotal'];
								$TotalDeducciones	= $jsonDatosEmpleado['deduccionesTotal'];

								$archivoPath = $dirName . 'nomina_' . $Documento . '_' . $IdEmpleado . '_' . $Referencia . '_' . $Periodo . '.json';
								file_put_contents($archivoPath, json_encode($jsonDatosEmpleado));

								if ($TotalDevengos == round($empleados[$i]['Devengos']) AND 
									$TotalDeducciones == round($empleados[$i]['Deducciones']))
								{
									file_put_contents($archivoPath, json_encode($jsonDatosEmpleado));
									array_push($jsonDatos['trabajador'], $jsonDatosEmpleado);

									if (!$logNE) $idsNENuevos .= ($idsNENuevos<>'' ? ',' : '') . $this->model->guardarLogNE(array(
										$IdPeriodo, $IdEmpleado, $Consecutivo, $archivoPath, '', null, $jsonDatosEmpleado['tipoDocumento'], $Documento, $NitEmpresa, $Periodo, '', 'EnProgreso'
									));
									else {
										$query = <<<EOD
											UPDATE nomina.log_ne 
												SET intentos = intentos + 1,
													fechaactualizacion=getdate(),
													estado = 'EnProgreso',
													error = '' 
										EOD;

										$query .= "WHERE id = ".$logNE['id'];

										$ok = $this->model->query($query);
										$idsNENuevos .= ($idsNENuevos<>'' ? ',' : '') . $logNE['id'];
									}

									$query = <<<EOD
										UPDATE PERIODOS 
											SET SecuenciaNE = PERIODOS.SecuenciaNE + 1 
											WHERE PERIODOS.Id = $IdPeriodo;
									EOD;

									if (!$logNE) $ok = $this->model->query($query);
								} else {
									if (!$logNE) $this->model->guardarLogNE(array(
										$IdPeriodo, $IdEmpleado, $Consecutivo, $archivoPath, '', null, $jsonDatosEmpleado['tipoDocumento'], $Documento, $NitEmpresa, $Periodo, 'Devengos y deducciones no concuerdan', 'Error'
									));
									else {
										$query = <<<EOD
											UPDATE nomina.log_ne 
												SET intentos = intentos + 1,
													fechaactualizacion=getdate(),
													estado = 'Error',
													error = 'Devengos y deducciones no concuerdan' 
										EOD;

										$query .= "WHERE id = ".$logNE['id'];

										$ok = $this->model->query($query);
									}

									$data['mensajeError'] .= label("Empleado $Documento - $NombreEmpleado con conceptos faltantes por registrar") . '<br>';
								}
							} else {
								$data['mensajeError'] .= label("Inconsistencia: empleado $Documento - $NombreEmpleado no existe") . '<br>';
							}
						}

						$archivoPathMaster = $dirName . 'nomina_MASTER_' . $Referencia . '_' . $Periodo . '_' . date('YmdGis') . '.json';
						file_put_contents($archivoPathMaster, json_encode($jsonDatos));
						$curl = curl_init();

						curl_setopt_array($curl, array(
							CURLOPT_URL => URL_APORTES . 'NominaElectronica/ProcesarNomina',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => json_encode($jsonDatos),
							CURLOPT_HTTPHEADER => array(
								'accept: application/json',
								'token: '.$this->token,
								'Content-Type: application/json'
							),
						));

						$response = curl_exec($curl);

						curl_close($curl);

						$response = json_decode($response);

						if (!empty($idsNENuevos) && $response && $response->idTrack) {
							$query = <<<EOD
								UPDATE nomina.log_ne 
									SET idTrack = '$response->idTrack',
										fechaactualizacion=getdate(),
										estado = 'EnProgreso',
										error = '',
										archivoMsterPath='$archivoPathMaster' 
								WHERE id in ($idsNENuevos)
							EOD;

							$ok = $this->model->query($query);
							$data['reg']['EstadoUltimaTransmision'] = 'EnProgreso';
						} else if (!empty($idsNENuevos)) {
							$errorMsg = json_encode($response->descripcion);
							$query = <<<EOD
								UPDATE nomina.log_ne 
									SET fechaactualizacion=getdate(),
										estado = 'Error',
										error = '$errorMsg',
										archivoMsterPath='$archivoPathMaster' 
								WHERE id in ($idsNENuevos)
							EOD;

							$ok = $this->model->query($query);
						}
					} else {
						$data['mensajeError'] .= label("No hay datos disponibles") . '<br>';
					}
				}
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = $data['reg']['EstadoUltimaTransmision'] == 'EnProgreso' ? '' : SERVERURL . '/nominaElectronica/parametros';
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
				$this->views->getView($this, 'actualizar', $data);
		}

		private function authAportesEnLinea() {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => URL_APORTES . "NominaElectronica/Autenticacion",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>json_encode(array(
					"user" => USER_APORTES,
					"password" => PASS_APORTES,
					"ambiente" => AMBIENTE_APORTES
				)),
				CURLOPT_HTTPHEADER => array(
					'accept: application/json',
					'Content-Type: application/json'
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$response = json_decode($response);
			return (isset($response) AND isset($response->token)) ? $response->token : NULL;
		}

		private function getAportesEnLinaState($idTrack, $tipoDocumento, $numeroDocumento, $nit, $periodoNomina) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => URL_APORTES . "NominaElectronica/ConsultaEstado",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>json_encode(array(
					"idTrack" => $idTrack,
					"trabajador" => array(array(
						"tipoDocumento" => $tipoDocumento,
						"numeroDocumento" => $numeroDocumento,
						"nit" => $nit,
						"periodoNomina" => $periodoNomina
					)
				))),
				CURLOPT_HTTPHEADER => array(
					'accept: application/json',
					'Content-Type: application/json',
					'token: '.$this->token
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$response = json_decode($response);
			return $response;
		}

		private function obtenerJSONEmpleado($regEmpleado, $DireccionEmpresa, $IdPeriodo, $Consecutivo, $FechaLiquidacionInicio, $FechaLiquidacionFin) {
			if ($regEmpleado) {
				$IdEmpleado 	= $regEmpleado['id'];
				$TipoContrato 	= getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['valor'];

				switch ($TipoContrato) {
					case 1:
					case 2:
						$TipoContrato = '01';
						break;
					case 3:
					case 5:
					case 7:
						$TipoContrato = 19;
						break;
					case 4:
						$TipoContrato = 12;
						break;
					default:
						$TipoContrato = '01';
						break;
				}

				$TipoIdentificacion = getRegistro('PARAMETROS', $regEmpleado['tipoidentificacion'])['valor'];

				switch ($TipoIdentificacion) {
					case 1:
						$TipoIdentificacion = 31;
						break;
					case 2:
						$TipoIdentificacion = 13;
						break;
					case 3:
						$TipoIdentificacion = 50;
						break;
					case 4:
						$TipoIdentificacion = 22;
						break;
					case 5:
						$TipoIdentificacion = 11;
						break;
					case 6:
						$TipoIdentificacion = 12;
						break;
					case 7:
						$TipoIdentificacion = 41;
						break;
					case 8:
					case 9:
						$TipoIdentificacion = 47;
						break;
					default:
						$TipoIdentificacion = 13;
						break;
				}

				$RegimenCesantias = getRegistro('PARAMETROS', $regEmpleado['regimencesantias'])['valor'];

				if ($RegimenCesantias == 2)
					$RegimenCesantias = 'true';
				else
					$RegimenCesantias = 'false';

				$TipoContrato2 = getRegistro('PARAMETROS', $regEmpleado['tipocontrato'])['valor'];

				switch ($TipoContrato2) {
					case 1:
						$TipoContrato2 = 2;
						break;
					case 2:
						$TipoContrato2 = 1;
						break;
					case 3:
					case 6:
					case 7:
						$TipoContrato2 = 4;
						break;
					case 4:
						$TipoContrato2 = 3;
						break;
					case 5:
						$TipoContrato2 = 5;
						break;
					default:
						$TipoContrato2 = 2;
						break;
				}

				$FechaIngreso 			= $regEmpleado['fechaingreso'];
				$FechaRetiro 			= is_null($regEmpleado['fecharetiro']) ? '' : $regEmpleado['fecharetiro'];
				$TiempoLaborado 		= dias360(date('Y-m-d'), $regEmpleado['fechaingreso']);
				$FechaGeneracion 		= date('Y-m-d');

				$SubtipoCotizante		= str_pad($regEmpleado['subtipocotizante'], 2, '0', STR_PAD_LEFT);
				$Documento				= $regEmpleado['documento'];
				$Apellido1				= $regEmpleado['apellido1'];
				$Apellido2				= $regEmpleado['apellido2'];

				if (empty($Apellido2)) $Apellido2 = 'N/A';

				$Nombre1 = $regEmpleado['nombre1'];
				if (empty($Nombre1)) $Nombre1 = 'N/A';

				$Nombre2				= $regEmpleado['nombre2']; 

				$DireccionEmpleado		= $regEmpleado['direccion'];
				if (empty($DireccionEmpleado)) $DireccionEmpleado = $DireccionEmpresa;

				$SueldoBasico			= $regEmpleado['sueldobasico'];
				$CodigoSAP				= $regEmpleado['codigosap'];
				$CuentaBancaria 		= $regEmpleado['cuentabancaria'];
				
				$Banco = getRegistro('BANCOS', $regEmpleado['idbanco'])['nombre'];

				if (! $Banco) $Banco = '';

				$TipoCuentaBancaria = getRegistro('PARAMETROS', $regEmpleado['tipocuentabancaria'])['detalle'];

				$regNovedad = getRegistro('nomina.log_ne', 0, "nomina.log_ne.idperiodo=$IdPeriodo AND nomina.log_ne.numeroDocumento='$Documento' AND nomina.log_ne.idTrack IS NOT NULL");
				if (isset($regNovedad) AND isset($regNovedad['idTrack'])) {
					$regNovedadState = $this->getAportesEnLinaState($regNovedad['idTrack'], $regNovedad["tipoDocumento"], $Documento, $regNovedad["nit"], $regNovedad["periodoNomina"]);
					if (isset($regNovedadState) AND isset($regNovedadState->trabajador[0])) {
						$regNovedad = array(
							"CUNE" => isset($regNovedad) ? $regNovedadState->trabajador[0]->cune : "",
							"FechaGen" => isset($regNovedad) ? $regNovedad["fechaactualizacion"] : "",
							"Numero" => isset($regNovedad) ? $regNovedad["consecutivo"] : ""
						);
					} $regNovedad = NULL;
				} else $regNovedad = NULL;

				$jsonDatosEmpleado = array(
					"tipoTrabajador" => "$TipoContrato",
					"subTipoTrabajador" => $SubtipoCotizante=='01' ? $SubtipoCotizante : '00',
					"altoRiesgoPension" => false,
					"tipoDocumento" => "$TipoIdentificacion",
					"numeroDocumento" => "$Documento",
					"primerApellido" => "$Apellido1",
					"segundoApellido" => "$Apellido2",
					"primerNombre" => "$Nombre1",
					"otrosNombres" => "$Nombre2",
					"lugarTrabajoPais" => "CO",
					"lugarTrabajoDepartamentoEstado" => "11",
					"lugarTrabajoMunicipioCiudad" => "001",
					"lugarTrabajoDireccion" => "$DireccionEmpleado",
					"salarioIntegral" => $RegimenCesantias=='true',
					"tipoDeContrato" => "$TipoContrato2",
					"sueldo" => round($SueldoBasico),
					"codigoTrabajador" => "$CodigoSAP",
					"TipoNota" => "1",
					"novedad" => isset($regNovedad),
					"Predecesor" => isset($regNovedad) ? $regNovedad : array(
						"CUNE" => "",
						"FechaGen" => "",
						"Numero" => ""
					),
					"fechaIngreso" => $FechaIngreso>$FechaLiquidacionInicio ? $FechaLiquidacionInicio : $FechaIngreso,
					"tiempoLaborado" => "$TiempoLaborado",
					"fechasPagos" => array(
						array(
							"fechaPago" => $FechaLiquidacionFin."T14:29:31.037Z"
						)
					),
					"tipoMoneda" => "COP",
					"tasaRepresentativa" => 0,
					"email" => "",
					"numeroSecuenciaXML" => array(
						"prefijo" => "NOMI",
						"consecutivo" => $Consecutivo,
						"numero" => "NOMI$Consecutivo"
					),
					"pago" => array(
						"forma" => "1",
						"metodo" => "46",
						"banco" => "$Banco",
						"tipoCuenta" => "$TipoCuentaBancaria",
						"numeroCuenta" => "$CuentaBancaria"
					),
					"notas" => 'Se redondea todo tipo de numeros con decimales debido a limitantes del servicio de Aportes en Linea ya que solo soporta numeros enteros.'
				);

				if (! empty($FechaRetiro)) $jsonDatosEmpleado["fechaRetiro"] = $FechaRetiro."T14:29:31.037Z";

				if ($FechaIngreso>$FechaLiquidacionInicio) $jsonDatosEmpleado["notas"] .= " Se altera fecha de ingreso por limitantes del servicio de Aportes en Linea, ya que indica que La fecha de ingreso del empleado debe ser igual a la fecha de liquidacion de inicio (fecha de ingreso original $FechaIngreso).";

				$TotalDevengos		= 0;
				$TotalDeducciones	= 0;

				// PARAMETROS DEVENGADOS TYPE
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '01' AND 
							AUXILIARES.Auxiliar IN ('001', '016', '002', '003', '005', '006', '007', '051');
					EOD;

				$regNomina=$this->model->leer($query);

				if ($regNomina) {
					$DiasTrabajados 	= $regNomina['Horas'] / 8;
					$SueldoTrabajado 	= $regNomina['Valor'];
				} else {
					$DiasTrabajados 	= "0";
					$SueldoTrabajado 	= "0.00";
				}

				$TotalDevengos += $SueldoTrabajado;

				$jsonDatosEmpleado["devengados"] = array(
					"basico" => array(
						"diasTrabajados" => round($DiasTrabajados),
						"sueldoTrabajado" => round($SueldoTrabajado)
					),
				);

				// TRANSPORTE
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '04' AND AUXILIARES.Auxiliar = '001';
				EOD;

				$regNomina=$this->model->leer($query);

				$Transporte = $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["transporte"] = array();

				if ($Transporte > 0) {
					$jsonDatosEmpleado["devengados"]["transporte"] = array(
						array(
							"auxilioTransporte" => round($Transporte),
							"viaticoManuAlojS" => null,
							"viaticoManuAlojNS" => null
						)
					);

					$TotalDevengos += $Transporte;
				}

				// HORAS EXTRAS DIURNAS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '02' AND 
							AUXILIARES.Auxiliar IN ('001', '051');
				EOD;

				$regNomina=$this->model->leer($query);

				$HED			= $regNomina['Horas'];
				$ValorHED 		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["heDs"] = array("hed" => array());

				if ($ValorHED > 0) {
					$jsonDatosEmpleado["devengados"]["heDs"] = array(
						"hed" => array(array(
							"cantidad" => "".round($HED),
							"porcentaje" => "25",
							"pago" => round($ValorHED)
						))
					);

					$TotalDevengos += $ValorHED;
				}

				// HORAS EXTRAS NOCTURNAS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '02' AND 
							AUXILIARES.Auxiliar IN ('002', '052');
				EOD;

				$regNomina=$this->model->leer($query);

				$HEN			= $regNomina['Horas'];
				$ValorHEN 		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["heNs"] = array("hen" => array());

				if ($ValorHEN > 0) {
					$jsonDatosEmpleado["devengados"]["heNs"] = array(
						"hen" => array(array(
							"cantidad" => "".round($HEN),
							"porcentaje" => "75",
							"pago" => round($ValorHEN)
						))
					);

					$TotalDevengos += $ValorHEN;
				}

				// RECARGO NOCTURNO
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '03' AND 
							AUXILIARES.Auxiliar IN ('001', '051');
				EOD;

				$regNomina	= $this->model->leer($query);

				$HRN		= $regNomina['Horas'];
				$ValorHRN 	= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["hrNs"] = array("hrn" => array());

				if ($ValorHRN > 0) {
					$jsonDatosEmpleado["devengados"]["hrNs"] = array(
						"hrn" => array(array(
							"cantidad" => "".round($HRN),
							"porcentaje" => "35",
							"pago" => round($ValorHRN)
						))
					);

					$TotalDevengos += $ValorHRN;
				}

				// HORAS EXTRAS DIURNAS FESTIVAS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '02' AND 
							AUXILIARES.Auxiliar IN ('003', '053');
				EOD;

				$regNomina	= $this->model->leer($query);

				$HEDDF		= $regNomina['Horas'];
				$ValorHEDDF	= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["heddFs"] = array("heddf" => array());

				if ($ValorHEDDF > 0) {
					$jsonDatosEmpleado["devengados"]["heddFs"] = array(
						"heddf" => array(array(
							"cantidad" => "".round($HEDDF),
							"porcentaje" => "100",
							"pago" => round($ValorHEDDF)
						))
					);

					$TotalDevengos += $ValorHEDDF;
				}

				// HORAS RECARGO DIURNO DOMINICAL FESTIVAS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '02' AND (
								AUXILIARES.Auxiliar = '005' OR
								AUXILIARES.Auxiliar = '007'
							);
				EOD;

				$regNomina	= $this->model->leer($query);

				$HRDDF		= $regNomina['Horas'];
				$ValorHRDDF	= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["hrddFs"] = array("hrddf" => array());

				if ($ValorHRDDF > 0) {
					$jsonDatosEmpleado["devengados"]["hrddFs"] = array(
						"hrddf" => array(array(
							"cantidad" => "".round($HRDDF),
							"porcentaje" => "75",
							"pago" => round($ValorHRDDF)
						))
					);

					$TotalDevengos += $ValorHRDDF;
				}

				// HORAS EXTRAS NOCTURNAS FESTIVAS 
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '02' AND 
							AUXILIARES.Auxiliar IN ('004', '054');
				EOD;

				$regNomina	= $this->model->leer($query);

				$HENDF		= $regNomina['Horas'];
				$ValorHENDF	= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["hendFs"] = array("hendf" => array());

				if ($ValorHENDF > 0) {
					$jsonDatosEmpleado["devengados"]["hendFs"] = array(
						"hendf" => array(array(
							"cantidad" => "".round($HENDF),
							"porcentaje" => "150",
							"pago" => round($ValorHENDF)
						))
					);

					$TotalDevengos += $ValorHENDF;
				}

				// HORAS RECARGO NOCTURNO DOMINICAL FESTIVAS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '03' AND 
							AUXILIARES.Auxiliar IN ('002','008', '052');
				EOD;

				$regNomina 	= $this->model->leer($query);

				$HRNDF		= $regNomina['Horas'];
				$ValorHRNDF	= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["hrndFs"] = array("hrndf" => array());

				if ($ValorHRNDF > 0) {
					$jsonDatosEmpleado["devengados"]["hrndFs"] = array(
						"hrndf" => array(array(
							"cantidad" => "".round($HRNDF),
							"porcentaje" => "110",
							"pago" => round($ValorHRNDF)
						))
					);

					$TotalDevengos += $ValorHRNDF;
				}

				// VACACIONES EN TIEMPO
				$query = <<<EOD
					SELECT ACUMULADOS.FechaInicial, 
							ACUMULADOS.FechaFinal, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '50' AND AUXILIARES.Auxiliar NOT IN ('051')
						GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
				EOD;

				$nomina = $this->model->listar($query);

				$jsonDatosVacacionesComunes = array();

				if ($nomina) {
					for ($j = 0; $j < count($nomina); $j++) {
						$regNomina 	 = $nomina[$j];

						$FechaInicio = $regNomina['FechaInicial'];
						$FechaFin 	 = $regNomina['FechaFinal'];
						$Cantidad	 = $regNomina['Horas'] / 8;
						$Pago		 = $regNomina['Valor'];

						array_push(
							$jsonDatosVacacionesComunes, array(
								"fechaInicio" => "$FechaInicio",
								"fechaFin" => "$FechaFin",
								"cantidad" => round($Cantidad),
								"pago" => round($Pago)
							)
						);

						$TotalDevengos += $Pago;
					}
				}

				$jsonDatosEmpleado["devengados"]["vacaciones"] = array(
					"vacacionesComunes" => $jsonDatosVacacionesComunes
				);

				// VACACIONES EN DINERO
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '51';
				EOD;

				$regNomina  = $this->model->leer($query);

				$Cantidad	= $regNomina['Horas'] / 8;
				$Pago		= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["vacaciones"]["vacacionesCompensadas"] = array();

				if ($Pago > 0) {
					$jsonDatosEmpleado["devengados"]["vacaciones"]["vacacionesCompensadas"] = array(
						array(
							"cantidad" => round($Cantidad),
							"pago" => round($Pago)
						)
					);

					$TotalDevengos += $Pago;
				}

				// PRIMA LEGAL
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, 0)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '52';
				EOD;

				$regNomina  = $this->model->leer($query);

				$Cantidad 	= $regNomina['Horas'] / 8;
				$Pago 		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["primas"] = array(
					"cantidad" => $Pago > 0 ? round($Cantidad) : 0,
					"pago" => $Pago > 0 ? round($Pago) : 0,
					"pagoNS" => 0
				);

				$TotalDevengos += $Pago;

				// CESANTIAS
				$query = <<<EOD
					SELECT SUM(IIF(MAYORES.Mayor='53', 
								IIF(PARAMETROS.Detalle = 'PAGO', 
									ACUMULADOS.Horas, 
									ACUMULADOS.Horas * -1), 0)) AS HorasCesantias, 
							SUM(IIF(MAYORES.Mayor='53', 
								IIF(PARAMETROS.Detalle = 'PAGO', 
									ACUMULADOS.Valor, 
									ACUMULADOS.Valor * -1), 0)) AS ValorCesantias, 
							SUM(IIF(MAYORES.Mayor='54', 
								IIF(PARAMETROS.Detalle = 'PAGO', 
									ACUMULADOS.Horas, 
									ACUMULADOS.Horas * -1), 0)) AS HorasInteres, 
							SUM(IIF(MAYORES.Mayor='54', 
								IIF(PARAMETROS.Detalle = 'PAGO', 
									ACUMULADOS.Valor, 
									ACUMULADOS.Valor * -1), 0)) AS ValorInteres
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor IN ('53', '54');
				EOD;

				$regNomina		= $this->model->leer($query);

				$Pago 			= $regNomina['ValorCesantias'];
				$Porcentaje		= round(($Pago>0 ? $regNomina['ValorInteres'] / $Pago : 1) * 100, 2);
				$PagoIntereses	= $regNomina['ValorInteres'];

				$jsonDatosEmpleado["devengados"]["cesantias"] = array(
					"pago" => $Pago > 0 ? round($Pago) : 0,
					"porcentaje" => $Pago > 0 ? round($Porcentaje) : 0,
					"pagoIntereses" => round($PagoIntereses)
				);

				$TotalDevengos += $Pago + $PagoIntereses;

				// INCAPACIDADES
				$query = <<<EOD
					SELECT MAYORES.Mayor, 
							AUXILIARES.Auxiliar,
							ACUMULADOS.FechaInicial,
							ACUMULADOS.FechaFinal, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar IN ('008', '009', '014')) OR
							(MAYORES.Mayor = '05' AND AUXILIARES.Auxiliar = '004'))
						GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar, ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
				EOD;

				$nomina=$this->model->listar($query);

				$jsonDatosEmpleado["devengados"]["incapacidades"] = array(
					"incapacidad" => array()
				);
				if ($nomina) {
					for ($j = 0; $j < count($nomina); $j++) {
						$regNomina		= $nomina[$j];
						$FechaInicio 	= $regNomina['FechaInicial'];
						$FechaFin		= $regNomina['FechaFinal'];
						$Cantidad		= $regNomina['Horas'] / 8;

						switch ($regNomina['Auxiliar']) {
							case '008':
								$Tipo = 1;
								break;
							case '014':
								$Tipo = 3;
								break;
							case '004':
								$Tipo = 3;
								break;
						}

						$Pago = $regNomina['Valor'];

						array_push($jsonDatosEmpleado["devengados"]["incapacidades"]["incapacidad"], array(
							"fechaInicio" => "$FechaInicio",
							"fechaFin" => "$FechaFin",
							"cantidad" => round($Cantidad),
							"tipo" => $Tipo,
							"pago" => round($Pago)
						));

						$TotalDevengos += $Pago;
					}
				}

				// LICENCIAS
				$jsonDatosEmpleado["devengados"]["licencias"] = array(
					"licenciaMP" => array(),
					"licenciaR" => array(),
					"licenciaNR" => array()
				);

				// LICENCIA MATERNIDAD / PATERNIDAD
				$query = <<<EOD
					SELECT ACUMULADOS.FechaInicial, 
							ACUMULADOS.FechaFinal, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor='01' AND AUXILIARES.Auxiliar = '010' 
						GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
				EOD;

				$nomina = $this->model->listar($query);

				if ($nomina) {
					for ($j = 0; $j < count($nomina); $j++) {
						$regNomina		= $nomina[$j];

						$FechaInicio 	= $regNomina['FechaInicial'];
						$FechaFin		= $regNomina['FechaFinal'];
						$Cantidad		= $regNomina['Horas'] / 8;
						$Pago			= $regNomina['Valor'];

						array_push($jsonDatosEmpleado["devengados"]["licencias"]["licenciaMP"], array(
							"fechaInicio" => "$FechaInicio",
							"fechaFin" => "$FechaFin",
							"cantidad" => round($Cantidad),
							"pago" => round($Pago)
						));

						$TotalDevengos += $Pago;
					}
				}

				// LICENCIAS REMUNERADAS
				$query = <<<EOD
					SELECT ACUMULADOS.FechaInicial, 
							ACUMULADOS.FechaFinal, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							(MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar IN ('011', '012', '015'))  
						GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
				EOD;

				$nomina = $this->model->listar($query);

				if ($nomina) {
					for ($j = 0; $j < count($nomina); $j++) {
						$regNomina		= $nomina[$j];

						$FechaInicio 	= $regNomina['FechaInicial'];
						$FechaFin		= $regNomina['FechaFinal'];
						$Cantidad		= $regNomina['Horas'] / 8;
						$Pago			= $regNomina['Valor'];

						array_push($jsonDatosEmpleado["devengados"]["licencias"]["licenciaR"], array(
							"fechaInicio" => "$FechaInicio",
							"fechaFin" => "$FechaFin",
							"cantidad" => round($Cantidad),
							"pago" => round($Pago)
						));

						$TotalDevengos += $Pago;
					}
				}

				// LICENCIAS NO REMUNERADAS
				$query = <<<EOD
					SELECT ACUMULADOS.FechaInicial, 
							ACUMULADOS.FechaFinal, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar IN ('052', '054')) OR 
							(MAYORES.Mayor = '17' AND AUXILIARES.Auxiliar = '051'))
						GROUP BY ACUMULADOS.FechaInicial, ACUMULADOS.FechaFinal;
				EOD;

				// BONIFICACIONES
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '10' AND AUXILIARES.Auxiliar <> '051';
				EOD;

				$regNomina		= $this->model->leer($query);

				$BonificacionS	= $regNomina['Valor'];
				$BonificacionNS	= 0;
				$jsonDatosEmpleado["devengados"]["bonificaciones"] = array("bonificacion" => array());

				if ($BonificacionS > 0) {
					$jsonDatosEmpleado["devengados"]["bonificaciones"] = array(
						"bonificacion" => array(
							array(
								"bonificacionS" => round($BonificacionS),
								"bonificacionNS" => $BonificacionNS>0 ? $BonificacionNS : null
							)
						)
					);

					$TotalDevengos += $BonificacionS + $BonificacionNS;
				}

				// AUXILIOS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar NOT IN ('021', '028', '051', '052', '067', '064')) OR 
							(MAYORES.Mayor = '09' AND AUXILIARES.Auxiliar = '001'));
				EOD;

				$regNomina	= $this->model->leer($query);

				$AuxilioS	= 0;
				$AuxilioNS	= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["auxilios"] = array("auxilio" => array());

				if ($AuxilioNS > 0) {
					$jsonDatosEmpleado["devengados"]["auxilios"] = array(
						"auxilio" => array(
							array(
								"auxilioS" => $AuxilioS > 0 ? $AuxilioS : null,
								"auxilioNS" => round($AuxilioNS)
							)
						)
					);
					$TotalDevengos += $AuxilioS + $AuxilioNS;
				}

				$jsonDatosEmpleado["devengados"]["huelgasLegales"] = array("huelgaLegal" => array());

				// OTRO CONCEPTO
				$query = <<<EOD
					SELECT AUXILIARES.Nombre, 
					SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '07' AND AUXILIARES.Auxiliar = '001') OR 
							(MAYORES.Mayor = '08' AND AUXILIARES.Auxiliar IN ('001', '002')) OR 
							(MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar = '001') OR 
							(MAYORES.Mayor = '99' AND AUXILIARES.Auxiliar IN ('001', '010')))  
						GROUP BY AUXILIARES.Nombre;
				EOD;

				$nomina	= $this->model->listar($query);

				$jsonDatosEmpleado["devengados"]["otrosConceptos"] = array(
					"otroConcepto" => array()
				);
				if ($nomina) {
					for ($j = 0; $j < count($nomina); $j++) {
						$regNomina		= $nomina[$j];

						$Descripcion 	= $regNomina['Nombre'];
						$ValorS			= 0;
						$ValorNS		= $regNomina['Valor'];

						if ($ValorNS > 0) {
							array_push($jsonDatosEmpleado["devengados"]["otrosConceptos"]["otroConcepto"], array(
								"descripcionConcepto" => "$Descripcion",
								"conceptoS" => $ValorS > 0 ? $ValorS : null,
								"conceptoNS" => round($ValorNS)
							));

							$TotalDevengos += $ValorS + $ValorNS;
						}
					}
				}

				$jsonDatosEmpleado["devengados"]["compensaciones"] = array("compensacion" => array());
				$jsonDatosEmpleado["devengados"]["bonoEPCTVs"] = array("bonoEPCTV" => array());

				// COMISIONES
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '06' AND AUXILIARES.Auxiliar NOT IN ('051');
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["comisiones"] = array("comision" => array());

				if ($Valor > 0) {
					$jsonDatosEmpleado["devengados"]["comisiones"] = array(
						"comision" => array(round($Valor))
					);
				
					$TotalDevengos += $Valor;
				}

				// PAGOS A TERCEROS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '20' AND 
							AUXILIARES.Auxiliar IN ('061', '062');
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor 		= $regNomina['Valor'];
				$jsonDatosEmpleado["devengados"]["pagosTerceros"] = array("pagoTercero" => array());

				if ($Valor > 0) {
					$jsonDatosEmpleado["devengados"]["pagosTerceros"] = array(
						"pagoTercero" => array(round($Valor))
					);

					$TotalDevengos += $Valor;
				}

				$jsonDatosEmpleado["devengados"]["anticipos"] = array(
					"anticipo" => array()
				);

				// DOTACION
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '99' AND AUXILIARES.Auxiliar='013';
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["dotacion"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDevengos += $Valor > 0 ? $Valor : 0;

				// APOYO SOSTENIMIENTO
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar in ('004', '017');
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["apoyoSost"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDevengos += $Valor > 0 ? $Valor : 0;

				$jsonDatosEmpleado["devengados"]["teletrabajo"] = 0;

				// BONIFICACION RETIRO
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar = '028';
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor 		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["bonifRetiro"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDevengos += $Valor > 0 ? $Valor : 0;

				// INDEMNIZACIONES
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '55';
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["indemnizacion"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDevengos += $Valor > 0 ? $Valor : 0;

				// REINTEGROS
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas, ACUMULADOS.Horas * -1)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, ACUMULADOS.Valor * -1)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto=AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '07' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '08' AND AUXILIARES.Auxiliar IN ('051', '052')) OR 
							(MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar IN ('021', '051')) OR 
							(MAYORES.Mayor = '15' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '17' AND AUXILIARES.Auxiliar = '054') OR 
							(MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar IN ('002', '003')));
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["devengados"]["reintegro"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDevengos += $Valor > 0 ? $Valor : 0;

				// SALUD
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '11' AND 
							AUXILIARES.Auxiliar = '001';
				EOD;

				$regNomina = $this->model->leer($query);

				if ($regNomina)
					$Valor = is_null($regNomina['Valor']) ? 0 : $regNomina['Valor'];
				else	
					$Valor = 0;

				$jsonDatosEmpleado["deducciones"] = array(
					"salud" => array(
						"porcentaje" => 4,
						"deduccion" => round($Valor)
					)
				);

				$TotalDeducciones += $Valor;

				// PENSION
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '11' AND 
							AUXILIARES.Auxiliar = '002';
				EOD;

				$regNomina = $this->model->leer($query);

				if ($regNomina)
					$Valor = is_null($regNomina['Valor']) ? 0 : $regNomina['Valor'];
				else	
					$Valor = 0;

				$jsonDatosEmpleado["deducciones"]["fondoPension"] = array(
					"porcentaje" => 4,
					"deduccion" => round($Valor)
				);
			
				$TotalDeducciones += $Valor;

				// FONDO DE SOLIDARIDAD
				$query = <<<EOD
					SELECT SUM(ACUMULADOS.Base) Base, 
							SUM(ACUMULADOS.Valor) Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '13' AND 
							AUXILIARES.Auxiliar = '001';
				EOD;

				$regNomina = $this->model->leer($query);

				if ($regNomina) {
					$PorcentajeFSP 	= 0.5;
					$ValorFSP 		= $regNomina['Valor'];
				} else {
					$PorcentajeFSP 	= 0;
					$ValorFSP 		= 0;
				}

				$query = <<<EOD
					SELECT SUM(ACUMULADOS.Base) Base, 
							SUM(ACUMULADOS.Valor) Valor
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '13' AND 
							AUXILIARES.Auxiliar = '002';
				EOD;

				$regNomina = $this->model->leer($query);

				if ($regNomina) {
					$PorcentajeFS 	= 0;
					$ValorFS 		= $regNomina['Valor'];
				} else {
					$PorcentajeFS 	= 0;
					$ValorFS 		= 0;
				}

				$SueldoMinimo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='ValorSueldoMinimo'")['valor'];

				if ($ValorFS > 0) {
					if ($regNomina['Base'] >= $SueldoMinimo * 4 AND 
						$regNomina['Base'] < $SueldoMinimo * 16)
						$PorcentajeFS = 0.5;

					if ($regNomina['Base'] >= $SueldoMinimo * 16 AND 
						$regNomina['Base'] < $SueldoMinimo * 17)
						$PorcentajeFS = 0.7;

					if ($regNomina['Base'] >= $SueldoMinimo * 17 AND 
						$regNomina['Base'] < $SueldoMinimo * 18)
						$PorcentajeFS = 0.9;

					if ($regNomina['Base'] >= $SueldoMinimo * 18 AND 
						$regNomina['Base'] < $SueldoMinimo * 19)
						$PorcentajeFS = 1.1;

					if ($regNomina['Base'] >= $SueldoMinimo * 19 AND 
						$regNomina['Base'] < $SueldoMinimo * 20)
						$PorcentajeFS = 1.3;

					if ($regNomina['Base'] >= $SueldoMinimo * 20)
						$PorcentajeFS = 1.5;
				}

				$jsonDatosEmpleado["deducciones"]["fondoSP"] = array(
					// "porcentaje" => $PorcentajeFSP,
					"deduccionSP" => $ValorFSP > 0 ? round($ValorFSP) : 0,
					// "porcentajeSub" => $PorcentajeFS,
					"deduccionSub" => $ValorFS > 0 ? round($ValorFS) : 0
				);

				$TotalDeducciones += ($ValorFSP > 0 ? $ValorFSP : 0) + ($ValorFS > 0 ? $ValorFS : 0);

				$jsonDatosEmpleado["deducciones"]["sindicato"] = array(
					"porcentaje" => "0",
					"deduccion" => 0
				);

				// SANCIONES
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '16';
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor 		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["sanciones"] = array(
					"sancion" => array()
				);

				if ($Valor > 0) {
					array_push($jsonDatosEmpleado["deducciones"]["sanciones"]["sancion"],
						array(
							"sancionPublic" => 0,
							"sancionPriv" => round($Valor)
						)
					);

					$TotalDeducciones += $Valor;
				}

				// LIBRANZAS
				$query = <<<EOD
					SELECT TERCEROS.Nombre, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
							LEFT JOIN TERCEROS 
								ON ACUMULADOS.IdTercero = TERCEROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '18' AND AUXILIARES.Auxiliar = '052') OR 
							(MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar =  '053')) 
						GROUP BY TERCEROS.Nombre;
				EOD;

				$nomina = $this->model->listar($query);

				$jsonDatosEmpleado["deducciones"]["libranzas"] = array(
					"libranza" => array()
				);
				if ($nomina) {
					for ($j = 0; $j < count($nomina); $j++) {
						$regNomina 	= $nomina[$j];

						$Nombre		= $regNomina['Nombre'];
						$Valor 		= $regNomina['Valor'];

						array_push($jsonDatosEmpleado["deducciones"]["libranzas"]["libranza"], array(
							"descripcion" => $Nombre ? $Nombre : 'Libranza sin detalle',
							"deduccion" => round($Valor)
						));

						$TotalDeducciones += $Valor;
					}
				}
				$jsonDatosEmpleado["deducciones"]["pagosTerceros"] = array("pagoTercero" => array());

				$jsonDatosEmpleado["deducciones"]["anticipos"] = array("anticipo" => array());

				// OTRAS DEDUCCIONES
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '99' AND AUXILIARES.Auxiliar IN ('002', '004', '005', '006', '007', '008', '011', '012', '014', '015', '016', '017')) OR
							(MAYORES.Mayor = '04' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '10' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '11' AND AUXILIARES.Auxiliar = '003') OR 
							(MAYORES.Mayor = '17' AND AUXILIARES.Auxiliar IN ('001', '002')) OR 
							(MAYORES.Mayor = '05' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '01' AND AUXILIARES.Auxiliar = '053') OR 
							(MAYORES.Mayor = '52' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '50' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '06' AND AUXILIARES.Auxiliar = '051') OR 
							(MAYORES.Mayor = '12' AND AUXILIARES.Auxiliar = '064'));
				EOD;

				$regNomina = $this->model->leer($query);
				$jsonDatosEmpleado["deducciones"]["otrasDeducciones"] = array("otraDeduccion" => array());

				if ($regNomina) {
					$Valor 		= $regNomina['Valor'];

					if ($Valor > 0) {
						$jsonDatosEmpleado["deducciones"]["otrasDeducciones"] = array(
							"otraDeduccion" => array(round($Valor))
						);
					}
					$TotalDeducciones += $Valor;
				}

				// PENSION VOLUNTARIA
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '21' AND 
							AUXILIARES.Auxiliar = '002';
				EOD;

				$regNomina 	= $this->model->leer($query);

				$Valor 		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["pensionVoluntaria"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor;

				// RETENCION FUENTE
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '15' AND AUXILIARES.Auxiliar IN ('001', '002', '004', '005');
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["retencionFuente"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				// AFC
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '21' AND 
							AUXILIARES.Auxiliar = '001';
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["afc"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				// COOPERATIVA
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '18' AND AUXILIARES.Auxiliar IN ('001', '051') 
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["cooperativa"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				// EMBARGOS FISCALES
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '19'
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["embargoFiscal"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				$jsonDatosEmpleado["deducciones"]["planComplementarios"] = 0;

				// EDUCACION
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '20' AND 
							AUXILIARES.Auxiliar = '060';
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["educacion"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				// REINTEGRO
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							((MAYORES.Mayor = '02' AND AUXILIARES.Auxiliar IN ('051', '052', '053', '054')) OR 
							(MAYORES.Mayor = '03' AND AUXILIARES.Auxiliar IN ('051', '052')));
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["reintegro"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				// DEUDA
				$query = <<<EOD
					SELECT SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Horas * -1, ACUMULADOS.Horas)) AS Horas, 
							SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor * -1, ACUMULADOS.Valor)) AS Valor  
						FROM ACUMULADOS 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS 
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						WHERE ACUMULADOS.IdPeriodo = $IdPeriodo AND 
							ACUMULADOS.IdEmpleado = $IdEmpleado AND 
							MAYORES.Mayor = '20' AND AUXILIARES.Auxiliar IN ('051', '054', '055', '056', '057', '058', '059', '063');
				EOD;

				$regNomina	= $this->model->leer($query);

				$Valor		= $regNomina['Valor'];

				$jsonDatosEmpleado["deducciones"]["deuda"] = $Valor > 0 ? round($Valor) : 0;
				$TotalDeducciones += $Valor > 0 ? $Valor : 0;

				$Neto = $TotalDevengos - $TotalDeducciones;

				$jsonDatosEmpleado["redondeo"] = 0;
				$jsonDatosEmpleado["devengadosTotal"] = round($TotalDevengos);
				$jsonDatosEmpleado["deduccionesTotal"] = round($TotalDeducciones);
				$jsonDatosEmpleado["comprobanteTotal"] = round($Neto);

				return $jsonDatosEmpleado;
			}
			return null;
		}

		public function report() {
			$regParam = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro='PeriodoEnLiquidacion'");
			$IdPeriodo = $regParam['valor'];

			$regP = getRegistro('PERIODOS', $IdPeriodo);
			$Referencia = $regP['referencia'];

			$Periodo = isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : NULL;

			if (!empty($Periodo)) $regP = getRegistro(
				'PERIODOS',
				0,
				"PERIODOS.Referencia = '$Referencia' AND PERIODOS.Periodo = $Periodo"
			);

			$Periodo = $regP['periodo'];

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'Periodo' 	=> $Periodo,
					),
				'mensajeError' => ''
			);

			if	( ! is_dir('descargas') ) mkdir('descargas');

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'Exportar') {
				$dirName = $this->DIR_NE . '/' . $Referencia . '/' . $Periodo . '/';
				if (isset($_REQUEST['Periodo']) AND is_dir($dirName)) {
					$files = array_diff(scandir($dirName), array('.', '..'));

					$data = array();
					$headers = array();

					foreach ($files as $fileName) {
						$fileContent = file_get_contents($dirName . $fileName);
						$parser = xml_parser_create();
						xml_parse_into_struct($parser, $fileContent, $xml);
						xml_parser_free($parser);

						$item = array();
						foreach ($xml as $value) {
							$tag = isset($value['tag']) ? $value['tag'] : '';
							$level = isset($value['level']) ? $value['level'] : 0;
							$value = isset($value['value']) ? $value['value'] : '';

							if ($level>=5 AND !empty($tag) AND !empty(trim($value))) {
								$header = explode(':', $tag)[1];
								$item[$header] = $value;
								$headers[$header] = $header;
							}
						}

						array_push($data, $item);
					}
					$csvFileName = 'descargas/' . COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_NOMINA_ELECTRONICA_' . $Referencia . '_' . $Periodo . '_' . date('YmdGis') . '.csv';
					generateCSV($csvFileName, $data, $headers);
				} elseif (is_dir($dirName))
					$data['mensajeError'] = label('Debe seleccionar un periodo') . '<br>';
				else $data['mensajeError'] = label('No existen registros para el periodo '.$Periodo) . '<br>';
			} elseif (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'DownloadConceptsNE') {
				$data = $this->model->getConcepts();
				$csvFileName = 'descargas/' . COMPANY . '_' . $_SESSION['Login']['Usuario'] . '_CONCEPTOS_NOMINA_ELECTRONICA_' . '_' . date('YmdGis') . '.csv';
				generateCSV($csvFileName, $data);
			}

			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = SERVERURL . '/nominaElectronica/report';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';

			if ($data) 
				$this->views->getView($this, 'reportNE', $data);
		}

		public function inicio()
		{
			$data['mensajeError'] = '';

			if (isset($_REQUEST['Action']) AND $_REQUEST['Action'] == 'IniciarSecuencia') 
			{
				$query = <<<EOD
					UPDATE EMPLEADOS 
						SET SEcuenciaNE = 0;
				EOD;

				$this->model->query($query);

				header('Location: ' . SERVERURL . '/nominaElectronica/parametros');
				exit();
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

			if ($data) 
				$this->views->getView($this, 'inicioContadores', $data);
		}

		public function borrar()
		{
			$Consecutivo = '82653';
			$Numero 	 = 'NOMI' . $Consecutivo;
			$Predecesor  = 'NOMI80054';
			$CUNE		 = 'd01b7d506df329841821d23ab25bce5f7a980b0d6af57088550e4207f1f96f19f7078d7e2959796f1d07a5ca76ffae4a';
			$Fecha	     = '2023-09-14';
			$FechaGeneracion = date('Y-m-d');

			$archivoXML = <<<EOD
				<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
					<soapenv:Header/>
					<soapenv:Body>
						<tem:EnviarNominaIndividualDeAjuste>
							<tem:Clave>/QOkJKubBxGTo0WX5xF9YK4XrAwSzlVjQxA3EuXCTXdY6/g6BvLp72FhoR5+sArzzZrFwYrdNY+Z9oTInX0dzQnVi1B2Oa0po94PnnhuxLg=</tem:Clave>
							<tem:NominaIndividualDeAjuste>
								<tem:UsuarioTransaccionERP>ADMIN_COMWARE</tem:UsuarioTransaccionERP>
								<tem:NombreIntegracion>COMWARE</tem:NombreIntegracion>
								<tem:TipoNota>2</tem:TipoNota>
								<tem:Eliminar>
									<tem:Prefijo>NOMI</tem:Prefijo>
									<tem:Consecutivo>$Consecutivo</tem:Consecutivo>
									<tem:EliminandoPredecesor>
										<tem:NumeroPred>$Predecesor</tem:NumeroPred>
										<tem:CUNEPred>$CUNE</tem:CUNEPred>
										<tem:FechaGenPred>$Fecha</tem:FechaGenPred>
									</tem:EliminandoPredecesor>
									<tem:LugarGeneracionXML>
										<tem:Pais>CO</tem:Pais>
										<tem:DepartamentoEstado>11</tem:DepartamentoEstado>
										<tem:MunicipioCiudad>11001</tem:MunicipioCiudad>
									</tem:LugarGeneracionXML>
									<tem:InformacionGeneral>
										<tem:TipoXML>103</tem:TipoXML>
										<tem:FechaGen>$FechaGeneracion</tem:FechaGen>
										<tem:PeriodoNomina>5</tem:PeriodoNomina>
										<tem:TipoMoneda>COP</tem:TipoMoneda>
									</tem:InformacionGeneral>
									<tem:Empleador>
										<tem:RazonSocial>COMWARE S.A.</tem:RazonSocial>
										<tem:NIT>860045379</tem:NIT>
										<tem:DV>1</tem:DV>
										<tem:Pais>CO</tem:Pais>
										<tem:DepartamentoEstado>11</tem:DepartamentoEstado>
										<tem:MunicipioCiudad>11001</tem:MunicipioCiudad>
										<tem:Direccion>SEDE  PRINCIPAL CRA. 13 # 97-98, BOGOTA - COLOMBIA</tem:Direccion>
									</tem:Empleador>
								</tem:Eliminar>
							</tem:NominaIndividualDeAjuste>
						</tem:EnviarNominaIndividualDeAjuste>
					</soapenv:Body>
				</soapenv:Envelope>			
			EOD;

			$curl = curl_init();

			curl_setopt_array(
					$curl, 
					array(
						CURLOPT_URL => 'https://co.edocnube.com/5.0/wsnomina/wsedoc_nomina.svc',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => $archivoXML,
						CURLOPT_HTTPHEADER => array(
							'Content-Type: text/xml;charset=UTF-8',
							'SOAPAction: http://tempuri.org/Iwsedoc_nomina/EnviarNominaIndividualDeAjuste',
							'Cookie: ASP.NET_SessionId=0kcpusfjgvvfctzxxckaadxr'
						)
					)
			);

			$response = curl_exec($curl);

			curl_close($curl);
		}
	}
?>

