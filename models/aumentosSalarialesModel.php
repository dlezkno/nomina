<?php
	class aumentosSalarialesModel extends pgSQL
	{
		public function __construct()
		{
			parent::__construct();
		}	
		
		public function contarRegistros($query)
		{
			$query = <<<EOD
				SELECT COUNT(*) AS Registros 
					FROM AUMENTOSSALARIALES 
						INNER JOIN EMPLEADOS 
							ON AUMENTOSSALARIALES.IdEmpleado = EMPLEADOS.Id 
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
					$query
			EOD; 

			$request = $this->leer($query);
			return $request['Registros'];
		}

		public function listarAumentosSalariales($query)
		{
			$query = <<<EOD
				SELECT AUMENTOSSALARIALES.*, 
					EMPLEADOS.Documento, 
					EMPLEADOS.Apellido1, 
					EMPLEADOS.Apellido2,  
					EMPLEADOS.Nombre1, 
					EMPLEADOS.Nombre2, 
					CARGOS.Nombre AS NombreCargo, 
					CENTROS.Nombre AS NombreCentro 
				FROM AUMENTOSSALARIALES 
					INNER JOIN EMPLEADOS 
						ON AUMENTOSSALARIALES.IdEmpleado = EMPLEADOS.Id 
					INNER JOIN CARGOS 
						ON EMPLEADOS.IdCargo = CARGOS.Id 
					INNER JOIN CENTROS 
						ON EMPLEADOS.IdCentro = CENTROS.Id 
				$query
			EOD;
			
			$request = $this->listar($query);
			return $request;
		}
		
		public function guardarAumentoSalarial(array $data)
		{
			$IdEmpleado = $data['IdEmpleado'];
			$FechaAumento = $data['FechaAumento'];
			$SueldoBasicoAnterior = $data['SueldoBasicoAnterior'];
			$SubsidioTransporteAnterior = $data['SubsidioTransporteAnterior'];
			$SueldoBasico = $data['SueldoBasico'];
			$SubsidioTransporte = $data['SubsidioTransporte'];
			$IdUsuario = $_SESSION['Login']['Id'];

			// ESTOS DATOS SE ACTUALIZAN AL ACUMULAR LA NOMINA
			// $Campo = 'SueldoBasico';

			// $query = <<<EOD
			// 	INSERT INTO LOGEMPLEADOS 
			// 		(IdEmpleado, Campo, ValorAnterior, ValorActual, IdUsuario)
			// 		VALUES (
			// 			$IdEmpleado, 
			// 			'$Campo', 
			// 			'$SueldoBasicoAnterior', 
			// 			'$SueldoBasico',
			// 			$IdUsuario);
			// EOD;

			// $ok = $this->query($query);

			// if ($SubsidioTransporteAnterior <> $SubsidioTransporte) 
			// {
			// 	$Campo = 'SubsidioTransporte';

			// 	$query = <<<EOD
			// 		INSERT INTO LOGEMPLEADOS 
			// 			(IdEmpleado, Campo, ValorAnterior, ValorActual, IdUsuario)
			// 			VALUES (
			// 				$IdEmpleado, 
			// 				'$Campo', 
			// 				'$SubsidioTransporteAnterior', 
			// 				'$SubsidioTransporte',
			// 				$IdUsuario);
			// 	EOD;

			// 	$ok = $this->query($query);
			// }

			// $query = <<<EOD
			// 	UPDATE EMPLEADOS
			// 		SET
			// 			SueldoBasico = $SueldoBasico, 
			// 			SubsidioTransporte = $SubsidioTransporte 
			// 		WHERE EMPLEADOS.Id = $IdEmpleado;
			// EOD;

			// $ok = $this->query($query);

			$query = <<<EOD
				INSERT INTO AUMENTOSSALARIALES (
					IdEmpleado, FechaAumento, SueldoBasicoAnterior, SubsidioTransporteAnterior, SueldoBasico, SubsidioTransporte, Procesado)
					VALUES (
					:IdEmpleado, 
					:FechaAumento, 
					:SueldoBasicoAnterior, 
					:SubsidioTransporteAnterior, 
					:SueldoBasico, 
					:SubsidioTransporte, 
					:Procesado); 
			EOD;

			$id = $this->adicionar($query, $data);

			return $id;
		}	
		
		public function buscarAumentoSalarial($query)
		{
			$request = $this->leer($query);
			return $request;
		}	
		
		public function actualizarAumentoSalarial(array $data, int $id)
		{
			$regAumento = getRegistro('AUMENTOSSALARIALES', $id);

			$IdEmpleado = $regAumento['idempleado'];

			$FechaAumento = $data['FechaAumento'];
			$SueldoBasico = $data['SueldoBasico'];
			$SubsidioTransporte = $data['SubsidioTransporte'];

			// ESTOS DATOS SE ACTUALIZAN AL ACUMULAR LA NOMINA
			// $query = <<<EOD
			// 	SELECT * 
			// 		FROM LOGEMPLEADOS 
			// 		WHERE LOGEMPLEADOS.IdEmpleado = $IdEmpleado AND 
			// 			LOGEMPLEADOS.Campo LIKE 'SueldoBasico' 
			// 		ORDER BY LOGEMPLEADOS.Id DESC;
			// EOD;

			// $logs = $this->listar($query);

			// if ($logs) 
			// {
			// 	$IdLog = $logs[0]['id'];

			// 	$query = <<<EOD
			// 		UPDATE LOGEMPLEADOS
			// 			SET ValorActual = '$SueldoBasico' 
			// 			WHERE LOGEMPLEADOS.Id = $IdLog;
			// 	EOD;

			// 	$ok = $this->query($query);
			// }

			// $query = <<<EOD
			// 	SELECT * 
			// 		FROM LOGEMPLEADOS 
			// 		WHERE LOGEMPLEADOS.IdEmpleado = $IdEmpleado AND 
			// 			LOGEMPLEADOS.Campo LIKE 'SubsidioTransporte' 
			// 		ORDER BY LOGEMPLEADOS.Id DESC;
			// EOD;

			// $logs = $this->listar($query);

			// if ($logs) 
			// {
			// 	$IdLog = $logs[0]['id'];

			// 	$query = <<<EOD
			// 		UPDATE LOGEMPLEADOS
			// 			SET ValorActual = '$SubsidioTranspsorte' 
			// 			WHERE LOGEMPLEADOS.Id = $IdLog;
			// 	EOD;

			// 	$ok = $this->query($query);
			// }

			// $query = <<<EOD
			// 	UPDATE EMPLEADOS
			// 		SET
			// 			SueldoBasico = $SueldoBasico, 
			// 			SubsidioTransporte = $SubsidioTransporte 
			// 		WHERE EMPLEADOS.Id = $IdEmpleado;
			// EOD;

			// $ok = $this->query($query);

			$query = <<<EOD
				UPDATE AUMENTOSSALARIALES
					SET 
						FechaAumento = '$FechaAumento', 
						SueldoBasico = $SueldoBasico, 
						SubsidioTransporte = $SubsidioTransporte, 
						FechaActualizacion = getDate()
				WHERE AUMENTOSSALARIALES.Id = $id;
			EOD;

			$ok = $this->query($query);

			return $ok;
		}

		public function borrarAumentoSalarial(int $id)
		{
			$regAumento = getRegistro('AUMENTOSSALARIALES', $id);

			$IdEmpleado = $regAumento['idempleado'];
			$FechaAumento = $regAumento['fechaaumento'];
			$SueldoBasicoAnterior = $regAumento['sueldobasicoanterior'];
			$SubsidioTransporteAnterior = $regAumento['subsidiotransporteanterior'];

			// ESTOS DATOS SE ACTUALIZAN AL ACUMULAR LA NOMINA
			// $query = <<<EOD
			// 	SELECT * 
			// 		FROM LOGEMPLEADOS 
			// 		WHERE LOGEMPLEADOS.IdEmpleado = $IdEmpleado AND 
			// 			LOGEMPLEADOS.Campo LIKE 'SueldoBasico' 
			// 		ORDER BY LOGEMPLEADOS.Id DESC;
			// EOD;

			// $logs = $this->listar($query);

			// if ($logs) 
			// {
			// 	$IdLog = $logs[0]['id'];

			// 	$query = <<<EOD
			// 		DELETE FROM LOGEMPLEADOS
			// 			WHERE LOGEMPLEADOS.Id = $IdLog;
			// 	EOD;

			// 	$ok = $this->query($query);
			// }

			// $query = <<<EOD
			// 	SELECT * 
			// 		FROM LOGEMPLEADOS 
			// 		WHERE LOGEMPLEADOS.IdEmpleado = $IdEmpleado AND 
			// 			LOGEMPLEADOS.Campo LIKE 'SubsidioTransporte' 
			// 		ORDER BY LOGEMPLEADOS.Id DESC;
			// EOD;

			// $logs = $this->listar($query);

			// if ($logs) 
			// {
			// 	$IdLog = $logs[0]['id'];

			// 	$query = <<<EOD
			// 		DELETE FROM LOGEMPLEADOS
			// 			WHERE LOGEMPLEADOS.Id = $IdLog;
			// 	EOD;

			// 	$ok = $this->query($query);
			// }

			// $query = <<<EOD
			// 	UPDATE EMPLEADOS
			// 		SET
			// 			SueldoBasico = $SueldoBasicoAnterior, 
			// 			SubsidioTransporte = $SubsidioTransporteAnterior 
			// 		WHERE EMPLEADOS.Id = $IdEmpleado;
			// EOD;

			// $ok = $this->query($query);

			$query = <<<EOD
				DELETE FROM AUMENTOSSALARIALES 
					WHERE AUMENTOSSALARIALES.Id = $id
			EOD;

			$ok = $this->query($query);

			return $ok;
		}
	}
?>