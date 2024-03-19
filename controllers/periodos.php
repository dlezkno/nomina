<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Periodos extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/periodos/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/periodos/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/periodos/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['PERIODOS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['PERIODOS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['PERIODOS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['PERIODOS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['PERIODOS']['Filtro']))
			{
				$_SESSION['PERIODOS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['PERIODOS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['PERIODOS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['PERIODOS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['PERIODOS']['Orden'])) 
					$_SESSION['PERIODOS']['Orden'] = 'PERIODOS.Referencia,PERIODOS.Periodicidad,PERIODOS.Periodo';

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if (empty($query))
						$query .= 'AND (';
					else
						$query .= 'OR ';

					if	( is_numeric($aFiltro[$lnCount]) )
					{
						$query .= 'PERIODOS.Referencia = ' . $aFiltro[$lnCount] . ' OR ';
						$query .= 'PERIODOS.Periodo = ' . $aFiltro[$lnCount] . ' ';
					}
					else 
					{
						if	( substr($aFiltro[$lnCount], 0, 1) == '#' )
						{
							$lcFiltro1 = substr($aFiltro[$lnCount], 1);
		
							$query .= "CAST(PERIODOS.FechaInicial AS VarChar) LIKE '" . mb_strtoupper($lcFiltro1) . "%' ";
							$query .= "OR CAST(PERIODOS.FechaFinal AS VarChar) LIKE '" . mb_strtoupper($lcFiltro1) . "%' ";
						}
						else
							$query .= "UPPER(REPLACE(PARAMETROS.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($lcFiltro) . "%' ";
					}
				}

				$query .= ')';
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['PERIODOS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarPeriodos($query);
			$this->views->getView($this, 'periodos', $data);
		}	
		
		public function adicionar()
		{
			$data = array(
				'reg' => array(
					'Referencia' => isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : 0,
					'Periodicidad' => isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : 0,
					'Periodo' => isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : 0,
					'FechaInicial' => isset($_REQUEST['FechaInicial']) ? $_REQUEST['FechaInicial'] : NULL,
					'FechaFinal' => isset($_REQUEST['FechaFinal']) ? $_REQUEST['FechaFinal'] : NULL
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Referencia']))
			{
				if	( empty($data['reg']['Referencia']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Referencia') . '</strong><br>';

				if	( empty($data['reg']['Periodicidad']) )
					$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Periodicidad') . '</strong><br>';

				if	( empty($data['reg']['Periodo']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Período') . '</strong><br>';

				if (empty($data['mensajeError'])) 
				{
					$Referencia = $data['reg']['Referencia'];
					$Periodicidad = $data['reg']['Periodicidad'];
					$Periodo = $data['reg']['Periodo'];

					$query = <<<EOD
						SELECT * 
							FROM PERIODOS 
							WHERE 
								PERIODOS.Referencia = $Referencia AND 
								PERIODOS.Periodicidad = $Periodicidad AND 
								PERIODOS.Periodo = $Periodo;
					EOD;

					$reg = $this->model->buscarPeriodo($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Período') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['FechaInicial']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';
			
				if	( empty($data['reg']['FechaFinal']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong><br>';
			
				if ($data['reg']['FechaInicial'] >= $data['reg']['FechaFinal'])
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong> ' . label('menor que la') . ' <strong>' . label('Fecha final') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarPeriodo($data['reg']);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/periodos/actualizarPeriodo';
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
				$_SESSION['Lista'] = SERVERURL . '/periodos/lista/' . $_SESSION['PERIODOS']['Pagina'];

				$this->views->getView($this, 'adicionar', $data);
			}
		}

		public function editar($id)
		{
			if (isset($_REQUEST['Referencia']))
			{
				$data = array(
					'reg' => array(
						'Referencia' => isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : 0,
						'Periodicidad' => isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : 0,
						'Periodo' => isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : 0,
						'FechaInicial' => isset($_REQUEST['FechaInicial']) ? $_REQUEST['FechaInicial'] : NULL,
						'FechaFinal' => isset($_REQUEST['FechaFinal']) ? $_REQUEST['FechaFinal'] : NULL
					),
					'mensajeError' => ''
				);
	
				if	( empty($data['reg']['Referencia']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Referencia') . '</strong><br>';

				if	( empty($data['reg']['Periodicidad']) )
					$data['mensajeError'] .= label('Debe seleccionar una') . ' <strong>' . label('Periodicidad') . '</strong><br>';

				if	( empty($data['reg']['Periodo']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Período') . '</strong><br>';

				if (empty($data['mensajeError'])) 
				{
					$Referencia = $data['reg']['Referencia'];
					$Periodicidad = $data['reg']['Periodicidad'];
					$Periodo = $data['reg']['Periodo'];

					$query = <<<EOD
						SELECT * 
							FROM PERIODOS 
							WHERE 
								PERIODOS.Referencia = $Referencia AND 
								PERIODOS.Periodicidad = $Periodicidad AND 
								PERIODOS.Periodo = $Periodo AND 
								PERIODOS.Id <> $id;
					EOD;

					$reg = $this->model->buscarPeriodo($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Período') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if ( empty($data['reg']['FechaInicial']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha de inicio') . '</strong><br>';
			
				if ( empty($data['reg']['FechaFinal']) )
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha final') . '</strong><br>';

				if ($data['reg']['FechaInicial'] >= $data['reg']['FechaFinal'])
					$data['mensajeError'] .= label('Debe digitar una') . ' <strong>' . label('Fecha inicial') . '</strong> ' . label('menor que la') . ' <strong>' . label('Fecha final') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarPeriodo($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/periodos/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/periodos/lista/' . $_SESSION['PERIODOS']['Pagina'];

				$query = <<<EOD
					SELECT * 
						FROM PERIODOS 
						WHERE PERIODOS.Id = $id;
				EOD;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Referencia' => $reg['referencia'],
						'Periodicidad' => $reg['periodicidad'], 
						'Periodo' => $reg['periodo'], 
						'FechaInicial' => $reg['fechainicial'], 
						'FechaFinal' => $reg['fechafinal'], 
					),
					'mensajeError' => ''
				);
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			if (isset($_REQUEST['Referencia']))
			{
				$data = array(
					'reg' => array(
						'Referencia' => isset($_REQUEST['Referencia']) ? $_REQUEST['Referencia'] : 0,
						'Periodicidad' => isset($_REQUEST['Periodicidad']) ? $_REQUEST['Periodicidad'] : 0,
						'Periodo' => isset($_REQUEST['Periodo']) ? $_REQUEST['Periodo'] : 0,
						'FechaInicial' => isset($_REQUEST['FechaInicial']) ? $_REQUEST['FechaInicial'] : NULL,
						'FechaFinal' => isset($_REQUEST['FechaFinal']) ? $_REQUEST['FechaFinal'] : NULL
					),
					'mensajeError' => ''
				);

				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM NOVEDADES 
						WHERE NOVEDADES.IdPeriodo = $id;
				EOD;

				// $reg = $this->model->buscarPeriodo($query);

				// if ($reg['registros'] > 0) 
				// {
				// 	$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Período de pago') . '</strong><br>';
				// }

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarPeriodo($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/periodos/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/periodos/lista/' . $_SESSION['PERIODOS']['Pagina'];

				$query = <<<EOD
					SELECT * 
						FROM PERIODOS 
						WHERE PERIODOS.Id = $id;
				EOD;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'Referencia' => $reg['referencia'],
						'Periodicidad' => $reg['periodicidad'],
						'Periodo' => $reg['periodo'],
						'FechaInicial' => $reg['fechainicial'],
						'FechaFinal' => $reg['fechafinal']
					),
					'mensajeError' => ''
				);
				
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
			$_SESSION['Lista'] = SERVERURL . '/periodos/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['PERIODOS']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$query = 'WHERE TRUE ';

				$aFiltro = explode(' ', $lcFiltro);

				for	( $lnCount = 0; $lnCount < count($aFiltro); $lnCount++ )
				{
					if	( is_numeric($aFiltro[$lnCount]) )
						$query .= 'AND (PERIODOS.Referencia = ' . $aFiltro[$lnCount] . ' OR ' .
									'PERIODOS.Periodicidad = ' . $aFiltro[$lnCount] . ' OR ' .
									'PERIODOS.Periodo = ' . $aFiltro[$lnCount] . ') ';
					else 
					{
						if	( substr($aFiltro[$lnCount], 0, 1) == '#' )
						{
							$lcFiltro1 = substr($aFiltro[$lnCount], 1);
		
							$query .= "AND ( " .
									"CAST(PERIODOS.FechaInicial AS VarChar) LIKE '" . mb_strtoupper($lcFiltro1) . "%' OR " .
									"CAST(PERIODOS.FechaFinal AS VarChar) LIKE '" . mb_strtoupper($lcFiltro1) . "%' ) ";
						}
						else
							$query .= "AND ( " .
									"CAST(PERIODOS.FechaInicial AS VarChar) LIKE '" . mb_strtoupper($aFiltro[$lnCount]) . "%' OR " .
									"CAST(PERIODOS.FechaFinal AS VarChar) LIKE '" . mb_strtoupper($aFiltro[$lnCount]) . "%' ) ";
					}
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['PERIODOS']['Orden']; 
			$data['rows'] = $this->model->listarPeriodos($query);
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
									$fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('D' . $i)->getValue());
									$Excel[$row][3] = $fecha->format('Y-m-d');
									$fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('E' . $i)->getValue());
									$Excel[$row][4] = $fecha->format('Y-m-d');
									$row++;
								}
							}

							for ( $i = 0; $i < count($Excel); $i++ )
							{
								// BUSCAMOS EL PERIODO PARA ADICIONAR O ACTUALIZAR
								$query = 'SELECT * ' .
										'FROM PERIODOS ' .
										'WHERE ' .
										'PERIODOS.Referencia = ' . $Excel[$i][0] . ' AND ' .
										'PERIODOS.Periodicidad = ' . $Excel[$i][1] . ' AND ' .
										'PERIODOS.Periodo = ' . $Excel[$i][2];

								$reg = $this->model->buscarPeriodo($query);

								if ($reg) 
									$this->model->actualizarPeriodo($Excel[$i], $reg['id']);
								else
									$this->model->guardarPeriodo($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/periodos/lista/' . $_SESSION['PERIODOS']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/periodos/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/periodos/lista/' . $_SESSION['PERIODOS']['Pagina'];
			
				// $_SESSION['Paginar'] = FALSE;
				
				// if	( isset($_REQUEST['Filtro']) )
				// {
				// 	$_SESSION['PERIODOS']['Filtro'] = $_REQUEST['Filtro'];
				// 	$_SESSION['PERIODOS']['Pagina'] = 1;
				// }

				// $lcFiltro = $_SESSION['PERIODOS']['Filtro'];

				// if (isset($_REQUEST['Orden']))
				// {
				// 	$_SESSION['PERIODOS']['Orden'] = $_REQUEST['Orden'];
				// 	$_SESSION['PERIODOS']['Pagina'] = 1;
				// }
				// else
				// 	if (! isset($_SESSION['PERIODOS']['Orden'])) 
				// 		$_SESSION['PERIODOS']['Orden'] = 'PERIODOS.Referencia,PERIODOS.Periodicidad,PERIODOS.Periodo';

				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>