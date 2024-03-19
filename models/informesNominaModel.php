<?php
	class informesNominaModel extends pgSQL
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

		public function nominaPorEmpleado($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorConcepto($query)
		{
			$query = <<<EOD
				SELECT MAYORES.Mayor,
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						COUNT(*) AS Registros, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES  
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre  
					ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorCentro($query)
		{
			$query = <<<EOD
				SELECT CENTROS.Centro,
						CENTROS.Nombre AS NombreCentro, 
						COUNT(*) AS Registros, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY CENTROS.Centro, CENTROS.Nombre   
					ORDER BY CENTROS.Nombre; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorFormaPago($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						PARAMETROS1.Detalle AS FormaDePago, 
						BANCOS.Nombre AS NombreBanco, 
						PARAMETROS2.Detalle AS TipoCuentaBancaria, 
						EMPLEADOS.CuentaBancaria, 
						SUM(IIF(PARAMETROS3.Detalle = 'PAGO', ACUMULADOS.Valor, - ACUMULADOS.Valor)) AS ValorAPagar,
						EMPLEADOS.fechaliquidacion,
						ACUMULADOS.fechacreacion,
						CENTROS.centro AS centro,
						PROYECTOS.centro AS proyecto
					FROM ACUMULADOS
						INNER JOIN EMPLEADOS ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 ON EMPLEADOS.FormaDePago = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 ON EMPLEADOS.TipoCuentaBancaria = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 ON AUXILIARES.Imputacion = PARAMETROS3.Id 
						LEFT JOIN BANCOS ON EMPLEADOS.IdBanco = BANCOS.Id 
						LEFT JOIN CENTROS ON ACUMULADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
					$query 
					GROUP BY EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2,
						EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, PARAMETROS1.Detalle, BANCOS.Nombre,
						PARAMETROS2.Detalle, EMPLEADOS.CuentaBancaria, EMPLEADOS.fechaliquidacion,
						CENTROS.centro, PROYECTOS.centro , ACUMULADOS.fechacreacion
					ORDER BY PARAMETROS1.Detalle, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorCentroConcepto($query)
		{
			$query = <<<EOD
				SELECT CENTROS.Centro,
						CENTROS.Nombre AS NombreCentro, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY CENTROS.Centro, CENTROS.Nombre, MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre    
					ORDER BY CENTROS.Centro, MAYORES.Mayor, AUXILIARES.Auxiliar; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorConceptoCentro($query)
		{
			$query = <<<EOD
				SELECT MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						CENTROS.Centro,
						CENTROS.Nombre AS NombreCentro, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
								ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, CENTROS.Centro, CENTROS.Nombre 
					ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar, CENTROS.Centro; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorEPS($query)
		{
			$query = <<<EOD
				WITH curSALARIO AS (
					SELECT EMPLEADOS.Id AS IdEmpleado, 
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, - ACUMULADOS.Valor)) AS IBC 
					FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1  
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2 
								ON MAYORES.ClaseConcepto = PARAMETROS2.Id
							INNER JOIN PARAMETROS AS PARAMETROS3
								ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
						$query AND 
							PARAMETROS2.Detalle = 'SALARIO' AND 
							PARAMETROS3.Detalle <> 'APRENDIZ DEL SENA' 
						GROUP BY EMPLEADOS.Id )
					SELECT TERCEROS.Documento AS NitEPS, 
							TERCEROS.Nombre AS NombreEPS, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							curSALARIO.IBC, 
							SUM(IIF(PARAMETROS1.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, - ACUMULADOS.Valor)) AS ValorEPS
						FROM ACUMULADOS 
							INNER JOIN curSALARIO 
								ON ACUMULADOS.IdEmpleado = curSALARIO.IdEmpleado 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN TERCEROS 
								ON EMPLEADOS.IdEPS = TERCEROS.Id 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1 
								ON AUXILIARES.Imputacion = PARAMETROS1.Id
							INNER JOIN PARAMETROS AS PARAMETROS2
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id 
						$query AND 
							( PARAMETROS2.Detalle = 'ES APORTE DE SALUD' OR 
							PARAMETROS2.Detalle = 'ES DEVOLUCIÓN SALUD' )
						GROUP BY TERCEROS.Documento, TERCEROS.Nombre, EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, curSALARIO.IBC 
						ORDER BY TERCEROS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2 
			EOD;
				
			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorFP($query)
		{
			$query = <<<EOD
				WITH curSALARIO AS (
					SELECT EMPLEADOS.Id AS IdEmpleado, 
						SUM(IIF(PARAMETROS1.Detalle = 'PAGO', ACUMULADOS.Valor, - ACUMULADOS.Valor)) AS IBC 
					FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1  
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2 
								ON MAYORES.ClaseConcepto = PARAMETROS2.Id
							INNER JOIN PARAMETROS AS PARAMETROS3
								ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
						$query AND 
							PARAMETROS2.Detalle = 'SALARIO' AND 
							PARAMETROS3.Detalle <> 'APRENDIZ DEL SENA' 
						GROUP BY EMPLEADOS.Id )
					SELECT TERCEROS.Documento AS NitFP, 
							TERCEROS.Nombre AS NombreFP, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							curSALARIO.IBC, 
							SUM(IIF(PARAMETROS1.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, - ACUMULADOS.Valor)) AS ValorFP
						FROM ACUMULADOS 
							INNER JOIN curSALARIO 
								ON ACUMULADOS.IdEmpleado = curSALARIO.IdEmpleado 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							LEFT JOIN TERCEROS 
								ON EMPLEADOS.IdFondoPensiones = TERCEROS.Id 
							INNER JOIN AUXILIARES
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1 
								ON AUXILIARES.Imputacion = PARAMETROS1.Id
							INNER JOIN PARAMETROS AS PARAMETROS2
								ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id 
						$query AND 
							PARAMETROS2.Detalle = 'ES APORTE DE PENSIÓN' 
						GROUP BY TERCEROS.Documento, TERCEROS.Nombre, EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, curSALARIO.IBC 
						ORDER BY TERCEROS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2 
			EOD;
				
			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorConceptoEmpleado($query)
		{
			$query = <<<EOD
				SELECT MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
								ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Centro, CENTROS.Nombre 
					ORDER BY MAYORES.Mayor, AUXILIARES.Auxiliar, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function planillaPILAE($query, $ArchivoNomina)
		{
			$query = <<<EOD
				SELECT DISTINCT $ArchivoNomina.IdEmpleado, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM $ArchivoNomina 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.TipoContrato = PARAMETROS.Id 
					$query AND
						PARAMETROS.Detalle <> 'PASANTÍA'
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function planillaPILAK($query, $ArchivoNomina)
		{
			$query = <<<EOD
				SELECT DISTINCT $ArchivoNomina.IdEmpleado, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM $ArchivoNomina 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.TipoContrato = PARAMETROS.Id 
					$query AND
						PARAMETROS.Detalle = 'PASANTÍA'
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function limpiarDataPILAE($IdPeriodo)
		{
			$this->query(<<<EOD
				DELETE FROM nomina.log_pila WHERE idperiodo=$IdPeriodo
			EOD);
		}

		public function guardarPILAE(array $data)
		{
			$query = <<<EOD
				INSERT INTO nomina.log_pila
					(
						idperiodo,
						ciclo,
						idempleado,
						archivo,
						idsarchivo,
						idsconcepto,
						dias,
						ibcpension,
						ibcsalud,
						ibcarl,
						ibcccf,
						ibcsena,
						ibcicbf,
						ibcsolidaridad,
						ibcsubsistencia,
						tarifapension,
						tarifasalud,
						tarifaarl,
						tarifaccf,
						tarifasena,
						tarifaicbf,
						tarifasolidaridad,
						tarifasubsistencia,
						valorpension,
						valorsalud,
						valorarl,
						valorccf,
						valorsena,
						valoricbf,
						valorsolidaridad,
						valorsubsistencia,
						linea
					)
					VALUES (
						:idperiodo,
						:ciclo,
						:idempleado,
						:archivo,
						:idsarchivo,
						:idsconcepto,
						:dias,
						:ibcpension,
						:ibcsalud,
						:ibcarl,
						:ibcccf,
						:ibcsena,
						:ibcicbf,
						:ibcsolidaridad,
						:ibcsubsistencia,
						:tarifapension,
						:tarifasalud,
						:tarifaarl,
						:tarifaccf,
						:tarifasena,
						:tarifaicbf,
						:tarifasolidaridad,
						:tarifasubsistencia,
						:valorpension,
						:valorsalud,
						:valorarl,
						:valorccf,
						:valorsena,
						:valoricbf,
						:valorsolidaridad,
						:valorsubsistencia,
						:linea
					); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}

		public function prenominaPorEmpleadoConcepto($query, $ArchivoNomina)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2,   
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', $ArchivoNomina.Valor, 0)) AS ValorDeducciones
					FROM $ArchivoNomina 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
								ON $ArchivoNomina.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, CENTROS.Centro, CENTROS.Nombre 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar ;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function nominaPorEmpleadoConcepto($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2,   
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', ACUMULADOS.Valor, 0)) AS ValorPagos, 
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', ACUMULADOS.Valor, 0)) AS ValorDeducciones
					FROM ACUMULADOS 
						INNER JOIN EMPLEADOS 
							ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON ACUMULADOS.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS 
							ON AUXILIARES.Imputacion = PARAMETROS.Id 
					$query 
					GROUP BY EMPLEADOS.Documento, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar, AUXILIARES.Nombre, CENTROS.Centro, CENTROS.Nombre 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, MAYORES.Mayor, AUXILIARES.Auxiliar ;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
	}
?>