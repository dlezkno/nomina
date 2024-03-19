<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class TipoDoc extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/tipodoc/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/tipodoc/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['TIPODOC']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['TIPODOC']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['TIPODOC']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['TIPODOC']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['TIPODOC']['Filtro']))
			{
				$_SESSION['TIPODOC']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['TIPODOC']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['TIPODOC']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['TIPODOC']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['TIPODOC']['Orden'])) 
					$_SESSION['TIPODOC']['Orden'] = 'TIPODOC.TipoDocumento';

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

					$query .= "UPPER(REPLACE(TIPODOC.TipoDocumento, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(TIPODOC.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['TIPODOC']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarTipoDoc($query);
			$this->views->getView($this, 'tipodoc', $data);
		}	
		
		public function adicionar()
		{
			$data = array(
				'reg' => array(
					'TipoDocumento' => isset($_REQUEST['TipoDocumento']) ? $_REQUEST['TipoDocumento'] : '',
					'Nombre' => isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
                    'TipoNumeracion' => isset($_REQUEST['TipoNumeracion']) ? $_REQUEST['TipoNumeracion'] : 0,
					'Prefijo' => isset($_REQUEST['Prefijo']) ? $_REQUEST['Prefijo'] : '',
					'Secuencia' => isset($_REQUEST['Secuencia']) ? $_REQUEST['Secuencia'] : 0
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['TipoDocumento'])) 
			{
				if	( empty($data['reg']['TipoDocumento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Tipo de documento') . '</strong><br>';
				else
				{
					$TipoDocumento = $data['reg']['TipoDocumento'];

					$query = <<<EOD
						SELECT * 
							FROM TIPODOC 
							WHERE TIPODOC.TipoDocumento = '$TipoDocumento';
					EOD;

					$reg = $this->model->buscarTipoDoc($query);

					if ($reg) 
						$data['mensajeError'] .= '<strong>' . label('Tipo de documento') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';
			
                if	( empty($data['reg']['TipoNumeracion']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de numeración') . '</strong><br>';
			
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarTipoDoc($data['reg']);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/tipodoc/actualizarTipoDoc';
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
				$_SESSION['Lista'] = SERVERURL . '/tipodoc/lista/' . $_SESSION['TIPODOC']['Pagina'];
	
				$this->views->getView($this, 'adicionar', $data);
			}
		}

		public function editar($id)
		{
			if (isset($_REQUEST['TipoDocumento']))
			{
				$data = array(
					'reg' => array(
                        'TipoDocumento' => isset($_REQUEST['TipoDocumento']) ? $_REQUEST['TipoDocumento'] : '',
                        'Nombre' => isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
                        'TipoNumeracion' => isset($_REQUEST['TipoNumeracion']) ? $_REQUEST['TipoNumeracion'] : 0,
                        'Prefijo' => isset($_REQUEST['Prefijo']) ? $_REQUEST['Prefijo'] : '',
                        'Secuencia' => isset($_REQUEST['Secuencia']) ? $_REQUEST['Secuencia'] : 0
                        ),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['TipoDocumento']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Tipo de documento') . '</strong><br>';
				else
				{
					$TipoDocumento = $data['reg']['TipoDocumento'];

					$query = <<<EOD
						SELECT * 
							FROM TIPODOC
							WHERE TIPODOC.TipoDocumento = '$TipoDocumento' AND 
								TIPODOC.Id <> $id;
					EOD;

					$reg = $this->model->buscarTipoDoc($query);

					if ($reg) 
                        $data['mensajeError'] .= '<strong>' . label('Tipo de documento') . '</strong> ' . label('ya existe') . '<br>';
				}
	
				if	( empty($data['reg']['Nombre']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Nombre') . '</strong><br>';

                if	( empty($data['reg']['TipoNumeracion']) )
					$data['mensajeError'] .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de numeración') . '</strong><br>';
                    
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarTipoDoc($data['reg'], $id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/tipodoc/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/tipodoc/lista/' . $_SESSION['TIPODOC']['Pagina'];

				$query = <<<EOD
					SELECT * 
						FROM TIPODOC 
						WHERE TIPODOC.Id = $id;
				EOD;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'TipoDocumento' => $reg['tipodocumento'],
						'Nombre' => $reg['nombre'],
                        'TipoNumeracion' => $reg['tiponumeracion'], 
                        'Prefijo' => $reg['prefijo'], 
                        'Secuencia' => $reg['secuencia']
					),
					'mensajeError' => ''
				);
				
				if ($data) 
					$this->views->getView($this, 'actualizar', $data);
			}
		}

		public function borrar($id)
		{
			if (isset($_REQUEST['TipoDocumento']))
			{
				$data = array(
					'reg' => array(
                        'TipoDocumento' => isset($_REQUEST['TipoDocumento']) ? $_REQUEST['TipoDocumento'] : '',
                        'Nombre' => isset($_REQUEST['Nombre']) ? $_REQUEST['Nombre'] : '',
                        'TipoNumeracion' => isset($_REQUEST['TipoNumeracion']) ? $_REQUEST['TipoNumeracion'] : 0,
                        'Prefijo' => isset($_REQUEST['Prefijo']) ? $_REQUEST['Prefijo'] : '',
                        'Secuencia' => isset($_REQUEST['Secuencia']) ? $_REQUEST['Secuencia'] : 0
					),
					'mensajeError' => ''
				);

				$query = <<<EOD
					SELECT COUNT(*) AS Registros 
						FROM COMPROBANTES 
						WHERE COMPROBANTES.IdTipoDoc = $id;
				EOD;

				$reg = $this->model->buscarTipoDoc($query);

				if ($reg['registros'] > 0) 
				{
					$data['mensajeError'] .= label('Existe información en otras tablas relacionada con este') . ' <strong>' . label('Tipo de documento') . '</strong><br>';
				}

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarTipoDoc($id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/tipodoc/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/tipodoc/lista/' . $_SESSION['TIPODOC']['Pagina'];

				$query = <<<EOD
					SELECT * 
						FROM TIPODOC 
						WHERE TIPODOC.Id = $id;
				EOD;
				
				$reg = $this->model->leer($query);

				$data = array(
					'reg' => array(
						'TipoDocumento' => $reg['tipodocumento'],
						'Nombre' => $reg['nombre'],
                        'TipoNumeracion' => $reg['tiponumeracion'], 
                        'Prefijo' => $reg['prefijo'], 
                        'Secuencia' => $reg['secuencia']
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
			$_SESSION['Lista'] = SERVERURL . '/tipodoc/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['TIPODOC']['Filtro'];

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

					$query .= "UPPER(REPLACE(TIPODOC.TipoDoc, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					$query .= "OR UPPER(REPLACE(TIPODOC.Nombre, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
				}
			}
			
			$query .= 'ORDER BY ' . $_SESSION['TIPODOC']['Orden']; 
			$data['rows'] = $this->model->listarTipoDocs($query);
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
								// BUSCAMOS EL CENTRO DE COSTOS PARA ADICIONAR O ACTUALIZAR
								$TipoDoc = $Excel[$i][0];
								$query = <<<EOD
									SELECT * 
										FROM TIPODOC 
										WHERE TIPODOC.TipoDoc = '$TipoDoc';
								EOD;

								$reg = $this->model->buscarTipoDoc($query);

								if ($reg) 
									$this->model->actualizarTipoDoc($Excel[$i], $reg['id']);
								else
									$this->model->guardarTipoDoc($Excel[$i]);
							}

							header('Location: ' . SERVERURL . '/tipodoc/lista/1');
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/tipodoc/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/tipodoc/lista/' . $_SESSION['TIPODOC']['Pagina'];
			
				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>