<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Bancos extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/bancos/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/bancos/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/bancos/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['BANCOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['BANCOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['BANCOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['BANCOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['BANCOS']['Filtro']))
			{
				$_SESSION['BANCOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['BANCOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['BANCOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['BANCOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['BANCOS']['Orden'])) 
					$_SESSION['BANCOS']['Orden'] = 'BANCOS.Banco';

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

					$query .= "UPPER(REPLACE(BANCOS.Banco, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(BANCOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['BANCOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarBancos($query);
			$this->views->getView($this, 'bancos', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/bancos/actualizarBanco';
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
			$_SESSION['Lista'] = SERVERURL . '/bancos/lista/' . $_SESSION['BANCOS']['Pagina'];

			$data = array(
				'reg' => array(
					isset($_REQUEST['banco']) ? $_REQUEST['banco'] : '',
					isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '',
					isset($_REQUEST['nit']) ? $_REQUEST['nit'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['banco'])) 
			{
				if	( empty($data['reg'][0]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Banco') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM BANCOS ' .
							"WHERE BANCOS.Banco = '" . $data['reg'][0] . "'";

					$reg = $this->model->buscarBanco($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Banco') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg'][1]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg'][2]) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nit.') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarBanco($data['reg']);

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
			if (isset($_REQUEST['banco']))
			{
				$data = array(
					'reg' => array(
						'banco' => isset($_REQUEST['banco']) ? $_REQUEST['banco'] : '',
						'nombre' => isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '',
						'nit' => isset($_REQUEST['nit']) ? $_REQUEST['nit'] : ''
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['banco']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Banco') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM BANCOS WHERE BANCOS.Banco = '" . $data['reg']['banco'] . "' AND BANCOS.Id <> " . $id;

					$reg = $this->model->buscarBanco($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Banco') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				// if	( empty($data['reg']['nit']) )
				// 	$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nit') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarBanco($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/bancos/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/bancos/lista/' . $_SESSION['BANCOS']['Pagina'];

				$query = 'SELECT * FROM BANCOS WHERE BANCOS.Id = ' . $id;
				
				$data['reg'] = $this->model->leer($query);
				$data['mensajeError'] = '';
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM BANCOS WHERE BANCOS.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				// $query = 'SELECT COUNT(*) AS Registros ' .
				// 		'FROM NITS ' .
				// 		'WHERE NITS.IdBanco = ' . $id;

				// $reg = $this->model->buscarBanco($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Banco') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarBanco($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/bancos/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/bancos/lista/' . $_SESSION['BANCOS']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/bancos/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['BANCOS']['Filtro'];

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

					$query .= "UPPER(REPLACE(BANCOS.Banco, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(BANCOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['BANCOS']['Orden']; 
			$data['rows'] = $this->model->listarBancos($query);
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
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL BANCO PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM BANCOS ' .
										"WHERE BANCOS.Banco = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarBanco($query);

								if ($reg) 
									$this->model->actualizarBanco($Excel[$i], $reg['id']);
								else
									$this->model->guardarBanco($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/bancos/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/bancos/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/bancos/lista/' . $_SESSION['BANCOS']['Pagina'];
			
				// $_SESSION['Paginar'] = FALSE;
				
				// if	( isset($_REQUEST['Filtro']) )
				// {
				// 	$_SESSION['BANCOS']['Filtro'] = $_REQUEST['Filtro'];
				// 	$_SESSION['BANCOS']['Pagina'] = 1;
				// }

				// $lcFiltro = $_SESSION['BANCOS']['Filtro'];

				// if (isset($_REQUEST['Orden']))
				// {
				// 	$_SESSION['BANCOS']['Orden'] = $_REQUEST['Orden'];
				// 	$_SESSION['BANCOS']['Pagina'] = 1;
				// }
				// else
				// 	if (! isset($_SESSION['BANCOS']['Orden'])) 
				// 		$_SESSION['BANCOS']['Orden'] = 'BANCOS.Nombre';

				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>