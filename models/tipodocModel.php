<?php
	class tipodocModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM TIPODOC 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarTipoDoc($query)
		{
			$query = <<<EOD
				SELECT * 
					FROM TIPODOC 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarTipoDoc(array $data)
		{
			$query = <<<EOD
				INSERT INTO TIPODOC 
					(TipoDocumento, Nombre, TipoNumeracion, Prefijo, Secuencia) 
					VALUES (
						:TipoDocumento, 
						:Nombre,
						:TipoNumeracion,
						:Prefijo,
						:Secuencia);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarTipoDoc($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarTipoDoc(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE TIPODOC 
					SET 
						TipoDocumento = :TipoDocumento, 
						Nombre = :Nombre, 
						TipoNumeracion = :TipoNumeracion, 
						Prefijo = :Prefijo, 
						Secuencia = :Secuencia, 
						FechaActualizacion = getDate() 
					WHERE TIPODOC.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarTipoDoc(int $id)
		{
			$query = <<<EOD
				DELETE 
					FROM TIPODOC  
					WHERE TIPODOC.Id = $id;
			EOD;
			
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>