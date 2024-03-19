<?php
	header('Content-Type: text/html; charset=UTF-8');

	// ESTE COMANDO PERMITE QUE EN LOS DATOS JSON VENGAN BIEN CODIFICADOS LOS ACENTOS
	header("Content-Type: text/plain; charset=UTF-8");

	require_once('../config/config.php');
	$conn = new PDO('sqlsrv:Server=' . DB_HOST . ';Database=' . DB_NAME . ';TrustServerCertificate=True;Encrypt=0;', DB_USER, DB_PASSWORD);
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	//Variables recibidas por POST de nuestra conexion AJAX
	$TipoConsulta = $_REQUEST['Tipo'];
	$Campo = $_REQUEST['Campo'];
	
	switch ( $TipoConsulta )
	{
		case 'Empleado':
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						EMPLEADOS.FechaRetiro 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE EMPLEADOS.Documento = '$Campo' AND 
						PARAMETROS.Detalle = 'ACTIVO';
			EOD;

			foreach ($conn->query($query) as $regEmpleado) 
			{
				$aDatos = array('documento' 	=> $regEmpleado['Documento'],
								'apellido1' 	=> $regEmpleado['Apellido1'],
								'apellido2' 	=> $regEmpleado['Apellido2'],
								'nombre1' 		=> $regEmpleado['Nombre1'],
								'nombre2' 		=> $regEmpleado['Nombre2'],
								'nombrecargo' 	=> $regEmpleado['NombreCargo'],
								'centro' 		=> $regEmpleado['Centro'], 
								'nombrecentro' 	=> $regEmpleado['NombreCentro'],
								'fecharetiro' 	=> $regEmpleado['FechaRetiro']
							);
			}

			if (! isset($aDatos)) 
			{
				$aDatos = array('documento' 	=> '',
								'apellido1' 	=> 'EMPLEADO NO EXISTE',
								'apellido2' 	=> '',
								'nombre1' 		=> '',
								'nombre2' 		=> '',
								'nombrecargo' 	=> '',
								'centro' 		=> '',
								'nombrecentro' 	=> '', 
								'fecharetiro'	=> NULL
							);	
			}

			echo json_encode($aDatos, JSON_FORCE_OBJECT);

			break;
			
		case 'EmpleadoRetirado':
			$query = <<<EOD
				SELECT EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CARGOS.Nombre AS NombreCargo, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						EMPLEADOS.FechaRetiro 
					FROM EMPLEADOS 
						LEFT JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						LEFT JOIN CENTROS 
							ON EMPLEADOS.IdCentro = CENTROS.Id 
						INNER JOIN PARAMETROS 
							ON EMPLEADOS.Estado = PARAMETROS.Id 
					WHERE EMPLEADOS.Documento = '$Campo' AND 
						PARAMETROS.Detalle = 'RETIRADO'
					ORDER BY EMPLEADOS.FechaRetiro ASC;
			EOD;

			foreach ($conn->query($query) as $regEmpleado) 
			{
				$aDatos = array('documento' 	=> $regEmpleado['Documento'],
								'apellido1' 	=> $regEmpleado['Apellido1'],
								'apellido2' 	=> $regEmpleado['Apellido2'],
								'nombre1' 		=> $regEmpleado['Nombre1'],
								'nombre2' 		=> $regEmpleado['Nombre2'],
								'nombrecargo' 	=> $regEmpleado['NombreCargo'],
								'centro' 		=> $regEmpleado['Centro'], 
								'nombrecentro' 	=> $regEmpleado['NombreCentro'],
								'fecharetiro' 	=> $regEmpleado['FechaRetiro']
							);
			}

			if (! isset($aDatos)) 
			{
				$aDatos = array('documento' 	=> '',
								'apellido1' 	=> 'EMPLEADO NO EXISTE',
								'apellido2' 	=> '',
								'nombre1' 		=> '',
								'nombre2' 		=> '',
								'nombrecargo' 	=> '',
								'centro' 		=> '',
								'nombrecentro' 	=> '', 
								'fecharetiro'	=> NULL
							);	
			}

			echo json_encode($aDatos, JSON_FORCE_OBJECT);

			break;
			
		case 'Concepto':
			$Mayor = substr($Campo, 0, 2);
			$Auxiliar = substr($Campo, 2, 3);

			$query = <<<EOD
				SELECT AUXILIARES.*, 
						PARAMETROS1.Detalle AS NombreTipoLiquidacion, 
						PARAMETROS2.Detalle AS NombreTipoRegistroAuxiliar 
					FROM AUXILIARES 
						INNER JOIN MAYORES 
							ON AUXILIARES.IdMayor = MAYORES.Id 
						INNER JOIN PARAMETROS AS PARAMETROS1
							ON MAYORES.TipoLiquidacion = PARAMETROS1.Id
						LEFT JOIN PARAMETROS AS PARAMETROS2
							ON AUXILIARES.TipoRegistroAuxiliar = PARAMETROS2.Id
					WHERE AUXILIARES.Auxiliar = '$Auxiliar' AND 
						MAYORES.Mayor = '$Mayor' AND 
						AUXILIARES.Borrado = 0;
			EOD;

			foreach ($conn->query($query) as $reg) 
			{
				$aDatos = array('nombre' => $reg['nombre'],
								'NombreTipoLiquidacion' => $reg['NombreTipoLiquidacion'],
								'NombreTipoRegistroAuxiliar' => $reg['NombreTipoRegistroAuxiliar'],
							);
			}	

			if (! isset($aDatos)) 
			{
				$aDatos = array('nombre' => 'CONCEPTO NO EXISTE',
								'NombreTipoLiquidacion' => '',
								'NombreTipoRegistroAuxiliar' => '',
							);	
			}

			echo json_encode($aDatos, JSON_FORCE_OBJECT);

			break;
					
		case 'Centro':
			$query = <<<EOD
				SELECT CENTROS.* 
					FROM CENTROS 
					WHERE CENTROS.Centro = '$Campo' AND 
						CENTROS.Borrado = 0;
			EOD;

			foreach ($conn->query($query) as $reg) 
			{
				$aDatos = array('centro' => $reg['centro'],
								'nombre' => $reg['nombre']
							);
			}	

			if (! isset($aDatos)) 
			{
				$aDatos = array('centro' => '', 
								'nombre' => 'CENTRO DE COSTOS NO EXISTE'
							);	
			}

			echo json_encode($aDatos, JSON_FORCE_OBJECT);

			break;
						
		case 'Periodo':
			$query = <<<EOD
				SELECT PERIODOS.* 
					FROM PERIODOS 
					WHERE PERIODOS.Id = $Campo;
			EOD;

			$reg = $conn->query($query);

			if	($reg)
			{
				$aDatos = pg_fetch_array($reg, NULL, PGSQL_ASSOC);
				echo json_encode($aDatos, JSON_FORCE_OBJECT);
			}

			break;

		case 'Tercero':
			$query = <<<EOD
				SELECT TERCEROS.* 
					FROM TERCEROS 
					WHERE TERCEROS.Documento = '$Campo';
			EOD;

			foreach ($conn->query($query) as $reg) 
			{
				$aDatos = array('documento' => $reg['documento'],
								'nombre' => $reg['nombre']
							);
			}	

			if (empty($aDatos)) 
			{
				$aDatos = array('documento' => '',
								'nombre' => 'TERCERO NO EXISTE'
							);	
			}

			echo json_encode($aDatos, JSON_FORCE_OBJECT);

			break;

		case 'Diagnostico':
			$query = <<<EOD
				SELECT DIAGNOSTICOS.* 
					FROM DIAGNOSTICOS 
					WHERE DIAGNOSTICOS.Diagnostico = '$Campo';
			EOD;

			foreach ($conn->query($query) as $regDiagnostico) 
			{
				$aDatos = array('diagnostico' => $regDiagnostico['diagnostico'],
								'nombre' => $regDiagnostico['nombre']
							);
			}

			if (! isset($aDatos)) 
			{
				$aDatos = array('diagnostico' => '',
								'nombre' => 'DIAGNÓSTICO NO EXISTE'
							);	
			}

			echo json_encode($aDatos, JSON_FORCE_OBJECT);

			break;

		default:
			// echo $TipoConsulta;
	}
?>
