<?php
	class informesEmpleadosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	

		public function empleadosGeneral1($query, $isRetired=false)
		{
			$queryAuxilios = <<<EOD
				SELECT
					', ' + AUXILIARES.Nombre
				FROM NOVEDADESPROGRAMABLES 
				INNER JOIN AUXILIARES ON NOVEDADESPROGRAMABLES.IdConcepto = AUXILIARES.Id
				WHERE NOVEDADESPROGRAMABLES.IdEmpleado = EMPLEADOS.Id
				GROUP BY AUXILIARES.Nombre
				FOR XML PATH('')
			EOD;

			$newQuery = <<<EOD
				SELECT
					PARAMETROS2.Detalle AS 'Tipo de Identificación',
					EMPLEADOS.Documento AS 'No. Identificación',
					EMPLEADOS.Apellido1 + ' ' + EMPLEADOS.Apellido2 + ' ' + EMPLEADOS.Nombre1 + ' ' + EMPLEADOS.Nombre2 AS 'APELLIDOS Y NOMBRES',
					PARAMETROS3.Detalle AS 'Genero',
					CARGOS.Nombre AS 'Cargo',
					CENTROS.centro 'Codigo CECO',
					CENTROS.Nombre AS 'Centro de Costo',
					PROYECTOS.centro AS 'Codigo Proyecto',
					PROYECTOS.Nombre AS 'Proyecto',
					EMPLEADOS.FechaIngreso AS 'Fecha de ingreso',
			EOD;

			if ($isRetired) $newQuery .= <<<EOD
					EMPLEADOS.FechaRetiro AS 'Fecha de retiro',
					PARAMETROS11.detalle AS 'Motivo de retiro',
			EOD;

			$newQuery .= <<<EOD
					PARAMETROS4.Detalle AS 'Tipo de contrato',
					EMPLEADOS.Fechavencimiento AS 'Fecha de expiración contrato',
					EMPLEADOS.Prorrogas AS 'No. de Prorrogas',
					EMPLEADOS.DuracionContrato AS 'Durabilidad Contrato',
					EMPLEADOS.SueldoBasico AS 'Salario básico',
					PARAMETROS9.Detalle AS 'Tipo de Salario',
					STUFF(($queryAuxilios), 1, 2, '') AS 'Auxilios',
					BANCOS.Nombre AS 'Banco',
					PARAMETROS5.Detalle AS 'Tipo cuenta',
					EMPLEADOS.cuentabancaria AS 'Cuenta Bancaria',
					CIUDADES1.nombre AS 'Ciudad de Nacimiento',
					EMPLEADOS.fechanacimiento AS 'Fecha de nacimiento',
					EMPLEADOS.fechaexpedicion AS 'Fecha de Expedición doc',
					EMPLEADOS.email AS 'Correo Personal',
					EMPLEADOS.emailcorporativo AS 'Correo Corporativo',
					EMPLEADOS.emailproyecto AS 'Correo del proyecto',
					EMPLEADOS.telefono AS 'Telefono de contacto',
					EMPLEADOS.direccion AS 'Dirección',
					EMPLEADOS.barrio AS 'Barrio',
					CIUDADES2.nombre AS 'Ciudad donde reside',
					PARAMETROS6.Detalle AS 'Estado Civil',
					PARAMETROS7.Detalle AS 'Sucursal',
					NULL AS 'Estructura financiera',
					VICEPRESIDENCIA.Apellido1 + ' ' + VICEPRESIDENCIA.Apellido2 + ' ' + VICEPRESIDENCIA.Nombre1 + ' ' + VICEPRESIDENCIA.Nombre2 AS 'Vicepresidencia',
					CIUDADES3.nombre AS 'Ciudad de Labor',
					EPS.nombre AS 'EPS',
					FP.nombre AS 'Fondo de pensiones',
					ARL.nombre AS 'ARL',
					EMPLEADOS.nivelriesgo 'Nivel de Riesgo',
					CESANTIAS.nombre AS 'Cesantias',
					CCF.nombre AS 'Caja de Compensación',
					PARAMETROS8.Detalle AS 'RH',
					PARAMETROS1.Detalle AS 'Estado Laboral',
					PARAMETROS7.Detalle AS 'Sede de labor',
					PARAMETROS10.Detalle AS 'Tipo de población',
					EMPLEADOS.profesion AS 'Profesión',
					NULL AS 'Carnet'
				FROM EMPLEADOS
				LEFT JOIN PARAMETROS AS PARAMETROS1 ON EMPLEADOS.Estado = PARAMETROS1.Id
				LEFT JOIN PARAMETROS AS PARAMETROS2 ON EMPLEADOS.TipoIdentificacion = PARAMETROS2.Id
				LEFT JOIN PARAMETROS AS PARAMETROS3 ON EMPLEADOS.Genero = PARAMETROS3.Id
				LEFT JOIN CARGOS ON EMPLEADOS.IdCargo = CARGOS.Id
				LEFT JOIN CENTROS ON EMPLEADOS.IdCentro = CENTROS.Id 
				LEFT JOIN CENTROS AS PROYECTOS ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
				LEFT JOIN PARAMETROS AS PARAMETROS4 ON EMPLEADOS.TipoContrato = PARAMETROS4.Id
				LEFT JOIN BANCOS on BANCOS.id = EMPLEADOS.idBanco
				LEFT JOIN PARAMETROS AS PARAMETROS5 ON EMPLEADOS.TipoCuentaBancaria = PARAMETROS5.Id
				LEFT JOIN CIUDADES AS CIUDADES1 ON EMPLEADOS.idciudadnacimiento = CIUDADES1.Id
				LEFT JOIN CIUDADES AS CIUDADES2 ON EMPLEADOS.idciudad = CIUDADES2.Id
				LEFT JOIN PARAMETROS AS PARAMETROS6 ON EMPLEADOS.estadocivil = PARAMETROS6.Id
				LEFT JOIN PARAMETROS AS PARAMETROS7 ON EMPLEADOS.ID = PARAMETROS7.Id
				LEFT JOIN EMPLEADOS AS VICEPRESIDENCIA ON VICEPRESIDENCIA.id = EMPLEADOS.vicepresidencia
				LEFT JOIN CIUDADES AS CIUDADES3 ON EMPLEADOS.idciudadtrabajo = CIUDADES3.Id
				LEFT JOIN TERCEROS AS EPS ON EPS.id = EMPLEADOS.ideps
				LEFT JOIN TERCEROS AS FP ON FP.id = EMPLEADOS.idfondopensiones
				LEFT JOIN TERCEROS AS ARL ON ARL.id = EMPLEADOS.idarl
				LEFT JOIN TERCEROS AS CESANTIAS ON CESANTIAS.id = EMPLEADOS.idfondocesantias
				LEFT JOIN TERCEROS AS CCF ON CCF.id = EMPLEADOS.idcajacompensacion
				LEFT JOIN PARAMETROS AS PARAMETROS8 ON EMPLEADOS.factorrh = PARAMETROS8.Id
				LEFT JOIN PARAMETROS AS PARAMETROS9 ON EMPLEADOS.regimencesantias  = PARAMETROS9.Id
				LEFT JOIN PARAMETROS AS PARAMETROS10 ON EMPLEADOS.grupopoblacional  = PARAMETROS10.Id
			EOD;

			if ($isRetired) $newQuery .= <<<EOD
				LEFT JOIN PARAMETROS AS PARAMETROS11 ON EMPLEADOS.motivoretiro  = PARAMETROS11.Id
			EOD;

			$newQuery .= <<<EOD
				$query 
				ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($newQuery);
			return $request;
		}

		public function empleadosGeneral2($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.Direccion, 
						CIUDADES.Nombre AS NombreCiudad, 
						EMPLEADOS.Telefono, 
						EMPLEADOS.Celular, 
						EMPLEADOS.Email 
					FROM EMPLEADOS
						INNER JOIN CIUDADES 
							ON EMPLEADOS.IdCIudad = CIUDADES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorCentro($query)
		{
			$query = <<<EOD
				SELECT CENTROS.Centro,
						CENTROS.Nombre AS NombreCentro, 
						PROYECTOS.Centro AS Proyecto, 
						PROYECTOS.Nombre AS NombreProyecto, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico
					FROM EMPLEADOS
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CENTROS AS PROYECTOS 
							ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY CENTROS.Nombre, PROYECTOS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorCargo($query)
		{
			$query = <<<EOD
				SELECT CARGOS.Cargo,
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						EMPLEADOS.SueldoBasico
					FROM EMPLEADOS
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY CARGOS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorCategoria($query)
		{
			$query = <<<EOD
				SELECT CATEGORIAS.Categoria,
						CATEGORIAS.Nombre AS NombreCategoria, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						EMPLEADOS.SueldoBasico 
					FROM EMPLEADOS 
						LEFT JOIN CATEGORIAS 
							ON EMPLEADOS.IdCategoria = CATEGORIAS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY CATEGORIAS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorAntiguedad($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.Direccion, 
						CIUDADES.Nombre AS NombreCiudad, 
						EMPLEADOS.FechaIngreso, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						EMPLEADOS.SueldoBasico 
					FROM EMPLEADOS
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN CIUDADES 
							ON EMPLEADOS.IdCiudad = CIUDADES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY EMPLEADOS.FechaIngreso, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorEPS($query)
		{
			$query = <<<EOD
				SELECT TERCEROS.Documento AS DocumentoEPS,
						TERCEROS.Nombre AS NombreEPS, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico 
					FROM EMPLEADOS
						LEFT JOIN TERCEROS
							ON EMPLEADOS.IdEPS = TERCEROS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY TERCEROS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorFC($query)
		{
			$query = <<<EOD
				SELECT TERCEROS.Documento AS DocumentoFC,
						TERCEROS.Nombre AS NombreFC, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico 
					FROM EMPLEADOS
						LEFT JOIN TERCEROS
							ON EMPLEADOS.IdFondoCesantias = TERCEROS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY TERCEROS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorFP($query)
		{
			$query = <<<EOD
				SELECT TERCEROS.Documento AS DocumentoFP,
						TERCEROS.Nombre AS NombreFP, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico 
					FROM EMPLEADOS
						LEFT JOIN TERCEROS
							ON EMPLEADOS.IdFondoPensiones = TERCEROS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY TERCEROS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosPorARP($query)
		{
			$query = <<<EOD
				SELECT TERCEROS.Documento AS DocumentoARP,
						TERCEROS.Nombre AS NombreARP, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico 
					FROM EMPLEADOS
						LEFT JOIN TERCEROS
							ON EMPLEADOS.IdARP = TERCEROS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY TERCEROS.Nombre, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosEnPeriodoPrueba($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaPeriodoPrueba, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico, 
						PARAMETROS2.Detalle AS NombreTipoEmpleado 
					FROM EMPLEADOS
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON EMPLEADOS.TipoEmpleado = PARAMETROS2.Id 
					$query AND
						EMPLEADOS.FechaPeriodoPrueba >= GETDATE() 
					ORDER BY EMPLEADOS.FechaPeriodoPrueba, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosNuevos($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaPeriodoPrueba, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico, 
						PARAMETROS2.Detalle AS NombreTipoEmpleado 
					FROM EMPLEADOS
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
						INNER JOIN PARAMETROS AS PARAMETROS2 
							ON CENTROS.TipoEmpleado = PARAMETROS2.Id 
					$query AND
						YEAR(EMPLEADOS.FechaIngreso) = YEAR(GETDATE()) AND  
						MONTH(EMPLEADOS.FechaIngreso) = MONTH(GETDATE())  
					ORDER BY EMPLEADOS.FechaIngreso, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function empleadosRetirados($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						EMPLEADOS.FechaIngreso, 
						EMPLEADOS.FechaRetiro, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.SueldoBasico, 
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
					$query AND
						YEAR(EMPLEADOS.FechaRetiro) = YEAR(GETDATE()) 
					ORDER BY EMPLEADOS.FechaRetiro, EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function cumpleanosEmpleados($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CARGOS.Cargo,
						CARGOS.Nombre AS NombreCargo, 
						EMPLEADOS.FechaNacimiento 
					FROM EMPLEADOS
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id
						INNER JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.Estado = PARAMETROS1.Id 
					$query 
					ORDER BY MONTH(EMPLEADOS.FechaNacimiento), DAY(EMPLEADOS.FechaNacimiento), EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}

		public function entregaDocumentos($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						CARGOS.Cargo, 
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
							ON EMPLEADOS.TipoEmpleado = PARAMETROS2.Id 
					$query 
					ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2; 
			EOD;

			$request = $this->listar($query);
			return $request;
		}


	}
?>