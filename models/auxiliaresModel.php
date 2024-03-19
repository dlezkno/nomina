<?php
	class auxiliaresModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM AUXILIARES 
						INNER JOIN MAYORES
							ON AUXILIARES.IdMayor = MAYORES.Id 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarAuxiliares($query)
		{
			$query = <<<EOD
				SELECT AUXILIARES.*, 
						PARAMETROS.Detalle AS Imputacion, 
						MAYORES.Mayor, 
						MAYORES.Nombre AS NombreMayor, 
						MAYORES.TipoLiquidacion   
				FROM AUXILIARES
					INNER JOIN MAYORES
						ON AUXILIARES.IdMayor = MAYORES.Id
					INNER JOIN PARAMETROS 
						ON AUXILIARES.Imputacion = PARAMETROS.Id  
				$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarAuxiliar(array $data)
		{
			$query = <<<EOD
				INSERT INTO AUXILIARES (
					IdMayor, Auxiliar, Nombre, TipoEmpleado, Imputacion, ModoLiquidacion, 
					FactorConversion, HoraFija, ValorFijo, 
					TipoAuxiliar, TipoRegistroAuxiliar, EsDispersable, CodigoNE, ) 
					VALUES (
					:IdMayor, 
					:Auxiliar,
					:Nombre, 
					:TipoEmpleado, 
					:Imputacion, 
					:ModoLiquidacion, 
					:FactorConversion, 
					:HoraFija, 
					:ValorFijo, 
					:TipoAuxiliar, 
					:TipoRegistroAuxiliar, 
					:EsDispersable
					:CodigoNE);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarAuxiliar($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarAuxiliar(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE AUXILIARES
					SET 
						IdMayor 			= :IdMayor,
						Auxiliar 			= :Auxiliar,
						Nombre 				= :Nombre,
						TipoEmpleado 		= :TipoEmpleado,
						Imputacion 			= :Imputacion,
						ModoLiquidacion 	= :ModoLiquidacion,
						FactorConversion 	= :FactorConversion,
						HoraFija 			= :HoraFija,
						ValorFijo 			= :ValorFijo,
						TipoAuxiliar 		= :TipoAuxiliar,
						TipoRegistroAuxiliar = :TipoRegistroAuxiliar, 
						EsDispersable 		= :EsDispersable, 
						CodigoNE 			= :CodigoNE,
						FechaActualizacion 	= getDate()
					WHERE AUXILIARES.Id = $id
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarAuxiliar(int $id)
		{
			$query = <<<EOD
				UPDATE AUXILIARES 
					SET Borrado = 1
					WHERE AUXILIARES.Id = $id
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>