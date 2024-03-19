<?php
	class renovacionesModel extends pgSQL
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
				INNER JOIN CENTROS ON EMPLEADOS.IdCentro = CENTROS.Id OR EMPLEADOS.IdProyecto = CENTROS.Id
				INNER JOIN CARGOS ON EMPLEADOS.IdCargo = CARGOS.Id  
				INNER JOIN PARAMETROS ON EMPLEADOS.Estado = PARAMETROS.Id 
				INNER JOIN PARAMETROS AS PARAMETROS2 ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
				LEFT JOIN EMPLEADOS AS GP ON CENTROS.IdGerente = GP.Id 
				LEFT JOIN RENOVACIONES  ON EMPLEADOS.Id = RENOVACIONES.IdEmpleado 
				$query
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarRenovaciones($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Id AS IdEmpleado, 
					EMPLEADOS.Documento, 
					EMPLEADOS.Apellido1, 
					EMPLEADOS.Apellido2, 
					EMPLEADOS.Nombre1, 
					EMPLEADOS.Nombre2, 
					CARGOS.Nombre AS NombreCargo, 
					CENTROS.Centro, 
					CENTROS.Nombre AS NombreCentro, 
					PARAMETROS2.Detalle AS TipoContrato, 
					EMPLEADOS.SueldoBasico, 
					EMPLEADOS.FechaIngreso, 
					EMPLEADOS.FechaVencimiento, 
					EMPLEADOS.FechaRetiro, 
					EMPLEADOS.Prorrogas, 
					PARAMETROS.Detalle AS EstadoEmpleado, 
					PARAMETROS2.Detalle AS TipoContrato, 
					GP.Id AS IdGP, 
					GP.Apellido1 AS Apellido1GP, 
					GP.Nombre1 AS Nombre1GP, 
					RENOVACIONES.CorreoEnviado  
				FROM EMPLEADOS  
				INNER JOIN CENTROS ON EMPLEADOS.IdCentro = CENTROS.Id OR EMPLEADOS.IdProyecto = CENTROS.Id
				INNER JOIN CARGOS ON EMPLEADOS.IdCargo = CARGOS.Id  
				INNER JOIN PARAMETROS ON EMPLEADOS.Estado = PARAMETROS.Id 
				INNER JOIN PARAMETROS AS PARAMETROS2 ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
				LEFT JOIN EMPLEADOS AS GP ON CENTROS.IdGerente = GP.Id 
				LEFT JOIN RENOVACIONES  ON EMPLEADOS.Id = RENOVACIONES.IdEmpleado 
				$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function buscarEmpleado($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
	}
?>