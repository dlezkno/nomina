<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Idiomas extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/idiomas/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/idiomas/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/idiomas/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['IDIOMAS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['IDIOMAS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['IDIOMAS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['IDIOMAS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['IDIOMAS']['Filtro']))
			{
				$_SESSION['IDIOMAS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['IDIOMAS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['IDIOMAS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['IDIOMAS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['IDIOMAS']['Orden'])) 
					$_SESSION['IDIOMAS']['Orden'] = 'IDIOMAS.Idioma';

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

					$query .= "UPPER(REPLACE(IDIOMAS.Idioma, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(IDIOMAS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['IDIOMAS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarIdiomas($query);
			$this->views->getView($this, 'idiomas', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/idiomas/actualizarIdioma';
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
			$_SESSION['Lista'] = SERVERURL . '/idiomas/lista/' . $_SESSION['IDIOMAS']['Pagina'];

			$data = array(
				'reg' => array(
					isset($_REQUEST['idioma']) ? $_REQUEST['idioma'] : '',
					isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['idioma'])) 
			{
				if	( empty($data['reg'][0]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Idioma') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM IDIOMAS ' .
							"WHERE IDIOMAS.Idioma = '" . $data['reg'][0] . "'";

					$reg = $this->model->buscarIdioma($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Idioma') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg'][1]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarIdioma($data['reg']);

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
			if (isset($_REQUEST['idioma']))
			{
				$data = array(
					'reg' => array(
						'idioma' => isset($_REQUEST['idioma']) ? $_REQUEST['idioma'] : '',
						'nombre' => isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '',
						'orden' => isset($_REQUEST['orden']) ? $_REQUEST['orden'] : ''
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['idioma']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Idioma') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM IDIOMAS WHERE IDIOMAS.Idioma = '" . $data['reg']['idioma'] . "' AND IDIOMAS.Id <> " . $id;

					$reg = $this->model->buscarIdioma($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Idioma') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarIdioma($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/idiomas/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/idiomas/lista/' . $_SESSION['IDIOMAS']['Pagina'];

				$query = 'SELECT * FROM IDIOMAS WHERE IDIOMAS.Id = ' . $id;
				
				$data['reg'] = $this->model->leer($query);
				$data['mensajeError'] = '';
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM IDIOMAS WHERE IDIOMAS.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				$query = 'SELECT COUNT(*) AS Registros ' .
						'FROM IDIOMASEMPLEADO ' .
						'WHERE IDIOMASEMPLEADO.IdIdioma = ' . $id;

				$reg = $this->model->buscarIdioma($query);

				if ($reg['registros'] > 0) 
				{
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Idioma') . '</strong><br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarIdioma($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/idiomas/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/idiomas/lista/' . $_SESSION['IDIOMAS']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/idiomas/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['IDIOMAS']['Filtro'];

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

					$query .= "UPPER(REPLACE(IDIOMAS.Idioma, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(IDIOMAS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['IDIOMAS']['Orden']; 
			$data['rows'] = $this->model->listarIdiomas($query);
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
									$Excel[$row][0] = trim($oHoja->getCell('A' . $i)->getCalculatedValue());
									$Excel[$row][1] = trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL IDIOMA PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM IDIOMAS ' .
										"WHERE IDIOMAS.Idioma = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarIdioma($query);

								if ($reg) 
									$this->model->actualizarIdioma($Excel[$i], $reg['id']);
								else
									$this->model->guardarIdioma($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/idiomas/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/idiomas/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/idiomas/lista/' . $_SESSION['IDIOMAS']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>