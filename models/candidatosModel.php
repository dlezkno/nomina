<?php
	class candidatosModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM EMPLEADOS 
						LEFT JOIN EMPLEADOS AS SICOLOGOS 
							ON EMPLEADOS.IdSicologo = SICOLOGOS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					$query;
			EOD;

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarCandidatos($query)
		{
			$query = <<<EOD
				SELECT EMPLEADOS.*, 
						CARGOS.Nombre AS NombreCargo, 
						CIUDADES.Nombre AS NombreCiudad, 
						PARAMETROS1.Detalle AS EstadoCivil, 
						SICOLOGOS.Apellido1 AS Apellido1S, 
						SICOLOGOS.Apellido2 AS Apellido2S, 
						SICOLOGOS.Nombre1 AS Nombre1S, 
						SICOLOGOS.Nombre2 AS Nombre2S, 
						PARAMETROS.Detalle AS EstadoEmpleado 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CIUDADES 
							ON EMPLEADOS.IdCiudad = CIUDADES.Id 
						LEFT JOIN EMPLEADOS AS SICOLOGOS 
							ON EMPLEADOS.IdSicologo = SICOLOGOS.Id 
						LEFT JOIN PARAMETROS AS PARAMETROS1 
							ON EMPLEADOS.EstadoCivil = PARAMETROS1.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					$query
			EOD;

			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarCandidato(array $data)
		{
			$Documento = $_SESSION['Login']['Documento'];

			$query = <<<EOD
				SELECT EMPLEADOS.Id 
					FROM EMPLEADOS 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE EMPLEADOS.Documento = '$Documento' AND 
						PARAMETROS.Detalle = 'ACTIVO';
			EOD;

			$reg = $this->leer($query);

			if ($reg)
				$IdSicologo = $reg['Id'];
			else
				$IdSicologo = 0;

			$query = <<<EOD
				INSERT INTO EMPLEADOS 
					(Documento, Apellido1, Apellido2, Nombre1, Nombre2, Email, Celular, Estado, IdSicologo ) 
					VALUES (
						:Documento, 
						:Apellido1, 
						:Apellido2, 
						:Nombre1, 
						:Nombre2, 
						:Email, 
						:Celular, 
						:Estado, 
						$IdSicologo);
			EOD;

			$id = $this->adicionar($query, $data);
			return $id;
		}	
		
		public function buscarCandidato($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarCandidato(array $data, int $id)
		{
			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						Documento				= :Documento, 
						Apellido1				= :Apellido1, 
						Apellido2				= :Apellido2, 
						Nombre1					= :Nombre1, 
						Nombre2					= :Nombre2, 
						Email 					= :Email, 
						Celular 				= :Celular, 
						Estado					= :Estado, 
						FechaActualizacion 		= getDate() 
					WHERE EMPLEADOS.Id = $id;
			EOD;

			$resp = $this->actualizar($query, $data);

			return $resp;
		}

		public function actualizarCondiciones(array $data, int $id)
		{
			$datos = array();
			$datos['Id'] 					= $data['Id'];
			$datos['IdCargo'] 				= $data['IdCargo'];
			$datos['IdCentro'] 				= $data['IdCentro'];
			$datos['IdProyecto'] 			= $data['IdProyecto'];
			$datos['Vicepresidencia'] 		= $data['Vicepresidencia'];
			$datos['IdSede'] 				= $data['IdSede'];
			$datos['TipoContrato'] 			= $data['TipoContrato'];
			$datos['ModalidadTrabajo'] 		= $data['ModalidadTrabajo'];
			$datos['SueldoBasico'] 			= $data['SueldoBasico'];
			$datos['duracionContrato'] 		= $data['duracionContrato'];
			$datos['Observaciones'] 		= strtoupper($data['Observaciones']);
			$datos['SubsidioTransporte'] 	= $data['SubsidioTransporte'];
			$datos['PeriodicidadPago'] 		= $data['PeriodicidadPago'];
			$datos['HorasMes'] 				= $data['HorasMes'];
			$datos['DiasAno'] 				= $data['DiasAno'];

			$query = <<<EOD
				UPDATE EMPLEADOS 
					SET 
						IdCargo					= :IdCargo, 
						IdCentro				= :IdCentro, 
						IdProyecto				= :IdProyecto, 
						Vicepresidencia			= :Vicepresidencia, 
						IdSede					= :IdSede, 
						TipoContrato			= :TipoContrato, 
						ModalidadTrabajo		= :ModalidadTrabajo, 
						SueldoBasico			= :SueldoBasico,						
						duracionContrato		= :duracionContrato,
						Observaciones			= :Observaciones, 
						SubsidioTransporte		= :SubsidioTransporte, 
						PeriodicidadPago		= :PeriodicidadPago, 
						HorasMes				= :HorasMes, 
						DiasAno					= :DiasAno, 
						SEL_CondicionesLaborales = 1, 
						FechaActualizacion 		= getDate() 
					WHERE EMPLEADOS.Id 			= :Id;
			EOD;

			$resp = $this->actualizar($query, $datos);

			return $resp;
		}

		public function borrarCandidato(int $id)
		{
			$query = 'DELETE FROM EMPLEADOS WHERE EMPLEADOS.Id = ' . $id;
			$resp = $this->borrar($query);

			return $resp;
		}
	}
?>