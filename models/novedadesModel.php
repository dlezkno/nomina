<?php
	class novedadesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($ArchivoNomina, $query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM $ArchivoNomina 
						INNER JOIN PERIODOS 
							ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
						INNER JOIN EMPLEADOS 
							ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS 
							ON $ArchivoNomina.IdCentro = CENTROS.Id 
						INNER JOIN AUXILIARES 
							ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1
							ON AUXILIARES.Imputacion = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
						LEFT JOIN TERCEROS 
							ON $ArchivoNomina.IdTercero = TERCEROS.Id 
					$query
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}


		public function listarEmpleados($query){
			$request = $this->listar($query);
			return $request;
		}

		public function listarNovedades($ArchivoNomina, $query)
		{
			$query = <<<EOD
				SELECT $ArchivoNomina.*, 
					TERCEROS.Documento AS DocumentoTercero, 
					TERCEROS.Nombre AS NombreTercero, 
					PERIODOS.Periodo, 
					PERIODOS.FechaInicial AS FechaInicialPeriodo, 
					PERIODOS.FechaFinal AS FechaFinalPeriodo,  
					EMPLEADOS.Documento, 
					EMPLEADOS.Apellido1, 
					EMPLEADOS.Apellido2,  
					EMPLEADOS.Nombre1, 
					EMPLEADOS.Nombre2, 
					CARGOS.Nombre AS NombreCargo, 
					CENTROS.Nombre AS NombreCentro, 
					MAYORES.Mayor, 
					AUXILIARES.Auxiliar, 
					AUXILIARES.Nombre AS NombreConcepto, 
					PARAMETROS1.Detalle AS Imputacion,
					PARAMETROS2.Detalle AS NombreTipoLiquidacion 
				FROM $ArchivoNomina 
					INNER JOIN PERIODOS 
						ON $ArchivoNomina.IdPeriodo = PERIODOS.Id 
					INNER JOIN EMPLEADOS 
						ON $ArchivoNomina.IdEmpleado = EMPLEADOS.Id 
					LEFT JOIN CARGOS 
						ON EMPLEADOS.IdCargo = CARGOS.Id 
					LEFT JOIN CENTROS 
						ON $ArchivoNomina.IdCentro = CENTROS.Id 
					INNER JOIN AUXILIARES 
						ON $ArchivoNomina.IdConcepto = AUXILIARES.Id 
					INNER JOIN MAYORES 
						ON AUXILIARES.IdMayor = MAYORES.Id 
					INNER JOIN PARAMETROS AS PARAMETROS1
						ON AUXILIARES.Imputacion = PARAMETROS1.Id 
					INNER JOIN PARAMETROS AS PARAMETROS2 
						ON MAYORES.TipoLiquidacion = PARAMETROS2.Id 
					LEFT JOIN TERCEROS 
						ON $ArchivoNomina.IdTercero = TERCEROS.Id 
				$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarNovedad($ArchivoNomina, array $data)
		{
			$query = <<<EOD
				INSERT INTO $ArchivoNomina (
					IdPeriodo, Ciclo, IdEmpleado, IdCentro, TipoEmpleado, IdConcepto, 
					Base, Porcentaje, Horas, Valor, IdTercero, FechaInicial, FechaFinal, Liquida )
					VALUES (
					:IdPeriodo, 
					:Ciclo, 
					:IdEmpleado, 
					:IdCentro, 
					:TipoEmpleado, 
					:IdConcepto, 
					:Base, 
					:Porcentaje, 
					:Horas, 
					:Valor,
					:IdTercero, 
					:FechaInicial, 
					:FechaFinal, 
					:Liquida); 
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarNovedad($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarNovedad($ArchivoNomina, array $data, int $id)
		{
			$query = <<<EOD
				UPDATE $ArchivoNomina
					SET 
						IdPeriodo  		= :IdPeriodo,
						Ciclo  			= :Ciclo,
						IdEmpleado 		= :IdEmpleado,
						IdCentro 		= :IdCentro, 
						TipoEmpleado 	= :TipoEmpleado, 
						IdConcepto 		= :IdConcepto, 
						Base 			= :Base, 
						Porcentaje 		= :Porcentaje, 
						Horas 			= :Horas,
						Valor 			= :Valor, 
						IdTercero 		= :IdTercero, 
						FechaInicial 	= :FechaInicial, 
						FechaFinal 		= :FechaFinal, 
						Liquida 		= :Liquida, 
						FechaActualizacion = getDate()
					WHERE $ArchivoNomina.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function actualizarNovedadAdicionar($ArchivoNomina, array $data, int $id)
		{
			$query = <<<EOD
				UPDATE $ArchivoNomina
					SET 
						IdPeriodo  		= :IdPeriodo,
						Ciclo  			= :Ciclo,
						IdEmpleado 		= :IdEmpleado,
						IdCentro 		= :IdCentro, 
						TipoEmpleado 	= :TipoEmpleado, 
						IdConcepto 		= :IdConcepto,
						Base 			= :Base, 
						Porcentaje 		= :Porcentaje, 
						Horas 			= $ArchivoNomina.Horas + :Horas,
						Valor 			= $ArchivoNomina.Valor + :Valor, 
						IdTercero 		= :IdTercero, 
						FechaInicial 	= :FechaInicial, 
						FechaFinal 		= :FechaFinal, 
						Liquida 		= 'N', 
						FechaActualizacion = getDate()
					WHERE $ArchivoNomina.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function borrarNovedad($ArchivoNomina, int $id)
		{
			$query = <<<EOD
				DELETE FROM $ArchivoNomina 
				WHERE $ArchivoNomina.Id = $id
			EOD;

			$resp = $this->borrar($query);

			return $resp;
		}

		public function leerRegistro($query)
		{
			$request = $this->leer($query);
			return $request;
		}
	}
?>