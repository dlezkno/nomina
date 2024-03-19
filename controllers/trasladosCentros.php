<?php
	require_once('./templates/vendor/autoload.php');

	class trasladosCentros extends Controllers
	{
		public function parametros($Ciclo = 0, $IdBanco = 0)
		{
			set_time_limit(0);

			$IdCentroActual = isset($_REQUEST['IdCentroActual']) ? $_REQUEST['IdCentroActual'] : 0;
			$IdCentroNuevo 	= isset($_REQUEST['IdCentroNuevo']) ? $_REQUEST['IdCentroNuevo'] : 0;

			// SE LEEN LOS PARÁMETROS
			$data = array(
				'reg' => array(
					'IdCentroActual' 	=> $IdCentroActual,  
					'IdCentroNuevo' 	=> $IdCentroNuevo,  
					'CantidadTraslados' => 0
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['IdCentroActual']))
			{
				// SE TRASLADAN LOS EMPLEADOS EN EL CENTRO DE COSTOS
				$query = <<<EOD
					SELECT EMPLEADOS.Id 
						FROM EMPLEADOS 
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE EMPLEADOS.IdCentro = $IdCentroActual AND 
							PARAMETROS.Detalle = 'ACTIVO';
				EOD;

				$empleados = $this->model->listar($query);

				if ($empleados)
				{
					$data['reg']['CantidadTraslados'] = count($empleados);
					$Campo = 'Centro';

					for ($i = 0; $i < count($empleados); $i++)
					{
						$IdEmpleado = $empleados[$i]['Id'];

						if ($IdCentroActual > 0) 
						{
							$reg = getRegistro('CENTROS', $IdCentroActual);
							if ($reg)
								$ValorAnterior = $reg['nombre'];
							else
								$ValorAnterior = '';
						}
						else
							$ValorAnterior = '';

						if ($IdCentroNuevo > 0)
						{
							$reg = getRegistro('CENTROS', $IdCentroNuevo);
							$ValorActual = $reg['nombre'];
						}
						else
							$ValorActual = '';

						$logEmpleado = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

						$ok = $this->model->guardarLogEmpleado($logEmpleado);

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET IdCentro = $IdCentroNuevo 
								WHERE EMPLEADOS.Id = $IdEmpleado; 
						EOD;

						$ok = $this->model->query($query);
					}
				}

				// SE TRASLADAN LOS EMPLEADOS EN EL PROYECTO
				$query = <<<EOD
					SELECT EMPLEADOS.Id 
						FROM EMPLEADOS 
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE EMPLEADOS.IdProyecto = $IdCentroActual AND 
							PARAMETROS.Detalle = 'ACTIVO';
				EOD;

				$empleados = $this->model->listar($query);

				if ($empleados)
				{
					$data['reg']['CantidadTraslados'] = count($empleados);
					$Campo = 'Proyecto';

					for ($i = 0; $i < count($empleados); $i++)
					{
						$IdEmpleado = $empleados[$i]['Id'];

						if ($IdCentroActual > 0) 
						{
							$reg = getRegistro('CENTROS', $IdCentroActual);
							if ($reg)
								$ValorAnterior = $reg['nombre'];
							else
								$ValorAnterior = '';
						}
						else
							$ValorAnterior = '';

						if ($IdCentroNuevo > 0)
						{
							$reg = getRegistro('CENTROS', $IdCentroNuevo);
							$ValorActual = $reg['nombre'];
						}
						else
							$ValorActual = '';

						$logEmpleado = array($IdEmpleado, $Campo, $ValorAnterior, $ValorActual, $_SESSION['Login']['Id']);

						$ok = $this->model->guardarLogEmpleado($logEmpleado);

						$query = <<<EOD
							UPDATE EMPLEADOS 
								SET IdProyecto = $IdCentroNuevo 
								WHERE EMPLEADOS.Id = $IdEmpleado; 
						EOD;

						$ok = $this->model->query($query);
					}
				}
			}

            $_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/trasladosCentros/parametros';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = '';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';

			if ($data) 
				$this->views->getView($this, 'parametros', $data);
		}
	}
?>