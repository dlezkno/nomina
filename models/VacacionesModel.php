<?php
	class VacacionesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function saveVacation(array $data) {
			$query = <<<EOD
					INSERT INTO VACACIONES
						(IdEmpleado, SueldoBasico, RecargoNocturno, SalarioBase, FechaCausacion, FechaLiquidacion, FechaInicio, FechaIngreso, DiasALiquidar, DiasEnTiempo, DiasEnDinero, DiasFestivos, Dias31, DiasProcesados, ValorLiquidado, ValorEnTiempo, ValorEnDinero, ValorFestivos, ValorDia31, Observaciones)
						VALUES (
							:IdEmpleado,
							:SueldoBasico, 
							:PromedioSalarioVariable,
							:Salario,
							:FechaCausacion,
							:FechaFinalPeriodo, 
							:FechaInicio, 
							:FechaIngreso, 
							:DiasVacaciones, 
							:DiasVacacionesTiempo,
							:DiasVacacionesDinero,
							:numSaturdaysSundaysHolidays,
							:day31,
							:DiasProcesados,
							:ValorLiquidado,
							:ValorEnTiempo,
							:ValorEnDinero,
							:ValorFestivos,
							:ValorDia31,
							:Observaciones);
				EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}

		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM VACACIONES 
						INNER JOIN EMPLEADOS 
							ON VACACIONES.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarVacaciones($query)
		{
			$query = <<<EOD
				SELECT VACACIONES.Id, 
						EMPLEADOS.Id AS IdEmpleado, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.SueldoBasico, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaVencimiento, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						VACACIONES.SalarioBase, 
						VACACIONES.DiasSancionYLicencia,
						VACACIONES.FechaCausacion, 
						VACACIONES.FechaLiquidacion, 
						VACACIONES.FechaInicio AS FechaInicioVacaciones, 
						VACACIONES.FechaIngreso AS FechaIngresoVacaciones,
						VACACIONES.DiasALiquidar,
						VACACIONES.DiasEnTiempo, 
						VACACIONES.DiasFestivos, 
						VACACIONES.Dias31, 
						VACACIONES.DiasEnDinero,  
						VACACIONES.ValorLiquidado, 
						VACACIONES.ValorEnTiempo,
						VACACIONES.ValorFestivos, 
						VACACIONES.ValorDia31, 
						VACACIONES.ValorEnDinero
					FROM VACACIONES
						INNER JOIN EMPLEADOS 
							ON VACACIONES.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
					$query;
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function contarRegistrosReporteVacaciones($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
				FROM nomina.vacaciones va
				INNER JOIN nomina.empleados e ON e.id = va.idempleado
				$query;
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
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