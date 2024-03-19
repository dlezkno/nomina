<?php
	class dispersionNominaModel extends pgSQL
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
				SELECT DETALLESSAP.AccountCode, 
						DETALLESSAP.CostingCode, 
						DETALLESSAP.ProjectCode, 
						DETALLESSAP.U_InfoCo01, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						DETALLESSAP.LineMemo, 
						DETALLESSAP.Debit, 
						DETALLESSAP.Credit
					FROM DETALLESSAP 
						INNER JOIN EMPLEADOS 
							ON DETALLESSAP.U_InfoCo01 = EMPLEADOS.CodigoSAP 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function dispersionNomina($query)
		{
			$query = <<<EOD
				SELECT PARAMETROS1.Detalle AS TipoIdentificacion, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Documento2, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.Direccion, 
						EMPLEADOS.Email, 
						EMPLEADOS.CuentaBancaria, 
						PARAMETROS2.Detalle AS TipoCuentaBancaria, 
						BANCOS.Banco, 
						BANCOS.Nombre AS NombreBanco, 
						SUM(IIF(PARAMETROS3.Detalle = 'PAGO', ACUMULADOS.Valor, -ACUMULADOS.Valor)) AS ValorPago 
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.TipoIdentificacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoCuentaBancaria = PARAMETROS2.Id 
						INNER JOIN BANCOS 
							ON EMPLEADOS.IdBanco = BANCOS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON AUXILIARES.Imputacion = PARAMETROS3.Id
					$query 
					GROUP BY PARAMETROS1.Detalle, EMPLEADOS.Documento, EMPLEADOS.Documento2, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, EMPLEADOS.Direccion, EMPLEADOS.Email, EMPLEADOS.CuentaBancaria, PARAMETROS2.Detalle, BANCOS.Banco, BANCOS.Nombre;
			EOD;

			$request = $this->listar($query);

			return $request;
		}

		public function actualizarPagoDispersionNomina($query)
		{
			$query = str_replace('WHERE ACUMULADOS', 'ACUMULADOS', $query);

			$query = str_replace('ACUMULADOS.PagoDispersado = 0 AND', '', $query); 

			$query = <<<EOD
				UPDATE ACUMULADOS 
					SET PagoDispersado = 1 
					FROM EMPLEADOS, BANCOS
					WHERE ACUMULADOS.IdEmpleado = EMPLEADOS.Id AND 
						EMPLEADOS.IdBanco = BANCOS.Id AND 
					$query;
			EOD;

			$ok = $this->query($query);

			return $ok;
		}
	}
?>