<?php
	class periodosModel extends pgSQL
	{
		public $prueba = 'prueba';

		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT count(*) AS Registros 
					FROM PERIODOS 
					INNER JOIN PARAMETROS 
						ON PERIODOS.Periodicidad = PARAMETROS.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarPeriodos($query)
		{
			$query = <<<EOD
				SELECT PERIODOS.Id, 
						PERIODOS.Referencia, 
						PERIODOS.Periodicidad, 
						PARAMETROS.Detalle, 
						PERIODOS.Periodo,  
						PERIODOS.FechaInicial, 
						PERIODOS.FechaFinal 
					FROM PERIODOS 
					INNER JOIN PARAMETROS 
						ON PERIODOS.Periodicidad = PARAMETROS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarPeriodo(array $data)
		{
			$query = <<<EOD
				INSERT INTO PERIODOS (
					Referencia, Periodicidad, Periodo, FechaInicial, FechaFinal)  
					VALUES (
					:Referencia, 
					:Periodicidad, 
					:Periodo, 
					:FechaInicial, 
					:FechaFinal)  
			EOD;
			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarPeriodo($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarPeriodo(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE PERIODOS 
					SET 
						Referencia = :Referencia, 
						Periodicidad = :Periodicidad, 
						Periodo = :Periodo, 
						FechaInicial = :FechaInicial, 
						FechaFinal = :FechaFinal, 
						FechaActualizacion = getDate() 
					WHERE PERIODOS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarPeriodo(int $id)
		{
			$query = <<<EOD
				DELETE 
					FROM PERIODOS 
					WHERE PERIODOS.Id = $id;
			EOD;
			
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>