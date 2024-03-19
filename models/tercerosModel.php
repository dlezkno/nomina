<?php
	class tercerosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM TERCEROS 
					$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarTerceros($query)
		{
			$query = <<<EOD
				SELECT * 
					FROM TERCEROS 
					$query;
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarTercero(array $data)
		{
			$query = <<<EOD
				INSERT INTO TERCEROS (
					TipoIdentificacion, Documento, Nombre, Nombre2, EsDeudor, EsAcreedor,  
					Direccion, IdCiudad, Telefono, Celular, Email, 
					FormaDePago, IdBanco, CuentaBancaria, TipoCuentaBancaria, 
					EsSindicato, EsEPS, EsARL, EsFondoCesantias, EsFondoPensiones, EsCCF, Codigo, 
					CodigoSAP) 
					VALUES (
					:TipoIdentificacion, 
					:Documento, 
					:Nombre, 
					:Nombre2, 
					:EsDeudor, 
					:EsAcreedor, 
					:Direccion, 
					:IdCiudad, 
					:Telefono, 
					:Celular, 
					:Email, 
					:FormaDePago, 
					:IdBanco, 
					:CuentaBancaria, 
					:TipoCuentaBancaria, 
					:EsSindicato, 
					:EsEPS, 
					:EsARL, 
					:EsFondoCesantias, 
					:EsFondoPensiones, 
					:EsCCF, 
					:Codigo, 
					:CodigoSAP); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarTercero($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarTercero(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE TERCEROS
					SET 
						TipoIdentificacion 	= :TipoIdentificacion, 
						Documento 			= :Documento, 
						Nombre  			= :Nombre, 
						Nombre2  			= :Nombre2, 
						EsDeudor 			= :EsDeudor, 
						EsAcreedor 			= :EsAcreedor, 
						Direccion 			= :Direccion, 
						IdCiudad 			= :IdCiudad, 
						Telefono 			= :Telefono, 
						Celular 			= :Celular, 
						Email 				= :Email, 
						FormaDePago 		= :FormaDePago, 
						IdBanco 			= :IdBanco, 
						CuentaBancaria 		= :CuentaBancaria, 
						TipoCuentaBancaria 	= :TipoCuentaBancaria, 
						EsSindicato 		= :EsSindicato, 
						EsEPS 				= :EsEPS, 
						CuentaEPS 			= :CuentaEPS, 
						EsARL 				= :EsARL, 
						CuentaARL 			= :CuentaARL, 
						EsFondoCesantias 	= :EsFondoCesantias, 
						CuentaFondoCesantias = :CuentaFondoCesantias, 
						EsFondoPensiones 	= :EsFondoPensiones, 
						CuentaFondoPensiones = :CuentaFondoPensiones, 
						EsCCF 				= :EsCCF, 
						CuentaCCF 			= :CuentaCCF, 
						CodigoSAP 			= :CodigoSAP, 
						FechaActualizacion 	= getDate()
					WHERE TERCEROS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarTercero(int $id)
		{
			$query = <<<EOD
				DELETE FROM TERCEROS 
					WHERE TERCEROS.Id = $id;
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>