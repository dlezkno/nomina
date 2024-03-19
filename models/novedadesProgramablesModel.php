<?php
	class novedadesProgramablesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM NOVEDADESPROGRAMABLES 
						INNER JOIN AUXILIARES
							ON NOVEDADESPROGRAMABLES.IdConcepto = AUXILIARES.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1 
							ON NOVEDADESPROGRAMABLES.TipoEmpleado = PARAMETROS1.Id 
						LEFT JOIN EMPLEADOS
							ON NOVEDADESPROGRAMABLES.IdEmpleado = EMPLEADOS.Id 
						LEFT JOIN CENTROS
							ON NOVEDADESPROGRAMABLES.IdCentro = CENTROS.Id 
						LEFT JOIN CARGOS
							ON NOVEDADESPROGRAMABLES.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON NOVEDADESPROGRAMABLES.Aplica = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON NOVEDADESPROGRAMABLES.ModoLiquidacion = PARAMETROS3.Id 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarNovedadesProgramables($query)
		{
			$query = <<<EOD
				SELECT NOVEDADESPROGRAMABLES.*, 
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						AUXILIARES.Nombre AS NombreConcepto, 
						PARAMETROS1.Detalle AS NombreTipoEmpleado,
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						PARAMETROS2.Detalle AS NombreAplica,  
						PARAMETROS3.Detalle AS NombreModoLiquidacion, 
						PARAMETROS4.Detalle AS NombreEstado,   
						TERCEROS.Documento AS DocumentoTercero, 
						TERCEROS.Nombre AS NombreTercero 
					FROM NOVEDADESPROGRAMABLES 
						INNER JOIN AUXILIARES
							ON NOVEDADESPROGRAMABLES.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES
							ON AUXILIARES.IdMayor = MAYORES.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1 
							ON NOVEDADESPROGRAMABLES.TipoEmpleado = PARAMETROS1.Id 
						LEFT JOIN EMPLEADOS
							ON NOVEDADESPROGRAMABLES.IdEmpleado = EMPLEADOS.Id 
						LEFT JOIN CENTROS
							ON NOVEDADESPROGRAMABLES.IdCentro = CENTROS.Id 
						LEFT JOIN CARGOS
							ON NOVEDADESPROGRAMABLES.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON NOVEDADESPROGRAMABLES.Aplica = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON NOVEDADESPROGRAMABLES.ModoLiquidacion = PARAMETROS3.Id 
						INNER JOIN PARAMETROS AS PARAMETROS4 
							ON NOVEDADESPROGRAMABLES.Estado = PARAMETROS4.Id 
						LEFT JOIN TERCEROS 
							ON NOVEDADESPROGRAMABLES.IdTercero = TERCEROS.Id 
					$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarNovedadProgramable(array $data)
		{
			$query = <<<EOD
				INSERT INTO NOVEDADESPROGRAMABLES (
					Fecha, IdConcepto, TipoEmpleado, IdEmpleado, IdCentro, IdCargo,  
					Horas, Valor, SalarioLimite, FechaLimite, IdTercero, Aplica, ModoLiquidacion, Estado) 
					VALUES (
					:Fecha, 
					:IdConcepto,
					:TipoEmpleado, 
					:IdEmpleado, 
					:IdCentro, 
					:IdCargo, 
					:Horas, 
					:Valor, 
					:SalarioLimite, 
					:FechaLimite, 
					:IdTercero, 
					:Aplica, 
					:ModoLiquidacion, 
					:Estado);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarNovedadProgramable($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarNovedadProgramable(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE NOVEDADESPROGRAMABLES
				SET 
					Fecha 				= :Fecha, 
					IdConcepto 			= :IdConcepto,
					TipoEmpleado 		= :TipoEmpleado, 
					IdEmpleado 			= :IdEmpleado,
					IdCentro 			= :IdCentro, 
					IdCargo 			= :IdCargo, 
					Horas 				= :Horas, 
					Valor 				= :Valor, 
					SalarioLimite 		= :SalarioLimite, 
					FechaLimite 		= :FechaLimite, 
					IdTercero 			= :IdTercero, 
					Aplica 				= :Aplica, 
					ModoLiquidacion 	= :ModoLiquidacion, 
					Estado 				= :Estado, 
					FechaActualizacion 	= getDate()
				WHERE NOVEDADESPROGRAMABLES.Id = $id
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarNovedadProgramable(int $id)
		{
			$query = <<<EOD
				DELETE FROM NOVEDADESPROGRAMABLES 
					WHERE NOVEDADESPROGRAMABLES.Id = $id
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>