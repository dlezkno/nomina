<?php
	class parametrosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = "SELECT COUNT(*) AS Registros FROM PARAMETROS $query";
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarParametros($query)
		{
			$query = "SELECT * FROM PARAMETROS $query";
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarParametro(array $data)
		{
			$query = <<<EOD
				INSERT INTO PARAMETROS 
					(Parametro, Detalle, Valor, Valor2, Texto, Fecha) 
					VALUES (
						:Parametro, 
						:Detalle, 
						:Valor, 
						:Valor2, 
						:Texto, 
						:Fecha);
			EOD;

			$id = $this->adicionar($query, $data);

			return $id;
		}	
		
		public function buscarParametro($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarParametro(array $data, int $Id)
		{
			$query = <<<EOD
				UPDATE PARAMETROS 
					SET 
						Parametro = :Parametro, 
						Detalle = :Detalle, 
						Valor = :Valor, 
						Valor2 = :Valor2, 
						Texto = :Texto, 
						Fecha = :Fecha, 
						FechaActualizacion = getDate() 
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarParametro(int $id)
		{
			$query = 'DELETE FROM PARAMETROS WHERE PARAMETROS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>