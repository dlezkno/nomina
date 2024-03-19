<?php
	class incapacidadesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM INCAPACIDADES 
						INNER JOIN EMPLEADOS 
							ON INCAPACIDADES.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES 
							ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
					$query
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarIncapacidades($query)
		{
			$query = <<<EOD
				SELECT INCAPACIDADES.*, 
					EMPLEADOS.Documento, 
					EMPLEADOS.Apellido1, 
					EMPLEADOS.Apellido2,  
					EMPLEADOS.Nombre1, 
					EMPLEADOS.Nombre2, 
					CARGOS.Nombre AS NombreCargo, 
					CENTROS.Nombre AS NombreCentro, 
					MAYORES.Mayor, 
					AUXILIARES.Auxiliar, 
					AUXILIARES.Nombre AS NombreConcepto, 
					DIAGNOSTICOS.Nombre AS NombreDiagnostico  
				FROM INCAPACIDADES 
					INNER JOIN EMPLEADOS 
						ON INCAPACIDADES.IdEmpleado = EMPLEADOS.Id 
					LEFT JOIN CARGOS 
						ON EMPLEADOS.IdCargo = CARGOS.Id 
					LEFT JOIN CENTROS 
						ON EMPLEADOS.IdCentro = CENTROS.Id 
					INNER JOIN AUXILIARES 
						ON INCAPACIDADES.IdConcepto = AUXILIARES.Id 
					INNER JOIN MAYORES 
						ON AUXILIARES.IdMayor = MAYORES.Id 
					LEFT JOIN DIAGNOSTICOS 
						ON INCAPACIDADES.IdDiagnostico = DIAGNOSTICOS.Id 
				$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarIncapacidad(array $data)
		{
			$query = <<<EOD
				INSERT INTO INCAPACIDADES (
					IdEmpleado, IdConcepto, FechaIncapacidad, FechaInicio, DiasIncapacidad, PorcentajeAuxilio, BaseLiquidacion, EsProrroga, IdDiagnostico )
					VALUES (
					:IdEmpleado, 
					:IdConcepto, 
					:FechaIncapacidad, 
					:FechaInicio, 
					:DiasIncapacidad, 
					:PorcentajeAuxilio, 
					:BaseLiquidacion, 
					:EsProrroga, 
					:IdDiagnostico); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarIncapacidad($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarIncapacidad(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE INCAPACIDADES
					SET 
						IdEmpleado = :IdEmpleado,
						IdConcepto = :IdConcepto, 
						FechaIncapacidad = :FechaIncapacidad, 
						FechaInicio = :FechaInicio, 
						DiasIncapacidad = :DiasIncapacidad, 
						PorcentajeAuxilio = :PorcentajeAuxilio, 
						BaseLiquidacion = :BaseLiquidacion, 
						EsProrroga = :EsProrroga, 
						IdDiagnostico = :IdDiagnostico, 
						FechaActualizacion = getDate()
				WHERE INCAPACIDADES.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarIncapacidad(int $id)
		{
			$query = <<<EOD
				DELETE FROM INCAPACIDADES 
				WHERE INCAPACIDADES.Id = $id
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}

		public function exportarIncapacidades()
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2 AS NombreEmpleado, 
						AUXILIARES.Nombre AS ClaseAusentismo, 
						INCAPACIDADES.FechaIncapacidad, 
						INCAPACIDADES.FechaInicio, 
						INCAPACIDADES.DiasIncapacidad, 
						INCAPACIDADES.DiasCausados, 
						INCAPACIDADES.PorcentajeAuxilio, 
						DIAGNOSTICOS.Diagnostico, 
						DIAGNOSTICOS.Nombre AS DescripcionDiagnostico, 
						INCAPACIDADES.FechaCreacion AS FechaRegistro
					FROM INCAPACIDADES 
						INNER JOIN EMPLEADOS 
							ON INCAPACIDADES.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN AUXILIARES 
							ON INCAPACIDADES.IdConcepto = AUXILIARES.Id
						LEFT JOIN DIAGNOSTICOS 
							ON INCAPACIDADES.IdDiagnostico = DIAGNOSTICOS.Id 
					ORDER BY INCAPACIDADES.FechaInicio, EMPLEADOS.Documento;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
	}
?>