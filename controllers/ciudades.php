<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Ciudades extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/ciudades/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/ciudades/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/ciudades/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['CIUDADES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['CIUDADES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['CIUDADES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['CIUDADES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['CIUDADES']['Filtro']))
			{
				$_SESSION['CIUDADES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['CIUDADES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['CIUDADES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['CIUDADES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['CIUDADES']['Orden'])) 
					$_SESSION['CIUDADES']['Orden'] = 'CIUDADES.Nombre';

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

					$query .= "UPPER(REPLACE(CIUDADES.Ciudad, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Departamento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PAISES.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['CIUDADES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarCiudades($query);
			$this->views->getView($this, 'ciudades', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/ciudades/actualizarCiudad';
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
			$_SESSION['Lista'] = SERVERURL . '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina'];

			$data = array(
				'reg' => array(
					isset($_REQUEST['ciudad']) ? $_REQUEST['ciudad'] : '',
					isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '',
					isset($_REQUEST['departamento']) ? $_REQUEST['departamento'] : '',
					isset($_REQUEST['idpais']) ? $_REQUEST['idpais'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['ciudad'])) 
			{
				if	( empty($data['reg'][0]) )
					$data['mensajeError'] .= label('Debe digitar un código de') . ' <strong>' . label('Ciudad') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM CIUDADES ' .
							"WHERE CIUDADES.Ciudad = '" . $data['reg'][0] . "'";

					$reg = $this->model->buscarCiudad($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Ciudad') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg'][1]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg'][2]) )
					// $data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Departamento') . '</strong><br>';
	
				if	( empty($data['reg'][3]) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('País') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarCiudad($data['reg']);

					if ($id) 
					{
						header('Location: ' . $_SESSION['Lista']);
						exit();
					}
				}
			}
			else
				$this->views->getView($this, 'adicionar', $data);
		}

		public function editar($id)
		{
			if (isset($_REQUEST['ciudad']))
			{
				$data = array(
					'reg' => array(
						'ciudad' => isset($_REQUEST['ciudad']) ? $_REQUEST['ciudad'] : '',
						'nombre' => isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '',
						'departamento' => isset($_REQUEST['departamento']) ? $_REQUEST['departamento'] : '',
						'idpais' => isset($_REQUEST['idpais']) ? $_REQUEST['idpais'] : '',
						'orden' => isset($_REQUEST['orden']) ? $_REQUEST['orden'] : 1
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['ciudad']) )
					$data['mensajeError'] .= label('Debe digitar un código de') . ' <strong>' . label('Ciudad') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM CIUDADES WHERE CIUDADES.Ciudad = '" . $data['reg']['ciudad'] . "' AND CIUDADES.Id <> " . $id;

					$reg = $this->model->buscarCiudad($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Ciudad') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['departamento']) )
					// $data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Departamento') . '</strong><br>';
	
				if	( empty($data['reg']['idpais']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('País') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarCiudad($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/ciudades/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina'];

				$query = 'SELECT * FROM CIUDADES WHERE CIUDADES.Id = ' . $id;
				
				$data['reg'] = $this->model->leer($query);
				$data['mensajeError'] = '';
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM CIUDADES WHERE CIUDADES.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				$query = 'SELECT COUNT(*) AS Registros ' .
						'FROM EMPLEADOS ' .
						'WHERE EMPLEADOS.IdCiudad = ' . $id;

				$reg = $this->model->buscarCiudad($query);

				if ($reg['registros'] > 0) 
				{
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con esta') . ' <strong>' . label('Ciudad') . '</strong><br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarCiudad($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/ciudades/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/ciudades/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['CIUDADES']['Filtro'];

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

					$query .= "UPPER(REPLACE(CIUDADES.Ciudad, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CIUDADES.Departamento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PAISES.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['CIUDADES']['Orden']; 
			$data['rows'] = $this->model->listarCiudades($query);
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
		
							$row = 0;
							$Excel = array();
		
							for ( $i = 2; $i <= $oHoja->getHighestRow(); $i++ )
							{
								if	( ! empty($oHoja->getCell('A' . $i)->getCalculatedValue()) )
								{
									$Excel[$row][0] = $oHoja->getCell('A' . $i)->getCalculatedValue();
									$Excel[$row][1] = $oHoja->getCell('B' . $i)->getCalculatedValue();
									$Excel[$row][2] = $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Excel[$row][3] = $oHoja->getCell('D' . $i)->getCalculatedValue();
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS LA CIUDAD PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM CIUDADES ' .
										"WHERE CIUDADES.Ciudad = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarCiudad($query);

								if ($reg) 
									$this->model->actualizarCiudad($Excel[$i], $reg['id']);
								else
									$this->model->guardarCiudad($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/ciudades/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/ciudades/lista/' . $_SESSION['CIUDADES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>