<?php
	class diagnosticosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM DIAGNOSTICOS $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarDiagnosticos($query)
		{
			$query = "SELECT * FROM DIAGNOSTICOS $query";
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarDiagnostico(array $data)
		{
			$query = 'INSERT INTO DIAGNOSTICOS (Diagnostico, Nombre) VALUES (?, ?)';
			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarDiagnostico($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarDiagnostico(array $data, int $id)
		{
			$query = 'UPDATE DIAGNOSTICOS ' .
					'SET ' .
					'Diagnostico = :diagnostico, ' .
					'Nombre = :nombre, ' .
					'FechaActualizacion = getDate() ' .
					'WHERE DIAGNOSTICOS.Id = ' . $id;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarDiagnostico(int $id)
		{
			$query = 'DELETE FROM DIAGNOSTICOS WHERE DIAGNOSTICOS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>