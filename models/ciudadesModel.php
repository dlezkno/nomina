<?php
	class ciudadesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM CIUDADES 
						INNER JOIN PAISES 
							ON CIUDADES.IdPais = PAISES.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCiudades($query)
		{
			$query = <<<EOD
				SELECT CIUDADES.*, 
						PAISES.Nombre1 AS NombrePais  
					FROM CIUDADES 
						LEFT JOIN PAISES 
							ON CIUDADES.IdPais = PAISES.Id 
					$query;
			EOD;
				
			$request = $this->listar($query);
			
			return $request;
		}
		
		public function guardarCiudad(array $data)
		{
			$query = <<<EOD
				INSERT INTO CIUDADES 
					(Ciudad, Nombre, Departamento, IdPais) 
					VALUES (?, ?, ?, ?);
			EOD;

			$id = $this->adicionar($query, $data);
			
			return $id;
		}	
		
		public function buscarCiudad($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarCiudad(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE CIUDADES 
					SET 
						Ciudad = :ciudad, 
						Nombre = :nombre, 
						Departamento = :departamento, ' .
						IdPais = :idpais, 
						Orden = :orden,  
						FechaActualizacion = getDate() 
					WHERE CIUDADES.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarCiudad(int $id)
		{
			$query = 'DELETE FROM CIUDADES WHERE CIUDADES.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>