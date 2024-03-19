<?php
	class dashboardModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarIngresos($FechaInicial, $FechaFinal)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE EMPLEADOS.FechaIngreso >= '$FechaInicial' AND 
						EMPLEADOS.FechaIngreso <= '$FechaFinal' AND 
						PARAMETROS.Detalle = 'ACTIVO';
			EOD;
	
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function contarEgresos($FechaInicial, $FechaFinal)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE EMPLEADOS.FechaRetiro >= '$FechaInicial' AND 
						EMPLEADOS.FechaRetiro <= '$FechaFinal' AND 
						PARAMETROS.Detalle = 'RETIRADO';
			EOD;
	
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function contarEmpleados()
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO';
			EOD;
	
			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function empleadosNuevos($FechaInicial, $FechaFinal)
		{
			$query = <<<EOD
				SELECT  EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2,
						CENTROS.Centro,
						CENTROS.Nombre AS NombreCentro,
						CARGOS.Cargo,
						CARGOS.Nombre AS NombreCargo,
						EMPLEADOS.FechaIngreso 
					FROM EMPLEADOS 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id
						INNER JOIN PARAMETROS
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.FechaIngreso >= '$FechaInicial' AND 
						EMPLEADOS.FechaIngreso <= '$FechaFinal' 
					ORDER BY EMPLEADOS.FechaIngreso, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
			EOD;
	
			$request = $this->listar($query);
			return $request;
		}

		public function empleadosRetirados($FechaInicial, $FechaFinal)
		{
			$query = <<<EOD
				SELECT  EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2,
						CENTROS.Centro,
						CENTROS.Nombre AS NombreCentro,
						CARGOS.Cargo,
						CARGOS.Nombre AS NombreCargo,
						EMPLEADOS.FechaRetiro 
					FROM EMPLEADOS 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id
						INNER JOIN PARAMETROS
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'RETIRADO' AND 
						EMPLEADOS.FechaRetiro >= '$FechaInicial' AND 
						EMPLEADOS.FechaRetiro <= '$FechaFinal' 
					ORDER BY EMPLEADOS.FechaRetiro, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
			EOD;
	
			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorCentro()
		{
			$query = <<<EOD
				SELECT CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						COUNT(*) AS Registros 
					FROM EMPLEADOS 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id
						INNER JOIN PARAMETROS
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO'
					GROUP BY CENTROS.Centro, CENTROS.Nombre 
					ORDER BY Registros DESC;
			EOD;
	
			$request = $this->listar($query);
			return $request;
		}

		public function cumpleanosEmpleados()
		{
			$DiaInicio = date('d', strtotime(date('Y-m-d') . ' - ' . date('N') . ' days' )) + 1; 
			$DiaFinal = $DiaInicio + 6;

			$query = <<<EOD
				SELECT EMPLEADOS.*, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Nombre AS NombreCargo, 
						PARAMETROS2.Detalle AS NombreTipoEmpleado 
					FROM EMPLEADOS
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON CENTROS.TipoEmpleado = PARAMETROS2.Id 
					WHERE PARAMETROS1.Detalle = 'ACTIVO' AND 
						MONTH(EMPLEADOS.FechaNacimiento) = MONTH(GETDATE()) AND 
						DAY(EMPLEADOS.FechaNacimiento) >= $DiaInicio AND 
						DAY(EMPLEADOS.FechaNacimiento) <= $DiaFinal  
					ORDER BY MONTH(EMPLEADOS.FechaNacimiento), DAY(EMPLEADOS.FechaNacimiento), EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function vencimientoContratos()
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						PROYECTOS.Centro AS Proyecto, 
						PROYECTOS.Nombre AS NombreProyecto, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.FechaRetiro, 
						EMPLEADOS.FechaVencimiento 
					FROM EMPLEADOS
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE (PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.FechaVencimiento <= EOMONTH(GETDATE() + 60)) OR
						(PARAMETROS.Detalle = 'RETIRADO' AND 
						YEAR(EMPLEADOS.FechaVencimiento) = YEAR(GETDATE()) AND 
						MONTH(EMPLEADOS.FechaVencimiento) = MONTH(GETDATE()))
				ORDER BY MONTH(EMPLEADOS.FechaVencimiento), DAY(EMPLEADOS.FechaVencimiento), EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function inconsistenciasEmpleados()
		{
			$inconsistencias = array();

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.IdCentro = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
								'Documento' => $datos[$i]['Documento'], 
								'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
								'Inconsistencia' => 'NO TIENE DEFINIDO UN CENTROS DE COSTOS');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.IdCargo = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN CARGO');
				}
			}

			// SE TRASLADA EL TIPO DE EMPLEADO A CENTROS DE COSTO
			// $query = <<<EOD
			// 	SELECT EMPLEADOS.Documento, 
			// 			EMPLEADOS.Apellido1, 
			// 			EMPLEADOS.Apellido2, 
			// 			EMPLEADOS.Nombre1, 
			// 			EMPLEADOS.Nombre2 
			// 		FROM EMPLEADOS
			// 			INNER JOIN PARAMETROS 
			// 				ON EMPLEADOS.Estado = PARAMETROS.Id 
			// 		WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
			// 			EMPLEADOS.TipoEmpleado = 0;
			// EOD;

			// $datos = $this->listar($query);

			// if ($datos) 
			// {
			// 	for ($i = 0; $i < count($datos); $i++) 
			// 	{ 
			// 		$inconsistencias[] = array(
			// 			'Documento' => $datos[$i]['Documento'], 
			// 			'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
			// 			'Inconsistencia' => 'NO TIENE DEFINIDO UN TIPO DE EMPLEADO');
			// 	}
			// }

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.Vicepresidencia = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDA UNA VICEPRESIDENCIA');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.PeriodicidadPago = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UNA PERIODICIDAD DE PAGO');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.ModalidadTrabajo = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UNA MODALIDAD DE TRABAJO');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.SueldoBasico = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN SUELDO BÁSICO');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.SubsidioTransporte = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO SUBSIDIO DE TRANSPORTE');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.TipoContrato = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN TIPO DE CONTRATO');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.IdEPS = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDA UNA EPS');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2  
							ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						PARAMETROS2.Detalle <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
						PARAMETROS2.Detalle <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
						PARAMETROS2.Detalle <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
						PARAMETROS2.Detalle <> 'PASANTÍA' AND 
						EMPLEADOS.IdCajaCompensacion = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDA UNA CAJA DE COMPENSACIÓN FAMILIAR');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.RegimenCesantias = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN RÉGIMEN DE CESANTÍAS');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2  
							ON EMPLEADOS.RegimenCesantias = PARAMETROS2.Id 
						INNER JOIN PARAMETROS AS PARAMETROS3  
							ON EMPLEADOS.TipoContrato = PARAMETROS3.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						PARAMETROS2.Detalle <> 'SALARIO INTEGRAL' AND 
						PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
						PARAMETROS3.Detalle <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
						PARAMETROS3.Detalle <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
						PARAMETROS3.Detalle <> 'PASANTÍA' AND 
						EMPLEADOS.IdFondoCesantias = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN FONDO DE CESANTÍAS');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoContrato = PARAMETROS2.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						PARAMETROS2.Detalle <> 'APRENDIZAJE - ETAPA LECTIVA' AND 
						PARAMETROS2.Detalle <> 'APRENDIZAJE - ETAPA PRÁCTICA' AND 
						PARAMETROS2.Detalle <> 'APRENDIZAJE - PRACTICANTE UNIVERSIDAD' AND 
						PARAMETROS2.Detalle <> 'PASANTÍA' AND 
						EMPLEADOS.IdFondoPensiones = 0 AND 
						EMPLEADOS.SubtipoCotizante = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN FONDO DE PENSIONES');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.FormaDePago = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDA UNA FORMA DE PAGO');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						(EMPLEADOS.IdBanco = 0 OR 
						EMPLEADOS.TipoCuentaBancaria = 0 OR 
						EMPLEADOS.CuentaBancaria = '');
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDA INFORMACIÓN BANCARIA');
				}
			}

			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2 
					FROM EMPLEADOS
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE PARAMETROS.Detalle = 'ACTIVO' AND 
						EMPLEADOS.MetodoRetencion = 0;
			EOD;

			$datos = $this->listar($query);

			if ($datos) 
			{
				for ($i = 0; $i < count($datos); $i++) 
				{ 
					$inconsistencias[] = array(
						'Documento' => $datos[$i]['Documento'], 
						'NombreEmpleado' => $datos[$i]['Apellido1'] . ' ' . $datos[$i]['Apellido2'] . ' ' . $datos[$i]['Nombre1'] . ' ' . $datos[$i]['Nombre2'], 
						'Inconsistencia' => 'NO TIENE DEFINIDO UN MÉTODO DE RETENCIÓN');
				}
			}

			sort($inconsistencias);

			return $inconsistencias;
		}
	}
?>