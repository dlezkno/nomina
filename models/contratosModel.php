<?php
	class contratosModel extends pgSQL
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
						CIUDADES.Nombre AS NombreCiudad, 
						PARAMETROS1.Detalle AS EstadoCivil, 
						PARAMETROS.Detalle AS EstadoEmpleado   
					FROM EMPLEADOS 
						LEFT JOIN CARGOS
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CIUDADES 
							ON EMPLEADOS.IdCiudad = CIUDADES.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.EstadoCivil = PARAMETROS1.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					$query
			EOD;

			$request = $this->listar($query);
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