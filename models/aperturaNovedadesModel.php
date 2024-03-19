<?php
	class aperturaNovedadesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function actualizarNovedad(array $data)
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
					PERIODOS.Referencia = $Referencia AND 
					PERIODOS.Periodicidad = $Periodicidad AND 
					PERIODOS.Periodo = $Periodo;
			EOD;

			$IdPeriodo = getId('PERIODOS', $query);

			$query = <<<EOD
				UPDATE PARAMETROS
					SET 
						Valor = $IdPeriodo,
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