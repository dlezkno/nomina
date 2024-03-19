<?php
	class contabilizacionSAPModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM DETALLESSAP 
						INNER JOIN EMPLEADOS  
							ON DETALLESSAP.U_InfoCo01 = EMPLEADOS.CodigoSAP
					$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCuentas($query)
		{
			$query = <<<EOD
				SELECT DETALLESSAP.LineNum, 
						DETALLESSAP.AccountCode, 
						DETALLESSAP.CostingCode, 
						DETALLESSAP.ProjectCode, 
						DETALLESSAP.U_InfoCo01, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						DETALLESSAP.LineMemo, 
						DETALLESSAP.Debit, 
						DETALLESSAP.Credit
					FROM DETALLESSAP 
						LEFT JOIN EMPLEADOS ON DETALLESSAP.IdEmpleado = EMPLEADOS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function comprobanteSAP($query)
		{
			$query = <<<EOD
				SELECT
					ACUMULADOS.id,
					ACUMULADOS.IdEmpleado, 
					ACUMULADOS.Archivo, 
					ACUMULADOS.IdsArchivo, 
					EMPLEADOS.CodigoSAP, 
					CENTROS.TipoEmpleado, 
					EMPLEADOS.IdEPS, 
					EMPLEADOS.IdFondoPensiones, 
					EMPLEADOS.IdFondoCesantias, 
					EMPLEADOS.IdARL, 
					EMPLEADOS.IdCajaCompensacion, 
					ACUMULADOS.ValorPension,
					ACUMULADOS.ValorSalud,
					ACUMULADOS.ValorARL,
					ACUMULADOS.ValorCCF,
					ACUMULADOS.ValorSENA,
					ACUMULADOS.ValorICBF,
					ACUMULADOS.ValorSolidaridad,
					ACUMULADOS.ValorSubsistencia,
					CENTROS.Centro, 
					PROYECTOS.Centro AS Proyecto, 
					EMPLEADOS.Documento AS DocumentoEmpleado
				FROM nomina.log_pila as ACUMULADOS  
					INNER JOIN EMPLEADOS ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id
					INNER JOIN CENTROS ON EMPLEADOS.IdCentro = CENTROS.Id 
					LEFT JOIN CENTROS AS PROYECTOS ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
				$query 
				ORDER BY ACUMULADOS.IdEmpleado;
			EOD;

			$request = $this->listar($query);

			return $request;
		}

		public function comprobanteSAP2($query)
		{
			$query = <<<EOD
				SELECT ACUMULADOS.IdConcepto, 
					ACUMULADOS.IdEmpleado, 
					EMPLEADOS.CodigoSAP, 
					CENTROS.TipoEmpleado, 
					EMPLEADOS.IdEPS, 
					EMPLEADOS.IdFondoPensiones, 
					EMPLEADOS.IdFondoCesantias, 
					EMPLEADOS.IdARL, 
					EMPLEADOS.IdCajaCompensacion, 
					MAYORES.Mayor, 
					AUXILIARES.Auxiliar, 
					AUXILIARES.Nombre AS NombreConcepto, 
					AUXILIARES.EsDispersable, 
					AUXILIARES.FactorConversion, 
					ACUMULADOS.Base, 
					ACUMULADOS.Valor, 
					PARAMETROS.Detalle AS Imputacion, 
					CENTROS.Centro, 
					PROYECTOS.Centro AS Proyecto, 
					ACUMULADOS.IdTercero,
					EMPLEADOS.Documento AS DocumentoEmpleado
				FROM ACUMULADOS  
					INNER JOIN EMPLEADOS 
						ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
					INNER JOIN AUXILIARES 
						ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
					INNER JOIN MAYORES 
						ON AUXILIARES.IdMayor = MAYORES.Id 
					INNER JOIN PARAMETROS 
						ON AUXILIARES.Imputacion = PARAMETROS.Id
					INNER JOIN CENTROS 
						ON EMPLEADOS.IdCentro = CENTROS.Id 
					LEFT JOIN CENTROS AS PROYECTOS 
						ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
				$query 
				ORDER BY ACUMULADOS.IdEmpleado, ACUMULADOS.IdConcepto;
			EOD;

			$request = $this->listar($query);

			return $request;
		}

		public function guardarRegistroSAP($datos,)
		{
			$CuentaNomina = getRegistro('PARAMETROS', 0, "Parametro = 'CuentaNomina' ")['valor'];
			$CuentaNomina = str_pad($CuentaNomina, 12, '0', STR_PAD_RIGHT);

			$CuentaDb 		= $datos['CuentaDb'];
			$CentroDb 		= $datos['CentroDb'];
			$ProyectoDb 	= $datos['ProyectoDb'];
			$CuentaCr 		= $datos['CuentaCr'];
			$CentroCr 		= $datos['CentroCr'];
			$ProyectoCr 	= $datos['ProyectoCr'];
			$CodigoSAPDb	= $datos['CodigoSAPDb'];
			$CodigoSAPCr	= $datos['CodigoSAPCr'];
			$Valor 			= $datos['Valor'];
			$Documento 		= $datos['Documento'];
			$FechaFinalP 	= $datos['FechaFinalP'];
			$NombreCuenta 	= $datos['NombreCuenta'];
			$Reference2		= $datos['Reference2'];
			$IdPeriodo		= $datos['IdPeriodo'];
			$IdComprobante	= $datos['IdComprobante'];
			$IdLogPila		= $datos['IdLogPila'];

			$IdEmpleado = $datos['IdEmpleado'];

			$Secuencia 		= $datos['Secuencia'];

			if ($CuentaDb == $CuentaNomina)
				$NombreCuenta = 'NOMINA POR PAGAR';

			$query = <<<EOD
				SELECT DETALLESSAP.LineNum, 
						DETALLESSAP.Debit, 
						DETALLESSAP.Credit  
					FROM DETALLESSAP 
					WHERE DETALLESSAP.AccountCode = '$CuentaDb' AND 
						DETALLESSAP.CostingCode = '$CentroDb' AND 
						DETALLESSAP.ProjectCode = '$ProyectoDb' AND 
						DETALLESSAP.U_infoco01 = '$CodigoSAPDb' AND
						DETALLESSAP.IdEmpleado = $IdEmpleado AND
						DETALLESSAP.IdPeriodo = $IdPeriodo AND
						DETALLESSAP.IdComprobante = $IdComprobante;
			EOD;

			$regNomina = $this->leer($query);

			if ($regNomina) {
				$LineNum = $regNomina['LineNum'];

				$query = <<<EOD
					UPDATE DETALLESSAP 
						SET Debit = DETALLESSAP.Debit + $Valor 
						WHERE DETALLESSAP.LineNum = $LineNum AND
							DETALLESSAP.IdEmpleado = $IdEmpleado AND
							DETALLESSAP.IdPeriodo = $IdPeriodo AND
							DETALLESSAP.IdComprobante = $IdComprobante;
				EOD;
			} else {
				$Secuencia++;

				$query = <<<EOD
					INSERT INTO DETALLESSAP
						(ConsecId, RecordKey, LineNum, AccountCode, ShortName, CostingCode, Projectcode, Debit, Credit, DueDate, LineMemo, Reference2, ReferenceDate1, ReferenceDate2, TaxDate, U_infoco01, U_CodRet, U_BaseRet, U_TarifaRet, Procesado, CodCompania, OcrCode2, IdEmpleado, IdPeriodo, IdComprobante, IdLogPila)
						VALUES (
							'$Documento',
							'$Documento',
							$Secuencia, 
							'$CuentaDb', 
							'NULL',
							'$CentroDb', 
							'$ProyectoDb', 
							$Valor, 
							0, 
							'$FechaFinalP', 
							'$NombreCuenta', 
							'$Reference2',
							'$FechaFinalP', 
							'$FechaFinalP', 
							'$FechaFinalP', 
							'$CodigoSAPDb', 
							'', 
							0,
							0,
							'NULL',
							1,
							'PROY-05',
							$IdEmpleado,
							$IdPeriodo,
							$IdComprobante,
							$IdLogPila);
				EOD;
			}

			$this->query($query);

			$NombreCuenta 	= $datos['NombreCuenta'];

			if ($CuentaCr == $CuentaNomina)
				$NombreCuenta = 'NOMINA POR PAGAR';

			$query = <<<EOD
				SELECT DETALLESSAP.LineNum, 
						DETALLESSAP.Debit, 
						DETALLESSAP.Credit  
					FROM DETALLESSAP 
					WHERE DETALLESSAP.AccountCode = '$CuentaCr' AND 
						DETALLESSAP.CostingCode = '$CentroCr' AND 
						DETALLESSAP.ProjectCode = '$ProyectoCr' AND 
						DETALLESSAP.U_infoco01 = '$CodigoSAPCr'AND
						DETALLESSAP.IdEmpleado = $IdEmpleado AND
						DETALLESSAP.IdPeriodo = $IdPeriodo AND
						DETALLESSAP.IdComprobante = $IdComprobante; 
			EOD;

			$regNomina = $this->leer($query);

			if ($regNomina)
			{
				$LineNum = $regNomina['LineNum'];

				$query = <<<EOD
					UPDATE DETALLESSAP 
						SET Credit = DETALLESSAP.Credit + $Valor 
						WHERE DETALLESSAP.LineNum = $LineNum AND
							DETALLESSAP.IdEmpleado = $IdEmpleado AND
							DETALLESSAP.IdPeriodo = $IdPeriodo AND
							DETALLESSAP.IdComprobante = $IdComprobante;
				EOD;
			}
			else
			{
				$Secuencia++;

				$query = <<<EOD
					INSERT INTO DETALLESSAP
						(ConsecId, RecordKey, LineNum, AccountCode, ShortName, CostingCode, Projectcode, Debit, Credit, DueDate, LineMemo, Reference2, ReferenceDate1, ReferenceDate2, TaxDate, U_infoco01, U_codRet, U_BaseRet, U_TarifaRet, Procesado, CodCompania, OcrCode2, IdEmpleado, IdPeriodo, IdComprobante, IdLogPila)
						VALUES (
							'$Documento',
							'$Documento',
							$Secuencia, 
							'$CuentaCr', 
							'NULL',
							'$CentroCr', 
							'$ProyectoCr', 
							0, 
							$Valor, 
							'$FechaFinalP', 
							'$NombreCuenta', 
							'$Reference2',
							'$FechaFinalP', 
							'$FechaFinalP', 
							'$FechaFinalP', 
							'$CodigoSAPCr', 
							'', 
							0,
							0,
							'NULL',
							1,
							'PROY-05',
							$IdEmpleado,
							$IdPeriodo,
							$IdComprobante,
							$IdLogPila);
				EOD;
			}

			$this->query($query);

			return $Secuencia;
		}

		public function cuentasEmpleado()
		{
			$query = <<<EOD
				SELECT ACUMULADOS.IdPeriodo, 
						ACUMULADOS.Ciclo,
						COMPROBANTES.Id AS IdComprobante, 
						ACUMULADOS.IdEmpleado, 
						ACUMULADOS.IdConcepto,
						'23071' AS ConsecId, 
						'23071' AS RecodKey, 
						0 AS LineNum, 
						COMPROBANTES.CuentaDb AS AccountCode, 
						NULL AS ShortName, 
						CENTROS.Centro AS CostingCode, 
						iif(PROYECTOS.Centro IS NULL, 'N000', PROYECTOS.Centro) AS ProjectCode, 
						iif(COMPROBANTES.Porcentaje = 0, round(ACUMULADOS.Valor * PARAMETROS2.Valor2 / 100, 0),
						round(ACUMULADOS.Valor * COMPROBANTES.Porcentaje / 100, 0)) AS Debit, 
						0 AS Credit, 
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS DueDate, 
						iif(TIPODOC.TipoDocumento = 'NOM' OR TIPODOC.TipoDocumento = 'PARAF', COMPROBANTES.Detalle, TIPODOC.Nombre) AS LineMemo, 
						TIPODOC.Nombre + ' DE JULIO 2023' AS Reference2,
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS ReferenceDate1, 
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS ReferenceDate2, 
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS TaxDate, 
						iif(PARAMETROS3.Detalle = 'NO DETALLA', '', 
						iif(PARAMETROS3.Detalle = 'DETALLA POR EMPLEADO', EMPLEADOS.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR EPS - EMPLEADO', EPS.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR ARL - EMPLEADO', ARL.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR FONDO DE CESANTÍAS - EMPLEADO', FC.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR FONDO DE PENSIONES - EMPLEADO', FP.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR CCF - EMPLEADO', CCF.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR TERCERO - EMPLEADO', TERCEROS.CodigoSAP,'')))))))) AS U_InfoCo01, 
						'' AS U_CodRet, 
						0 AS U_BaseRet, 
						0 AS U_TarifaRet, 
						NULL AS Procesado, 
						1 AS CodCompania, 
						'PROY-05' AS OcrCod2
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.Idempleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.Idcentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS 
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						RIGHT JOIN COMPROBANTES 
							ON ACUMULADOS.IdConcepto = COMPROBANTES.IdConcepto 
						INNER JOIN TIPODOC 
							ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
						INNER JOIN AUXILIARES 
							ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.NivelRiesgo = PARAMETROS2.Id 
						INNER JOIN PERIODOS 
							ON ACUMULADOS.IdPeriodo = PERIODOS.Id
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON COMPROBANTES.TipoTercero = PARAMETROS3.Id 
						LEFT JOIN TERCEROS AS EPS 
							ON EMPLEADOS.IdEPS = EPS.Id 
						LEFT JOIN TERCEROS AS ARL 
							ON EMPLEADOS.IdARL = ARL.Id 
						LEFT JOIN TERCEROS AS FC 
							ON EMPLEADOS.IdFondoCesantiAS = FC.Id 
						LEFT JOIN TERCEROS AS FP 
							ON EMPLEADOS.IdFondoPensiones = FP.Id 
						LEFT JOIN TERCEROS AS CCF 
							ON EMPLEADOS.IdCajaCompensacion = CCF.Id 
						LEFT JOIN TERCEROS 
							ON ACUMULADOS.IdTercero = TERCEROS.Id 
					WHERE ACUMULADOS.IdPeriodo = 20 AND 
						CENTROS.TipoEmpleado = COMPROBANTES.TipoEmpleado AND 
						EMPLEADOS.Documento = '79284900' 
				UNION ALL
				SELECT ACUMULADOS.Idperiodo, 
						ACUMULADOS.Ciclo,
						COMPROBANTES.Id AS IdComprobante, 
						ACUMULADOS.IdEmpleado, 
						ACUMULADOS.IdConcepto,
						'23071' AS ConsecId, 
						'23071' AS RecodKey, 
						0 AS LineNum, 
						COMPROBANTES.CuentaCr AS AccountCode, 
						NULL AS ShortName, 
						CENTROS.Centro AS CostingCode, 
						iif(PROYECTOS.Centro IS NULL, 'N000', PROYECTOS.Centro) AS ProjectCode, 
						0 AS Debit, 
						iif(COMPROBANTES.Porcentaje = 0, round(ACUMULADOS.Valor * PARAMETROS2.valor2 / 100, 0),
						round(ACUMULADOS.Valor * COMPROBANTES.Porcentaje / 100, 0)) AS Credit, 
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS DueDate, 
						iif(TIPODOC.TipoDocumento = 'NOM' or TIPODOC.TipoDocumento = 'PARAF', COMPROBANTES.Detalle, TIPODOC.Nombre) AS LineMemo, 
						TIPODOC.Nombre + ' DE JULIO 2023' AS Reference2,
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS ReferenceDate1, 
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS ReferenceDate2, 
						concat(year(PERIODOS.FechaFinal), month(PERIODOS.FechaFinal), day(PERIODOS.FechaFinal)) AS TaxDate, 
						iif(PARAMETROS3.Detalle = 'NO DETALLA', '', 
						iif(PARAMETROS3.Detalle = 'DETALLA POR EMPLEADO', EMPLEADOS.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR EPS - EMPLEADO', EPS.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR ARL - EMPLEADO', ARL.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR FONDO DE CESANTÍAS - EMPLEADO', FC.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR FONDO DE PENSIONES - EMPLEADO', FP.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR CCF - EMPLEADO', CCF.CodigoSAP, 
						iif(PARAMETROS3.Detalle = 'DETALLA POR TERCERO - EMPLEADO', TERCEROS.CodigoSAP,'')))))))) AS U_InfoCo01,
						'' AS U_CodRet, 
						0 AS U_BaseRet, 
						0 AS U_TarifaRet, 
						NULL AS Procesado, 
						1 AS CodCompania, 
						'PROY-05' AS OcrCod2
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.Idcentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS 
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						RIGHT JOIN COMPROBANTES 
							ON ACUMULADOS.IdConcepto = COMPROBANTES.IdConcepto 
						INNER JOIN TIPODOC 
							ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
						INNER JOIN AUXILIARES 
							ON COMPROBANTES.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.NivelRiesgo = PARAMETROS2.Id 
						INNER JOIN PERIODOS 
							ON ACUMULADOS.IdPeriodo = PERIODOS.Id
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON COMPROBANTES.TipoTercero = PARAMETROS3.Id 
						LEFT JOIN TERCEROS AS EPS 
							ON EMPLEADOS.IdEPS = EPS.Id 
						LEFT JOIN TERCEROS AS ARL 
							ON EMPLEADOS.IdARL = ARL.Id 
						LEFT JOIN TERCEROS AS FC 
							ON EMPLEADOS.IdFondoCesantiAS = FC.Id 
						LEFT JOIN TERCEROS AS FP 
							ON EMPLEADOS.IdFondoPensiones = FP.Id 
						LEFT JOIN TERCEROS AS CCF 
							ON EMPLEADOS.IdCajaCompensacion = CCF.Id 
						LEFT JOIN TERCEROS 
							ON ACUMULADOS.IdTercero = TERCEROS.Id 
					WHERE ACUMULADOS.IdPeriodo = 20 AND 
						CENTROS.TipoEmpleado = COMPROBANTES.TipoEmpleado AND 
						EMPLEADOS.Documento = '79284900' 
			EOD;

		}
	}
?>