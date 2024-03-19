<?php
	class desprendiblesNominaModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function comprobantePago($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.SueldoBasico, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaVencimiento, 
						EMPLEADOS.Email, 
						PARAMETROS3.Detalle AS NombreFormaDePago, 
						BANCOS.Nombre AS NombreBanco, 
						EMPLEADOS.CuentaBancaria, 
						PARAMETROS4.Detalle AS NombreTipoCuentaBancaria, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						TERCEROS1.Nombre AS NombreEPS, 
						TERCEROS2.Nombre AS NombreFondoPension, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						PARAMETROS1.Detalle AS Imputacion,
						PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
						ACUMULADOS.Horas, 
						ACUMULADOS.Valor, 
						ACUMULADOS.Saldo 
					FROM ACUMULADOS
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CENTROS 
							ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN TERCEROS AS TERCEROS1 
							ON EMPLEADOS.IdEPS = TERCEROS1.Id 
						LEFT JOIN TERCEROS AS TERCEROS2 
							ON EMPLEADOS.IdFondoPensiones = TERCEROS2.Id 
						LEFT JOIN BANCOS 
							ON EMPLEADOS.IdBanco = BANCOS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON EMPLEADOS.FormaDePago = PARAMETROS3.Id 
						INNER JOIN PARAMETROS AS PARAMETROS4 
							ON EMPLEADOS.TipoCuentaBancaria = PARAMETROS4.Id 
					$query 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar 
			EOD;

			$request = $this->listar($query);
			return $request;
		}
	}
?>