<?php
	class estadoSolicitudModel extends pgSQL
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
						INNER JOIN PARAMETROS AS PARAMETROS1
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarEmpleados($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.*, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Nombre AS NombreCentro, 
						PROYECTOS.Nombre AS NombreProyecto, 
						PARAMETROS1.Detalle AS EstadoEmpleado,    
						PARAMETROS2.Detalle AS TipoContrato 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
					$query
			EOD;

			$request = $this->listar($query);
			return $request;
		}
	}
?>