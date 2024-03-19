<?php
	require_once('./templates/vendor/autoload.php');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Parametros extends Controllers
	{
		public function lista($pagina)
		{
			$_SESSION['NuevoRegistro'] = SERVERURL . '/parametros/adicionar';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = '';
			$_SESSION['Retroceder'] = '';
			$_SESSION['Avanzar'] = '';
			$_SESSION['Novedades'] = '';
			if ($_SESSION['Login']['Perfil'] == ADMINISTRADOR)
				$_SESSION['Importar'] = SERVERURL . '/parametros/importar';
			else
				$_SESSION['Importar'] = '';
			$_SESSION['ImportarArchivo'] = '';
			$_SESSION['Exportar'] = '';
			$_SESSION['ExportarArchivo'] = '';
			$_SESSION['Informe'] = SERVERURL . '/parametros/informe';
			$_SESSION['GenerarInforme'] = '';
			$_SESSION['Correo'] = '';
			$_SESSION['Lista'] = '';
		
			$_SESSION['Paginar'] = TRUE;

			$_SESSION['PARAMETROS']['Pagina'] = $pagina;
			$_SESSION['PaginaActual'] = $_SESSION['PARAMETROS']['Pagina'];
			
			if	( isset($_REQUEST['Filtro']) )
			{
				$_SESSION['PARAMETROS']['Filtro'] = $_REQUEST['Filtro'];
				$_SESSION['PARAMETROS']['Pagina'] = 1;
				$pagina = 1;
			}

			if (! isset($_SESSION['PARAMETROS']['Filtro']))
			{
				$_SESSION['PARAMETROS']['Filtro'] = '';
			}

			$lcFiltro = $_SESSION['PARAMETROS']['Filtro'];

			if (isset($_REQUEST['Orden']))
			{
				$_SESSION['PARAMETROS']['Orden'] = $_REQUEST['Orden'];
				$_SESSION['PARAMETROS']['Pagina'] = 1;
				$pagina = 1;
			}
			else
				if (! isset($_SESSION['PARAMETROS']['Orden'])) 
					$_SESSION['PARAMETROS']['Orden'] = 'PARAMETROS.Parametro,PARAMETROS.Valor';

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

					if	( is_numeric($aFiltro[$lnCount]) )
						$query .= 'PARAMETROS.Valor = ' . $aFiltro[$lnCount] . ' ';
					else 
					{
						if	( substr($aFiltro[$lnCount], 0, 1) == '#' )
						{
							$lcFiltro1 = substr($aFiltro[$lnCount], 1);
		
							$query .= "CAST(PARAMETROS.Fecha AS VarChar) LIKE '" . mb_strtoupper($lcFiltro1) . "%' ";
						}
						else
							$query .= "UPPER(REPLACE(PARAMETROS.Parametro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
							$query .= "OR UPPER(REPLACE(PARAMETROS.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ')) LIKE '%" . mb_strtoupper($aFiltro[$lnCount]) . "%' ";
					}
				}
			}
			
			$data['registros'] = $this->model->contarRegistros($query);
			$lineas = LINES;
			$offset = (min($pagina, intdiv($data['registros'], $lineas) + 1) - 1) * $lineas;
			$query .= 'ORDER BY ' . $_SESSION['PARAMETROS']['Orden'] . ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $lineas . ' ROWS ONLY'; 
			$data['rows'] = $this->model->listarParametros($query);
			$this->views->getView($this, 'parametros', $data);
		}	
		
		public function adicionar()
		{
			$_SESSION['NuevoRegistro'] = '';
			$_SESSION['BorrarRegistro'] = '';
			$_SESSION['ActualizarRegistro'] = SERVERURL . '/parametros/actualizarParametro';
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
			$_SESSION['Lista'] = SERVERURL . '/parametros/lista/' . $_SESSION['PARAMETROS']['Pagina'];

			$data = array(
				'reg' => array(
					'Parametro' => isset($_REQUEST['Parametro']) ? $_REQUEST['Parametro'] : '',
					'Detalle' => isset($_REQUEST['Detalle']) ? $_REQUEST['Detalle'] : '',
					'Valor' => isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : 0,
					'Valor2' => isset($_REQUEST['Valor2']) ? $_REQUEST['Valor2'] : 0,
					'Texto' => isset($_REQUEST['Texto']) ? $_REQUEST['Texto'] : '',
					'Fecha' => (isset($_REQUEST['Fecha']) AND ! empty($_REQUEST['Fecha'])) ? $_REQUEST['Fecha'] : NULL
				),
				'mensajeError' => ''
			);

			if (isset($_REQUEST['Parametro'])) 
			{
				if	( empty($data['reg']['Parametro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Parámetro') . '</strong><br>';
	
				if	( empty($data['reg']['Detalle']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Detalle') . '</strong><br>';

				if	( $data['reg']['Valor'] < 0 )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor') . '</strong><br>';

				$Parametro = $data['reg']['Parametro'];
				$Detalle = $data['reg']['Detalle'];
				$Valor = $data['reg']['Valor'];

				$query = <<<EOD
					SELECT * FROM PARAMETROS 
						WHERE PARAMETROS.Parametro = '$Parametro' AND 
							PARAMETROS.Detalle = '$Detalle' AND 
							PARAMETROS.Valor = $Valor;
				EOD;

				$ok = $this->model->leer($query);

				if ($ok) 
					$data['mensajeError'] .= '<strong>' . label('Parámetro') . '</strong> ' . label('ya existe') . '<br>';
	
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'adicionar', $data);
				else
				{
					$id = $this->model->guardarParametro($data['reg']);

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

		public function editar($Id)
		{
			if (isset($_REQUEST['Parametro']))
			{
				$data = array(
					'reg' => array(
						'Parametro' => isset($_REQUEST['Parametro']) ? $_REQUEST['Parametro'] : '',
						'Detalle' => isset($_REQUEST['Detalle']) ? $_REQUEST['Detalle'] : '',
						'Valor' => isset($_REQUEST['Valor']) ? $_REQUEST['Valor'] : '',
						'Valor2' => isset($_REQUEST['Valor2']) ? $_REQUEST['Valor2'] : '',
						'Texto' => isset($_REQUEST['Texto']) ? $_REQUEST['Texto'] : '',
						'Fecha' => (isset($_REQUEST['Fecha']) AND ! empty($_REQUEST['Fecha'])) ? $_REQUEST['Fecha'] : NULL
					),
					'mensajeError' => ''
				);

				if	( empty($data['reg']['Parametro']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Parámetro') . '</strong><br>';
	
				if	( empty($data['reg']['Detalle']) )
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Detalle') . '</strong><br>';

				if	( $data['reg']['Valor'] < 0)
					$data['mensajeError'] .= label('Debe digitar un') . ' <strong>' . label('Valor') . '</strong><br>';

				$Parametro = $data['reg']['Parametro'];
				$Detalle = $data['reg']['Detalle'];
				$Valor = $data['reg']['Valor'];

				$query = <<<EOD
					SELECT * FROM PARAMETROS 
						WHERE PARAMETROS.Parametro = '$Parametro' AND 
							PARAMETROS.Detalle = '$Detalle' AND 
							PARAMETROS.Valor = $Valor AND 
							PARAMETROS.Id <> $Id;
				EOD;

				$ok = $this->model->leer($query);

				if ($ok) 
					$data['mensajeError'] .= '<strong>' . label('Parámetro') . '</strong> ' . label('ya existe') . '<br>';

				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->actualizarParametro($data['reg'], $Id);

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
				$_SESSION['ActualizarRegistro'] = SERVERURL . '/parametros/actualizar';
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
				$_SESSION['Lista'] = SERVERURL . '/parametros/lista/' . $_SESSION['PARAMETROS']['Pagina'];

				$query = <<<EOD
					SELECT PARAMETROS.* 
						FROM PARAMETROS  
						WHERE PARAMETROS.Id = $Id;
				EOD;

				$reg = $this->model->leer($query);

				if ($reg)
				{
					$data['reg'] = array(
						'Id' => $reg['id'], 
						'Parametro' => $reg['parametro'], 
						'Detalle' => $reg['detalle'], 
						'Valor' => $reg['valor'], 
						'Valor2' => $reg['valor2'], 
						'Texto' => $reg['texto'], 
						'Fecha' => $reg['fecha']);
					$data['mensajeError'] = '';

					if ($data) 
						$this->views->getView($this, 'actualizar', $data);
				}
			}
		}

		public function borrar($Id)
		{
			if (isset($_REQUEST['Parametro']))
			{
				if	( $data['mensajeError'] )
					$this->views->getView($this, 'actualizar', $data);
				else
				{
					$resp = $this->model->borrarParametro($Id);

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
				$_SESSION['BorrarRegistro'] = SERVERURL . '/parametros/borrar';
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
				$_SESSION['Lista'] = SERVERURL . '/parametros/lista/' . $_SESSION['PARAMETROS']['Pagina'];

				$query = <<<EOD
					SELECT PARAMETROS.* 
						FROM PARAMETROS  
						WHERE PARAMETROS.Id = $Id;
				EOD;

				$reg = $this->model->leer($query);

				if ($reg)
				{
					$data['reg'] = array(
						'Id' => $reg['id'], 
						'Parametro' => $reg['parametro'], 
						'Detalle' => $reg['detalle'], 
						'Valor' => $reg['valor'], 
						'Valor2' => $reg['valor2'], 
						'Texto' => $reg['texto'], 
						'Fecha' => $reg['fecha']);
					$data['mensajeError'] = '';

					if ($data) 
						$this->views->getView($this, 'actualizar', $data);
				}
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
			$_SESSION['Lista'] = SERVERURL . '/parametros/lista/1';
		
			$_SESSION['Paginar'] = FALSE;

			$lcFiltro = $_SESSION['PARAMETROS']['Filtro'];

			$query = '';

			if	( ! empty($lcFiltro) )
			{
				$lcFiltro = mb_strtoupper($lcFiltro); 

				$query = <<<EOD
					WHERE (REPLACE(PARAMETROS.Parametro, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ') LIKE '%$lcFiltro%' OR  
						REPLACE(PARAMETROS.Detalle, 'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜñ', 'AEIOUAEIOUAEIOUAEIOUÑ') LIKE '%$lcFiltro%')
				EOD;
			}
			
			$query .= 'ORDER BY ' . $_SESSION['PARAMETROS']['Orden']; 
			$data['rows'] = $this->model->listarParametros($query);
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
									$Parametro 	= trim($oHoja->getCell('A' . $i)->getCalculatedValue());
									$Detalle 	= trim($oHoja->getCell('B' . $i)->getCalculatedValue());
									$Valor 		= $oHoja->getCell('C' . $i)->getCalculatedValue();
									$Valor2 	= $oHoja->getCell('D' . $i)->getCalculatedValue();
									$Texto 		= trim($oHoja->getCell('E' . $i)->getCalculatedValue());
									$Fecha 		= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($oHoja->getCell('F' . $i)->getCalculatedValue())->format('Y-m-d');

									$aParametro = array(
										'Parametro' => $Parametro, 
										'Detalle' 	=> $Detalle, 
										'Valor' 	=> $Valor, 
										'Valor2' 	=> $Valor2, 
										'Texto' 	=> $Texto, 
										'Fecha' 	=> $Fecha 
									);
								
									// BUSCAMOS EL PARAMETRO
									$query = <<<EOD
										SELECT * 
											FROM PARAMETROS 
											WHERE PARAMETROS.Parametro = '$Parametro'' AND 
												PARAMETROS.Detalle = '$Detalle' AND 
												PARAMETROS.Valor = $Valor;
									EOD;

									$reg = $this->model->leer($query);

									if ($reg) 
										$this->model->actualizarParametro($aParametro, $reg['id']);
									else
										$this->model->guardarParametro($aParametero);
								}
							}

							header('Location: ' . SERVERURL . '/parametros/lista/' . $_SESSION['PARAMETROS']['Pagina']);
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
				$_SESSION['ImportarArchivo'] =  SERVERURL . '/parametros/importar';
				$_SESSION['Exportar'] = '';
				$_SESSION['ExportarArchivo'] = '';
				$_SESSION['Informe'] = '';
				$_SESSION['GenerarInforme'] = '';
				$_SESSION['Correo'] = '';
				$_SESSION['Lista'] = SERVERURL. '/parametros/lista/' . $_SESSION['PARAMETROS']['Pagina'];
			
				// $_SESSION['Paginar'] = FALSE;
				
				// if	( isset($_REQUEST['Filtro']) )
				// {
				// 	$_SESSION['PARAMETROS']['Filtro'] = $_REQUEST['Filtro'];
				// 	$_SESSION['PARAMETROS']['Pagina'] = 1;
				// }

				// $lcFiltro = $_SESSION['PARAMETROS']['Filtro'];

				// if (isset($_REQUEST['Orden']))
				// {
				// 	$_SESSION['PARAMETROS']['Orden'] = $_REQUEST['Orden'];
				// 	$_SESSION['PARAMETROS']['Pagina'] = 1;
				// }
				// else
				// 	if (! isset($_SESSION['PARAMETROS']['Orden'])) 
				// 		$_SESSION['PARAMETROS']['Orden'] = 'PARAMETROS.Parametro,PARAMETROS.Valor';

				$this->views->getView($this, 'importar', $data);
			}
		}
	}
?>