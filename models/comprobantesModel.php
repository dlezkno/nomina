<?php
	class comprobantesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM COMPROBANTES 
						INNER JOIN TIPODOC 
							ON COMPROBANTES.IdTipoDoc = TIPODOC.Id
						INNER JOIN AUXILIARES
							ON COMPROBANTES.IdConcepto = AUXILIARES.Id
						INNER JOIN MAYORES
							ON AUXILIARES.IdMayor = MAYORES.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1
							ON COMPROBANTES.TipoEmpleado = PARAMETROS1.Id
						INNER JOIN PARAMETROS AS PARAMETROS2
							ON AUXILIARES.Imputacion = PARAMETROS2.Id
						INNER JOIN PARAMETROS AS PARAMETROS3
							ON COMPROBANTES.TipoTercero = PARAMETROS3.Id
					$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarComprobantes($query)
		{
			$query = <<<EOD
				SELECT COMPROBANTES.Id, 
						TIPODOC.TipoDocumento,
						MAYORES.Mayor, 
						AUXILIARES.Auxiliar, 
						COMPROBANTES.Detalle AS NombreConcepto, 
						PARAMETROS1.Detalle AS TipoEmpleado, 
						PARAMETROS2.Detalle AS Imputacion, 
						COMPROBANTES.Porcentaje, 
						COMPROBANTES.CuentaDb, 
						COMPROBANTES.DetallaCentroDb, 
						COMPROBANTES.CuentaCr, 
						COMPROBANTES.DetallaCentroCr, 
						PARAMETROS3.Detalle AS TipoTercero, 
						COMPROBANTES.Exonerable  
				FROM COMPROBANTES
					INNER JOIN TIPODOC 
						ON COMPROBANTES.IdTipoDoc = TIPODOC.Id
					INNER JOIN AUXILIARES
						ON COMPROBANTES.IdConcepto = AUXILIARES.Id
					INNER JOIN MAYORES
						ON AUXILIARES.IdMayor = MAYORES.Id 
					LEFT JOIN PARAMETROS AS PARAMETROS1
						ON COMPROBANTES.TipoEmpleado = PARAMETROS1.Id
					INNER JOIN PARAMETROS AS PARAMETROS2
						ON AUXILIARES.Imputacion = PARAMETROS2.Id
					INNER JOIN PARAMETROS AS PARAMETROS3
						ON COMPROBANTES.TipoTercero = PARAMETROS3.Id
				$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarComprobante(array $data)
		{
			$query = <<<EOD
				INSERT INTO COMPROBANTES 
					(IdTipoDoc, IdConcepto, TipoEmpleado, Porcentaje, CuentaDb, DetallaCentroDb, CuentaCr, DetallaCentroCr, TipoTercero) 
					VALUES (
						:IdTipoDoc,
						:IdConcepto,
						:TipoEmpleado,
						:Porcentaje,
						:CuentaDb, 
						:DetallaCentroDb,
						:CuentaCr, 
						:DetallaCentroCr,
						:TipoTercero);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}

		public function actualizarComprobante(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE COMPROBANTES
				SET 
					IdTipoDoc  			= :IdTipoDoc,
					IdConcepto  		= :IdConcepto,
					TipoEmpleado 		= :TipoEmpleado, 
					Porcentaje 			= :Porcentaje,
					CuentaDb 			= :CuentaDb,
					DetallaCentroDb 	= :DetallaCentroDb,
					CuentaCr 			= :CuentaCr,
					DetallaCentroCr 	= :DetallaCentroCr,
					TipoTercero 		= :TipoTercero,
					FechaActualizacion 	= getDate()
				WHERE COMPROBANTES.Id = $id
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}


		public function listarRegistros($query)
		{
			$request = $this->listar($query);
			return $request;
		}	

		public function actualizarRegistros($query)
		{
			$request = $this->query($query);
			return $request;
		}	

		public function borrarComprobante(int $id)
		{
			$query = <<<EOD
				DELETE FROM COMPROBANTES 
					WHERE COMPROBANTES.Id = $id;
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>