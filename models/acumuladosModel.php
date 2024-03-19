<?php
	class acumuladosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query, $resumen = false)
		{
			if	($resumen)
			{
				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN CENTROS 
								ON ACUMULADOS.IdCentro = CENTROS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1 
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2 
								ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						$query 
						GROUP BY EMPLEADOS.Documento, MAYORES.Mayor, AUXILIARES.Auxiliar;
				EOD; 

				$request = $this->listar($query);
				return count($request);
			}
			else
			{
				$query = <<<EOD
					SELECT COUNT(*) AS Registros  
						FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN CENTROS 
								ON ACUMULADOS.IdCentro = CENTROS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1 
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2 
								ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						$query;
				EOD;

				$request = $this->leer($query);
				return $request['Registros'];
			}
		}

		public function listarAcumulados($query, $resumen = false)
		{
			if	($resumen)
			{
				$query = <<<EOD
					SELECT EMPLEADOS.Id, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							CENTROS.Centro, 
							CENTROS.Nombre AS NombreCentro, 
							PROYECTOS.Centro AS Proyecto, 
							PROYECTOS.Nombre AS NombreProyecto, 
							MAYORES.Mayor, 
							AUXILIARES.Auxiliar, 
							AUXILIARES.Nombre AS NombreConcepto, 
							PARAMETROS1.Detalle AS Imputacion, 
							PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
							TERCEROS.Nombre AS NombreTercero,
							MIN(ACUMULADOS.FechaInicialPeriodo) AS FechaInicialPeriodo, 
							MAX(ACUMULADOS.FechaFinalPeriodo) AS FechaFinalPeriodo, 
							SUM(ACUMULADOS.Horas) AS Horas, 
							SUM(ACUMULADOS.Valor) AS Valor 
						FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN CENTROS 
								ON ACUMULADOS.IdCentro = CENTROS.Id 
							LEFT JOIN CENTROS AS PROYECTOS 
								ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1 
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2 
								ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
							LEFT JOIN TERCEROS 
								ON ACUMULADOS.IdTercero = TERCEROS.Id 
						$query;
				EOD;
			}
			else
			{
				$query = <<<EOD
					SELECT EMPLEADOS.Id, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							CENTROS.Centro, 
							CENTROS.Nombre AS NombreCentro, 
							PROYECTOS.Centro AS Proyecto, 
							PROYECTOS.Nombre AS NombreProyecto, 
							MAYORES.Mayor, 
							AUXILIARES.Auxiliar, 
							AUXILIARES.Nombre AS NombreConcepto, 
							PARAMETROS1.Detalle AS Imputacion, 
							PARAMETROS2.Detalle AS NombreTipoLiquidacion, 
							ACUMULADOS.FechaInicialPeriodo, 
							ACUMULADOS.FechaFinalPeriodo, 
							ACUMULADOS.Horas, 
							ACUMULADOS.Valor, 
							TERCEROS.Nombre AS NombreTercero 
						FROM ACUMULADOS 
							INNER JOIN EMPLEADOS 
								ON ACUMULADOS.IdEmpleado = EMPLEADOS.Id 
							INNER JOIN CENTROS 
								ON ACUMULADOS.IdCentro = CENTROS.Id 
							LEFT JOIN CENTROS AS PROYECTOS 
								ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
							INNER JOIN AUXILIARES 
								ON ACUMULADOS.IdConcepto = AUXILIARES.Id 
							INNER JOIN MAYORES 
								ON AUXILIARES.IdMayor = MAYORES.Id 
							INNER JOIN PARAMETROS AS PARAMETROS1 
								ON AUXILIARES.Imputacion = PARAMETROS1.Id 
							INNER JOIN PARAMETROS AS PARAMETROS2 
								ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
							LEFT JOIN TERCEROS 
								ON ACUMULADOS.idTercero = TERCEROS.Id 
						$query;
				EOD;
			}

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarAcumulado(array $data)
		{
			$query = <<<EOD
				INSERT INTO ACUMULADOS 
					(IdPeriodo, Ciclo, IdEmpleado, IdConcepto, Horas, Valor, 
					IdCentro, TipoEmpleado, IdCredito, FechaInicial, FechaFinal)
					VALUES (
						:IdPeriodo,
						:Ciclo,
						:IdEmpleado,
						:IdConcepto,
						:Horas,
						:Valor,
						:IdCentro, 
						:TipoEmpleado,
						:IdCredito,
						:FechaInicial,
						:FechaFinal);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
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

		public function guardarLogEmpleado($data)
		{
			$query = <<<EOD
				INSERT INTO LOGEMPLEADOS 
					(IdEmpleado, Campo, ValorAnterior, ValorActual, IdUsuario)
					VALUES (
						:IdEmpleado, 
						:Campo, 
						:ValorAnterior, 
						:ValorActual,
						:IdUsuario);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
	}
?>