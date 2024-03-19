<?php
	class retirosEmpleadosModel extends pgSQL
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
					INNER JOIN PARAMETROS AS PARAMETROS1  
						ON EMPLEADOS.Estado = PARAMETROS1.Id 
					INNER JOIN PARAMETROS AS PARAMETROS2   
						ON EMPLEADOS.MotivoRetiro = PARAMETROS2.Id 
				$query
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarRetirosEmpleados($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Id, 
					EMPLEADOS.Documento, 
					EMPLEADOS.Apellido1, 
					EMPLEADOS.Apellido2,  
					EMPLEADOS.Nombre1, 
					EMPLEADOS.Nombre2, 
					EMPLEADOS.FechaRetiro, 
					EMPLEADOS.FechaLiquidacion, 
					PARAMETROS2.Detalle AS MotivoRetiro 
				FROM EMPLEADOS  
					INNER JOIN PARAMETROS AS PARAMETROS1  
						ON EMPLEADOS.Estado = PARAMETROS1.Id 
					INNER JOIN PARAMETROS AS PARAMETROS2   
						ON EMPLEADOS.MotivoRetiro = PARAMETROS2.Id 
				$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function retirarEmpleado(array $data)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						Estado = :Estado, 
						FechaRetiro = :FechaRetiro, 
						MotivoRetiro = :MotivoRetiro, 
						FechaLiquidacion = NULL 
					WHERE EMPLEADOS.Id = :IdEmpleado;
			EOD;

			$ok = $this->actualizar($query, $data);

			return $ok;
		}	
		
		public function borrarRetiroEmpleado(int $id, int $Estado)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						Estado = $Estado, 
						FechaRetiro = NULL, 
						FechaLiquidacion = NULL, 
						MotivoRetiro = 0 
					WHERE EMPLEADOS.Id = $id;
			EOD;
				
			$resp = $this->query($query);

			return $resp;
		}
	}
?>