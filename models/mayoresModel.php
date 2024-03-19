<?php
	class mayoresModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM MAYORES 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarMayores($query)
		{
			$query = <<<EOD
				SELECT MAYORES.*, 
						PARAMETROS1.Detalle AS NombreTipoLiquidacion, 
						PARAMETROS2.Detalle AS NombreClaseConcepto, 
						PARAMETROS3.Detalle AS NombreTipoRetencion
					FROM MAYORES 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON MAYORES.TipoLiquidacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON MAYORES.ClaseConcepto = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3 
							ON MAYORES.TipoRetencion = PARAMETROS3.Id 
				$query;
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarMayor(array $data)
		{
			$query = <<<EOD
				INSERT INTO MAYORES (
					Mayor, Nombre, TipoLiquidacion, ClaseConcepto, TipoRetencion, 
					BasePrimas, BaseVacaciones, BaseCesantias, 
					AcumulaSanciones, AcumulaLicencias,
					ControlaSaldos, RenglonCertificado, ExcluidoNE) 
					VALUES (
						:Mayor, 
						:Nombre, 
						:TipoLiquidacion, 
						:ClaseConcepto, 
						:TipoRetencion, 
						:BasePrimas, 
						:BaseVacaciones, 
						:BaseCesantias, 
						:AcumulaSanciones, 
						:AcumulaLicencias, 
						:ControlaSaldos, 
						:RenglonCertificado, 
						:ExcluidoNE);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarMayor($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarMayor(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE MAYORES
				SET 
					Mayor  				= :Mayor,
					Nombre  			= :Nombre,
					TipoLiquidacion 	= :TipoLiquidacion,
					ClaseConcepto 		= :ClaseConcepto,
					TipoRetencion 		= :TipoRetencion,
					BasePrimas 			= :BasePrimas,
					BaseVacaciones 		= :BaseVacaciones,
					BaseCesantias 		= :BaseCesantias,
					AcumulaSanciones 	= :AcumulaSanciones,
					AcumulaLicencias 	= :AcumulaLicencias,
					ControlaSaldos 		= :ControlaSaldos,
					RenglonCertificado 	= :RenglonCertificado,
					ExcluidoNE			= :ExcluidoNE, 
					FechaActualizacion 	= getDate()
				WHERE MAYORES.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarMayor(int $id)
		{
			$query = <<<EOD
				DELETE FROM MAYORES 
					WHERE MAYORES.Id = $id;
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>