<?php
	class bancosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM BANCOS $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarBancos($query)
		{
			$query = "SELECT * FROM BANCOS $query";
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarBanco(array $data)
		{
			$query = 'INSERT INTO BANCOS (banco, nombre, nit) VALUES (?, ?, ?)';
			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarBanco($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarBanco(array $data, int $id)
		{
			$query = 'UPDATE BANCOS ' .
					'SET ' .
					'Banco = :banco, ' .
					'Nombre = :nombre, ' .
					'Nit = :nit, ' .
					'FechaActualizacion = getDate() ' .
					'WHERE BANCOS.Id = ' . $id;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarBanco(int $id)
		{
			$query = 'DELETE FROM BANCOS WHERE BANCOS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>