<?php
	class informeContabilizacionAcumuladosModel extends pgSQL
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
						LEFT JOIN EMPLEADOS 
							ON DETALLESSAP.U_InfoCo01 = EMPLEADOS.CodigoSAP 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function cuentasSAP($query)
		{
			// $query = <<<EOD
			// 	SELECT 
			// 			ACUMULADOS.IdEmpleado, 
			// 			ACUMULADOS.IdConcepto, 
			// 			AUXILIARES.Nombre, 
			// 			AUXILIARES.EsDispersable, 
			// 			ACUMULADOS.IdCentro, 
			// 			CETNROS.Centro, 
			// 			ACUMULADOS.Valor, 
			// 			DISPERSIONPORCENTRO.IdCentro, 
			// 			CENTROSD.Centro, 
			// 			DISPERSIONPORCENTRO.Porcentaje, 
			// 			iif(AUXILIARES.EsDispersable = 1, 
			// 				round(ACUMULADOS.Valor * DISPERSIONPORCENTRO.Porcentaje / 100, 0), 0) AS ValorDisperso
			// 		FROM ACUMULADOS  
			// 			INNER JOIN AUXILIARES  
			// 				ON ACUMULADOS.IdConcepto = AUXILIARES.Id
			// 			INNER JOIN CENTROS 
			// 				ON ACUMULADOS.IdCentro = CENTROS.Id
			// 			LEFT JOIN DISPERSIONPORCENTRO 
			// 				ON ACUMULADOS.IdPeriodo = DISPERSIONPORCENTRO.IdPeriodo AND 
			// 					ACUMULADOS.IdEmpleado = DISPERSIONPORCENTRO.IdEmpleado 
			// 			LEFT JOIN CENTROS AS CENTROSD 
			// 				ON DISPERSIONPORCENTRO.IdCentro = CENTROSD.Id 
			// 		WHERE ACUMULADOS.IdPeriodo = 21 AND 
			// 			ACUMULDOS.IdEmpleado = 1953;
			// EOD;

			$query = <<<EOD
				SELECT TIPODOC.TipoDocumento, 
						MAYORES.Mayor + AUXILIARES.Auxiliar AS Concepto, 
						COMPROBANTES.Detalle, 
						COMPROBANTES.CuentaDb, 
						iif(COMPROBANTES.DetallaCentroDb = 1, CENTROSDB.Centro, '') AS CentroDb, 
						iif(COMPROBANTES.DetallaCentroDb = 1, PROYECTOSDB.Centro, '') AS ProyectoDb, 
						COMPROBANTES.CuentaCr, 
						iif(COMPROBANTES.DetallaCentroCr = 1, CENTROSCR.Centro, '') AS CentroCr, 
						iif(COMPROBANTES.DetallaCentroCr = 1, PROYECTOSCR.Centro, '') AS ProyectoCr, 
						PARAMETROS1.Detalle AS TipoEmpleado, 
						ACUMULADOS.Base, 
						IIF(COMPROBANTES.Porcentaje = 0, PARAMETROS4.Valor2, COMPROBANTES.Porcentaje) AS Porcentaje, 
							cast(IIF(COMPROBANTES.Porcentaje = 0, 
								round(ACUMULADOS.Base * PARAMETROS4.Valor2 / 100, 0), 
								round(ACUMULADOS.Valor * COMPROBANTES.Porcentaje / 100, 0)) AS numeric(12,2)) AS Valor,
						iif(PARAMETROS2.Valor = 1, EMPLEADOS.Documento, 
							iif(PARAMETROS2.Valor = 2, EPS.Documento, 
							iif(PARAMETROS2.Valor = 3, ARL.Documento, 
							iif(PARAMETROS2.Valor = 4, FP.Documento, 
							iif(PARAMETROS2.Valor = 5, FC.Documento, 
							iif(PARAMETROS2.Valor = 6, CCF.Documento, 
							iif(PARAMETROS2.Valor = 7, TER.Documento, ''))))))) AS Tercero, 
						iif(PARAMETROS2.Valor = 1, EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2, 
						iif(PARAMETROS2.Valor = 2, EPS.Nombre, 
							iif(PARAMETROS2.Valor = 3, ARL.Nombre, 
							iif(PARAMETROS2.Valor = 4, FP.Nombre, 
							iif(PARAMETROS2.Valor = 5, FC.Nombre, 
							iif(PARAMETROS2.Valor = 6, CCF.Nombre, 
							iif(PARAMETROS2.Valor = 7, TER.Nombre, ''))))))) AS NombreTercero, 
						iif(PARAMETROS2.Valor = 1, EMPLEADOS.CodigoSAP, 
							iif(PARAMETROS2.Valor = 2, EPS.CodigoSAP, 
							iif(PARAMETROS2.Valor = 3, ARL.CodigoSAP, 
							iif(PARAMETROS2.Valor = 4, FP.CodigoSAP, 
							iif(PARAMETROS2.Valor = 5, FC.CodigoSAP, 
							iif(PARAMETROS2.Valor = 6, CCF.CodigoSAP, 
							iif(PARAMETROS2.Valor = 7, TER.CodigoSAP, ''))))))) AS CodigoSAPTercero, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2 AS NombreEmpleado, 
						EMPLEADOS.CodigoSAP AS CodigoSAPEmpleado, 
						PARAMETROS3.Detalle AS RegimenCesantias, 
						COMPROBANTES.Exonerable 
					FROM ACUMULADOS
						INNER JOIN COMPROBANTES ON ACUMULADOS.IdConcepto = COMPROBANTES.IdConcepto
						INNER JOIN EMPLEADOS ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN TIPODOC ON COMPROBANTES.IdTipoDoc = TIPODOC.Id 
						LEFT JOIN CENTROS AS CENTROSDB ON ACUMULADOS.IdCentro = CENTROSDB.Id 
						LEFT JOIN CENTROS AS PROYECTOSDB ON EMPLEADOS.IdProyecto = PROYECTOSDB.Id 
						LEFT JOIN CENTROS AS CENTROSCR ON ACUMULADOS.IdCentro = CENTROSCR.Id 
						LEFT JOIN CENTROS AS PROYECTOSCR ON EMPLEADOS.IdProyecto = PROYECTOSCR.Id 
						INNER JOIN AUXILIARES ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN CENTROS ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 ON CENTROS.TipoEmpleado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 ON COMPROBANTES.TipoTercero = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 ON EMPLEADOS.RegimenCesantias = PARAMETROS3.Id 
						INNER JOIN PARAMETROS AS PARAMETROS4 ON EMPLEADOS.NivelRiesgo = PARAMETROS4.Id 
						LEFT JOIN TERCEROS AS EPS ON EMPLEADOS.IdEps = EPS.Id
						LEFT JOIN TERCEROS AS FP ON EMPLEADOS.IdFondoPensiones = FP.Id
						LEFT JOIN TERCEROS AS FC ON EMPLEADOS.IdFondoCesantias = FC.Id
						LEFT JOIN TERCEROS AS CCF ON EMPLEADOS.IdCajaCompensacion = CCF.Id
						LEFT JOIN TERCEROS AS TER ON ACUMULADOS.IdTercero = TER.Id
						LEFT JOIN TERCEROS AS ARL ON EMPLEADOS.IdARL = ARL.Id 
					$query 
					ORDER BY EMPLEADOS.Documento,TIPODOC.TipoDocumento,Concepto;
			EOD;

			$request = $this->listar($query);

			return $request;
		}

		public function guardarRegistro(array $data)
		{
			$query = <<<EOD
				INSERT INTO INFORMECONTABILIZACIONSAP 
					(TipoDocumento, Concepto, Detalle, CuentaDb, CentroDb, ProyectoDb, CuentaCr, CentroCr, ProyectoCr, TipoEmpleado, Base, Porcentaje, Valor, Tercero, NombreTercero, CodigoSAPTercero, Documento, NombreEmpleado, CodigoSAPEmpleado)
					VALUES (
						:TipoDocumento, 
						:Concepto, 
						:Detalle, 
						:CuentaDb, 
						:CentroDb, 
						:ProyectoDb, 
						:CuentaCr, 
						:CentroCr, 
						:ProyectoCr,
						:TipoEmpleado, 
						:Porcentaje, 
						:Valor, 
						:Tercero, 
						:NombreTercero, 
						:CodigoSAPTercero, 
						:Documento, 
						:NombreEmpleado,
						:CodigoSAPEmpleado); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		

	}
?>
