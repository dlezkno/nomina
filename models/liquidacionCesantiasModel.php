<?php
	class liquidacionCesantiasModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM CESANTIAS 
						INNER JOIN EMPLEADOS 
							ON CESANTIAS.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarLiquidacionCesantias($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Id, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						CESANTIAS.FechaLiquidacion, 
						CESANTIAS.FechaIngreso, 
						CESANTIAS.FechaInicio, 
						CESANTIAS.DiasCesantias, 
						CESANTIAS.DiasSancionYLicencias, 
						CESANTIAS.SueldoBasico, 
						CESANTIAS.SalarioBase, 
						CESANTIAS.ValorCesantias, 
						CESANTIAS.AnticipoCesantias, 
						CESANTIAS.InteresCesantias  
					FROM CESANTIAS 
						INNER JOIN EMPLEADOS 
							ON CESANTIAS.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function leerRegistro($query)
		{
			$request = $this->leer($query);
			return $request;
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

		public function liquidar(array $data)
		{
			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");

			$Referencia = $data['Referencia'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Referencia,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");

			$Periodicidad = $data['Periodicidad'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Periodicidad,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");

			$Periodo = $data['Periodo'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Periodo,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'CicloEnLiquidacion'");

			$Ciclo = $data['Ciclo'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $Ciclo,
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			$Id = getId('PARAMETROS', "PARAMETROS.Parametro = 'FechaLimiteNovedades'");

			$Fecha = $data['FechaLimiteNovedades'];

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Fecha = '$Fecha',
						FechaActualizacion = getDate()
					WHERE PARAMETROS.Id = $Id;
			EOD;

			$resp = $this->actualizar($query, array());

			return $resp;
		}
	}
?>