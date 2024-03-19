<?php
	class categoriasModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM CATEGORIAS $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCategorias($query)
		{
			$query = "SELECT * FROM CATEGORIAS $query";
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarCategoria(array $data)
		{
			$query = 'INSERT INTO CATEGORIAS (Categoria, Nombre) VALUES (?, ?)';
			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarCategoria($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarCategoria(array $data, int $id)
		{
			$query = 'UPDATE CATEGORIAS ' .
					'SET ' .
					'Categoria = :categoria, ' .
					'Nombre = :nombre, ' .
					'FechaActualizacion = getDate() ' .
					'WHERE CATEGORIAS.Id = ' . $id;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarCategoria(int $id)
		{
			$query = 'DELETE FROM CATEGORIAS WHERE CATEGORIAS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>