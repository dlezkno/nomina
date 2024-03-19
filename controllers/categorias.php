<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Categorias extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/categorias/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/categorias/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/categorias/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['CATEGORIAS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['CATEGORIAS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['CATEGORIAS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['CATEGORIAS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['CATEGORIAS']['Filtro']))
			{
				$_SESSION['CATEGORIAS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['CATEGORIAS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['CATEGORIAS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['CATEGORIAS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['CATEGORIAS']['Orden'])) 
					$_SESSION['CATEGORIAS']['Orden'] = 'CATEGORIAS.Categoria';

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

					$query .= "UPPER(REPLACE(CATEGORIAS.Categoria, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CATEGORIAS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['CATEGORIAS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarCategorias($query);
			$this->views->getView($this, 'categorias', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/categorias/actualizarCategoria';
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
			$_SESSION['Lista'] = SERVERURL . '/categorias/lista/' . $_SESSION['CATEGORIAS']['Pagina'];

			$data = array(
				'reg' => array(
					isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : '',
					isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['categoria'])) 
			{
				if	( empty($data['reg'][0]) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Categoría') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM CATEGORIAS ' .
							"WHERE CATEGORIAS.Categoria = '" . $data['reg'][0] . "'";

					$reg = $this->model->buscarCategoria($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Categoria') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg'][1]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarCategoria($data['reg']);

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
			if (isset($_REQUEST['categoria']))
			{
				$data = array(
					'reg' => array(
						'categoria' => isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : '',
						'nombre' => isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : ''
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['categoria']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Categoría') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM CATEGORIAS WHERE CATEGORIAS.Categoria = '" . $data['reg']['categoria'] . "' AND CATEGORIAS.Id <> " . $id;

					$reg = $this->model->buscarCategoria($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Categoría') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarCategoria($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/categorias/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/categorias/lista/' . $_SESSION['CATEGORIAS']['Pagina'];

				$query = 'SELECT * FROM CATEGORIAS WHERE CATEGORIAS.Id = ' . $id;
				
				$data['reg'] = $this->model->leer($query);
				$data['mensajeError'] = '';
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM CATEGORIAS WHERE CATEGORIAS.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				// $query = 'SELECT COUNT(*) AS Registros ' .
				// 		'FROM NITS ' .
				// 		'WHERE NITS.IdCategoria = ' . $id;

				// $reg = $this->model->buscarCategoria($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con esta') . ' <strong>' . label('Categoría') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarCategoria($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/categorias/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/categorias/lista/' . $_SESSION['CATEGORIAS']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/categorias/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['CATEGORIAS']['Filtro'];

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

					$query .= "UPPER(REPLACE(CATEGORIAS.Categoria, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(CATEGORIAS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['CATEGORIAS']['Orden']; 
			$data['rows'] = $this->model->listarCategorias($query);
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
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL CENTRO DE COSTOS PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM CATEGORIAS ' .
										"WHERE CATEGORIAS.Categoria = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarCategoria($query);

								if ($reg) 
									$this->model->actualizarCategoria($Excel[$i], $reg['id']);
								else
									$this->model->guardarCategoria($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/categorias/lista/' . $_SESSION['CATEGORIAS']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/categorias/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/categorias/lista/1';
			
				// $_SESSION['Paginar'] = FALSE;
				
				// if	( isset($_REQUEST['Filtro']) )
				// {
				// 	$_SESSION['CATEGORIAS']['Filtro'] = $_REQUEST['Filtro'];
				// 	$_SESSION['CATEGORIAS']['Pagina'] = 1;
				// }

				// if (! isset($_SESSION['CATEGORIAS']['Filtro']))
				// {
				// 	$_SESSION['CATEGORIAS']['Filtro'] = '';
				// }
	
				// $lcFiltro = $_SESSION['CATEGORIAS']['Filtro'];

				// if (isset($_REQUEST['Orden']))
				// {
				// 	$_SESSION['CATEGORIAS']['Orden'] = $_REQUEST['Orden'];
				// 	$_SESSION['CATEGORIAS	']['Pagina'] = 1;
				// }
				// else
				// 	if (! isset($_SESSION['CATEGORIAS']['Orden'])) 
				// 		$_SESSION['CATEGORIAS']['Orden'] = 'CATEGORIAS.Categoria';

				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>