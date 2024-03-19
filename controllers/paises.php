<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Paises extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/paises/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/paises/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/paises/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['PAISES']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['PAISES']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['PAISES']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['PAISES']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['PAISES']['Filtro']))
			{
				$_SESSION['PAISES']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['PAISES']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['PAISES']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['PAISES']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['PAISES']['Orden'])) 
					$_SESSION['PAISES']['Orden'] = 'PAISES.Nombre1';

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

					$query .= "UPPER(REPLACE(PAISES.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PAISES.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PAISES.Nombre3, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['PAISES']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarPaises($query);
			$this->views->getView($this, 'paises', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/paises/actualizarPais';
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
			$_SESSION['Lista'] = SERVERURL . '/paises/lista/' . $_SESSION['CIUDADES']['Pagina'];

			$data = array(
				'reg' => array(
					'Nombre1' => isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '',
					'Nombre2' => isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '',
					'Nombre3' => isset($_REQUEST['Nombre3']) ? $_REQUEST['Nombre3'] : '',
					'Iso2' => isset($_REQUEST['Iso2']) ? $_REQUEST['Iso2'] : '',
					'Iso3' => isset($_REQUEST['Iso3']) ? $_REQUEST['Iso3'] : '',
					'PhoneCode' => isset($_REQUEST['PhoneCode']) ? $_REQUEST['PhoneCode'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Nombre1'])) 
			{
				if	( empty($data['reg']['Nombre1']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre en español') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM PAISES ' .
							"WHERE PAISES.Nombre1 = '" . $data['reg']['Nombre1'] . "'";

					$reg = $this->model->buscarPais($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Nombre en español') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre2']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre en inglés') . '</strong><br>';
			
				if	( empty($data['reg']['Nombre3']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre en francés') . '</strong><br>';
	
				if	( empty($data['reg']['Iso2']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('ISO-2') . '</strong><br>';

				if	( empty($data['reg']['Iso3']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('ISO-3') . '</strong><br>';

				if	( empty($data['reg']['PhoneCode']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Phone code') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarPais($data['reg']);

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
			if (isset($_REQUEST['Nombre1']))
			{
				$data = array(
					'reg' => array(
						'Nombre1' => isset($_REQUEST['Nombre1']) ? $_REQUEST['Nombre1'] : '',
						'Nombre2' => isset($_REQUEST['Nombre2']) ? $_REQUEST['Nombre2'] : '',
						'Nombre3' => isset($_REQUEST['Nombre3']) ? $_REQUEST['Nombre3'] : '',
						'Iso2' => isset($_REQUEST['Iso2']) ? $_REQUEST['Iso2'] : '',
						'Iso3' => isset($_REQUEST['Iso3']) ? $_REQUEST['Iso3'] : '',
						'PhoneCode' => isset($_REQUEST['PhoneCode']) ? $_REQUEST['PhoneCode'] : '',
						'Orden' => isset($_REQUEST['Orden']) ? $_REQUEST['Orden'] : 1
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Nombre1']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre en español') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM PAISES WHERE PAISES.Nombre1 = '" . $data['reg']['Nombre1'] . "' AND PAISES.Id <> " . $id;

					$reg = $this->model->buscarPais($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Nombre en español') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre2']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre en inglés') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM PAISES WHERE PAISES.Nombre1 = '" . $data['reg']['Nombre2'] . "' AND PAISES.Id <> " . $id;

					$reg = $this->model->buscarPais($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Nombre en inglés') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre3']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre en francés') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM PAISES WHERE PAISES.Nombre1 = '" . $data['reg']['Nombre3'] . "' AND PAISES.Id <> " . $id;

					$reg = $this->model->buscarPais($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Nombre en francés') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Iso2']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('ISO-2') . '</strong><br>';
			
				if	( empty($data['reg']['Iso3']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('ISO-3') . '</strong><br>';
			
				if	( empty($data['reg']['PhoneCode']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Phone code') . '</strong><br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarPais($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/paises/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/paises/lista/' . $_SESSION['PAISES']['Pagina'];

				$query = 'SELECT * FROM PAISES WHERE PAISES.Id = ' . $id;

				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Id' => $reg['id'], 
						'Nombre1' => $reg['nombre1'],
						'Nombre2' => $reg['nombre2'],
						'Nombre3' => $reg['nombre3'],
						'Iso2' => $reg['iso2'],
						'Iso3' => $reg['iso3'],
						'PhoneCode' => $reg['phonecode'],
						'Orden' => $reg['orden']
					),
					'mensajeError' => ''
				);

				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM PAISES WHERE PAISES.Id = ' . $id;
				
			$reg = $this->model->leer($query);

			$data = array(
				'reg' => array(
					'Id' => $reg['id'], 
					'Nombre1' => $reg['nombre1'],
					'Nombre2' => $reg['nombre2'],
					'Nombre3' => $reg['nombre3'],
					'Iso2' => $reg['iso2'],
					'Iso3' => $reg['iso3'],
					'PhoneCode' => $reg['phonecode'],
					'Orden' => $reg['orden']
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Nombre1']))
			{
				$query = 'SELECT COUNT(*) AS Registros ' .
						'FROM EMPLEADOS ' .
						'WHERE EMPLEADOS.IdPais = ' . $id;

				$reg = $this->model->buscarPais($query);

				if ($reg['Registros'] > 0) 
				{
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con esta') . ' <strong>' . label('País') . '</strong><br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarPais($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/paises/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/paises/lista/' . $_SESSION['PAISES']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/paises/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['PAISES']['Filtro'];

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

					$query .= "UPPER(REPLACE(PAISES.Nombre1, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PAISES.Nombre2, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(PAISES.Nombre3, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['PAISES']['Orden']; 
			$data['rows'] = $this->model->listarPaises($query);
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
									$Excel[$row][2] = trim($oHoja->getCell('C' . $i)->getCalculatedValue());
									$Excel[$row][3] = trim($oHoja->getCell('D' . $i)->getCalculatedValue());
									$Excel[$row][4] = trim($oHoja->getCell('E' . $i)->getCalculatedValue());
									$Excel[$row][5] = trim($oHoja->getCell('F' . $i)->getCalculatedValue());
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS LA CIUDAD PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM PAISES ' .
										"WHERE PAISES.Nombre1 = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarPais($query);

								if ($reg) 
									$this->model->actualizarPais($Excel[$i], $reg['id']);
								else
									$this->model->guardarPais($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/paises/lista/' . $_SESSION['PAISES']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/paises/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/paises/lista/' . $_SESSION['PAISES']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>