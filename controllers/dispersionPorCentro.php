<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class DispersionPorCentro extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/dispersionPorCentro/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR OR $_SESSION['Login']['Perfil'] == RRHH OR $_SESSION['Login']['Perfil'] == RRHH_AUX)
				$_SESSION['Importar'] = SERVERURL . '/dispersionPorCentro/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/dispersionPorCentro/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['DISPERSIONPORCENTRO']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['DISPERSIONPORCENTRO']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['DISPERSIONPORCENTRO']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['DISPERSIONPORCENTRO']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['DISPERSIONPORCENTRO']['Filtro']))
			{
				$_SESSION['DISPERSIONPORCENTRO']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['DISPERSIONPORCENTRO']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['DISPERSIONPORCENTRO']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['DISPERSIONPORCENTRO']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['DISPERSIONPORCENTRO']['Orden'])) 
					$_SESSION['DISPERSIONPORCENTRO']['Orden'] = 'EMPLEADOS.Apellido1,EMPLEADOS.Apellido2,EMPLEADOS.Nombre1,EMPLEADOS.Nombre2,CENTROS.Nombre';

			$Referencia 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$Periodicidad 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$Ciclo 					= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'CicloEnLiquidacion'")['valor'];
			$FechaLimiteNovedades 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			$regPeriodo 			= getRegistro('PERIODOS', $IdPeriodo);
			$Periodo 				= $regPeriodo['periodo'];

			$regPeriodicidad 		= getRegistro('PARAMETROS', $Periodicidad);
			$cPeriodicidad 			= substr($regPeriodicidad['detalle'], 0, 1);

			if ($FechaLimiteNovedades < date('Y-m-d')) 
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['Importar'] = '';
				$data['RO'] = TRUE;
			}
			else
				$data['RO'] = FALSE;

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= ' AND (';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ') ';
			}

			$query = "WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo $query";
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['DISPERSIONPORCENTRO']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarNovedades($query);
			$this->views->getView($this, 'novedades', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/dispersionPorCentro/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/dispersionPorCentro/lista/' . $_SESSION['DISPERSIONPORCENTRO']['Pagina'];

			$Referencia = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$Periodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$Periodo = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$FechaLimiteNovedades = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar dispersionPorCentro ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit();
			}

			$query = <<<EOD
				PERIODOS.Referencia = $Referencia AND 
				PERIODOS.Periodicidad = $Periodicidad AND 
				PERIODOS.Periodo = $Periodo;
			EOD;	

			$regPeriodo = getRegistro('PERIODOS', 0, $query);

			$data = array(
				'reg' => array(
					'IdPeriodo' => $IdPeriodo,
					'Periodo' => $Periodo,
					'FechaInicial' => $regPeriodo['fechainicial'],
					'FechaFinal' => $regPeriodo['fechafinal'],
					'IdEmpleado' => 0,
					'IdCentro' => 0,
					'Porcentaje' => isset($_REQUEST['Porcentaje']) ? $_REQUEST['Porcentaje'] : 0
				),	
				'mensajeError' => '',
				'Nov' => array() 
			);	

			$IdPeriodo = $data['reg']['IdPeriodo'];

			$cPeriodicidad = getRegistro('PARAMETROS', $regPeriodo['periodicidad'])['detalle'];

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$regEmp = buscarRegistro('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");

					if ($regEmp) 
					{
						$data['reg']['IdEmpleado'] = $regEmp['id'];
						$data['reg']['IdCentro'] = $regEmp['idcentro'];
						$IdEmpleado = $regEmp['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Centro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
				else
				{
					$regCentro = buscarRegistro('CENTROS', "CENTROS.Centro = '" . $_REQUEST['Centro'] . "'");

					if (! $regCentro) 
						$data['mensajeError'] .= '<strong>' . label('Centro de costos') . '</strong> ' . label('no existe') . '<br>';
					else
					{
						$IdCentro = $regCentro['id'];
						$data['reg']['IdCentro'] = $regCentro['id'];
					}

					$query = <<<EOD
							DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND
							DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND
							DISPERSIONPORCENTRO.IdCentro = $IdCentro;
					EOD;

					$regNom = buscarRegistro('DISPERSIONPORCENTRO', $query);

					if ($regNom) 
						$data['mensajeError'] .= '<strong>' . label('Novedad') . '</strong> ' . label('ya existe') . '<br>';
				}

				if ($_REQUEST['Porcentaje'] <= 0 OR $_REQUEST['Porcentaje'] > 100)
					$data['mensajeError'] .= '<strong>' . label('Porcentaje') . '</strong> ' . label('debe ser > que 0 y menor 0 igual a 100') . '<br>';
				else
				{
					$query = <<<EOD
							SELECT SUM(DISPERSIONPORCENTRO.Porcentaje) AS Porcentaje 
								FROM DISPERSIONPORCENTRO 
								WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND
								DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado 
					EOD;

					$reg = $this->model->leer($query);

					if ($reg AND $reg['Porcentaje'] > 100) 
						$data['mensajeError'] .= '<strong>' . label('Porcentaje total') . '</strong> ' . label('no puede superar el 100%') . '<br>';
				}

				if	( ! empty($data['mensajeError']) )
				{
					$this->views->getView($this, 'adicionar', $data);
					exit();
				}
				else
				{
					$id = $this->model->guardarNovedad($data['reg']);

					if ($id) 
					{
						// LISTA DE DISPERSIONPORCENTRO DEL EMPLEADO
						if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
						{
							$query = <<<EOD
								WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND 
									DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado 
								ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Nombre;
							EOD;
					
							$data['Nov'] = $this->model->listarNovedades($query);
						}
						else
							$data['Nov'] = array();

						$_REQUEST['NombreEmpleado'] = '';
						$_REQUEST['Cargo'] = '';
						$_REQUEST['Centro'] = '';
						$_REQUEST['NombreCentro'] = '';
						$data['reg']['Porcentaje'] = 0;
	
						$this->views->getView($this, 'adicionar', $data);
						exit();
					}
				}
			}
			else
			{
				$data['Nov'] = array();

				$this->views->getView($this, 'adicionar', $data);
				exit();
			}
		}

		public function editar($id)
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/dispersionPorCentro/actualizarNovedad';
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
			$_SESSION['Lista'] = SERVERURL . '/dispersionPorCentro/lista/' . $_SESSION['DISPERSIONPORCENTRO']['Pagina'];

			$Referencia 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'")['valor'];
			$Periodicidad 			= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'")['valor'];
			$IdPeriodo 				= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'")['valor'];
			$regPeriodo 			= getRegistro('PERIODOS', $IdPeriodo);
			$Periodo 				= $regPeriodo['periodo'];
			$FechaLimiteNovedades 	= getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'")['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar dispersionPorCentro ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit();
			}

			$query = <<<EOD
				SELECT DISPERSIONPORCENTRO.IdPeriodo, 
						PERIODOS.Periodo, 
						PERIODOS.FechaInicial, 
						PERIODOS.FechaFinal, 
						DISPERSIONPORCENTRO.IdEmpleado, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CARGOS.Nombre AS NombreCargo, 
						DISPERSIONPORCENTRO.IdCentro, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						DISPERSIONPORCENTRO.Porcentaje 
					FROM DISPERSIONPORCENTRO 
						INNER JOIN PERIODOS 
							ON DISPERSIONPORCENTRO.IdPeriodo = PERIODOS.Periodo 
						INNER JOIN EMPLEADOS 
							ON DISPERSIONPORCENTRO.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN CENTROS
							ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
					WHERE DISPERSIONPORCENTRO.Id = $id; 
			EOD;

			$regNov = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'IdPeriodo' 	=> $regNov['IdPeriodo'],
					'IdEmpleado' 	=> $regNov['IdEmpleado'],
					'IdCentro' 		=> $regNov['IdCentro'],
					'Porcentaje' 	=> isset($_REQUEST['Porcentaje']) ? $_REQUEST['Porcentaje'] : 0
				),	
				'mensajeError' 	=> '',
				'Nov' 			=> array(), 
				'Periodo' 		=> $regNov['Periodo'],
				'FechaInicial' 	=> $regNov['FechaInicial'],
				'FechaFinal' 	=> $regNov['FechaFinal'],
				'Documento' 	=> $regNov['Documento'], 
				'Apellido1' 	=> $regNov['Apellido1'],
				'Apellido2' 	=> $regNov['Apellido2'],
				'Nombre1' 		=> $regNov['Nombre1'],
				'Nombre2' 		=> $regNov['Nombre2'],
				'Cargo' 		=> $regNov['NombreCargo'], 
				'Centro' 		=> $regNov['Centro'], 
				'NombreCentro' 	=> $regNov['NombreCentro']
			);	

			$IdPeriodo = $data['reg']['IdPeriodo'];
			$Ciclo = $data['reg']['Ciclo'];
			
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$cPeriodicidad = getRegistro('PARAMETROS', $regPeriodo['periodicidad'])['detalle'];

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$regEmp = buscarRegistro('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");

					if ($regEmp) 
					{
						$data['reg']['IdEmpleado'] = $regEmp['id'];
						$data['reg']['IdCentro'] = $regEmp['idcentro'];
						$IdEmpleado = $regEmp['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Centro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
				else
				{
					$regCentro = buscarRegistro('CENTROS', "CENTROS.Centro = '" . $_REQUEST['Centro'] . "'");

					if (! $regCentro) 
						$data['mensajeError'] .= '<strong>' . label('Centro de costos') . '</strong> ' . label('no existe') . '<br>';
					else
					{
						$IdCentro = $regCentro['id'];
						$data['reg']['IdCentro'] = $regCentro['id'];
					}

					$query = <<<EOD
							DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND
							DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND
							DISPERSIONPORCENTRO.IdCentro = $IdCentro AND 
							DISPERSIONPORCENTRO.Id <> $id;
					EOD;

					$regNom = buscarRegistro('DISPERSIONPORCENTRO', $query);

					if ($regNom) 
						$data['mensajeError'] .= '<strong>' . label('Novedad') . '</strong> ' . label('ya existe') . '<br>';

				}

				if ($_REQUEST['Porcentaje'] <= 0 OR $_REQUEST['Porcentaje'] > 100)
					$data['mensajeError'] .= '<strong>' . label('Porcentaje') . '</strong> ' . label('debe ser > que 0 y menor 0 igual a 100') . '<br>';
				else
				{
					$query = <<<EOD
							SELECT SUM(DISPERSIONPORCENTRO.Porcentaje) AS Porcentaje 
								FROM DISPERSIONPORCENTRO 
								WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND
									DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND 
									DISPERSIONPORCENTRO.Id <> $id;
					EOD;

					$reg = $this->model->leer($query);

					if ($reg AND $reg['Porcentaje'] + $_REQUEST['Porcentaje'] > 100) 
						$data['mensajeError'] .= '<strong>' . label('Porcentaje total') . '</strong> ' . label('no puede superar el 100%') . '<br>';
					else
						$data['reg']['Porcentaje'] = $_REQUEST['Porcentaje'];
				}

				if	( ! empty($data['mensajeError']) )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit();
				}
				else
				{
					$id = $this->model->actualizarNovedad($data['reg'], $id);

					if ($id) 
					{
						// LISTA DE DISPERSIONPORCENTRO DEL EMPLEADO
						if (! empty($IdPeriodo) AND ! empty($Ciclo) AND ! empty($IdEmpleado)) 
						{
							$query = <<<EOD
								WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND 
									DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado 
								ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Nombre;
							EOD;
					
							$data['Nov'] = $this->model->listarNovedades($query);
						}
						else
							$data['Nov'] = array();

						$this->views->getView($this, 'actualizar', $data);
						exit();
					}
				}
			}
			else
			{
				// LISTA DE DISPERSIONPORCENTRO DEL EMPLEADO
				if (! empty($id)) 
				{
					$IdEmpleado = $data['reg']['IdEmpleado'];

					$query = <<<EOD
						WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND 
							DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarNovedades($query);
				}
				else
					$data['Nov'] = array();

				$this->views->getView($this, 'actualizar', $data);
				exit();
			}
		}

		public function borrar($id)
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = SERVERURL . '/dispersionPorCentro/actualizarNovedad';
			$_SESSION['ActualizarRegistro'] = '';
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
			$_SESSION['Lista'] = SERVERURL . '/dispersionPorCentro/lista/' . $_SESSION['DISPERSIONPORCENTRO']['Pagina'];

			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'ReferenciaEnLiquidacion'");
			$Referencia = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodicidadEnLiquidacion'");
			$Periodicidad = $reg['valor'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'PeriodoEnLiquidacion'");
			$Periodo = $reg['valor'];
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];
			$reg = getRegistro('PARAMETROS', 0, "PARAMETROS.Parametro = 'FechaLimiteNovedades'");
			$FechaLimiteNovedades = $reg['fecha'];

			if (date('Y-m-d') > $FechaLimiteNovedades) 
			{
				$data['mensajeError'] = 'Fecha límite para reportar dispersionPorCentro ha expirado.';
				$this->views->getView($this, 'adicionar', $data);
				exit();
			}

			$query = <<<EOD
				SELECT DISPERSIONPORCENTRO.IdPeriodo, 
						DISPERSIONPORCENTRO.IdEmpleado, 
						EMPLEADOS.Documento, 
						EMPLEADOS.Apellido1, 
						EMPLEADOS.Apellido2, 
						EMPLEADOS.Nombre1, 
						EMPLEADOS.Nombre2, 
						CARGOS.Nombre AS NombreCargo, 
						DISPERSIONPORCENTRO.IdCentro, 
						CENTROS.Centro, 
						CENTROS.Nombre AS NombreCentro, 
						DISPERSIONPORCENTRO.Porcentaje 
					FROM DISPERSIONPORCENTRO 
						INNER JOIN EMPLEADOS 
							ON DISPERSIONPORCENTRO.IdEmpleado = EMPLEADOS.Id
						INNER JOIN CARGOS 
							ON EMPLEADOS.IdCargo = CARGOS.Id 
						INNER JOIN CENTROS
							ON DISPERSIONPORCENTRO.IdCentro = CENTROS.Id 
					WHERE DISPERSIONPORCENTRO.Id = $id; 
			EOD;

			$regNov = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'IdPeriodo' 	=> $regNov['IdPeriodo'],
					'IdEmpleado' 	=> $regNov['IdEmpleado'],
					'IdCentro' 		=> $regNov['IdCentro'],
					'Porcentaje' 	=> $regNov['Porcentaje']
				),	
				'mensajeError' 	=> '',
				'Nov' 			=> array(), 
				'Documento' 	=> $regNov['Documento'], 
				'Apellido1' 	=> $regNov['Apellido1'],
				'Apellido2' 	=> $regNov['Apellido2'],
				'Nombre1' 		=> $regNov['Nombre1'],
				'Nombre2' 		=> $regNov['Nombre2'],
				'Cargo' 		=> $regNov['NombreCargo'], 
				'Centro' 		=> $regNov['Centro'], 
				'NombreCentro' 	=> $regNov['NombreCentro']
			);	

			$IdPeriodo = $data['reg']['IdPeriodo'];
			$Ciclo = $data['reg']['Ciclo'];
			
			$regPeriodo = getRegistro('PERIODOS', $IdPeriodo);
			$Periodo = $regPeriodo['periodo'];

			$cPeriodicidad = getRegistro('PARAMETROS', $regPeriodo['periodicidad'])['detalle'];

			if (isset($_REQUEST['Documento'])) 
			{
				if	( empty($_REQUEST['Documento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento de empleado') . '</strong><br>';
				else
				{
					$regEmp = buscarRegistro('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");

					if ($regEmp) 
					{
						$data['reg']['IdEmpleado'] = $regEmp['id'];
						$data['reg']['IdCentro'] = $regEmp['idcentro'];
						$IdEmpleado = $regEmp['id'];
					}
					else
						$data['mensajeError'] .= '<strong>' . label('Empleado') . '</strong> ' . label('no existe') . '<br>';
				}
				
				if	( empty($_REQUEST['Centro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
				else
				{
					$regCentro = buscarRegistro('CENTROS', "CENTROS.Centro = '" . $_REQUEST['Centro'] . "'");

					if (! $regCentro) 
						$data['mensajeError'] .= '<strong>' . label('Centro de costos') . '</strong> ' . label('no existe') . '<br>';
					else
					{
						$IdCentro = $regCentro['id'];
						$data['reg']['IdCentro'] = $regCentro['id'];
					}

					$query = <<<EOD
							DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND
							DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND
							DISPERSIONPORCENTRO.IdCentro = $IdCentro AND 
							DISPERSIONPORCENTRO.Id <> $id;
					EOD;

					$regNom = buscarRegistro('DISPERSIONPORCENTRO', $query);

					if ($regNom) 
						$data['mensajeError'] .= '<strong>' . label('Novedad') . '</strong> ' . label('ya existe') . '<br>';

				}

				if ($_REQUEST['Porcentaje'] <= 0 OR $_REQUEST['Porcentaje'] > 100)
					$data['mensajeError'] .= '<strong>' . label('Porcentaje') . '</strong> ' . label('debe ser > que 0 y menor 0 igual a 100') . '<br>';
				else
				{
					$query = <<<EOD
							SELECT SUM(DISPERSIONPORCENTRO.Porcentaje) AS Porcentaje 
								FROM DISPERSIONPORCENTRO 
								WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND
									DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND 
									DISPERSIONPORCENTRO.Id <> $id;
					EOD;

					$reg = $this->model->leer($query);

					if ($reg AND $reg['Porcentaje'] + $_REQUEST['Porcentaje'] > 100) 
						$data['mensajeError'] .= '<strong>' . label('Porcentaje total') . '</strong> ' . label('no puede superar el 100%') . '<br>';
					else
						$data['reg']['Porcentaje'] = $_REQUEST['Porcentaje'];
				}

				if	( ! empty($data['mensajeError']) )
				{
					$this->views->getView($this, 'actualizar', $data);
					exit();
				}
				else
				{
					$id = $this->model->borrarNovedad($id);

					header('Location: ' . SERVERURL . '/dispersionPorCentro/lista/1');
					exit();
				}
			}
			else
			{
				// LISTA DE DISPERSIONPORCENTRO DEL EMPLEADO
				if (! empty($id)) 
				{
					$IdEmpleado = $data['reg']['IdEmpleado'];

					$query = <<<EOD
						WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND 
							DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2, CENTROS.Nombre;
					EOD;
			
					$data['Nov'] = $this->model->listarNovedades($query);
				}
				else
					$data['Nov'] = array();

				$this->views->getView($this, 'actualizar', $data);
				exit();
			}
		}

		public function informe()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = 00;
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = SERVERURL . '/dispersionPorCentro/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['DISPERSIONPORCENTRO']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'WHERE ';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(EMPLEADOS.Documento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Apellido2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(EMPLEADOS.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['DISPERSIONPORCENTRO']['Orden']; 
			$data['rows'] = $this->model->listarNovedades($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();
			$data['mensajeError'] = '';

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] .= "Seleccione un <strong>Archivo en Excel</strong><br>";
				}
				else
				{
					ini_set('max_execution_time', 600);
					
					$archivo = $_FILES['archivo']['name'];
		
					if ( copy($_FILES['archivo']['tmp_name'], $archivo)) 
					{
						if ( file_exists ($archivo) )
						{
							// $oExcel = IOFactory::load($archivo);
							$Excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);	
							$oHoja = $Excel->getSheet(0);

							$EmpleadoAnt = '';

							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								$Referencia 	= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
								$Periodicidad 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
								$Periodo 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
								$Empleado 		= trim($oHoja->getCell('D' . $i)->getCalculatedValue());
								$Centro 		= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
								if (left($Centro, 1) <> 'S')
									$Centro			= str_pad($Centro, 5, '0', STR_PAD_LEFT);
								$Porcentaje 	= $oHoja->getCell('F' . $i)->getCalculatedValue();

								if ($i == 2) 
								{
									$cPeriodicidad = substr($Periodicidad, 0, 1);
									$Periodicidad = getRegistro('PARAMETROS', 0, "PARAMETROS.Detalle = '$Periodicidad'")['id'];

									if (! $Periodicidad) 
									{
										$data['mensajeError'] .= 'Periodicidad no existe (' . $cPeriodicidad . ') <br>';
										break;
									}

									$regPeriodo = getRegistro('PERIODOS', 0, "PERIODOS.Referencia = '$Referencia' AND PERIODOS.Periodicidad = $Periodicidad AND PERIODOS.Periodo = $Periodo");

									if ($regPeriodo)
										$IdPeriodo = $regPeriodo['id'];
									else
									{
										$data['mensajeError'] .= 'Período de nómina no existe (' . $Referencia . ' - ' . $Periodicidad . ' - ' . $Periodo . ') <br>';
										break;
									}

									$query = <<<EOD
										DELETE FROM DISPERSIONPORCENTRO 
											WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo;
									EOD;

									$this->model->query($query);
								}

								if ($Empleado <> $EmpleadoAnt) 
								{
									$EmpleadoAnt = $Empleado;

									$query = <<<EOD
										SELECT EMPLEADOS.*, 
												PARAMETROS.Detalle 
											FROM EMPLEADOS
												INNER JOIN PARAMETROS 
													ON EMPLEADOS.Estado = PARAMETROS.Id 
											WHERE EMPLEADOS.Documento = '$Empleado';
									EOD;

									$regEmp = $this->model->buscarNovedad($query);

									$IdEmpleado = $regEmp['id'];


									// if ($regEmp AND $regEmp['Detalle'] == 'ACTIVO')
									// else
									// {
									// 	$data['mensajeError'] .= 'Empleado no existe (' . $Empleado . ') o no está activo<br>';
									// 	continue;
									// }
								}

								$IdCentro = getId('CENTROS', "CENTROS.Centro = '$Centro'");

								if ($IdCentro == 0) 
								{
									$data['mensajeError'] .= 'Centro de costos no existe (' . $Centro . ')<br>';
									continue;
								}

								if ($Porcentaje < 0 OR $Porcentaje > 100) 
								{
									$data['mensajeError'] .= 'Porcentaje debe ser mayor que 0% y menor o igual a 100% (' . $Porcentaje . ')<br>';
									continue;
								}

								$datos = array($IdPeriodo, $IdEmpleado, $IdCentro, $Porcentaje); 

								$query = <<<EOD
									SELECT SUM(DISPERSIONPORCENTRO.Porcentaje) AS Porcentaje 
										FROM DISPERSIONPORCENTRO 
										WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND 
											DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND 
											DISPERSIONPORCENTRO.IdCentro = $IdCentro;
								EOD;

								$regNov = $this->model->buscarNovedad($query);

								if ($regNov AND $regNov['Porcentaje'] + $Porcentaje > 100) 
								{
									$data['mensajeError'] .= 'Porcentajes del empleado no debe superar el 100% (' . $Empleado . ')<br>';
									continue;
								}

								if (empty($data['mensajeError'])) 
								{
									$query = <<<EOD
										SELECT DISPERSIONPORCENTRO.Id 
											FROM DISPERSIONPORCENTRO 
											WHERE DISPERSIONPORCENTRO.IdPeriodo = $IdPeriodo AND 
												DISPERSIONPORCENTRO.IdEmpleado = $IdEmpleado AND 
												DISPERSIONPORCENTRO.IdCentro = $IdCentro;
									EOD;

									$regNov = $this->model->buscarNovedad($query);

									if ($regNov)
										$this->model->actualizarNovedad($datos, $regNov['Id']);
									else
										$this->model->guardarNovedad($datos);
								}
							}

							if (! empty($data['mensajeError'])) 
								$this->views->getView($this, 'importar', $data);
							else
								header('Location: ' . SERVERURL . '/dispersionPorCentro/lista/1');
							
							exit();
						}
					}
				}
			}

			if (isset($_FILES) AND count($_FILES) == 0 OR ! empty($data['mensajeError'])) 
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = '';
				$_SESSION['Retroceder'] = '';
				$_SESSION['Avanzar'] = '';
				$_SESSION['Novedades'] = '';
				$_SESSION['Importar'] =  '';
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/dispersionPorCentro/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/dispersionPorCentro/lista/' . $_SESSION['DISPERSIONPORCENTRO']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>