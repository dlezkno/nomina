<?php
	class entrevista2Model extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS 
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCandidatos($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.*, 
						CARGOS.Nombre AS NombreCargo, 
						SICOLOGOS.Apellido1 AS Apellido1S, 
						SICOLOGOS.Apellido2 AS Apellido2S, 
						SICOLOGOS.Nombre1 AS Nombre1S, 
						SICOLOGOS.Nombre2 AS Nombre2S, 
						PARAMETROS.Detalle AS EstadoEmpleado 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS 
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						INNER JOIN EMPLEADOS AS SICOLOGOS 
							ON EMPLEADOS.IdSicologo = SICOLOGOS.Id 
					$query
			EOD;

			$request = $this->listar($query);
			return $request;
		}
	}
?>