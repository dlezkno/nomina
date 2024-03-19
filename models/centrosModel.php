<?php
	class centrosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM CENTROS 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCentros($query)
		{
			$query = <<<EOD
				SELECT CENTROS.Id, 
						CENTROS.Centro, 
						CENTROS.Nombre, 
						CENTROS.FechaVencimiento, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						PARAMETROS1.Detalle AS NombreTipoEmpleado, 
						PARAMETROS2.Detalle AS Vicepresidencia, 
						CENTROS.Borrado 
					FROM CENTROS 
						LEFT JOIN EMPLEADOS 
							ON CENTROS.IdGerente = EMPLEADOS.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1 
							ON CENTROS.TipoEmpleado = PARAMETROS1.Id
						LEFT JOIN PARAMETROS AS PARAMETROS2  
							ON CENTROS.Vicepresidencia = PARAMETROS2.Id
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarCentro(array $data)
		{
			$query = <<<EOD
				INSERT INTO CENTROS 
					(Centro, Nombre, FechaVencimiento, TipoEmpleado, IdGerente, Vicepresidencia) 
					VALUES (
						:Centro, 
						:Nombre, 
						:FechaVencimiento, 
						:TipoEmpleado, 
						:IdGerente, 
						:Vicepresidencia);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarCentro($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarCentro(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE CENTROS 
					SET 
						Centro 				= :Centro, 
						Nombre 				= :Nombre, 
						FechaVencimiento	= :FechaVencimiento, 
						TipoEmpleado		= :TipoEmpleado, 
						IdGerente 			= :IdGerente, 
						Vicepresidencia		= :Vicepresidencia, 
						FechaActualizacion 	= getDate() 
					WHERE CENTROS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarCentro(int $id)
		{
			$query = <<<EOD
				UPDATE CENTROS 
					SET Borrado = 1 
					WHERE CENTROS.Id = $id;
			EOD;
			
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>