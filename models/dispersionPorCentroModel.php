<?php
	class dispersionPorCentroModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM DISPERSIONPORCENTRO 
						INNER JOIN EMPLEADOS 
							ON DISPERSIONPORCENTRO.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CENTROS 
							ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
					$query
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarNovedades($query)
		{
			$query = <<<EOD
				SELECT DISPERSIONPORCENTRO.Id, 
						PERIODOS.Referencia, 
						PARAMETROS.Detalle AS Periodicidad, 
						PERIODOS.Periodo, 
						PERIODOS.FechaInicial, 
						PERIODOS.FechaFinal, 
						EMPLEADOS.Documento, 	
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2,  
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						DISPERSIONPORCENTRO.Porcentaje 
					FROM DISPERSIONPORCENTRO 
						INNER JOIN PERIODOS 
							ON DISPERSIONPORCENTRO.IdPeriodo = PERIODOS.Id 
						INNER JOIN PARAMETROS 
							ON PERIODOS.Periodicidad = PARAMETROS.Id 
						INNER JOIN EMPLEADOS 
							ON DISPERSIONPORCENTRO.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN CENTROS 
							ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
					$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarNovedad(array $data)
		{
			$query = <<<EOD
				INSERT INTO DISPERSIONPORCENTRO 
					(IdPeriodo, IdEmpleado, IdCentro, Porcentaje)
					VALUES (
					:IdPeriodo, 
					:IdEmpleado, 
					:IdCentro, 
					:Porcentaje); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarNovedad($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarNovedad(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE DISPERSIONPORCENTRO
				SET 
					IdPeriodo = :IdPeriodo, 
					IdEmpleado = :IdEmpleado,
					IdCentro = :IdCentro, 
					Porcentaje = :Porcentaje, 
					FechaActualizacion = getDate()
				WHERE DISPERSIONPORCENTRO.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarNovedad(int $id)
		{
			$query = <<<EOD
				DELETE FROM DISPERSIONPORCENTRO 
					WHERE DISPERSIONPORCENTRO.Id = $id
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>