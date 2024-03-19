<?php
	class paisesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM PAISES $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarPaises($query)
		{
			$query = "SELECT * FROM PAISES $query";
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarPais(array $data)
		{
			$query = <<<EOD
				INSERT INTO PAISES 
					(Nombre1, Nombre2, Nombre3, Iso2, Iso3, PhoneCode)
					VALUES (
						:Nombre1,
						:Nombre2,
						:Nombre3,
						:Iso2,
						:Iso3,
						:PhoneCode);
			EOD;

			$id = $this->adicionar($query, $data);
			
			return $id;
		}	
		
		public function buscarPais($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarPais(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE PAISES 
					SET 
						Nombre1 = :Nombre1, 
						Nombre2 = :Nombre2, 
						Nombre3 = :Nombre3, 
						Iso2 = :Iso2,
						Iso3 = :Iso3,
						PhoneCode = :PhoneCode, 
						Orden = :Orden, 
						FechaActualizacion = getDate() 
					WHERE PAISES.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarPais(int $id)
		{
			$query = 'DELETE FROM PAISES WHERE PAISES.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>