<?php
	class liquidacionPrenominaModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function contarRegistros($ArchivoNomina, $query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM $ArchivoNomina 
						INNER JOIN EMPLEADOS
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN AUXILIARES
							ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
					$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarLiquidacionPrenomina($ArchivoNomina, $query1, $query)
		{
			$query = <<<EOD
				WITH TOTALES AS (
					SELECT EMPLEADOS.Id, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, 0)) AS TotalDb,
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', $ArchivoNomina.Valor, 0)) AS TotalCr
						FROM $ArchivoNomina
							INNER JOIN EMPLEADOS 
								ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id
							LEFT JOIN CENTROS 
								ON EMPLEADOS.IdCentro = CENTROS.Id 
							LEFT JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							INNER JOIN AUXILIARES 
								ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
							INNER JOIN PARAMETROS  
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						$query1
						GROUP BY EMPLEADOS.Id )
				SELECT PERIODOS.Periodo, 
						$ArchivoNomina.Ciclo, 
						PERIODOS.FechaInicial, 
						PERIODOS.FechaFinal, 
						EMPLEADOS.Id, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.SueldoBasico, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaVencimiento, 
						EMPLEADOS.HorasMes, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						PARAMETROS1.Detalle AS Imputacion,
						PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
						PARAMETROS3.Detalle AS EstadoEmpleado, 
						$ArchivoNomina.Base, 
						$ArchivoNomina.Horas, 
						$ArchivoNomina.Valor, 
						$ArchivoNomina.Saldo, 
						$ArchivoNomina.FechaInicial AS FechaInicialVC, 
						$ArchivoNomina.FechaFinal AS FechaFinalVC, 
						$ArchivoNomina.Liquida,
						TOTALES.TotalDb, 
						TOTALES.TotalCr 
					FROM $ArchivoNomina
						INNER JOIN PERIODOS 
							ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN AUXILIARES 
							ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON EMPLEADOS.Estado = PARAMETROS3.Id 
						INNER JOIN TOTALES  
							ON EMPLEADOS.Id = TOTALES.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function informePrenomina($ArchivoNomina, $query1, $query)
		{
			$query = <<<EOD
				WITH TOTALES AS (
					SELECT AUXILIARES.Id, 
						SUM(IIF(PARAMETROS.Detalle = 'PAGO', $ArchivoNomina.Valor, 0)) AS TotalDb,  
						SUM(IIF(PARAMETROS.Detalle = 'DEDUCCIÓN', $ArchivoNomina.Valor, 0)) AS TotalCr
						FROM $ArchivoNomina
							INNER JOIN EMPLEADOS 
								ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id
							LEFT JOIN CENTROS 
								ON EMPLEADOS.IdCentro = CENTROS.Id 
							LEFT JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							INNER JOIN AUXILIARES 
								ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
							INNER JOIN PARAMETROS  
								ON AUXILIARES.Imputacion = PARAMETROS.Id 
						$query1
						GROUP BY AUXILIARES.Id )
				SELECT PERIODOS.Periodo, 
						$ArchivoNomina.Ciclo, 
						PERIODOS.FechaInicial, 
						PERIODOS.FechaFinal, 
						EMPLEADOS.Id, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.SueldoBasico, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaVencimiento, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						MAYORES.Mayor,
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						PARAMETROS1.Detalle AS Imputacion,
						PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
						$ArchivoNomina.Horas, 
						$ArchivoNomina.Valor, 
						$ArchivoNomina.Saldo, 
						$ArchivoNomina.FechaInicial AS FechaInicialVC, 
						$ArchivoNomina.FechaFinal AS FechaFinalVC, 
						$ArchivoNomina.Liquida,
						TOTALES.TotalDb, 
						TOTALES.TotalCr 
					FROM $ArchivoNomina
						INNER JOIN PERIODOS 
							ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN AUXILIARES 
							ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						INNER JOIN TOTALES  
							ON AUXILIARES.Id = TOTALES.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function leerRegistro($query)
		{
			$request = $this->leer($query);
			return $request;
		}	

		public function listarRegistros($query)
		{
			$request = $this->listar($query);
			return $request;
		}	

		public function actualizarRegistros($query)
		{
			$request = $this->query($query);
			return $request;
		}	

		public function guardarNovedad($ArchivoNomina, $datos)
		{
			$query = <<<EOD
				INSERT INTO $ArchivoNomina 
					(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Base, Porcentaje, Horas, Valor, Saldo, FechaInicial, FechaFinal, Liquida, Afecta, IdCentro, TipoEmpleado, IdTercero, IdCredito) 
					VALUES ( 
						:IdPeriodo, 
						:Ciclo, 
						:IdEmpleado, 
						:IdConcepto, 
						:Base, 
						:Porcentaje, 
						:Horas, 
						:Valor, 
						:Saldo, 
						:FechaInicial, 
						:FechaFinal, 
						:Liquida, 
						:Afecta, 
						:IdCentro, 
						:TipoEmpleado, 
						:IdTercero, 
						:IdCredito);
			EOD;

			$id = $this->adicionar($query, $datos);
			return $id;
		}

		public function liquidar(array $data)
		{
			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");

			$Referencia = $data['Referencia'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Referencia,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");

			$Periodicidad = $data['Periodicidad'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Periodicidad,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");

			$Periodo = $data['Periodo'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Periodo,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Ciclo = $data['Ciclo'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Ciclo,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'FechaLimiteNovedades'");

			$Fecha = $data['FechaLimiteNovedades'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Fecha = '$Fecha',
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			return $resp;
		}

		public function exportarPrenomina($ArchivoNomina, $query)
		{
			$query = <<<EOD
				SELECT PERIODOS.FechaInicial AS FechaInicialPeriodo, 
						PERIODOS.FechaFinal AS FechaFinalPeriodo, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						$ArchivoNomina.Base, 
						IIF(PARAMETROS2.Detalle = 'HORAS', $ArchivoNomina.Horas, $ArchivoNomina.Horas / 8) AS Horas, 
						IIF(PARAMETROS2.Detalle = 'HORAS', 'HORAS', IIF(PARAMETROS2.Detalle = 'DÍAS', 'DIAS', '')) AS Tiempo, 
						IIF(PARAMETROS1.Detalle = 'PAGO', $ArchivoNomina.Valor, 0) AS Pagos, 
						IIF(PARAMETROS1.Detalle <> 'PAGO', $ArchivoNomina.Valor, 0) AS Deducciones, 
						$ArchivoNomina.FechaInicial, 
						$ArchivoNomina.FechaFinal, 
						TERCEROS.Nombre AS NombreTercero 
					FROM $ArchivoNomina 
						INNER JOIN PERIODOS ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
						INNER JOIN EMPLEADOS ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						LEFT JOIN TERCEROS ON $ArchivoNomina.IdTercero = TERCEROS.Id 
					$query; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}
	}
?>