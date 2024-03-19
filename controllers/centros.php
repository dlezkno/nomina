<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Centros extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/centros/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/centros/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/centros/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['CENTROS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['CENTROS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['CENTROS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['CENTROS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['CENTROS']['Filtro']))
			{
				$_SESSION['CENTROS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['CENTROS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['CENTROS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['CENTROS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['CENTROS']['Orden'])) 
					$_SESSION['CENTROS']['Orden'] = 'CENTROS.Centro';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query = 'AND (';
					else
						$query .= 'OR ';

					$query .= "UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}

				$query .= ')';
			}

			$query = 'WHERE CENTROS.Borrado = 0 ' . $query;
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['CENTROS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarCentros($query);
			$this->views->getView($this, 'centros', $data);
		}	
		
		public function adicionar()
		{
			$data = array(
				'reg' => array(
					'Centro' 			=> isset($_REQUEST['Centro']) ? $_REQUEST['Centro'] : '',
					'Nombre' 			=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '', 
					'FechaVencimiento' 	=> isset($_REQUEST['FechaVencimiento']) ? $_REQUEST['FechaVencimiento'] : NULL, 
					'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0,
					'IdGerente' 		=> isset($_REQUEST['IdGerente']) ? $_REQUEST['IdGerente'] : 0,
					'Vicepresidencia'	=> isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Centro'])) 
			{
				if	( empty($data['reg']['Centro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Centro de costo') . '</strong><br>';
				else
				{
					$Centro = $data['reg']['Centro'];

					$query = <<<EOD
						SELECT * 
							FROM CENTROS 
							WHERE CENTROS.Centro = '$Centro' AND 
								CENTROS.Borrado = 0;
					EOD;

					$reg = $this->model->buscarCentro($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Centro') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['FechaInicio']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';
			
				// if	( empty($data['reg']['FechaVencimiento']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong><br>';
			
				if	( empty($data['reg']['TipoEmpleado']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';
			
				// if	( empty($data['reg']['Documento']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';

				if (! empty($_REQUEST['Documento']))
				{
					$data['reg']['IdGerente'] = getId('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");

					$data['reg']['IdGerente'] = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");

					if ($data['reg']['IdGerente'] == 0)
						$data['mensajeError'] .= '<strong>' . label('Gerente de proyecto') . ' </strong>' . label('no existe') . '<br>';
				}

				// if	( empty($data['reg']['Vicepresidencia']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Vicepresidencia') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarCentro($data['reg']);

					if ($id) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/centros/actualizarCentro';
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
				$_SESSION['Lista'] = SERVERURL . '/centros/lista/' . $_SESSION['CENTROS']['Pagina'];
	
				$this->views->getView($this, 'adicionar', $data);
			}
		}

		public function editar($id)
		{
			if (isset($_REQUEST['Centro']))
			{
				$data = array(
					'reg' => array(
						'Centro' 			=> isset($_REQUEST['Centro']) ? $_REQUEST['Centro'] : '',
						'Nombre' 			=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '', 
						'FechaVencimiento' 	=> (isset($_REQUEST['FechaVencimiento']) AND ! empty($_REQUEST['FechaVencimiento'])) ? $_REQUEST['FechaVencimiento'] : NULL, 
						'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0, 
						'IdGerente' 		=> 0, 
						'Vicepresidencia' 	=> isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0, 
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Centro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Centro de costo') . '</strong><br>';
				else
				{
					$Centro = $data['reg']['Centro'];

					$query = <<<EOD
						SELECT * 
							FROM CENTROS
							WHERE CENTROS.Centro = '$Centro' AND 
								CENTROS.Id <> $id AND 
								CENTROS.Borrado = 0;
					EOD;

					$reg = $this->model->buscarCentro($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Centro de costo') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['FechaVencimiento']) )
				// 	$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de vencimiento') . '</strong><br>';
			
				if	( empty($data['reg']['TipoEmpleado']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de empleado') . '</strong><br>';
			
				// if	( empty($data['reg']['Documento']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';

				if (! empty($_REQUEST['Documento']))
					$data['reg']['IdGerente'] = getId('EMPLEADOS', "EMPLEADOS.Documento = '" . $_REQUEST['Documento'] . "'");

				// if	( empty($data['reg']['Vicepresidencia']) )
				// 	$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Vicepresidencia') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarCentro($data['reg'], $id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = '';
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/centros/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/centros/lista/' . $_SESSION['CENTROS']['Pagina'];

				$query = <<<EOD
					SELECT CENTROS.Centro, 
							CENTROS.Nombre, 
							CENTROS.FechaInicio, 
							CENTROS.FechaVencimiento, 
							CENTROS.TipoEmpleado, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							CARGOS.Nombre AS NombreCargo, 
							CENTROS2.Nombre AS NombreCentro, 
							CENTROS.IdGerente, 
							CENTROS.Vicepresidencia   
						FROM CENTROS 
							LEFT JOIN EMPLEADOS 
								ON CENTROS.IdGerente = EMPLEADOS.Id 
							LEFT JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							LEFT JOIN CENTROS AS CENTROS2 
								ON EMPLEADOS.IdCentro = CENTROS2.Id 
						WHERE CENTROS.Id = $id;
				EOD;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Centro' 			=> $reg['Centro'],
						'Nombre' 			=> $reg['Nombre'], 
						'FechaInicio' 		=> $reg['FechaInicio'], 
						'FechaVencimiento' 	=> $reg['FechaVencimiento'], 
						'TipoEmpleado' 		=> $reg['TipoEmpleado'], 
						'IdGerente'			=> $reg['IdGerente'], 
						'Documento'			=> $reg['Documento'], 
						'NombreEmpleado'	=> $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], 
						'Cargo'				=> $reg['NombreCargo'], 
						'Vicepresidencia'	=> $reg['Vicepresidencia']
					),
					'mensajeError' => ''
				);

				$query = <<<EOD
					SELECT EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							CARGOS.Nombre AS NombreCargo 
						FROM EMPLEADOS 
							LEFT JOIN CENTROS 
								ON EMPLEADOS.IdCentro = CENTROS.Id 
							LEFT JOIN CENTROS AS PROYECTOS  
								ON EMPLEADOS.IdProyecto = PROYECTOS.Id 
							LEFT JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE (EMPLEADOS.IdCentro = $id OR 
							EMPLEADOS.IdProyecto = $id) AND 
							PARAMETROS.Detalle = 'ACTIVO' 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
				EOD;

				$dataEmp = $this->model->listar($query);
	
				if ($dataEmp)
				{
					for ($i = 0; $i < count($dataEmp); $i++) 
					{ 
						$data['regEmp'][$i] = array(
							'Documento' 		=> $dataEmp[$i]['Documento'],
							'NombreEmpleado' 	=> $dataEmp[$i]['Apellido1'] . ' ' . $dataEmp[$i]['Apellido2'] . ' ' . $dataEmp[$i]['Nombre1'] . ' ' . $dataEmp[$i]['Nombre2'],
							'Cargo' => $dataEmp[$i]['NombreCargo']
						);
					}
				}
				else
					$data['regEmp'] = false;

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			if (isset($_REQUEST['Centro']))
			{
				$data = array(
					'reg' => array(
						'Centro' 			=> isset($_REQUEST['Centro']) ? $_REQUEST['Centro'] : '',
						'Nombre' 			=> isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '', 
						'FechaInicio' 		=> (isset($_REQUEST['FechaInicio']) AND ! empty($_REQUEST['FechaInicio'])) ? $_REQUEST['FechaInicio'] : NULL, 
						'FechaVencimiento' 	=> (isset($_REQUEST['FechaVencimiento']) AND ! empty($_REQUEST['FechaVencimiento'])) ? $_REQUEST['FechaVencimiento'] : NULL, 
						'TipoEmpleado' 		=> isset($_REQUEST['TipoEmpleado']) ? $_REQUEST['TipoEmpleado'] : 0, 
						'IdGerente' 		=> 0, 
						'Vicepresidencia' 	=> isset($_REQUEST['Vicepresidencia']) ? $_REQUEST['Vicepresidencia'] : 0, 
					),
					'mensajeError' => ''
				);

				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM EMPLEADOS 
						WHERE EMPLEADOS.IdCentro = $id;
				EOD;

				$reg = $this->model->buscarCentro($query);

				if ($reg['registros'] > 0) 
				{
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Centro de costos') . '</strong><br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarCentro($id);

					if ($resp) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
			{
				$_SESSION['NuevoRegistro'] = '';
				$_SESSION['BorrarRegistro'] = SERVERURL . '/centros/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/centros/lista/' . $_SESSION['CENTROS']['Pagina'];

				$query = <<<EOD
					SELECT CENTROS.Centro, 
							CENTROS.Nombre, 
							CENTROS.FechaInicio, 
							CENTROS.FechaVencimiento, 
							CENTROS.TipoEmpleado, 
							EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							CARGOS.Nombre AS NombreCargo, 
							CENTROS2.Nombre AS NombreCentro, 
							CENTROS.IdGerente, 
							CENTROS.Vicepresidencia   
						FROM CENTROS 
							LEFT JOIN EMPLEADOS 
								ON CENTROS.IdGerente = EMPLEADOS.Id 
							LEFT JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id 
							LEFT JOIN CENTROS AS CENTROS2 
								ON EMPLEADOS.IdCentro = CENTROS2.Id 
						WHERE CENTROS.Id = $id;
				EOD;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Centro' 			=> $reg['Centro'],
						'Nombre' 			=> $reg['Nombre'], 
						'FechaInicio' 		=> $reg['FechaInicio'], 
						'FechaVencimiento' 	=> $reg['FechaVencimiento'], 
						'TipoEmpleado' 		=> $reg['TipoEmpleado'], 
						'IdGerente'			=> $reg['IdGerente'], 
						'Documento'			=> $reg['Documento'], 
						'NombreEmpleado'	=> $reg['Apellido1'] . ' ' . $reg['Apellido2'] . ' ' . $reg['Nombre1'] . ' ' . $reg['Nombre2'], 
						'Cargo'				=> $reg['NombreCargo'], 
						'Vicepresidencia'	=> $reg['Vicepresidencia']
					),
					'mensajeError' => ''
				);

				$query = <<<EOD
					SELECT EMPLEADOS.Documento, 
							EMPLEADOS.Apellido1, 
							EMPLEADOS.Apellido2, 
							EMPLEADOS.Nombre1, 
							EMPLEADOS.Nombre2, 
							CARGOS.NOmbre AS NombreCargo 
						FROM EMPLEADOS 
							INNER JOIN CENTROS 
								ON EMPLEADOS.IdCentro = CENTROS.Id
							INNER JOIN CARGOS 
								ON EMPLEADOS.IdCargo = CARGOS.Id
							INNER JOIN PARAMETROS 
								ON EMPLEADOS.Estado = PARAMETROS.Id 
						WHERE EMPLEADOS.IdCentro = $id AND 
							PARAMETROS.Detalle = 'ACTIVO' 
						ORDER BY EMPLEADOS.Apellido1, EMPLEADOS.Apellido2, EMPLEADOS.Nombre1, EMPLEADOS.Nombre2;
				EOD;

				$dataEmp = $this->model->listar($query);

				if ($dataEmp)
				{
					for ($i = 0; $i < count($dataEmp); $i++) 
					{ 
						$data['regEmp'][$i] = array(
							'Documento' 		=> $dataEmp[$i]['Documento'],
							'NombreEmpleado' 	=> $dataEmp[$i]['Apellido1'] . ' ' . $dataEmp[$i]['Apellido2'] . ' ' . $dataEmp[$i]['Nombre1'] . ' ' . $dataEmp[$i]['Nombre2'],
							'Cargo' => $dataEmp[$i]['NombreCargo']
						);
					}
				}
				else
					$data['regEmp'] = false;

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
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
			$_SESSION['Lista'] = SERVERURL . '/centros/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['CENTROS']['Filtro'];

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

					$query .= "UPPER(REPLACE(CENTROS.Centro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CENTROS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PARAMETROS.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['CENTROS']['Orden']; 
			$data['rows'] = $this->model->listarCentros($query);
			$this->views->getView($this, 'informe', $data);
		}

		public function importar()
		{
			$data = array();

			if (isset($_FILES) AND count($_FILES) > 0) 
			{
				if	( empty($_FILES['archivo']['name']) )
				{
					$data['mensajeError'] = "Seleccione un <strong>Archivo en Excel</strong><br>";
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
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$Centro 		= $oHoja->getCell('A' . $i)->getCalculatedValue();
									$NombreCentro 	= $oHoja->getCell('B' . $i)->getCalculatedValue();
									// $TipoEmpleado 	= $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Documento 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Borrado 		= $oHoja->getCell('E' . $i)->getCalculatedValue();


									// $regParametro = GetRegistro('PARAMETROS', 0, "PARAMETROS.Detalle = 'TipoEmpleado' AND PARAMETROS.Valor = $TipoEmpleado");

									// $TipoEmpleado = $regParametro['id'];

									if (! empty($Documento))
										$IdEmpleado = getId('EMPLEADOS', "EMPLEADOS.Documento = '$Documento'");
									else
										$IdEmpleado = 0;

									$datos = array(
										'Centro'		=> $Centro, 
										'NombreCentro'	=> $NombreCentro, 
										'IdGerente'		=> $IdEmpleado, 
										'Borrado'		=> $Borrado);

									$query = <<<EOD
										SELECT * 
											FROM CENTROS 
											WHERE CENTROS.Centro = '$Centro' AND 
												CENTROS.Borrado = 0;
									EOD;

									$reg = $this->model->buscarCentro($query);

									if ($reg) 
										$this->model->actualizarCentro($datos, $reg['id']);
									else
										$this->model->guardarCentro($datos);
								}
							}

							header('Location: ' . SERVERURL . '/centros/lista/1');
							exit;
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/centros/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/centros/lista/' . $_SESSION['CENTROS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>