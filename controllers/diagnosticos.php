<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Diagnosticos extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/diagnosticos/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/diagnosticos/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/diagnosticos/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['DIAGNOSTICOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['DIAGNOSTICOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['DIAGNOSTICOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['DIAGNOSTICOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['DIAGNOSTICOS']['Filtro']))
			{
				$_SESSION['DIAGNOSTICOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['DIAGNOSTICOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['DIAGNOSTICOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['DIAGNOSTICOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['DIAGNOSTICOS']['Orden'])) 
					$_SESSION['DIAGNOSTICOS']['Orden'] = 'DIAGNOSTICOS.Diagnostico';

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

					$query .= "UPPER(REPLACE(DIAGNOSTICOS.Diagnostico, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(DIAGNOSTICOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['DIAGNOSTICOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarDiagnosticos($query);
			$this->views->getView($this, 'diagnosticos', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/diagnosticos/actualizarDiagnostico';
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
			$_SESSION['Lista'] = SERVERURL . '/diagnosticos/lista/' . $_SESSION['DIAGNOSTICOS']['Pagina'];

			$data = array(
				'reg' => array(
					isset($_REQUEST['diagnostico']) ? $_REQUEST['diagnostico'] : '',
					isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : ''
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['diagnostico'])) 
			{
				if	( empty($data['reg'][0]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Diagnóstico') . '</strong><br>';
				else
				{
					$query = 'SELECT * FROM DIAGNOSTICOS ' .
							"WHERE DIAGNOSTICOS.Diagnostico = '" . $data['reg'][0] . "'";

					$reg = $this->model->buscarDiagnostico($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Diagnóstico') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg'][1]) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarDiagnostico($data['reg']);

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
			if (isset($_REQUEST['diagnostico']))
			{
				$data = array(
					'reg' => array(
						'diagnostico' => isset($_REQUEST['diagnostico']) ? $_REQUEST['diagnostico'] : '',
						'nombre' => isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : ''
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['diagnostico']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Diagnóstico') . '</strong><br>';
				else
				{
					$query = "SELECT * FROM DIAGNOSTICOS WHERE DIAGNOSTICOS.Diagnostico = '" . $data['reg']['diagnostico'] . "' AND DIAGNOSTICOS.Id <> " . $id;

					$reg = $this->model->buscarDiagnostico($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Diagnóstico') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarDiagnostico($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/diagnosticos/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/diagnosticos/lista/' . $_SESSION['DIAGNOSTICOS']['Pagina'];

				$query = 'SELECT * FROM DIAGNOSTICOS WHERE DIAGNOSTICOS.Id = ' . $id;
				
				$data['reg'] = $this->model->leer($query);
				$data['mensajeError'] = '';
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			$query = 'SELECT * FROM DIAGNOSTICOS WHERE DIAGNOSTICOS.Id = ' . $id;
				
			$data['reg'] = $this->model->leer($query);
			$data['mensajeError'] = '';

			if (isset($_REQUEST['id']))
			{
				$query = 'SELECT COUNT(*) AS Registros ' .
						'FROM INCAPACIDADES ' .
						'WHERE INCAPACIDADES.IdDiagnostico = ' . $id;

				$reg = $this->model->buscarDiagnostico($query);

				if ($reg['registros'] > 0) 
				{
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Diagnóstico') . '</strong><br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarDiagnostico($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/diagnosticos/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/diagnosticos/lista/' . $_SESSION['DIAGNOSTICOS']['Pagina'];

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
			$_SESSION['Lista'] = SERVERURL . '/diagnosticos/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['DIAGNOSTICOS']['Filtro'];

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

					$query .= "UPPER(REPLACE(DIAGNOSTICOS.Diagnostico, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(DIAGNOSTICOS.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['DIAGNOSTICOS']['Orden']; 
			$data['rows'] = $this->model->listarDiagnosticos($query);
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
					ini_set('max_execution_time', 6000);
					
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
								// BUSCAMOS EL CENTRO DE COSTOS PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM DIAGNOSTICOS ' .
										"WHERE DIAGNOSTICOS.Diagnostico = '" . $Excel[$i][0] . "'";

								$reg = $this->model->buscarDiagnostico($query);

								if ($reg) 
									$this->model->actualizarDiagnostico($Excel[$i], $reg['id']);
								else
									$this->model->guardarDiagnostico($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/diagnosticos/lista/' . $_SESSION['DIAGNOSTICOS']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/diagnosticos/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/diagnosticos/lista/1';
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>