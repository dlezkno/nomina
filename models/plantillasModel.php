<?php
	class plantillasModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
				FROM PLANTILLAS 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarPlantillas($query)
		{
			$query = <<<EOD
				SELECT PLANTILLAS.*, 
						PARAMETROS1.Detalle AS NombreEstadoEmpleado, 
						PARAMETROS2.Detalle AS NombreTipoPlantilla, 
						PARAMETROS3.Detalle AS NombreTipoContrato 
				FROM PLANTILLAS
					INNER JOIN PARAMETROS AS PARAMETROS1
						ON PLANTILLAS.EstadoEmpleado = PARAMETROS1.Id 
					INNER JOIN PARAMETROS AS PARAMETROS2
						ON PLANTILLAS.TipoPlantilla = PARAMETROS2.Id 
					LEFT JOIN PARAMETROS AS PARAMETROS3
						ON PLANTILLAS.TipoContrato = PARAMETROS3.Id 
				$query;
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarPlantilla(array $data)
		{
			$query = <<<EOD
				INSERT INTO PLANTILLAS 
					(EstadoEmpleado, TipoPLantilla, TipoContrato, Asunto, Plantilla, CodigoDocumento) 
					VALUES (
					:EstadoEmpleado, 
					:TipoPlantilla, 
					:TipoContrato,
					:Asunto, 
					:Plantilla, 
					:CodigoDocumento);  
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarPlantilla($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarPlantilla(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE PLANTILLAS
				SET 
				EstadoEmpleado = :EstadoEmpleado,
				TipoPlantilla = :TipoPlantilla, 
				TipoContrato = :TipoContrato,
				Asunto = :Asunto,
				Plantilla = :Plantilla, 
				CodigoDocumento = :CodigoDocumento, 
				FechaActualizacion = getDate()
				WHERE PLANTILLAS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarPlantilla(int $id)
		{
			$query = <<<EOD
				DELETE FROM PLANTILLAS 
					WHERE PLANTILLAS.Id = $id;
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>