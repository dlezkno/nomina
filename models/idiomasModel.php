<?php
	class idiomasModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM IDIOMAS $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarIdiomas($query)
		{
			$query = "SELECT * FROM IDIOMAS $query";
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarIdioma(array $data)
		{
			$query = 'INSERT INTO IDIOMAS (Idioma, Nombre) VALUES (?, ?)';
			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarIdioma($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarIdioma(array $data, int $id)
		{
			$query = 'UPDATE IDIOMAS ' .
					'SET ' .
					'Idioma = :idioma, ' .
					'Nombre = :nombre, ' .
					'Orden = :orden, ' .
					'FechaActualizacion = getDate() ' .
					'WHERE IDIOMAS.Id = ' . $id;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarIdioma(int $id)
		{
			$query = 'DELETE FROM IDIOMAS WHERE IDIOMAS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>